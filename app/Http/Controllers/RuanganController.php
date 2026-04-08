<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
class RuanganController extends Controller
{
    public function index(Request $request)
    {
        // AJAX (DataTables)
        if ($request->ajax()) {
            return response()->json([
                'data' => Ruangan::select('id', 'nama_ruangan')->get()
            ]);
        }

        // View
        return view('Admin.Ruangan');
    }

    // 🔥 STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan',
        ]);

        Ruangan::create([
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    // 🔥 UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan,' . $id,
        ]);

        $ruangan = Ruangan::findOrFail($id);

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return response()->json([
            'message' => 'Ruangan berhasil diperbarui'
        ]);
    }

    // 🔥 DELETE
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json([
            'success' => 'Data berhasil dihapus'
        ]);
    }

}
