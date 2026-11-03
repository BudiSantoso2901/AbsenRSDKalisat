<?php

namespace App\Http\Controllers\MobilCinta\Perawat;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\Ruangan;
use App\Services\MobilCinta\JourneyListQueryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(JourneyListQueryService $query): View
    {
        /** @var Account $account */
        $account = Auth::guard('mobil_cinta')->user();
        return view('mobil-cinta.perawat.dashboard', [
            'rooms' => Ruangan::query()->orderBy('nama_ruangan')->get(['id', 'nama_ruangan']),
            'journeys' => $query->forAccount($account)->latest('diajukan_pada')->limit(10)->get(),
        ]);
    }
}
