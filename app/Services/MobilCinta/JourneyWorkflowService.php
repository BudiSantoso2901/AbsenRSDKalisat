<?php

namespace App\Services\MobilCinta;

use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class JourneyWorkflowService
{
    public function createRequest(Account $actor, array $input): Journey
    {
        $this->assertRole($actor, 'perawat');
        $validated = Validator::make($input, [
            'ruangan_id' => ['required', 'integer', 'exists:ruangans,id'],
            'nama_pasien' => ['required', 'string', 'max:255'],
            'nomor_rekam_medis' => ['required', 'string', 'max:255'],
            'jenis_kelamin_pasien' => ['required', 'string', 'max:30'],
            'penjamin' => ['required', 'string', 'max:255'],
            'tujuan' => ['required', 'string', 'max:255'],
            'catatan' => ['required', 'string', 'max:5000'],
        ])->validate();

        return DB::transaction(function () use ($actor, $validated) {
            $journey = Journey::query()->create([
                ...$validated,
                'nomor_perjalanan' => $this->newJourneyNumber(),
                'diajukan_oleh_id' => $actor->getKey(),
                'diajukan_pada' => now(),
            ]);

            return $journey;
        });
    }

    /**
     * Memperbarui pengajuan perjalanan milik Perawat.
     */
    public function updateRequest(
        Journey $journey,
        Account $actor,
        array $input
    ): Journey {
        $this->assertRole($actor, 'perawat');

        $validated = Validator::make($input, [
            'ruangan_id' => ['required', 'integer', 'exists:ruangans,id'],
            'nama_pasien' => ['required', 'string', 'max:255'],
            'nomor_rekam_medis' => ['required', 'string', 'max:255'],
            'jenis_kelamin_pasien' => ['required', 'in:L,P'],
            'penjamin' => ['required', 'in:UHC,PBI,NON PBI'],
            'tujuan' => ['required', 'string', 'max:255'],
            'catatan' => ['required', 'string', 'max:5000'],
        ])->validate();

        return DB::transaction(function () use ($journey, $actor, $validated) {
            $locked = Journey::query()
                ->lockForUpdate()
                ->findOrFail($journey->getKey());

            // Pastikan pengajuan milik Perawat yang sedang login.
            if (
                (int) $locked->diajukan_oleh_id !==
                (int) $actor->getKey()
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Anda tidak berhak mengubah pengajuan milik akun lain.',
                ]);
            }

            // Hanya status diajukan yang boleh diedit.
            if ($locked->status !== Journey::STATUS_REQUESTED) {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan sudah diproses dan tidak dapat diedit.',
                ]);
            }

            $locked->fill($validated);
            $locked->save();

            return $locked->fresh([
                'requester',
                'room',
            ]);
        });
    }

    public function assign(Journey $journey, Account $driver, Vehicle $vehicle, Account $coordinator): Journey
    {
        $this->assertRole($coordinator, 'koordinator');
        $this->assertRole($driver, 'sopir');

        return DB::transaction(function () use ($journey, $driver, $vehicle, $coordinator) {
            $locked = Journey::query()->lockForUpdate()->findOrFail($journey->getKey());

            if (
                !in_array($locked->status, [
                    Journey::STATUS_REQUESTED,
                    Journey::STATUS_ASSIGNED,
                    Journey::STATUS_REJECTED,
                ], true)
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Perjalanan ini tidak dapat ditugaskan pada status saat ini.',
                ]);
            }

            $oldStatus = $locked->status;
            $oldDriverId = $locked->sopir_id;
            $oldVehicleId = $locked->kendaraan_id;
            $locked->sopir_id = $driver->getKey();
            $locked->nama_sopir = $driver->nama;
            $locked->kendaraan_id = $vehicle->getKey();
            $locked->ditugaskan_oleh_id = $coordinator->getKey();
            $locked->ditugaskan_pada = now();
            $locked->status = Journey::STATUS_ASSIGNED;
            $locked->save();

            return $locked->fresh(['driver', 'vehicle', 'coordinator']);
        });
    }

    public function reject(Journey $journey, Account $coordinator, ?string $note = null): Journey
    {
        $this->assertRole($coordinator, 'koordinator');

        $validated = Validator::make(
            ['catatan' => $note],
            [
                'catatan' => ['required', 'string', 'max:5000'],
            ],
        )->validate();

        $note = $validated['catatan'];

        return DB::transaction(function () use ($journey, $note) {
            $locked = Journey::query()
                ->lockForUpdate()
                ->findOrFail($journey->getKey());

            if ($locked->status !== Journey::STATUS_REQUESTED) {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan ini sudah diproses dan tidak dapat ditolak.',
                ]);
            }

            $locked->alasan_penolakan = $note;
            $locked->status = Journey::STATUS_REJECTED;

            $locked->save();

            return $locked->fresh();
        });
    }

    public function cancel(Journey $journey, Account $actor, ?string $note = null): Journey
    {
        $this->assertActive($actor);
        $note = $this->validateNote($note);

        return DB::transaction(function () use ($journey, $actor, $note) {
            $locked = Journey::query()->lockForUpdate()->findOrFail($journey->getKey());
            $role = $actor->role?->nama_peran;
            $allowed = match ($role) {
                'perawat' => (int) $locked->diajukan_oleh_id === (int) $actor->getKey()
                    && $locked->status === Journey::STATUS_REQUESTED,
                'koordinator', 'admin' => in_array($locked->status, [Journey::STATUS_REQUESTED, Journey::STATUS_ASSIGNED], true),
                default => false,
            };

            if (!$allowed) {
                throw ValidationException::withMessages(['status' => 'Akun ini tidak berwenang membatalkan perjalanan pada status tersebut.']);
            }

            $oldStatus = $locked->status;
            $locked->status = Journey::STATUS_CANCELLED;
            $locked->save();
            return $locked;
        });
    }

    public function depart(Journey $journey, Account $driver, array $input): Journey
    {
        $this->assertRole($driver, 'sopir');
        $validated = Validator::make($input, [
            'kilometer_berangkat' => ['required', 'numeric', 'min:0'],
            'latitude_berangkat' => ['required', 'numeric', 'between:-90,90'],
            'longitude_berangkat' => ['required', 'numeric', 'between:-180,180'],
            'foto_berangkat' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ])->validate();

        return $this->saveDriverMilestone($journey, $driver, $validated, 'foto_berangkat', function (Journey $locked, array $data, ?string $photoPath) use ($driver) {
            if ($locked->status !== Journey::STATUS_ASSIGNED || (int) $locked->sopir_id !== (int) $driver->getKey()) {
                throw ValidationException::withMessages(['status' => 'Hanya sopir yang sedang ditugaskan dapat mencatat keberangkatan.']);
            }

            $oldStatus = $locked->status;
            $locked->kilometer_berangkat = $data['kilometer_berangkat'];
            $locked->latitude_berangkat = $data['latitude_berangkat'];
            $locked->longitude_berangkat = $data['longitude_berangkat'];
            $locked->foto_berangkat = $photoPath;
            $locked->waktu_berangkat = now();
            $locked->status = Journey::STATUS_DEPARTED;
            $locked->save();
            return $locked;
        });
    }

    public function arrive(Journey $journey, Account $driver, array $input): Journey
    {
        $this->assertRole($driver, 'sopir');
        $validated = Validator::make($input, [
            'kilometer_sampai' => ['required', 'numeric', 'min:0'],
            'latitude_sampai' => ['required', 'numeric', 'between:-90,90'],
            'longitude_sampai' => ['required', 'numeric', 'between:-180,180'],
            'foto_sampai' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'catatan_sopir' => ['nullable', 'string', 'max:5000'],
        ])->validate();

        return $this->saveDriverMilestone($journey, $driver, $validated, 'foto_sampai', function (Journey $locked, array $data, ?string $photoPath) use ($driver) {
            if ($locked->status !== Journey::STATUS_DEPARTED || (int) $locked->sopir_id !== (int) $driver->getKey()) {
                throw ValidationException::withMessages(['status' => 'Hanya sopir yang sedang bertugas dapat mencatat kedatangan.']);
            }

            Journey::validateKilometers($locked->kilometer_berangkat, $data['kilometer_sampai']);
            $oldStatus = $locked->status;
            $locked->kilometer_sampai = $data['kilometer_sampai'];
            $locked->latitude_sampai = $data['latitude_sampai'];
            $locked->longitude_sampai = $data['longitude_sampai'];
            $locked->foto_sampai = $photoPath;
            $locked->waktu_sampai = now();
            if ($locked->waktu_berangkat === null || $locked->waktu_sampai->lt($locked->waktu_berangkat)) {
                throw ValidationException::withMessages(['waktu_sampai' => 'Waktu kedatangan tidak boleh mendahului keberangkatan.']);
            }
            $locked->catatan_sopir = $data['catatan_sopir'] ?? null;
            $locked->status = Journey::STATUS_COMPLETED;
            $locked->save();
            return $locked;
        });
    }

    private function transitionBeforeDeparture(Journey $journey, Account $actor, string $newStatus, string $activity, ?string $note, array $allowedStatuses): Journey
    {
        return DB::transaction(function () use ($journey, $actor, $newStatus, $activity, $note, $allowedStatuses) {
            $locked = Journey::query()->lockForUpdate()->findOrFail($journey->getKey());

            if (!in_array($locked->status, $allowedStatuses, true)) {
                throw ValidationException::withMessages(['status' => 'Perjalanan sudah berubah dan tidak dapat diproses pada status saat ini.']);
            }

            $oldStatus = $locked->status;
            $locked->status = $newStatus;
            $locked->save();
            return $locked;
        });
    }

    private function saveDriverMilestone(Journey $journey, Account $driver, array $validated, string $photoField, callable $save): Journey
    {
        $photo = $validated[$photoField] ?? null;
        unset($validated[$photoField]);
        $storedPath = null;

        try {
            return DB::transaction(function () use ($journey, $driver, $validated, $photo, $photoField, $save, &$storedPath) {
                $locked = Journey::query()->lockForUpdate()->findOrFail($journey->getKey());

                if ($photo instanceof UploadedFile) {
                    $storedPath = $photo->store('perjalanan', 'mobil_cinta_private');

                    if (!$storedPath) {
                        throw new RuntimeException('Foto perjalanan gagal disimpan pada disk privat.');
                    }
                }

                return $save($locked, $validated, $storedPath);
            });
        } catch (Throwable $exception) {
            if ($storedPath !== null) {
                Storage::disk('mobil_cinta_private')->delete($storedPath);
            }

            throw $exception;
        }
    }

    private function newJourneyNumber(): string
    {
        do {
            $number = 'MC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Journey::query()->where('nomor_perjalanan', $number)->exists());

        return $number;
    }

    private function validateNote(?string $note): ?string
    {
        $validated = Validator::make(['catatan' => $note], [
            'catatan' => ['nullable', 'string', 'max:5000'],
        ])->validate();

        return $validated['catatan'] ?? null;
    }

    private function assertRole(Account $account, string $role): void
    {
        $this->assertActive($account);

        if ($account->role?->nama_peran !== $role) {
            throw ValidationException::withMessages(['role' => "Tindakan ini hanya tersedia untuk role {$role}."]);
        }
    }

    private function assertActive(Account $account): void
    {
        if (!$account->exists || !$account->status_aktif) {
            throw ValidationException::withMessages(['account' => 'Akun tidak aktif atau tidak valid.']);
        }
    }
}
