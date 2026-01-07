<?php

namespace App\Http\Controllers;

use App\Models\JamKerja;
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    //DATA READ JAM KERJA
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(JamKerja::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-warning btn-sm btn-edit"
                        data-id="' . $row->id . '"
                        data-nama="' . $row->nama_jam_kerja . '"
                        data-mulai="' . $row->jam_mulai . '"
                        data-selesai="' . $row->jam_selesai . '"
                        data-toleransi="' . $row->toleransi_menit . '">
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

        return view('Admin.JamKerja');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jam_kerja' => 'required|string|max:255',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'toleransi_menit' => 'required|integer',
        ]);

        JamKerja::create([
            'nama_jam_kerja' => $request->nama_jam_kerja,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'toleransi_menit' => $request->toleransi_menit,
        ]);

        return redirect()->back()->with('success', 'Jam Kerja berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jam_kerja' => 'required|string|max:255',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'toleransi_menit' => 'required|integer',
        ]);

        $jamKerja = JamKerja::findOrFail($id);
        $jamKerja->update([
            'nama_jam_kerja' => $request->nama_jam_kerja,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'toleransi_menit' => $request->toleransi_menit,
        ]);

        return response()->json([
            'message' => 'Jam Kerja berhasil diperbarui'
        ]);
    }
    public function destroy($id)
    {
        $jamKerja = JamKerja::findOrFail($id);
        $jamKerja->delete();

        return response()->json([
            'message' => 'Jam Kerja berhasil dihapus'
        ]);
    }
}
