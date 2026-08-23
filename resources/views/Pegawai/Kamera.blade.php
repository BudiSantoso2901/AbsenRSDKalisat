@extends('_layouts.layouts')

@section('content')

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>

/* =========================================================
   CAMERA
========================================================= */

#my_camera{
    width:270px;
    height:270px;
    margin:auto;
    padding:5px;

    border-radius:16px;

    background:linear-gradient(
        135deg,
        #ff2da6,
        #c83fa8,
        #28c76f
    );

    box-shadow:none;
}

#my_camera video{
    width:100%!important;
    height:100%!important;

    border-radius:11px;

    object-fit:cover;
    background:#000;

    transform:scaleX(-1)!important;
}


/* =========================================================
   CARD
========================================================= */

.absensi-card{
    border-radius:18px;
    overflow:hidden;
}

.absensi-card .card-body{
    padding:20px;
}


/* =========================================================
   CAMERA TITLE
========================================================= */

.camera-title{
    text-align:center;
    margin-bottom:12px;
}

.camera-title h5{
    margin:0;
    font-size:16px;
}

.camera-title small{
    color:#999;
}


/* =========================================================
   LOCATION
========================================================= */

.location-box{
    background:#f5fbf7;
    border:1px solid #d9efde;
    border-radius:15px;
    padding:14px;
    margin-bottom:18px;
}

.location-title{
    font-weight:600;
    color:#687785;
    margin-bottom:10px;
}

.location-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
}

.location-item{
    background:#fff;
    border:1px solid #eee;
    border-radius:10px;
    padding:10px;
}

.location-item.full{
    grid-column:1 / -1;
}

.location-label{
    display:block;
    font-size:11px;
    color:#999;
    margin-bottom:3px;
}

.location-value{
    font-size:13px;
    font-weight:600;
}

.gps-status{
    margin-top:10px;
    color:#28a745;
    font-size:13px;
}

.location-detail{
    margin-top:5px;
    font-size:11px;
    color:#999;
}


/* =========================================================
   MAP
========================================================= */

.map-toggle{
    width:100%;
    margin-top:10px;

    padding:9px;

    border:1px solid #d9efde;
    background:#fff;
    color:#36894d;

    border-radius:10px;
}

.map-box{
    display:none;
    margin-top:10px;

    border-radius:12px;
    overflow:hidden;
}

.map-box.show{
    display:block;
}

#attendance-map{
    height:300px;
    width:100%;
}


/* =========================================================
   BUTTON
========================================================= */

.actions{
    display:flex;
    gap:8px;
    margin-top:15px;
    margin-bottom:18px;
}

.btn-reset,
.btn-absen{
    flex:1;

    height:42px;
    min-height:42px;

    padding:6px 10px;

    border-radius:8px;

    font-size:13px;
    font-weight:600;

    box-shadow:none;
}


/* =========================================================
   REFRESH
========================================================= */

.btn-reset{
    background:#fff;
    border:1px solid #ddd;
    color:#666;
}

.btn-reset:hover{
    background:#f8f8f8;
}


/* =========================================================
   ABSEN
========================================================= */

.btn-absen{
    background:#28a745;
    border:1px solid #28a745;
    color:#fff;
}

.btn-absen:hover{
    background:#218838;
}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px){

    #my_camera{
        width:260px;
        height:260px;
    }

    .actions{
        flex-wrap:nowrap;
    }

    .btn-reset,
    .btn-absen{
        width:auto;
        flex:1;
    }

}

</style>


{{-- =========================================================
     MAIN CONTAINER
========================================================= --}}

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="absensi-card card shadow-sm">

        <div class="card-body">


            {{-- =================================================
                 CAMERA TITLE
            ================================================= --}}

            <div class="camera-title">

                <h5>
                    📷 Ambil Foto Absensi
                </h5>

                <small>
                    Posisikan wajah di tengah kamera.
                </small>

            </div>


            {{-- =================================================
                 CAMERA
            ================================================= --}}

            <div id="section-hadir"
                 class="mb-4">

                <div id="my_camera"></div>

            </div>


            {{-- =================================================
                 FORM
            ================================================= --}}

            <form id="form-absensi"
                  enctype="multipart/form-data">

                @csrf


                {{-- PENTING:
                     LATITUDE & LONGITUDE ADA DI DALAM FORM
                --}}

                <input type="hidden"
                       name="latitude"
                       id="inputLat">

                <input type="hidden"
                       name="longitude"
                       id="inputLng">


                {{-- =================================================
                     MODE ABSENSI
                ================================================= --}}

                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Mode Absensi
                    </label>

                    <select class="form-select"
                            id="mode_absen"
                            name="mode_absen">

                        <option value="normal">
                            Absen Kerja
                        </option>

                        <option value="kegiatan">
                            Apel / Jumat Sehat
                        </option>

                    </select>

                </div>


                {{-- =================================================
                     STATUS
                ================================================= --}}

                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Status Kehadiran
                    </label>

                    <select class="form-select"
                            name="status"
                            id="status_absen">

                        <option value="hadir">
                            Hadir
                        </option>

                        <option value="izin">
                            Izin
                        </option>

                        <option value="sakit">
                            Sakit
                        </option>

                        <option value="cuti">
                            Cuti
                        </option>

                    </select>

                </div>


                {{-- =================================================
                     IZIN / SAKIT
                ================================================= --}}

                <div id="section-izin"
                     style="display:none">

                    <div class="mb-3">

                        <label class="form-label">
                            Keterangan <strong>*</strong>
                        </label>

                        <textarea
                            class="form-control"
                            name="keterangan"
                            rows="3"
                            placeholder="Tuliskan keterangan..."></textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Upload Surat
                        </label>

                        <input type="file"
                               name="surat"
                               class="form-control">

                    </div>

                </div>


                {{-- =================================================
                     BUTTON
                ================================================= --}}

                <div class="actions">

                    <button type="button"
                            class="btn-reset"
                            id="btnReset">

                        🔄 Refresh Lokasi

                    </button>


                    <button type="submit"
                            class="btn-absen"
                            id="btnSubmit">

                        📷 Absen Sekarang

                    </button>

                </div>

            </form>

            {{-- =================================================
                 LOCATION
            ================================================= --}}

            <div class="location-box">

                <div class="location-title">

                    📍 Lokasi Absensi

                </div>


                <div class="location-grid">


                    {{-- LOKASI --}}

                    <div class="location-item full">

                        <span class="location-label">
                            Lokasi
                        </span>

                        <span class="location-value"
                              id="locationName">

                            {{ $lokasi->nama_lokasi }}

                        </span>

                    </div>


                    {{-- RADIUS --}}

                    <div class="location-item">

                        <span class="location-label">
                            Radius
                        </span>

                        <span class="location-value"
                              id="locationRadius">

                            {{ $lokasi->radius_meter }} m

                        </span>

                    </div>


                    {{-- JAM KERJA --}}

                    <div class="location-item">

                        <span class="location-label">
                            Jam Kerja
                        </span>

                        <span class="location-value">

                            {{ $jamKerja->jam_mulai }}
                            -
                            {{ $jamKerja->jam_selesai }}

                        </span>

                    </div>

                </div>


                {{-- =================================================
                     GPS STATUS
                ================================================= --}}

                <div class="gps-status">

                    ●

                    <span id="gpsText">
                        GPS sedang digunakan
                    </span>

                </div>


                {{-- =================================================
                     KOORDINAT
                ================================================= --}}

                <div class="location-detail">

                    Lat:
                    <b id="lat">-</b>

                    &nbsp;

                    Lng:
                    <b id="lng">-</b>

                </div>


                {{-- =================================================
                     MAP BUTTON
                ================================================= --}}

                <button type="button"
                        class="map-toggle"
                        id="mapToggle">

                    🗺️

                    <span id="mapToggleText">
                        Tampilkan Peta
                    </span>

                </button>


                {{-- =================================================
                     MAP
                ================================================= --}}

                <div class="map-box"
                     id="mapBox">

                    <div id="attendance-map"></div>

                </div>

            </div>




        </div>

    </div>

</div>


{{-- =========================================================
     LEAFLET
========================================================= --}}

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

/* =========================================================
   HELPER
========================================================= */

const $ = id => document.getElementById(id);

/* =========================================================
   POPUP SWEETALERT
========================================================= */
function popupPesan(message, defaultIcon = 'warning'){

    const pesan =
        String(
            message ||
            'Terjadi kesalahan.'
        );

    const lower =
        pesan.toLowerCase();

    let icon =
        defaultIcon;

    let title =
        'Perhatian';


    if(
        lower.includes('berhasil') ||
        lower.includes('sukses')
    ){

        icon =
            'success';

        title =
            'Berhasil';

    }else if(
        lower.includes('gagal') ||
        lower.includes('error') ||
        lower.includes('server') ||
        lower.includes('tidak valid')
    ){

        icon =
            'error';

        title =
            'Gagal';

    }


    return Swal.fire({

        icon:
            icon,

        title:
            title,

        text:
            pesan,

        confirmButtonText:
            'OK',

        confirmButtonColor:
            '#097612'

    });

}


/* =========================================================
   ELEMENT
========================================================= */

const form =
    $('form-absensi');

const modeSelect =
    $('mode_absen');

const statusSelect =
    $('status_absen');

const btnSubmit =
    $('btnSubmit');

const btnReset =
    $('btnReset');

const sectionHadir =
    $('section-hadir');

const sectionIzin =
    $('section-izin');


let isSubmitting = false;


/* =========================================================
   MODE ABSENSI
========================================================= */

modeSelect.addEventListener(
    'change',
    function(){

        if(this.value === 'kegiatan'){

            btnSubmit.innerHTML =
                '🚩 Absen Kegiatan';

            btnSubmit.style.background =
                '#ff9800';

        }else{

            btnSubmit.innerHTML =
                '📷 Absen Sekarang';

            btnSubmit.style.background =
                '#28a745';

        }

        renderLokasi(this.value);

    }
);


/* =========================================================
   STATUS
========================================================= */

statusSelect.addEventListener(
    'change',
    function(){

        if(this.value === 'hadir'){

            sectionHadir.style.display =
                'block';

            sectionIzin.style.display =
                'none';

        }else{

            sectionHadir.style.display =
                'none';

            sectionIzin.style.display =
                'block';

        }

    }
);


/* =========================================================
   DATA LOKASI
========================================================= */

const lokasiPegawai = {

    lat: {{ $lokasi->latitude }},

    lng: {{ $lokasi->longitude }},

    radius: {{ $lokasi->radius_meter }},

    nama: @json($lokasi->nama_lokasi)

};


const lokasiKegiatan = {

    lat: -8.13484147,

    lng: 113.82144392,

    radius: 50,

    nama: 'Lokasi Apel / Jumat Sehat'

};


function getLokasi(mode){

    return mode === 'kegiatan'
        ? lokasiKegiatan
        : lokasiPegawai;

}


/* =========================================================
   MAP TILE
========================================================= */

const osm =
    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom:19,

            attribution:
                '© OpenStreetMap contributors'
        }
    );


/*
 * SATELIT
 */

const esri =
    L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom:19,

            attribution:
                'Tiles © Esri'
        }
    );


/* =========================================================
   MAP VARIABLES
========================================================= */

let map = null;

let lokasiMarker = null;

let radiusCircle = null;

let markerUser = null;


/* =========================================================
   INIT MAP
========================================================= */

function initMap(){

    if(map)
        return;


    map =
        L.map(
            'attendance-map',
            {

                center:[
                    lokasiPegawai.lat,
                    lokasiPegawai.lng
                ],

                zoom:17,

                scrollWheelZoom:false,

                /*
                 * DEFAULT SATELIT
                 */

                layers:[
                    esri
                ]

            }
        );


    /*
     * PILIH MAP
     */

    L.control.layers({

        'Satelit':
            esri,

        'Street Map':
            osm

    }).addTo(map);

}


/* =========================================================
   RENDER LOKASI
========================================================= */

function renderLokasi(mode='normal'){

    initMap();


    const lokasi =
        getLokasi(mode);


    if(lokasiMarker)
        map.removeLayer(
            lokasiMarker
        );


    if(radiusCircle)
        map.removeLayer(
            radiusCircle
        );


    map.setView(
        [
            lokasi.lat,
            lokasi.lng
        ],
        17
    );


    lokasiMarker =
        L.marker([
            lokasi.lat,
            lokasi.lng
        ])
        .addTo(map)
        .bindPopup(
            `<b>${lokasi.nama}</b>`
        );


    radiusCircle =
        L.circle(
            [
                lokasi.lat,
                lokasi.lng
            ],
            {

                radius:
                    lokasi.radius,

                color:
                    mode === 'kegiatan'
                        ? 'orange'
                        : 'green',

                fillColor:
                    mode === 'kegiatan'
                        ? '#ff9800'
                        : '#4CAF50',

                fillOpacity:
                    .2

            }
        )
        .addTo(map);


    $('locationName').innerText =
        lokasi.nama;


    $('locationRadius').innerText =
        lokasi.radius + ' m';


    setTimeout(
        () => map.invalidateSize(),
        200
    );

}


/* =========================================================
   MAP TOGGLE
========================================================= */

$('mapToggle').addEventListener(
    'click',
    function(){

        const box =
            $('mapBox');


        const show =
            box.classList.toggle(
                'show'
            );


        $('mapToggleText').innerText =
            show
                ? 'Sembunyikan Peta'
                : 'Tampilkan Peta';


        if(show){

            renderLokasi(
                modeSelect.value
            );

        }

    }
);


/* =========================================================
   GPS
========================================================= */

function getGPS(){

    if(!navigator.geolocation){

        $('gpsText').innerText =
            'GPS tidak tersedia';

        return;

    }


    $('gpsText').innerText =
        'Mencari lokasi...';


    navigator.geolocation.getCurrentPosition(

        function(position){

            const lat =
                position.coords.latitude;

            const lng =
                position.coords.longitude;


            /*
             * TAMPILKAN KOORDINAT
             */

            $('lat').innerText =
                lat.toFixed(6);

            $('lng').innerText =
                lng.toFixed(6);


            /*
             * SIMPAN KE FORM
             */

            $('inputLat').value =
                lat;

            $('inputLng').value =
                lng;


            /*
             * STATUS GPS
             *
             * Tidak menampilkan +66m
             */

            $('gpsText').innerText =
                'GPS aktif';


            /*
             * USER MARKER
             */

            if(map){

                if(markerUser){

                    map.removeLayer(
                        markerUser
                    );

                }


                markerUser =
                    L.marker([
                        lat,
                        lng
                    ])
                    .addTo(map)
                    .bindPopup(
                        'Lokasi Anda'
                    );

            }

        },


        function(error){

            console.log(
                'GPS Error:',
                error
            );


            if(error.code === 1){

                $('gpsText').innerText =
                    'Izin lokasi ditolak';

            }else{

                $('gpsText').innerText =
                    'GPS tidak tersedia';

            }

        },


        {

            enableHighAccuracy:true,

            timeout:30000,

            maximumAge:0

        }

    );

}


/*
 * GPS AWAL
 */

getGPS();


/* =========================================================
   REFRESH GPS
========================================================= */

btnReset.addEventListener(
    'click',
    function(){

        const oldText =
            this.innerHTML;


        this.disabled =
            true;


        this.innerHTML =
            '⏳ Mencari lokasi...';


        getGPS();


        setTimeout(
            () => {

                this.disabled =
                    false;


                this.innerHTML =
                    oldText;

            },
            1500
        );

    }
);


/* =========================================================
   WEBCAM
========================================================= */

function getCameraSize(){

    if(window.innerWidth <= 768){

        return {

            width:260,

            height:260

        };

    }


    return {

        width:420,

        height:420

    };

}


const camSize =
    getCameraSize();


if(typeof Webcam !== 'undefined'){

    Webcam.set({

        width:
            camSize.width,

        height:
            camSize.height,

        image_format:
            'jpeg',

        jpeg_quality:
            85,

        constraints:{

            facingMode:
                'user'

        }

    });


    Webcam.attach(
        '#my_camera'
    );

}


/* =========================================================
   FORM SUBMIT
========================================================= */

form.addEventListener(
    'submit',
    function(e){

        e.preventDefault();


        if(isSubmitting)
            return;


        isSubmitting =
            true;


        const status =
            statusSelect.value;


        const mode =
            modeSelect.value;


        const formData =
            new FormData(this);


        /* =================================================
           CEK GPS

           HADIR    -> WAJIB GPS
           KEGIATAN -> WAJIB GPS
           IZIN     -> TIDAK WAJIB GPS
           SAKIT    -> TIDAK WAJIB GPS
        ================================================= */

        if(
            status === 'hadir' ||
            mode === 'kegiatan'
        ){

            if(
                !$('inputLat').value ||
                !$('inputLng').value
            ){

                isSubmitting =
                    false;


                popupPesan(
                    'Lokasi GPS belum ditemukan. Silakan refresh lokasi.',
                    'warning'
                );


                return;

            }

        }


        /* =================================================
           HADIR
        ================================================= */

        if(status === 'hadir'){

            /*
             * Jangan kirim keterangan
             */

            formData.delete(
                'keterangan'
            );


            /*
             * Jangan kirim surat
             */

            formData.delete(
                'surat'
            );


            /*
             * CEK KAMERA
             */

            if(
                typeof Webcam === 'undefined'
            ){

                isSubmitting =
                    false;


                popupPesan(
                    'Kamera belum siap.',
                    'warning'
                );


                return;

            }


            btnSubmit.disabled =
                true;


            btnSubmit.innerHTML =
                '⏳ Mengambil foto...';


            Webcam.snap(
                function(data){

                    if(!data){

                        isSubmitting =
                            false;


                        btnSubmit.disabled =
                            false;


                        btnSubmit.innerHTML =
                            mode === 'kegiatan'
                                ? '🚩 Absen Kegiatan'
                                : '📷 Absen Sekarang';


                        popupPesan(
                            'Foto gagal diambil.',
                            'error'
                        );


                        return;

                    }


                    formData.append(
                        'foto',
                        dataURItoBlob(data),
                        'absen.jpg'
                    );


                    kirim(
                        formData
                    );

                }
            );


            return;

        }


        /* =================================================
           IZIN / SAKIT

           GPS TIDAK WAJIB
           FOTO TIDAK DIKIRIM
        ================================================= */

        const ket =
            this.querySelector(
                'textarea[name="keterangan"]'
            );


        if(
            !ket ||
            !ket.value.trim()
        ){

            isSubmitting =
                false;


            popupPesan(
                'Keterangan wajib diisi untuk izin, sakit atau cuti.',
                'warning'
            );


            return;

        }


        /*
         * Jangan kirim foto
         */

        formData.delete(
            'foto'
        );


        /*
         * KIRIM IZIN / SAKIT
         */

        kirim(
            formData
        );

    }
);


/* =========================================================
   SEND
========================================================= */

async function kirim(formData){

    const mode =
        modeSelect.value;


    const url =
        mode === 'kegiatan'

            ? "{{ route('absensi.kegiatan') }}"

            : "{{ route('absensi.store') }}";


    btnSubmit.disabled =
        true;


    btnSubmit.innerHTML =
        '⏳ Memproses...';


    try{

        const response =
            await fetch(
                url,
                {

                    method:'POST',

                    headers:{

                        'X-CSRF-TOKEN':
                            "{{ csrf_token() }}",

                        'Accept':
                            'application/json'

                    },

                    body:
                        formData

                }
            );


        const text =
            await response.text();


        let data = {};


        try{

            data =
                text
                    ? JSON.parse(text)
                    : {};

        }catch{

            throw new Error(
                'Response server tidak valid.'
            );

        }


        /*
         * ERROR
         *
         * Pesan dari AbsensiController
         * akan masuk ke sini.
         */

        if(!response.ok){

            if(data.errors){

                const errors =
                    Object.values(
                        data.errors
                    )
                    .flat()
                    .join('\n');


                throw new Error(
                    errors
                );

            }


            throw new Error(
                data.message ||
                'Gagal melakukan absensi.'
            );

        }


        /*
         * BERHASIL
         */

        if (data.telat) {

            await Swal.fire({
                icon: 'warning',
                title: 'Anda Terlambat',
                html: `
                    <strong>${data.menitTelat} menit</strong><br>
                    Badge: <strong>${data.badge}</strong>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#097612'
            });

        } else {

            await popupPesan(
                data.message ||
                'Absensi berhasil.',
                'success'
            );

        }

        window.location.href = "{{ route('pegawai.dashboard') }}";


    }catch(error){

        console.error(
            'Absensi error:',
            error
        );


        /*
         * POPUP PESAN DARI CONTROLLER
         *
         * Contoh:
         *
         * "Belum waktunya absen masuk"
         * "Belum waktunya absen pulang"
         * "Anda berada di luar area absensi"
         * "Absensi hari ini sudah lengkap"
         * "Di luar jam Apel"
         * dll.
         */

        await popupPesan(
            error.message ||
            'Terjadi kesalahan server.',
            'warning'
        );


        /*
         * Setelah gagal,
         * tombol bisa digunakan lagi.
         */

        isSubmitting =
            false;


        btnSubmit.disabled =
            false;


        btnSubmit.innerHTML =
            mode === 'kegiatan'
                ? '🚩 Absen Kegiatan'
                : '📷 Absen Sekarang';

    }

}


/* =========================================================
   DATA URI -> BLOB
========================================================= */

function dataURItoBlob(dataURI){

    const parts =
        dataURI.split(',');


    const mime =
        parts[0]
            .match(
                /:(.*?);/
            )[1];


    const binary =
        atob(parts[1]);


    const array =
        new Uint8Array(
            binary.length
        );


    for(
        let i = 0;
        i < binary.length;
        i++
    ){

        array[i] =
            binary.charCodeAt(i);

    }


    return new Blob(
        [array],
        {

            type:mime

        }
    );

}


/* =========================================================
   DEFAULT STATUS
========================================================= */

statusSelect.dispatchEvent(
    new Event('change')
);

</script>

@endpush

@endsection