<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    //READ DATA JABATAN
    public function index(Request $request)
    {
        // 🔥 JIKA REQUEST DARI DATATABLES (AJAX)
        if ($request->ajax()) {
            return response()->json([
                'data' => Jabatan::select('id', 'nama_jabatan')->get()
            ]);
        }

        // 🔥 JIKA BUKAN AJAX (VIEW BIASA)
        return view('Admin.Jabatan');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return response()->json([
            'message' => 'Jabatan berhasil diperbarui'
        ]);
    }
    public function destroy($id)
    {
        if (Pegawai::where('id_jabatan', $id)->exists()) {
            return response()->json([
                'message' => 'Jabatan masih digunakan oleh pegawai'
            ], 422);
        }
        $jabatan = Jabatan::findOrFail($id);
        $jabatan->delete();

        return response()->json([
            'success' => 'Data berhasil dihapus'
        ]);
    }
}
