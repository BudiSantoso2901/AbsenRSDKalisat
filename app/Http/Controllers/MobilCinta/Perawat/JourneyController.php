<?php

namespace App\Http\Controllers\MobilCinta\Perawat;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\Ruangan;
use App\Services\MobilCinta\JourneyWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JourneyController extends Controller
{
    /**
     * Menyimpan pengajuan perjalanan baru.
     */
    public function store(
        Request $request,
        JourneyWorkflowService $workflow
    ): RedirectResponse {
        /** @var Account $actor */
        $actor = Auth::guard('mobil_cinta')->user();

        $workflow->createRequest($actor, $request->all());

        return redirect()
            ->route('mobil-cinta.perawat.riwayat')
            ->with('status', 'Permintaan perjalanan berhasil diajukan.');
    }

    /**
     * Menampilkan halaman edit pengajuan.
     */
    public function edit(Journey $journey): View
    {
        /** @var Account $actor */
        $actor = Auth::guard('mobil_cinta')->user();

        // Pastikan pengajuan milik Perawat yang sedang login.
        abort_unless(
            (int) $journey->diajukan_oleh_id === (int) $actor->getKey(),
            403,
            'Anda tidak berhak mengedit pengajuan ini.'
        );

        // Hanya pengajuan yang belum ditugaskan yang bisa diedit.
        abort_unless(
            $journey->status === Journey::STATUS_REQUESTED,
            403,
            'Pengajuan ini sudah diproses dan tidak dapat diedit.'
        );

        $rooms = Ruangan::query()
            ->orderBy('nama_ruangan')
            ->get(['id', 'nama_ruangan']);

        return view('mobil-cinta.perawat.perjalanan.edit', [
            'journey' => $journey,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Memperbarui pengajuan perjalanan.
     */
    public function update(
        Request $request,
        Journey $journey,
        JourneyWorkflowService $workflow
    ): RedirectResponse {
        /** @var Account $actor */
        $actor = Auth::guard('mobil_cinta')->user();

        $workflow->updateRequest(
            $journey,
            $actor,
            $request->all()
        );

        return redirect()
            ->route('mobil-cinta.perawat.riwayat')
            ->with('status', 'Pengajuan perjalanan berhasil diperbarui.');
    }

    /**
     * Membatalkan pengajuan perjalanan.
     */
    public function cancel(
        Request $request,
        Journey $journey,
        JourneyWorkflowService $workflow
    ): RedirectResponse {
        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        /** @var Account $actor */
        $actor = Auth::guard('mobil_cinta')->user();

        $workflow->cancel(
            $journey,
            $actor,
            $validated['catatan'] ?? null
        );

        return redirect()
            ->back()
            ->with('status', 'Pengajuan perjalanan berhasil diperbarui.');
    }
}