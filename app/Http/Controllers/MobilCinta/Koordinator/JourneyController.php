<?php

namespace App\Http\Controllers\MobilCinta\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Vehicle;
use App\Services\MobilCinta\JourneyWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JourneyController extends Controller
{
    public function assign(Request $request, Journey $journey, JourneyWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validate([
            'sopir_id' => ['required', 'integer', 'exists:mobil_cinta_akun,id'],
            'kendaraan_id' => ['required', 'integer', 'exists:mobil_cinta_kendaraan,id'],
        ]);
        /** @var Account $coordinator */
        $coordinator = Auth::guard('mobil_cinta')->user();
        $driver = Account::query()->findOrFail($validated['sopir_id']);
        $vehicle = Vehicle::query()->findOrFail($validated['kendaraan_id']);
        $workflow->assign($journey, $driver, $vehicle, $coordinator);

        return redirect()->route('mobil-cinta.koordinator.dashboard')->with('status', 'Penugasan berhasil disimpan.');
    }

    public function reject(Request $request, Journey $journey, JourneyWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validate(['catatan' => ['required', 'string', 'max:5000']]);
        /** @var Account $coordinator */
        $coordinator = Auth::guard('mobil_cinta')->user();
        $workflow->reject($journey, $coordinator, $validated['catatan']);

        return redirect()->route('mobil-cinta.koordinator.riwayat')->with('status', 'Permintaan perjalanan ditolak.');
    }
}
