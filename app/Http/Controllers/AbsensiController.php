<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    //kamera
    public function kamera()
    {
        return view('Pegawai.Kamera');
    }
}
