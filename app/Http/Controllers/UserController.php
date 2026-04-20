<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //view indec
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'data' => User::get()
            ]);
        }
        return view('Admin.User');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'kode_akses' => 'required'
        ]);

        // 🔥 VALIDASI KODE
        if ($request->kode_akses !== 'add666') {
            return response()->json([
                'message' => 'Kode akses salah untuk tambah user'
            ], 403);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan'
        ]);
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'kode_akses' => 'required'
        ]);

        if ($request->kode_akses !== 'up666') {
            return response()->json([
                'message' => 'Kode akses salah untuk update'
            ], 403);
        }

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return response()->json([
            'message' => 'User berhasil diperbarui'
        ]);
    }
    public function destroy(Request $request, $id)
    {
        if ($request->kode_akses !== 'dl666') {
            return response()->json([
                'message' => 'Kode akses salah untuk hapus'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
