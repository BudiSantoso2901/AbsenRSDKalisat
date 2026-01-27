<?php

return [
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah terdaftar.',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'date' => ':attribute harus berupa tanggal yang valid.',
    'exists' => ':attribute tidak valid.',


    'attributes' => [
        'name' => 'Nama Lengkap',
        'nip' => 'NIP',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
        'id_jabatan' => 'Jabatan',
        'id_lokasi' => 'Lokasi Kerja',
        'id_jam_kerja' => 'Jam Kerja',
        'tanggal_lahir' => 'Tanggal Lahir',
    ],

];
