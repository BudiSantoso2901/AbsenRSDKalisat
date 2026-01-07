<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * LIST + DATATABLE
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(Lokasi::query())
                ->addIndexColumn()
                ->addColumn('koordinat', function ($row) {
                    return $row->latitude . ', ' . $row->longitude;
                })
                ->addColumn('radius', function ($row) {
                    return $row->radius_meter . ' m';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-id="' . $row->id . '"
                            data-nama="' . $row->nama_lokasi . '"
                            data-alamat="' . $row->alamat . '"
                            data-lat="' . $row->latitude . '"
                            data-lng="' . $row->longitude . '"
                            data-radius="' . $row->radius_meter . '">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete"
                            data-id="' . $row->id . '">
                            Hapus
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Admin.Lokasi');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat'        => 'required|string',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'radius_meter'  => 'required|integer|min:1',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi',
            'latitude.required'    => 'Latitude wajib diisi',
            'longitude.required'   => 'Longitude wajib diisi',
            'radius_meter.required' => 'Radius wajib diisi',
        ]);

        Lokasi::create($validated);

        return response()->json([
            'message' => 'Lokasi berhasil ditambahkan'
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $validated = $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat'        => 'required|string',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'radius_meter'  => 'required|integer|min:1',
        ]);

        $lokasi->update($validated);

        return response()->json([
            'message' => 'Lokasi berhasil diperbarui'
        ]);
    }

    /**
     * DELETE
     * (AMAN: BLOK JIKA DIPAKAI PEGAWAI)
     */
    public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);

        if ($lokasi->pegawai()->exists()) {
            return response()->json([
                'message' => 'Lokasi tidak bisa dihapus karena masih digunakan pegawai'
            ], 422);
        }

        $lokasi->delete();

        return response()->json([
            'message' => 'Lokasi berhasil dihapus'
        ]);
    }
}
