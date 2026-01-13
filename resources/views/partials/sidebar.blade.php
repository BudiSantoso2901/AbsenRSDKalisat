<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <img src="{{ asset('assets/img/images-removebg-preview.png') }}" width="150">
        </a>
    </div>

    <ul class="menu-inner py-1">

        {{-- ================= ADMIN ================= --}}
        @auth('web')
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li
                class="menu-item {{ request()->is('pegawai/*', 'jabatan/*', 'jam-kerja/*', 'lokasi/*') ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class="menu-icon bx bx-layer"></i>
                    <div>Master</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('pegawai.index') ? 'active' : '' }}">
                        <a href="{{ route('pegawai.index') }}" class="menu-link">Data Pegawai</a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('jabatan.index') ? 'active' : '' }}">
                        <a href="{{ route('jabatan.index') }}" class="menu-link">Data Jabatan</a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('jam-kerja.index') ? 'active' : '' }}">
                        <a href="{{ route('jam-kerja.index') }}" class="menu-link">Jam Kerja</a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('lokasi.index') ? 'active' : '' }}">
                        <a href="{{ route('lokasi.index') }}" class="menu-link">Lokasi</a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('absensi.index') ? 'active' : '' }}">
                <a href="{{ route('absensi.index') }}" class="menu-link">
                    <i class="menu-icon bx bx-clipboard"></i>
                    <div>Data Absensi</div>
                </a>
            </li>
        @endauth

        {{-- ================= PEGAWAI ================= --}}
        @auth('pegawai')
            <li class="menu-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                <a href="{{ route('pegawai.dashboard') }}" class="menu-link">
                    <i class="menu-icon bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('pegawai.kamera') ? 'active' : '' }}">
                <a href="{{ route('pegawai.kamera') }}" class="menu-link">
                    <i class="menu-icon bx bx-camera"></i>
                    <div>Absensi</div>
                </a>
            </li>
        @endauth

        {{-- ================= LOGOUT ================= --}}
        <li class="menu-item">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
            </form>
            <a href="#" class="menu-link"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="menu-icon bx bx-exit"></i>
                <div>Logout</div>
            </a>
        </li>

    </ul>
</aside>
