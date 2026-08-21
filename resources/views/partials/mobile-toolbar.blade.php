<nav class="mobile-bottom-toolbar">

    {{-- HOME --}}
    <a href="{{ route('pegawai.dashboard') }}"
        class="mobile-toolbar-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">

        <i class='bx bx-home'></i>

        <span>Home</span>

    </a>


    {{-- PANDUAN --}}
    <a href="{{ route('pegawai.panduan') }}"
        class="mobile-toolbar-item {{ request()->routeIs('pegawai.panduan') ? 'active' : '' }}">

        <i class='bx bx-book-open'></i>

        <span>Panduan</span>

    </a>


    {{-- KAMERA ABSENSI - TOMBOL TENGAH --}}
   @if (!request()->routeIs('pegawai.kamera'))

    <a href="{{ route('pegawai.kamera') }}"
        class="mobile-toolbar-center">

        <div class="mobile-toolbar-center-icon">

            <svg class="face-id-icon" viewBox="0 0 64 64" fill="none">

                <!-- Sudut kiri atas -->
                <path d="M8 20V12C8 9.8 9.8 8 12 8H20"
                    stroke="white"
                    stroke-width="3"
                    stroke-linecap="round" />

                <!-- Sudut kanan atas -->
                <path d="M44 8H52C54.2 8 56 9.8 56 12V20"
                    stroke="white"
                    stroke-width="3"
                    stroke-linecap="round" />

                <!-- Sudut kiri bawah -->
                <path d="M8 44V52C8 54.2 9.8 56 12 56H20"
                    stroke="white"
                    stroke-width="3"
                    stroke-linecap="round" />

                <!-- Sudut kanan bawah -->
                <path d="M44 56H52C54.2 56 56 54.2 56 52V44"
                    stroke="white"
                    stroke-width="3"
                    stroke-linecap="round" />

                <g transform="translate(32 32) scale(1.18) translate(-32 -32)">

                <!-- Bentuk wajah -->
                    <path d="M22 25C22 20.6 25.6 17 30 17H34C38.4 17 42 20.6 42 25V35C42 40.5 37.5 45 32 45C26.5 45 22 40.5 22 35V25Z"
                    stroke="white"
                    stroke-width="2.5"
                    stroke-linejoin="round" />

                <!-- Mata -->
                <circle cx="27" cy="29" r="1.5" fill="white" />
                <circle cx="37" cy="29" r="1.5" fill="white" />

                <!-- Hidung -->
                <path d="M32 30V34"
                    stroke="white"
                    stroke-width="2"
                    stroke-linecap="round" />

                <!-- Mulut -->
                <path d="M28 37C30 39 34 39 36 37"
                    stroke="white"
                    stroke-width="2"
                    stroke-linecap="round" />

                </g>
            </svg>

        </div>

        {{-- LABEL ABSEN --}}
        <span class="mobile-toolbar-center-label">
            Absen
        </span>

    </a>

@endif


    {{-- ABSEN KONTEN --}}
    <a href="{{ route('pegawai.konten.index') }}"
        class="mobile-toolbar-item {{ request()->routeIs('pegawai.konten.*') ? 'active' : '' }}">

        <i class='bx bx-file'></i>

        <span>Konten</span>

    </a>


    {{-- PROFIL --}}
    <a href="{{ route('profile.edit') }}"
        class="mobile-toolbar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">

        <i class='bx bx-user'></i>

        <span>Profil</span>

    </a>

</nav>