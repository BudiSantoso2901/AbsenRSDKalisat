<?php

namespace App\Http\Controllers\MobilCinta\Sopir;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Services\MobilCinta\JourneyListQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        JourneyListQueryService $query,
        Request $request
    ): View {
        /** @var Account $account */
        $account = Auth::guard('mobil_cinta')->user();

        $tab = $request->query('tab', 'home');

        if (!in_array($tab, ['home', 'tugas', 'aktif', 'profil'], true)) {
            $tab = 'home';
        }

        // Ambil hanya perjalanan yang memang ditugaskan kepada Sopir login.
        // QueryListService tetap menjadi sumber pembatasan kepemilikan perjalanan.
        $journeys = $query->forAccount($account)
            ->whereIn('status', [
                Journey::STATUS_ASSIGNED,
                Journey::STATUS_DEPARTED,
            ])
            ->with(['vehicle', 'room'])
            ->latest('diajukan_pada')
            ->get();

        return view('mobil-cinta.sopir.dashboard', [
            'account' => $account,
            'tab' => $tab,
            'assignedJourneys' => $journeys
                ->where('status', Journey::STATUS_ASSIGNED)
                ->values(),
            'activeJourneys' => $journeys
                ->where('status', Journey::STATUS_DEPARTED)
                ->values(),
        ]);
    }
}
