<?php

namespace App\Http\Controllers\MobilCinta\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Journey;
use App\Models\MobilCinta\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('mobil-cinta.koordinator.dashboard', [
            'journeys' => Journey::query()->with(['requester:id,nama', 'room:id,nama_ruangan'])->whereIn('status', [Journey::STATUS_REQUESTED, Journey::STATUS_ASSIGNED])->latest('diajukan_pada')->get(),
            'drivers' => Account::query()->whereHas('role', fn ($query) => $query->where('nama_peran', 'sopir'))->where('status_aktif', true)->orderBy('nama')->get(['id', 'nama']),
            'vehicles' => Vehicle::query()->orderBy('nomor_polisi')->get(['id', 'nomor_polisi']),
        ]);
    }
}
