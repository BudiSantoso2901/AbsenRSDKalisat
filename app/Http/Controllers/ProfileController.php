<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function edit()
    {

        /** @var \App\Models\Pegawai|null $pegawai */
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('Pegawai.Profile', compact('pegawai'));
    }

    // Memproses update data profil pegawai
    public function update(Request $request)
    {

        /** @var \App\Models\Pegawai $pegawai */
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            return redirect()->route('login');
        }

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'foto_pegawai' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update Nama
        $pegawai->name = $request->name;

        // Update Password jika diisi
        if ($request->filled('password')) {
            $pegawai->password = Hash::make($request->password);
        }

        // Update Foto Pegawai jika diunggah
        if ($request->hasFile('foto_pegawai')) {
            if ($pegawai->foto_pegawai && Storage::disk('public')->exists($pegawai->foto_pegawai)) {
                Storage::disk('public')->delete($pegawai->foto_pegawai);
            }

            // Simpan foto baru
            $path = $request->file('foto_pegawai')->store('foto_pegawai', 'public');
            $pegawai->foto_pegawai = $path;
        }

        $pegawai->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}
