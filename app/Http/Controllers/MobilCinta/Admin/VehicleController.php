<?php

namespace App\Http\Controllers\MobilCinta\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Daftar kendaraan Mobil Cinta.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = Vehicle::query()
            ->withCount('journeys')
            ->orderBy('nomor_polisi');

        if ($search !== '') {
            $query->where(
                'nomor_polisi',
                'like',
                '%' . $search . '%'
            );
        }

        $vehicles = $query
            ->paginate(10)
            ->withQueryString();

        $totalVehicles = Vehicle::count();

        $usedVehicles = Vehicle::has('journeys')->count();

        $neverUsedVehicles = $totalVehicles - $usedVehicles;

        return view(
            'mobil-cinta.admin.kendaraan.index',
            compact(
                'vehicles',
                'search',
                'totalVehicles',
                'usedVehicles',
                'neverUsedVehicles'
            )
        );
    }

    /**
     * Simpan kendaraan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'nomor_polisi' => strtoupper(
                trim((string) $request->input('nomor_polisi'))
            ),
            '_form_context' => 'vehicle-create',
        ]);

        $validated = $request->validate([
            'nomor_polisi' => [
                'required',
                'string',
                'max:20',
                'unique:mobil_cinta_kendaraan,nomor_polisi',
            ],
        ], [
            'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
            'nomor_polisi.max' => 'Nomor polisi maksimal 20 karakter.',
            'nomor_polisi.unique' => 'Nomor polisi sudah terdaftar.',
        ]);

        Vehicle::create([
            'nomor_polisi' => $validated['nomor_polisi'],
        ]);

        return redirect()
            ->route('mobil-cinta.admin.kendaraan.index')
            ->with('status', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Perbarui nomor polisi kendaraan.
     */
    public function update(
        Request $request,
        Vehicle $vehicle
    ): RedirectResponse {
        $request->merge([
            'nomor_polisi' => strtoupper(
                trim((string) $request->input('nomor_polisi'))
            ),
            '_form_context' => 'vehicle-edit',
            'vehicle_id' => $vehicle->id,
        ]);

        $validated = $request->validate([
            'nomor_polisi' => [
                'required',
                'string',
                'max:20',
                Rule::unique(
                    'mobil_cinta_kendaraan',
                    'nomor_polisi'
                )->ignore($vehicle->id),
            ],
        ], [
            'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
            'nomor_polisi.max' => 'Nomor polisi maksimal 20 karakter.',
            'nomor_polisi.unique' => 'Nomor polisi sudah digunakan kendaraan lain.',
        ]);

        $vehicle->update([
            'nomor_polisi' => $validated['nomor_polisi'],
        ]);

        return redirect()
            ->route('mobil-cinta.admin.kendaraan.index')
            ->with('status', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Hapus kendaraan hanya jika belum pernah digunakan.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->journeys()->exists()) {
            return redirect()
                ->route('mobil-cinta.admin.kendaraan.index')
                ->with(
                    'error',
                    'Kendaraan tidak dapat dihapus karena sudah memiliki riwayat perjalanan.'
                );
        }

        $vehicle->delete();

        return redirect()
            ->route('mobil-cinta.admin.kendaraan.index')
            ->with('status', 'Kendaraan berhasil dihapus.');
    }
}