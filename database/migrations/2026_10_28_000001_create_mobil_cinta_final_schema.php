<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TARGET_TABLES = [
        'mobil_cinta_peran',
        'mobil_cinta_akun',
        'mobil_cinta_kendaraan',
        'mobil_cinta_perjalanan',
        'mobil_cinta_riwayat_aktivitas',
    ];

    private const SOURCE_TABLES = [
        'mobil_cinta_roles',
        'mobil_cinta_accounts',
        'mobil_cinta_vehicles',
        'mobil_cinta_guarantors',
        'mobil_cinta_trip_requests',
        'mobil_cinta_assignments',
        'mobil_cinta_journeys',
        'mobil_cinta_status_histories',
        'ruangans',
    ];

    public function up(): void
    {
        // Pretend mode must never dump copied patient fields or password hashes.
        // It previews DDL only; data preflight and copying run only for a real migration.
        if (DB::connection()->pretending()) {
            $this->createTargetTables();

            return;
        }

        $this->assertMigrationPreconditions();
        $this->assertLegacyStatusesAreMappable();

        $this->createTargetTables();
        DB::transaction(function () {
            $this->copyRoles();
            $this->copyAccounts();
            $this->copyVehicles();
            $this->copyJourneysAndActivity();
        });
    }

    /**
     * This migration copies live operational data. A rollback that drops the
     * target tables would destroy the migrated copy, so reversal is forbidden.
     */
    public function down(): void
    {
        throw new RuntimeException(
            'Migration Mobil Cinta final bersifat non-destruktif dan tidak dapat di-rollback otomatis. '
            .'Gunakan rencana pemulihan yang ditinjau setelah backup dan verifikasi data.'
        );
    }

    private function assertMigrationPreconditions(): void
    {
        foreach (self::SOURCE_TABLES as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("Tabel sumber {$table} tidak tersedia; migrasi final dibatalkan sebelum perubahan schema.");
            }
        }

        $normalizedRoles = [];
        foreach (DB::table('mobil_cinta_roles')->select(['name'])->orderBy('id')->cursor() as $role) {
            $normalizedName = $this->normalizeRole((string) $role->name);

            if (isset($normalizedRoles[$normalizedName])) {
                throw new RuntimeException('Nama role lama bentrok setelah normalisasi; migrasi final dibatalkan sebelum perubahan schema.');
            }

            $normalizedRoles[$normalizedName] = true;
        }

        $existingTargets = array_filter(self::TARGET_TABLES, fn (string $table) => Schema::hasTable($table));

        if ($existingTargets !== []) {
            throw new RuntimeException(
                'Tabel target Mobil Cinta sudah ada (' . implode(', ', $existingTargets) . '). '
                .'Periksa status migration dan data sebelum melanjutkan; migration tidak menimpa tabel.'
            );
        }
    }

    private function assertLegacyStatusesAreMappable(): void
    {
        foreach (DB::table('mobil_cinta_trip_requests')->select(['id', 'status'])->orderBy('id')->cursor() as $request) {
            $this->mapStatus((string) $request->status, (int) $request->id);
        }

        foreach (DB::table('mobil_cinta_status_histories')->select(['id', 'trip_request_id', 'old_status', 'new_status'])->orderBy('id')->cursor() as $history) {
            if ($history->old_status !== null) {
                $this->mapStatus((string) $history->old_status, (int) $history->trip_request_id);
            }

            $this->mapStatus((string) $history->new_status, (int) $history->trip_request_id);
        }
    }

    private function createTargetTables(): void
    {
        Schema::create('mobil_cinta_peran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peran')->unique('mc_peran_nama_uq');
            $table->timestamps();
        });

        Schema::create('mobil_cinta_akun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peran_id')->constrained('mobil_cinta_peran')->restrictOnDelete();
            $table->string('nama');
            $table->string('nama_pengguna')->unique('mc_akun_pengguna_uq');
            $table->string('kata_sandi');
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('login_terakhir_pada')->nullable();
            $table->timestamps();
            $table->index('peran_id', 'mc_akun_peran_idx');
        });

        Schema::create('mobil_cinta_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->unique('mc_kendaraan_nomor_uq');
            $table->timestamps();
        });

        Schema::create('mobil_cinta_perjalanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_perjalanan')->unique('mc_perjalanan_nomor_uq');
            $table->foreignId('diajukan_oleh_id')->constrained('mobil_cinta_akun')->restrictOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->string('nama_pasien');
            $table->string('nomor_rekam_medis');
            $table->string('jenis_kelamin_pasien')->nullable();
            $table->string('penjamin');
            $table->string('tujuan');
            $table->text('catatan')->nullable();
            $table->string('status')->default('diajukan');
            $table->timestamp('diajukan_pada')->nullable();
            $table->foreignId('sopir_id')->nullable()->constrained('mobil_cinta_akun')->restrictOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('mobil_cinta_kendaraan')->restrictOnDelete();
            $table->foreignId('ditugaskan_oleh_id')->nullable()->constrained('mobil_cinta_akun')->restrictOnDelete();
            $table->timestamp('ditugaskan_pada')->nullable();
            $table->decimal('kilometer_berangkat', 10, 2)->nullable();
            $table->decimal('lintang_berangkat', 10, 7)->nullable();
            $table->decimal('bujur_berangkat', 11, 7)->nullable();
            $table->string('foto_berangkat')->nullable();
            $table->timestamp('waktu_berangkat')->nullable();
            $table->decimal('kilometer_sampai', 10, 2)->nullable();
            $table->decimal('lintang_sampai', 10, 7)->nullable();
            $table->decimal('bujur_sampai', 11, 7)->nullable();
            $table->string('foto_sampai')->nullable();
            $table->timestamp('waktu_sampai')->nullable();
            $table->text('catatan_sopir')->nullable();
            $table->timestamps();
            $table->index('diajukan_oleh_id', 'mc_perjalanan_pengaju_idx');
            $table->index('ruangan_id', 'mc_perjalanan_ruangan_idx');
            $table->index('sopir_id', 'mc_perjalanan_sopir_idx');
            $table->index('kendaraan_id', 'mc_perjalanan_kendaraan_idx');
            $table->index('ditugaskan_oleh_id', 'mc_perjalanan_penugas_idx');
            $table->index(['status', 'diajukan_pada'], 'mc_perjalanan_status_waktu_idx');
        });

        Schema::create('mobil_cinta_riwayat_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjalanan_id')->constrained('mobil_cinta_perjalanan')->restrictOnDelete();
            $table->foreignId('dilakukan_oleh_id')->constrained('mobil_cinta_akun')->restrictOnDelete();
            $table->string('jenis_aktivitas');
            $table->string('status_lama')->nullable();
            $table->string('status_baru')->nullable();
            $table->json('detail_perubahan')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('sopir_terkait_id')->nullable()->constrained('mobil_cinta_akun')->restrictOnDelete();
            $table->timestamps();
            $table->index(['perjalanan_id', 'created_at'], 'mc_riwayat_perjalanan_waktu_idx');
            $table->index(['sopir_terkait_id', 'created_at'], 'mc_riwayat_sopir_waktu_idx');
            $table->index('dilakukan_oleh_id', 'mc_riwayat_pelaku_idx');
        });
    }

    private function copyRoles(): void
    {
        DB::table('mobil_cinta_roles')->orderBy('id')->chunkById(250, function ($roles) {
            foreach ($roles as $role) {
                DB::table('mobil_cinta_peran')->insert([
                    'id' => $role->id,
                    'nama_peran' => $this->normalizeRole((string) $role->name),
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ]);
            }
        });
    }

    private function copyAccounts(): void
    {
        DB::table('mobil_cinta_accounts')->orderBy('id')->chunkById(250, function ($accounts) {
            foreach ($accounts as $account) {
                DB::table('mobil_cinta_akun')->insert([
                    'id' => $account->id,
                    'peran_id' => $account->role_id,
                    'nama' => $account->name,
                    'nama_pengguna' => $account->username,
                    'kata_sandi' => $account->password,
                    'status_aktif' => $account->is_active,
                    'login_terakhir_pada' => $account->last_login_at,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ]);
            }
        });
    }

    private function copyVehicles(): void
    {
        DB::table('mobil_cinta_vehicles')->orderBy('id')->chunkById(250, function ($vehicles) {
            foreach ($vehicles as $vehicle) {
                DB::table('mobil_cinta_kendaraan')->insert([
                    'id' => $vehicle->id,
                    'nomor_polisi' => $vehicle->plate_number,
                    'created_at' => $vehicle->created_at,
                    'updated_at' => $vehicle->updated_at,
                ]);
            }
        });
    }

    private function copyJourneysAndActivity(): void
    {
        $guarantors = DB::table('mobil_cinta_guarantors')->pluck('name', 'id');

        DB::table('mobil_cinta_trip_requests')->orderBy('id')->chunkById(100, function ($requests) use ($guarantors) {
            $requestIds = $requests->pluck('id')->all();
            $assignmentsByRequest = DB::table('mobil_cinta_assignments')
                ->whereIn('trip_request_id', $requestIds)
                ->orderBy('assigned_at')
                ->orderBy('id')
                ->get()
                ->groupBy('trip_request_id');
            $assignmentIds = $assignmentsByRequest->flatten(1)->pluck('id')->unique()->all();
            $journeysByAssignment = DB::table('mobil_cinta_journeys')
                ->whereIn('assignment_id', $assignmentIds ?: [0])
                ->get()
                ->keyBy('assignment_id');
            $statusHistoriesByRequest = DB::table('mobil_cinta_status_histories')
                ->whereIn('trip_request_id', $requestIds)
                ->orderBy('changed_at')
                ->orderBy('id')
                ->get()
                ->groupBy('trip_request_id');

            foreach ($requests as $request) {
                $assignments = $assignmentsByRequest->get($request->id, collect());
                $currentAssignment = $assignments
                    ->where('assignment_status', 'active')
                    ->sortByDesc(fn ($assignment) => [$assignment->assigned_at, $assignment->id])
                    ->first();
                $journeyAssignment = $currentAssignment;

                if (!$journeyAssignment || !$journeysByAssignment->has($journeyAssignment->id)) {
                    $journeyAssignment = $assignments
                        ->filter(fn ($assignment) => $journeysByAssignment->has($assignment->id))
                        ->sortByDesc(fn ($assignment) => [$assignment->assigned_at, $assignment->id])
                        ->first();
                }

                $legacyJourney = $journeyAssignment ? $journeysByAssignment->get($journeyAssignment->id) : null;
                $status = $this->mapStatus((string) $request->status, (int) $request->id);
                $trip = DB::table('mobil_cinta_trip_requests as requests')
                    ->leftJoin('mobil_cinta_guarantors as guarantors', 'guarantors.id', '=', 'requests.guarantor_id')
                    ->where('requests.id', $request->id)
                    ->first(['guarantors.name as guarantor_name']);

                DB::table('mobil_cinta_perjalanan')->insert([
                    'id' => $request->id,
                    'nomor_perjalanan' => $request->request_number,
                    'diajukan_oleh_id' => $request->requested_by,
                    'ruangan_id' => $request->room_id,
                    'nama_pasien' => $request->patient_name,
                    'nomor_rekam_medis' => $request->medical_record_no,
                    'jenis_kelamin_pasien' => $request->patient_gender,
                    'penjamin' => $trip->guarantor_name ?? '',
                    'tujuan' => $request->destination,
                    'catatan' => $request->notes,
                    'status' => $status,
                    'diajukan_pada' => $request->submitted_at ?? $request->created_at,
                    'sopir_id' => $currentAssignment?->driver_id,
                    'kendaraan_id' => $currentAssignment?->vehicle_id,
                    'ditugaskan_oleh_id' => $currentAssignment?->assigned_by,
                    'ditugaskan_pada' => $currentAssignment?->assigned_at,
                    'kilometer_berangkat' => $legacyJourney?->start_km,
                    'lintang_berangkat' => $legacyJourney?->start_latitude,
                    'bujur_berangkat' => $legacyJourney?->start_longitude,
                    'foto_berangkat' => $legacyJourney?->start_photo_path,
                    'waktu_berangkat' => $legacyJourney?->started_at,
                    'kilometer_sampai' => $legacyJourney?->end_km,
                    'lintang_sampai' => $legacyJourney?->end_latitude,
                    'bujur_sampai' => $legacyJourney?->end_longitude,
                    'foto_sampai' => $legacyJourney?->end_photo_path,
                    'waktu_sampai' => $legacyJourney?->finished_at,
                    'catatan_sopir' => $legacyJourney?->driver_note,
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                ]);

                $histories = $statusHistoriesByRequest->get($request->id, collect());

                foreach ($histories as $history) {
                    $relatedDriver = $this->driverAssignedAt($assignments, $history->changed_at);
                    $this->insertActivity([
                        'perjalanan_id' => $request->id,
                        'dilakukan_oleh_id' => $history->changed_by,
                        'jenis_aktivitas' => 'status_diubah',
                        'status_lama' => $history->old_status === null ? null : $this->mapStatus((string) $history->old_status, (int) $request->id),
                        'status_baru' => $this->mapStatus((string) $history->new_status, (int) $request->id),
                        'detail_perubahan' => ['asal' => 'riwayat_status_lama', 'id_sumber' => $history->id],
                        'catatan' => $history->note,
                        'sopir_terkait_id' => $relatedDriver,
                        'created_at' => $history->changed_at,
                    ]);
                }

                foreach ($assignments as $index => $assignment) {
                    $this->insertActivity([
                        'perjalanan_id' => $request->id,
                        'dilakukan_oleh_id' => $assignment->assigned_by,
                        'jenis_aktivitas' => $index === 0 && $assignment->assignment_status !== 'replaced'
                            ? 'penugasan_ditetapkan'
                            : 'penugasan_diganti',
                        'status_lama' => null,
                        'status_baru' => null,
                        'detail_perubahan' => [
                            'asal' => 'penugasan_lama',
                            'id_sumber' => $assignment->id,
                            'kendaraan_lama_id' => $assignment->vehicle_id,
                            'status_penugasan_lama' => $assignment->assignment_status,
                        ],
                        'catatan' => null,
                        'sopir_terkait_id' => $assignment->driver_id,
                        'created_at' => $assignment->assigned_at ?? $assignment->created_at,
                    ]);
                }

                if ($histories->isEmpty()) {
                    $this->insertActivity([
                        'perjalanan_id' => $request->id,
                        'dilakukan_oleh_id' => $request->requested_by,
                        'jenis_aktivitas' => 'data_dipindahkan',
                        'status_lama' => null,
                        'status_baru' => $status,
                        'detail_perubahan' => ['asal' => 'permintaan_lama'],
                        'catatan' => null,
                        'sopir_terkait_id' => $currentAssignment?->driver_id,
                        'created_at' => $request->submitted_at ?? $request->created_at,
                    ]);
                }
            }
        });
    }

    private function insertActivity(array $activity): void
    {
        $createdAt = $activity['created_at'] ?? now();
        $detail = $activity['detail_perubahan'] ?? null;
        $activity['detail_perubahan'] = $detail === null
            ? null
            : json_encode($detail, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $activity['created_at'] = $createdAt;
        $activity['updated_at'] = $createdAt;

        DB::table('mobil_cinta_riwayat_aktivitas')->insert($activity);
    }

    private function driverAssignedAt($assignments, ?string $time): ?int
    {
        if ($time === null) {
            return null;
        }

        return $assignments
            ->filter(fn ($assignment) => $assignment->assigned_at !== null && $assignment->assigned_at <= $time)
            ->sortByDesc(fn ($assignment) => [$assignment->assigned_at, $assignment->id])
            ->first()?->driver_id;
    }

    private function normalizeRole(string $role): string
    {
        return match (strtolower(trim($role))) {
            'administrator' => 'admin',
            'coordinator' => 'koordinator',
            'nurse' => 'perawat',
            'driver' => 'sopir',
            default => strtolower(trim($role)),
        };
    }

    private function mapStatus(string $status, int $journeyId): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'requested', 'request', 'diajukan' => 'diajukan',
            'assigned', 'approved', 'ditugaskan' => 'ditugaskan',
            'en_route', 'in_progress', 'departed', 'berangkat' => 'berangkat',
            'completed', 'complete', 'finished', 'done', 'selesai' => 'selesai',
            'rejected', 'ditolak' => 'ditolak',
            'cancelled', 'canceled', 'dibatalkan' => 'dibatalkan',
            default => throw new RuntimeException(
                "Status lama tidak dikenal pada perjalanan ID {$journeyId}; migrasi final dibatalkan sebelum perubahan schema."
            ),
        };
    }
};
