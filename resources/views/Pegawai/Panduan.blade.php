@extends('_layouts.layouts')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <iframe
        class="scribe-frame"
        src="https://scribehow.com/embed/Panduan_Absensi_dan_Pengaturan_Profil_Pegawai_RSD_Kalisat__Cw9PX8LeQsaL2rsTOcYazA?as=scrollable"
        title="Panduan Absensi dan Pengaturan Profil Pegawai RSD Kalisat"
        allowfullscreen>
    </iframe>

</div>

<style>
.scribe-frame{
    width:100%;
    height:800px;
    min-height:640px;
    border:0;
}

@media(max-width:767px){
    .scribe-frame{
        height:calc(100dvh - 120px);
        min-height:600px;
    }
}
</style>

@endsection