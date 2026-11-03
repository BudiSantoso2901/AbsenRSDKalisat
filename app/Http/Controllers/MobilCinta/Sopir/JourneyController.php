<?php

namespace App\Http\Controllers\MobilCinta\Sopir;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Services\MobilCinta\JourneyWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\table;

class JourneyController extends Controller
{
    public function depart(Request $request, Journey $journey, JourneyWorkflowService $workflow): RedirectResponse
    {
        /** @var Account $driver */
        $driver = Auth::guard('mobil_cinta')->user();
        $workflow->depart($journey, $driver, $request->all());

        return redirect()->route('mobil-cinta.sopir.dashboard', ['tab' => 'aktif'])->with('status', 'Keberangkatan berhasil dicatat.');
    }

    public function arrive(Request $request, Journey $journey, JourneyWorkflowService $workflow): RedirectResponse
    {
        /** @var Account $driver */
        $driver = Auth::guard('mobil_cinta')->user();
        $workflow->arrive($journey, $driver, $request->all());

        return redirect()->route('mobil-cinta.sopir.riwayat')->with('status', 'Kedatangan berhasil dicatat.');
    }
}
