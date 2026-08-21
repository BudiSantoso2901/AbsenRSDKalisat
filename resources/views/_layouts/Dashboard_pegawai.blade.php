@extends('_layouts.layouts')

@section('content')

<style>
.dashboard{
    max-width:1100px;
    margin:auto;
    padding-bottom:25px;
}

.dashboard-card{
    background:#fff;
    border-radius:16px;
    box-shadow:0 3px 14px rgba(0,0,0,.06);
}

/* WELCOME */
.welcome-card{
    padding:20px 10px 17px;
}

.welcome-card h4{
    font-size:25px;
    font-weight:700;
    color:#2d3035;
    margin:0 0 6px;
}

.welcome-card p{
    margin:0;
    color:#8b929c;
    font-size:15px;
}

/* JADWAL */
.schedule-card{
    padding:16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.schedule-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.schedule-icon{
    width:46px;
    height:46px;
    border-radius:12px;
    background:#eaf8f0;
    color:#20b66a;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:23px;
}

.schedule-title{
    font-size:14px;
    color:#777;
    margin-bottom:5px;
}

.schedule-time{
    font-size:16px;
    font-weight:700;
    color:#333;
}

.schedule-edit{
    border:0;
    background:#eaf8f0;
    color:#20b66a;
    border-radius:10px;
    padding:9px 14px;
    font-size:13px;
    font-weight:600;
}

/* ABSENSI HARI INI */
.absen-card{
    position:relative;
    width:100%;
    min-height:145px;
    padding:18px 17px;
    overflow:hidden;
    color:#fff;
    background:linear-gradient(135deg,#20b96b,#0ca456);
}

.absen-card:before{
    content:"";
    position:absolute;
    width:145px;
    height:145px;
    right:-40px;
    top:-70px;
    border-radius:50%;
    background:rgba(255,255,255,.09);
}

.absen-card:after{
    content:"";
    position:absolute;
    width:90px;
    height:90px;
    right:60px;
    bottom:-55px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
}

.absen-content{
    position:relative;
    z-index:2;
    width:72%;
}

.absen-label{
    font-size:15px;
    font-weight:600;
    margin-bottom:5px;
}

.absen-date{
    color:#fff!important;
    font-size:17px;
    font-weight:700;
    margin-bottom:7px;
}

.absen-desc{
    max-width:230px;
    font-size:13px;
    line-height:1.45;
    font-weight:500;
    margin-bottom:0;
}

/* FOTO */
.face-link{
    position:absolute;
    z-index:4;
    right:17px;
    top:50%;
    transform:translateY(-50%);
    width:72px;
    height:72px;
    padding:4px;
    border-radius:50%;
    background:#fff;
    border:4px solid #ef32a5;
    box-shadow:0 0 0 3px rgba(255,255,255,.35);
}

.face-link img{
    width:100%;
    height:100%;
    border-radius:50%;
    object-fit:cover;
}

/* SUMMARY */
.summary-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:11px;
}

.summary-card{
    min-height:125px;
    padding:17px 13px;
    background:#fff;
    border-radius:15px;
    box-shadow:0 3px 12px rgba(0,0,0,.05);
}

.summary-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.summary-title{
    font-size:14px;
    font-weight:600;
    color:#555;
}

.summary-icon{
    width:37px;
    height:37px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}

.hadir-icon,
.izin-icon,
.sakit-icon,
.cuti-icon{
    color:#20b66a;
    background:#eaf8f0;
}

.summary-number{
    margin-top:11px;
    font-size:26px;
    line-height:1;
    font-weight:700;
    color:#5d7187;
}

.summary-sub{
    margin-top:10px;
    font-size:12px;
    color:#9ca3ab;
}

/* AKTIVITAS */
.activity-card{
    padding:18px;
}

.card-heading{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:14px;
}

.card-heading h5{
    margin:0;
    font-size:19px;
    color:#65758a;
    font-weight:700;
}

.card-heading span{
    font-size:13px;
    color:#8f98a3;
}

.activity-item{
    display:flex;
    align-items:center;
    gap:13px;
    padding:13px 0;
    border-bottom:1px solid #f0f0f0;
}

.activity-item:last-child{
    border-bottom:0;
    padding-bottom:0;
}

.activity-icon{
    flex:none;
    width:46px;
    height:46px;
    border-radius:12px;
    background:#eaf8f0;
    color:#20b66a;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.activity-info{
    flex:1;
}

.activity-info strong{
    display:block;
    font-size:15px;
    color:#3d4147;
    margin-bottom:5px;
}

.activity-info small{
    display:block;
    font-size:13px;
    color:#888;
}

.activity-time{
    font-size:15px;
    font-weight:700;
    color:#20a965;
}

/* RIWAYAT */
.history-card{
    padding:18px;
}

.history-item{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 0;
    border-bottom:2px solid #eef1f4;
}

.history-time-row{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:7px;
    margin-top:2px;
}

.history-time{
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 8px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
    line-height:1.2;
}

.history-time i{
    font-size:15px;
}

.history-time.masuk{
    background:#eaf8f0;
    color:#159f59;
}

.history-time.pulang{
    background:#fff0f6;
    color:#ff1493;
}

.history-time.empty{
    opacity:.65;
}

.history-date{
    width:48px;
    height:48px;
    flex:none;
    border-radius:11px;
    background:#eaf8f0;
    color:#159f59;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}

.history-date strong{
    font-size:19px;
    line-height:18px;
}

.history-date small{
    font-size:11px;
    font-weight:700;
}

.history-info{
    flex:1;
    min-width:0;
}

.history-info strong{
    display:block;
    font-size:15px;
    color:#718198;
    margin-bottom:5px;
}

.history-info small{
    display:block;
    font-size:13px;
    line-height:1.4;
    color:#888;
}

.history-heading{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:14px;
}

.history-heading h5{
    margin:0;
    font-size:19px;
    color:#65758a;
    font-weight:700;
}

.history-filter{
    margin:0;
}

.history-filter input{
    height:38px;
    padding:6px 10px;
    border:1.5px solid #e0e4e8;
    border-radius:10px;
    background:#fff;
    color:#555;
    font-size:13px;
    outline:none;
    cursor:pointer;
}

.history-filter input:focus{
    border-color:#f06292;
    box-shadow:0 0 0 3px rgba(240,98,146,.10);
}

/* PAGINATION */
.history-pagination{
    margin-top:14px;
    display:flex;
    justify-content:center;
}

.history-pagination .pagination{
    margin:0;
    gap:4px;
}

.history-pagination .page-link{
    min-width:34px;
    height:34px;
    padding:6px 10px;
    border-radius:9px!important;
    border:1px solid #e5e7eb;
    color:#65758a;
    font-size:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:none;
}

.history-pagination .page-item.active .page-link{
    background:#20b66a;
    border-color:#20b66a;
    color:#fff;
}

.history-pagination .page-item.disabled .page-link{
    color:#b7bdc5;
    background:#f8f9fa;
}

/* STATUS */
.status-badge{
    padding:8px 11px;
    border-radius:10px;
    background:#eaf8f0;
    color:#159f59;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}

.status-izin,
.status-sakit,
.status-cuti{
    background:#eaf8f0;
    color:#159f59;
}

/* MODAL */
#modalUbahShift .modal-content{
    border:0;
    border-radius:16px;
}

#modalUbahShift .modal-title{
    font-size:20px;
    font-weight:700;
}

#modalUbahShift .form-label{
    font-size:15px;
    font-weight:600;
}

#modalUbahShift .form-select{
    font-size:15px;
}

#modalUbahShift .btn-absen{
    background:#20b66a;
    color:#fff!important;
}

#shiftMessage{
    font-size:14px;
    line-height:1.5;
}

/* MOBILE */
@media(max-width:767px){

    .dashboard{
        width:100%;
        padding-bottom:25px;
    }

    .welcome-card{
        padding:20px 9px 17px;
    }

    .welcome-card h4{
        font-size:24px;
    }

    .welcome-card p{
        font-size:14px;
    }

    .schedule-card{
        padding:15px 13px;
    }

    .schedule-left{
        gap:10px;
    }

    .schedule-icon{
        width:43px;
        height:43px;
        font-size:21px;
    }

    .schedule-title{
        font-size:14px;
    }

    .schedule-time{
        font-size:15px;
    }

    .schedule-edit{
        padding:8px 12px;
        font-size:13px;
    }

    .absen-card{
        width:100%;
        min-height:145px;
        padding:18px 17px;
    }

    .absen-content{
        width:70%;
    }

    .absen-label{
        font-size:15px;
    }

    .absen-date{
        font-size:17px;
    }

    .absen-desc{
        font-size:13px;
        line-height:1.45;
    }

    .face-link{
        right:17px;
        top:50%;
        transform:translateY(-50%);
        width:72px;
        height:72px;
    }

    .summary-grid{
        gap:8px;
    }

    .summary-card{
        min-height:120px;
        padding:16px 10px;
    }

    .summary-title{
        font-size:13px;
    }

    .summary-icon{
        width:34px;
        height:34px;
        font-size:18px;
    }

    .summary-number{
        font-size:25px;
    }

    .summary-sub{
        font-size:11px;
    }

    .card-heading h5{
        font-size:18px;
    }

    .card-heading span{
        font-size:12px;
    }

    .activity-item{
        gap:11px;
        padding:12px 0;
    }

    .activity-icon{
        width:43px;
        height:43px;
        font-size:20px;
    }

    .activity-info strong{
        font-size:14px;
    }

    .activity-info small{
        font-size:13px;
    }

    .activity-time{
        font-size:14px;
    }

    .history-item{
        gap:10px;
        padding:12px 0;
    }

    .history-date{
        width:46px;
        height:46px;
    }

    .history-date strong{
        font-size:18px;
    }

    .history-date small{
        font-size:10px;
    }

    .history-info strong{
        font-size:14px;
    }

    .history-info small{
        font-size:13px;
    }

    .history-time-row{
        gap:5px;
    }

    .history-time{
        padding:5px 7px;
        font-size:11px;
    }

    .status-badge{
        padding:7px 10px;
        font-size:11px;
    }

    .history-heading{
        gap:8px;
    }

    .history-heading h5{
        font-size:18px;
    }

    .history-filter input{
        width:145px;
        height:37px;
        font-size:12px;
    }
}

</style>


<div class="container-xxl flex-grow-1 container-p-y">

<div class="dashboard">


{{-- WELCOME --}}

<div class="welcome-card">

    <h4>
        Halo, {{ $pegawai->name }} &#128075;
    </h4>

    <p>
        Semoga harimu menyenangkan. Jangan lupa absensi hari ini.
    </p>

</div>


{{-- JADWAL KERJA --}}

@php
    $shiftAktif = $pegawai->id_jam_kerja
        ? $jamKerja->firstWhere('id', $pegawai->id_jam_kerja)
        : $jamKerja->first();
@endphp


@if($shiftAktif)

<div class="dashboard-card schedule-card mb-3">

    <div class="schedule-left">

        <div class="schedule-icon">
            <i class="bx bx-time-five"></i>
        </div>

        <div>

            <div class="schedule-title">
                Jadwal Kerja
            </div>

            <div class="schedule-time">

                {{ $shiftAktif->nama_jam_kerja ?? 'Jam Kerja' }}

                &nbsp;&middot;&nbsp;

                {{ $shiftAktif->jam_mulai }}
                -
                {{ $shiftAktif->jam_selesai }}

            </div>

        </div>

    </div>


    <button
        type="button"
        class="schedule-edit"
        data-bs-toggle="modal"
        data-bs-target="#modalUbahShift">

        Ubah

    </button>

</div>

@endif


{{-- DATA HARI INI --}}

@php

    $today = $absensi->first(function($item){

        return \Carbon\Carbon::parse(
            $item->tanggal
        )->isToday();

    });

    $todayStatus =
        strtolower($today->status ?? '');

@endphp


{{-- ABSENSI HARI INI --}}

<div class="dashboard-card absen-card mb-3">

    <div class="absen-content">

        <div class="absen-label">
            Absensi Hari Ini
        </div>


        <div class="absen-date">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </div>


        <div class="absen-desc">

            @if(!$today)

                Yuk, jangan lupa melakukan absensi
                sebelum mulai bekerja.

            @elseif($todayStatus === 'hadir')

                Absensi masuk kamu sudah tercatat.

            @elseif($todayStatus === 'izin')

                Hari ini kamu tercatat izin.

            @elseif($todayStatus === 'sakit')

                Hari ini kamu tercatat sakit.

            @elseif($todayStatus === 'cuti')

                Hari ini kamu tercatat cuti.

            @else

                Status absensi hari ini:
                {{ ucfirst($todayStatus) }}

            @endif

        </div>

    </div>


    <a
        href="{{ route('profile.edit') }}"
        class="face-link">

        <img
            src="{{ $pegawai->foto_pegawai
                ? asset('storage/' . $pegawai->foto_pegawai)
                : asset('assets/img/default-avatar.png') }}"
            alt="Foto Profil">

    </a>

</div>


{{-- HADIR / IZIN / SAKIT / CUTI --}}

<div class="summary-grid mb-3">


    {{-- HADIR --}}

    <div class="summary-card">

        <div class="summary-top">

            <span class="summary-title">
                Hadir
            </span>

            <div class="summary-icon hadir-icon">
                <i class="bx bx-check"></i>
            </div>

        </div>

        <div class="summary-number">
            {{ $hadir }}
        </div>

        <div class="summary-sub">
            Bulan ini
        </div>

    </div>


    {{-- IZIN --}}

    <div class="summary-card">

        <div class="summary-top">

            <span class="summary-title">
                Izin
            </span>

            <div class="summary-icon izin-icon">
                <i class="bx bx-file"></i>
            </div>

        </div>

        <div class="summary-number">
            {{ $izin }}
        </div>

        <div class="summary-sub">
            Bulan ini
        </div>

    </div>


    {{-- SAKIT --}}

    <div class="summary-card">

        <div class="summary-top">

            <span class="summary-title">
                Sakit
            </span>

            <div class="summary-icon sakit-icon">
                <i class="bx bx-plus"></i>
            </div>

        </div>

        <div class="summary-number">
            {{ $sakit }}
        </div>

        <div class="summary-sub">
            Bulan ini
        </div>

    </div>


    {{-- CUTI --}}

    <div class="summary-card">

        <div class="summary-top">

            <span class="summary-title">
                Cuti
            </span>

            <div class="summary-icon cuti-icon">
                <i class="bx bx-calendar-check"></i>
            </div>

        </div>

        <div class="summary-number">
            {{ $cuti ?? 0 }}
        </div>

        <div class="summary-sub">
            Bulan ini
        </div>

    </div>

</div>


{{-- AKTIVITAS HARI INI --}}

<div class="dashboard-card activity-card mb-3">

    <div class="card-heading">

        <h5>
            Aktivitas Hari Ini
        </h5>

        <span>
            {{ now()->locale('id')->translatedFormat('d F Y') }}
        </span>

    </div>


    {{-- ABSEN MASUK --}}

    <div class="activity-item">

        <div class="activity-icon">
            <i class="bx bx-log-in"></i>
        </div>


        <div class="activity-info">

            <strong>
                Absen Masuk
            </strong>

            <small>

                @if(!$today)

                    Belum absen

                @elseif($todayStatus === 'hadir')

                    Waktu masuk

                @elseif($todayStatus === 'izin')

                    Log izin

                @elseif($todayStatus === 'sakit')

                    Log sakit

                @elseif($todayStatus === 'cuti')

                    Log cuti

                @else

                    {{ ucfirst($todayStatus) }}

                @endif

            </small>

        </div>


        <div class="activity-time">

            @if(
                $todayStatus === 'hadir'
                && $today->waktu_masuk
            )

                {{ \Carbon\Carbon::parse(
                    $today->waktu_masuk
                )->format('H:i') }}

            @elseif(
                in_array(
                    $todayStatus,
                    ['izin','sakit','cuti']
                )
            )

                @if($today->waktu_masuk)

                    {{ \Carbon\Carbon::parse(
                        $today->waktu_masuk
                    )->format('H:i') }}

                @elseif($today->created_at)

                    {{ \Carbon\Carbon::parse(
                        $today->created_at
                    )->format('H:i') }}

                @else

                    -

                @endif

            @else

                -

            @endif

        </div>

    </div>


    {{-- ABSEN PULANG --}}

    <div class="activity-item">

        <div class="activity-icon">
            <i class="bx bx-log-out"></i>
        </div>


        <div class="activity-info">

            <strong>
                Absen Pulang
            </strong>

            <small>

                @if(
                    $today &&
                    $today->waktu_pulang
                )

                    Waktu pulang

                @elseif(
                    in_array(
                        $todayStatus,
                        ['izin','sakit','cuti']
                    )
                )

                    Tidak ada absen pulang

                @else

                    Belum absen

                @endif

            </small>

        </div>


        <div class="activity-time">

            @if(
                $today &&
                $today->waktu_pulang
            )

                {{ \Carbon\Carbon::parse(
                    $today->waktu_pulang
                )->format('H:i') }}

            @else

                -

            @endif

        </div>

    </div>

</div>


{{-- RIWAYAT ABSENSI --}}

<div class="dashboard-card history-card">

    <div class="history-heading">

        <h5>
            Riwayat Absensi
        </h5>


        <form method="GET" class="history-filter">

            <input
                type="month"
                name="periode"
                value="{{ request('periode', now()->format('Y-m')) }}"
                onchange="this.form.submit()">

        </form>

    </div>


    @forelse($riwayatAbsensi as $row)

        @php

            $tanggal =
                \Carbon\Carbon::parse(
                    $row->tanggal
                );


            $status =
                strtolower(
                    $row->status ?? ''
                );


            $statusClass = match($status){

                'izin' =>
                    'status-izin',

                'sakit' =>
                    'status-sakit',

                'cuti' =>
                    'status-cuti',

                default =>
                    ''

            };

        @endphp


        <div class="history-item">


            {{-- TANGGAL --}}

            <div class="history-date">

                <strong>
                    {{ $tanggal->format('d') }}
                </strong>

                <small>

                    {{ $tanggal
                        ->locale('id')
                        ->translatedFormat('M') }}

                </small>

            </div>


            {{-- INFORMASI --}}

            <div class="history-info">

                <strong>

                    {{ $tanggal
                        ->locale('id')
                        ->translatedFormat('l') }}

                </strong>


                <small>

                    @if(
                        in_array(
                            $status,
                            ['izin','sakit','cuti']
                        )
                    )

                        Log:

                        @if($row->waktu_masuk)

                            {{ \Carbon\Carbon::parse(
                                $row->waktu_masuk
                            )->format('H:i') }}

                        @elseif($row->created_at)

                            {{ \Carbon\Carbon::parse(
                                $row->created_at
                            )->format('H:i') }}

                        @else

                            -

                        @endif

                    @else

                        <span class="history-time-row">


                            <span
                                class="history-time masuk
                                {{ !$row->waktu_masuk ? 'empty' : '' }}">

                                <i class="bx bx-log-in"></i>

                                Masuk

                                <b>

                                    {{ $row->waktu_masuk
                                        ? \Carbon\Carbon::parse(
                                            $row->waktu_masuk
                                        )->format('H:i')
                                        : '-' }}

                                </b>

                            </span>


                            <span
                                class="history-time pulang
                                {{ !$row->waktu_pulang ? 'empty' : '' }}">

                                <i class="bx bx-log-out"></i>

                                <b>Pulang</b>

                                <b>

                                    {{ $row->waktu_pulang
                                        ? \Carbon\Carbon::parse(
                                            $row->waktu_pulang
                                        )->format('H:i')
                                        : '-' }}

                                </b>

                            </span>


                        </span>

                    @endif

                </small>

            </div>


            {{-- STATUS --}}

            <span
                class="status-badge {{ $statusClass }}">

                {{ strtoupper(
                    $row->status ?? '-'
                ) }}

            </span>

        </div>


    @empty

        <div class="text-center py-3">

            <small class="text-muted">

                Belum ada riwayat absensi
                bulan ini.

            </small>

        </div>

    @endforelse


    {{-- PAGINATION --}}

    @if(
        method_exists($riwayatAbsensi, 'hasPages')
        && $riwayatAbsensi->hasPages()
    )

        <div class="history-pagination">

            {{ $riwayatAbsensi
                ->onEachSide(1)
                ->links('pagination::bootstrap-5') }}

        </div>

    @endif

</div>


</div>

</div>


{{-- MODAL UBAH JADWAL --}}

<div
    class="modal fade"
    id="modalUbahShift"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form id="formUbahShift">

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">
                        Ubah Jadwal Kerja
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <label
                        for="id_jam_kerja"
                        class="form-label">

                        Pilih Jadwal Kerja

                    </label>


                    <select
                        name="id_jam_kerja"
                        id="id_jam_kerja"
                        class="form-select"
                        required>

                        <option value="">
                            -- Pilih Jadwal --
                        </option>


                        @foreach($jamKerja as $jam)

                            <option
                                value="{{ $jam->id }}"
                                {{ $pegawai->id_jam_kerja == $jam->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $jam->nama_jam_kerja ?? 'Jam Kerja' }}

                                ({{ $jam->jam_mulai }}
                                -
                                {{ $jam->jam_selesai }})

                            </option>

                        @endforeach

                    </select>


                    <div
                        id="shiftMessage"
                        class="small mt-2">
                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Batal

                    </button>


                    <button
                        type="submit"
                        class="btn-absen">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@push('scripts')

<script>

$(function(){

    $('#formUbahShift').on(
        'submit',
        function(e){

            e.preventDefault();


            const form =
                $(this);


            const button =
                form.find(
                    'button[type="submit"]'
                );


            const message =
                $('#shiftMessage');


            message
                .removeClass(
                    'text-success text-danger'
                )
                .text('');


            button
                .prop('disabled',true)
                .text('Menyimpan...');


            $.ajax({

                url:
                    "{{ route('pegawai.updateShift') }}",

                type:
                    "POST",

                data:
                    form.serialize(),


                success:function(response){

                    message
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .text(
                            response.message ||
                            'Jadwal kerja berhasil diubah.'
                        );


                    setTimeout(
                        function(){

                            location.reload();

                        },
                        600
                    );

                },


                error:function(xhr){

                    let text =
                        'Gagal mengubah jadwal.';


                    if(
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ){

                        text =
                            xhr.responseJSON.message;

                    }


                    message
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text(text);


                    button
                        .prop('disabled',false)
                        .text('Simpan');

                }

            });

        }
    );

});

</script>

@endpush

@endsection