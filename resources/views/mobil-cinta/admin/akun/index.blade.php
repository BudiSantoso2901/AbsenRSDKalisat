@extends('mobil-cinta.layouts.app')

@section('title', 'Master Akun')

@section('content')

    <style>
        /* =====================================
                           PAGE HEADING
                        ===================================== */

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 23px;
        }

        /* =====================================
                           ACCOUNT SUMMARY
                        ===================================== */

        .account-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #fff;
            border: 1px solid #e5e9e7;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(25, 65, 35, .035);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 12px;
            background: #eaf6ec;
            color: #178b2a;
            font-size: 24px;
        }

        .summary-card small {
            display: block;
            color: #7b858c;
            font-size: 11px;
            font-weight: 700;
        }

        .summary-card strong {
            display: block;
            color: #263238;
            font-size: 23px;
            font-weight: 800;
            line-height: 1.3;
        }

        /* =====================================
                           FILTER
                        ===================================== */

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            align-items: end;
            gap: 12px;
        }

        .filter-grid .form-group {
            margin: 0;
        }

        /* =====================================
                           ACCOUNT TABLE
                        ===================================== */

        .account-name {
            display: block;
            color: #263238;
            font-weight: 800;
        }

        .account-username {
            display: block;
            color: #818b84;
            font-size: 11px;
        }

        .account-pagination {
            display: flex;
            justify-content: center;
            margin-top: 22px;
        }

        .account-pagination-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .account-pagination-nav a,
        .account-pagination-nav span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 36px;
            height: 36px;
            padding: 0 11px;
            border: 1px solid #dce8df;
            border-radius: 8px;
            background: #ffffff;
            color: #53695b;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: .2s;
        }

        .account-pagination-nav a:hover {
            background: #eaf8ef;
            border-color: #08a653;
            color: #078b48;
        }

        .account-pagination-nav a.pagination-active {
            background: #08a653;
            border-color: #08a653;
            color: #ffffff;
        }

        .account-pagination-nav .pagination-disabled {
            color: #aab5ad;
            background: #f5f8f5;
            cursor: not-allowed;
        }

        @media(max-width: 480px) {
            .account-pagination-nav {
                gap: 4px;
            }

            .account-pagination-nav a,
            .account-pagination-nav span {
                min-width: 32px;
                height: 33px;
                padding: 0 8px;
                font-size: 10px;
            }
        }

        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .btn-sm {
            padding: 7px 10px;
            font-size: 11px;
        }

        .reset-box {
            margin-top: 10px;
            padding: 12px;
            min-width: 210px;
            background: #f7faf7;
            border: 1px solid #e1ebe2;
            border-radius: 10px;
        }

        .reset-box summary {
            color: #178b2a;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .reset-box input {
            margin-top: 8px;
            padding: 8px 10px;
            font-size: 11px;
        }

        .empty-state {
            padding: 38px 15px;
            text-align: center;
            color: #818b84;
        }

        .empty-state i {
            display: block;
            margin-bottom: 8px;
            color: #178b2a;
            font-size: 38px;
        }

        /* AUTOCOMPLETE CARI AKUN */
        .account-search {
            position: relative;
            width: 100%;
        }

        .account-suggestions[hidden] {
            display: none !important;
        }

        .account-suggestions {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            z-index: 9999;

            background: #FFFFFF;
            border: 1px solid #D7EDE0;
            border-radius: 12px;

            box-shadow: 0 8px 24px rgba(8, 100, 55, 0.13);

            max-height: 280px;
            overflow-y: auto;
        }

        .account-suggestion-item {
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 12px 15px;

            background: #FFFFFF;
            border: none;
            border-bottom: 1px solid #EEF5F0;

            text-align: left;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .account-suggestion-item:last-child {
            border-bottom: none;
        }

        .account-suggestion-item:hover {
            background: #EAF8F0;
        }

        .account-suggestion-name {
            color: #18352A;
            font-weight: 600;
            font-size: 14px;
        }

        .account-suggestion-username {
            color: #718078;
            font-size: 12px;
            margin-top: 3px;
        }

        .account-suggestion-empty {
            padding: 14px;
            color: #718078;
            font-size: 13px;
        }

        /* =====================================
                           STATUS BADGE
                        ===================================== */

        .status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .status-active {
            background: #eaf6ec;
            color: #14752a;
        }

        .status-inactive {
            background: #f0f1f2;
            color: #687078;
        }

        /* =====================================
                           EDIT MODAL
                        ===================================== */

        .account-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;

            display: none;
            align-items: center;
            justify-content: center;

            padding: 20px;
            background: rgba(20, 35, 24, .58);
            backdrop-filter: blur(3px);
        }

        .account-modal-overlay.is-open {
            display: flex;
        }

        .account-modal {
            width: 100%;
            max-width: 650px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;

            background: #fff;
            border: 1px solid #e3eae4;
            border-radius: 18px;

            box-shadow: 0 24px 70px rgba(0, 0, 0, .20);

            animation: modalFade .2s ease;
        }

        @keyframes modalFade {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .account-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;

            padding: 21px 25px;

            border-bottom: 1px solid #e8ede9;
            background: #fff;
        }

        .account-modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .account-modal-icon {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;

            border-radius: 12px;
            background: #eaf6ec;
            color: #178b2a;
            font-size: 22px;
        }

        .account-modal-title h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .account-modal-title p {
            margin: 2px 0 0;
            color: #838b94;
            font-size: 11px;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;

            border: 1px solid #e4e9e5;
            border-radius: 9px;

            background: #fff;
            color: #68736b;

            font-size: 21px;
            cursor: pointer;
        }

        .modal-close:hover {
            background: #eaf6ec;
            color: #097612;
        }

        .account-modal-body {
            padding: 24px 25px;
        }

        .account-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;

            padding: 17px 25px;
            border-top: 1px solid #e8ede9;
            background: #fafcfb;
        }

        .modal-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 17px;
        }

        .modal-form-grid .full-width {
            grid-column: 1 / -1;
        }

        .modal-error {
            display: block;
            margin-top: 5px;
            color: #dc3545;
            font-size: 11px;
        }

        .modal-status {
            display: flex;
            align-items: center;
            gap: 10px;

            padding: 12px 14px;

            border: 1px solid #dce8de;
            border-radius: 10px;
            background: #f7faf7;
        }

        .modal-status input {
            width: 17px;
            height: 17px;
            margin: 0;
            accent-color: #178b2a;
        }

        .modal-status label {
            margin: 0;
            color: #35443a;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .modal-help {
            display: block;
            margin-top: 6px;
            color: #838b94;
            font-size: 10px;
        }

        /* =====================================
                           RESPONSIVE
                        ===================================== */

        @media(max-width: 750px) {
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filter-grid .search-field {
                grid-column: 1 / -1;
            }
        }

        @media(max-width: 600px) {
            .account-modal-overlay {
                padding: 10px;
            }

            .account-modal {
                max-height: calc(100vh - 20px);
                border-radius: 14px;
            }

            .account-modal-header,
            .account-modal-body {
                padding: 18px;
            }

            .account-modal-footer {
                padding: 15px 18px;
            }

            .modal-form-grid {
                grid-template-columns: 1fr;
            }

            .modal-form-grid .full-width {
                grid-column: auto;
            }
        }

        @media(max-width: 480px) {
            .account-summary {
                gap: 9px;
            }

            .summary-card {
                padding: 13px;
                gap: 9px;
            }

            .summary-icon {
                width: 39px;
                height: 39px;
                font-size: 20px;
            }

            .summary-card strong {
                font-size: 19px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-grid .search-field {
                grid-column: auto;
            }
        }
    </style>

    @php
        $currentAccountId = auth('mobil_cinta')->id();
    @endphp


    {{-- =====================================
     PAGE HEADING
===================================== --}}

    <div class="page-heading">

        <div>
            <span class="role">
                <i class="bx bx-user-circle"></i>
                ADMINISTRASI
            </span>

            <h1 style="margin-top:10px;">
                Master Akun
            </h1>

            <p class="page-subtitle">
                Pengelolaan akun pengguna sistem Mobil Cinta RSD Kalisat.
            </p>
        </div>

        <a href="{{ route('mobil-cinta.admin.akun.create') }}" class="btn btn-primary">

            <i class="bx bx-plus-circle"></i>
            Tambah Akun

        </a>

    </div>


    {{-- =====================================
     RINGKASAN AKUN
===================================== --}}

    <div class="account-summary">

        <div class="summary-card">

            <div class="summary-icon">
                <i class="bx bx-group"></i>
            </div>

            <div>
                <small>Total Akun</small>

                <strong>
                    {{ number_format($totalAccounts, 0, ',', '.') }}
                </strong>
            </div>

        </div>

        <div class="summary-card">

            <div class="summary-icon">
                <i class="bx bx-user-check"></i>
            </div>

            <div>
                <small>Akun Aktif</small>

                <strong>
                    {{ number_format($activeAccounts, 0, ',', '.') }}
                </strong>
            </div>

        </div>

    </div>


    {{-- =====================================
     DAFTAR PENGGUNA
===================================== --}}

    <section class="card card-accent">

        <h2 class="section-heading" style="margin-top:0;">
            <i class="bx bx-list-ul" style="color:#178b2a;"></i>
            Daftar Pengguna
        </h2>

        {{-- FILTER --}}

        <form method="GET" action="{{ route('mobil-cinta.admin.akun.index') }}">

            <div class="filter-grid">

                <div class="form-group search-field account-search">

                    <label class="form-label" for="accountSearchInput">
                        Cari Akun
                    </label>

                    <input type="text" id="accountSearchInput" name="q" value="{{ $search }}"
                        placeholder="Cari nama atau username..." autocomplete="off">

                    <div id="accountSuggestions" class="account-suggestions" hidden></div>

                </div>

                <div class="form-group">

                    <label class="form-label" for="peran_id">
                        Peran
                    </label>

                    <select name="peran_id" id="peran_id">

                        <option value="">
                            Semua Peran
                        </option>

                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ (string) $roleId === (string) $role->id ? 'selected' : '' }}>

                                {{ ucfirst($role->nama_peran) }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label class="form-label" for="status">
                        Status
                    </label>

                    <select name="status" id="status">

                        <option value="">Semua Status</option>

                        <option value="1" {{ (string) $status === '1' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="0" {{ (string) $status === '0' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>

                </div>

                <div class="action-group">

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i>
                        Cari
                    </button>

                    <a href="{{ route('mobil-cinta.admin.akun.index') }}" class="btn btn-outline">
                        Reset
                    </a>

                </div>

            </div>

        </form>


        {{-- TABEL AKUN --}}

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th width="55">No.</th>
                        <th>Pengguna</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th width="260">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($accounts as $account)
                        <tr>

                            <td>
                                {{ $accounts->firstItem() + $loop->index }}
                            </td>

                            <td>

                                <span class="account-name">
                                    {{ $account->nama }}
                                </span>

                                <span class="account-username">
                                    {{ '@' . $account->nama_pengguna }}
                                </span>

                            </td>

                            <td>

                                <span class="role">
                                    {{ ucfirst($account->role?->nama_peran ?? '-') }}
                                </span>

                            </td>

                            <td>

                                @if ($account->status_aktif)
                                    <span class="status status-active">
                                        <i class="bx bx-check-circle"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="status status-inactive">
                                        <i class="bx bx-x-circle"></i>
                                        Nonaktif
                                    </span>
                                @endif

                            </td>

                            <td>

                                @if ($account->login_terakhir_pada)
                                    {{ $account->login_terakhir_pada->format('d/m/Y H:i') }}
                                @else
                                    <span style="color:#9aa39d;">
                                        Belum pernah login
                                    </span>
                                @endif

                            </td>

                            <td>

                                <div class="action-group">

                                    {{-- TOMBOL EDIT POPUP --}}

                                    <button type="button" class="btn btn-outline btn-sm js-edit-account"
                                        data-account-id="{{ $account->id }}" data-name="{{ $account->nama }}"
                                        data-username="{{ $account->nama_pengguna }}"
                                        data-role-id="{{ $account->peran_id }}"
                                        data-status="{{ $account->status_aktif ? '1' : '0' }}"
                                        data-is-self="{{ (int) $currentAccountId === (int) $account->id ? '1' : '0' }}">

                                        <i class="bx bx-edit"></i>
                                        Edit

                                    </button>


                                    {{-- AKTIF / NONAKTIF --}}

                                    @if ((int) $currentAccountId !== (int) $account->id)
                                        <form method="POST"
                                            action="{{ route('mobil-cinta.admin.akun.toggle-status', $account) }}"
                                            onsubmit="return confirm('Yakin ingin {{ $account->status_aktif ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="btn {{ $account->status_aktif ? 'btn-danger' : 'btn-primary' }} btn-sm">

                                                <i
                                                    class="bx {{ $account->status_aktif ? 'bx-user-x' : 'bx-user-check' }}"></i>

                                                {{ $account->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}

                                            </button>

                                        </form>
                                    @else
                                        <span class="status status-active">
                                            Akun Anda
                                        </span>
                                    @endif

                                </div>


                                {{-- RESET PASSWORD --}}

                                <details class="reset-box">

                                    <summary>
                                        <i class="bx bx-key"></i>
                                        Reset Password
                                    </summary>

                                    <form method="POST"
                                        action="{{ route('mobil-cinta.admin.akun.reset-password', $account) }}"
                                        onsubmit="return confirm('Yakin ingin mengganti password akun ini?')">

                                        @csrf
                                        @method('PATCH')

                                        <input type="password" name="password" placeholder="Password baru" minlength="8"
                                            autocomplete="new-password" required>

                                        <input type="password" name="password_confirmation"
                                            placeholder="Konfirmasi password" minlength="8" autocomplete="new-password"
                                            required>

                                        <button type="submit" class="btn btn-primary btn-sm"
                                            style="width:100%;margin-top:8px;">

                                            <i class="bx bx-save"></i>
                                            Simpan Password

                                        </button>

                                    </form>

                                </details>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="empty-state">

                                    <i class="bx bx-user-x"></i>

                                    <strong>Data akun tidak ditemukan</strong>

                                    <p>
                                        Coba ubah kata kunci atau filter pencarian.
                                    </p>

                                </div>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if ($accounts->hasPages())
            <div class="account-pagination">

                <div class="account-pagination-nav">

                    {{-- SEBELUMNYA --}}
                    @if ($accounts->onFirstPage())
                        <span class="pagination-disabled">
                            <i class="bx bx-chevron-left"></i>
                            Sebelumnya
                        </span>
                    @else
                        <a href="{{ $accounts->previousPageUrl() }}">
                            <i class="bx bx-chevron-left"></i>
                            Sebelumnya
                        </a>
                    @endif


                    {{-- NOMOR HALAMAN --}}
                    @foreach ($accounts->getUrlRange(1, $accounts->lastPage()) as $page => $url)
                        <a href="{{ $url }}"
                            class="{{ $accounts->currentPage() === $page ? 'pagination-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach


                    {{-- BERIKUTNYA --}}
                    @if ($accounts->hasMorePages())
                        <a href="{{ $accounts->nextPageUrl() }}">
                            Berikutnya
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    @else
                        <span class="pagination-disabled">
                            Berikutnya
                            <i class="bx bx-chevron-right"></i>
                        </span>
                    @endif

                </div>

            </div>
        @endif

        <p class="page-subtitle" style="margin-top:12px;">
            Menampilkan {{ $accounts->count() }}
            dari {{ $accounts->total() }} akun sesuai filter.
        </p>

    </section>


    {{-- =====================================
     MODAL EDIT AKUN
===================================== --}}

    <div class="account-modal-overlay" id="editAccountModal" aria-hidden="true">

        <div class="account-modal" role="dialog" aria-modal="true" aria-labelledby="editAccountTitle">

            {{-- HEADER MODAL --}}

            <div class="account-modal-header">

                <div class="account-modal-title">

                    <div class="account-modal-icon">
                        <i class="bx bx-user-circle"></i>
                    </div>

                    <div>
                        <h2 id="editAccountTitle">
                            Edit Akun
                        </h2>

                        <p>
                            Perbarui informasi akun Mobil Cinta.
                        </p>
                    </div>

                </div>

                <button type="button" class="modal-close" id="closeEditAccount" aria-label="Tutup modal">

                    <i class="bx bx-x"></i>

                </button>

            </div>


            {{-- FORM EDIT --}}

            <form id="editAccountForm" method="POST" action="">

                @csrf
                @method('PUT')

                <input type="hidden" name="_form_context" value="edit-account">

                <input type="hidden" name="edit_account_id" id="edit_account_id">


                {{-- ROLE HIDDEN UNTUK AKUN SENDIRI --}}

                <input type="hidden" name="peran_id" id="edit_role_hidden" disabled>


                {{-- STATUS SELALU DIKIRIM MELALUI INPUT HIDDEN --}}

                <input type="hidden" name="status_aktif" id="edit_status_hidden" value="0">


                <div class="account-modal-body">

                    <div class="modal-form-grid">

                        {{-- NAMA --}}

                        <div class="form-group full-width">

                            <label class="form-label" for="edit_nama">
                                Nama Lengkap
                            </label>

                            <input type="text" name="nama" id="edit_nama" class="form-control" maxlength="255"
                                required>

                            @error('nama')
                                <small class="modal-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- USERNAME --}}

                        <div class="form-group">

                            <label class="form-label" for="edit_nama_pengguna">
                                Username
                            </label>

                            <input type="text" name="nama_pengguna" id="edit_nama_pengguna" class="form-control"
                                maxlength="255" required>

                            @error('nama_pengguna')
                                <small class="modal-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- ROLE --}}

                        <div class="form-group">

                            <label class="form-label" for="edit_peran_id">
                                Peran Pengguna
                            </label>

                            <select name="peran_id" id="edit_peran_id" class="form-control" required>

                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ ucfirst($role->nama_peran) }}
                                    </option>
                                @endforeach

                            </select>

                            @error('peran_id')
                                <small class="modal-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- PASSWORD BARU --}}

                        <div class="form-group">

                            <label class="form-label" for="edit_password">
                                Password Baru
                            </label>

                            <input type="password" name="password" id="edit_password" class="form-control"
                                minlength="8" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah">

                            <small class="modal-help">
                                Minimal 8 karakter jika ingin mengganti password.
                            </small>

                            @error('password')
                                <small class="modal-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- KONFIRMASI PASSWORD --}}

                        <div class="form-group">

                            <label class="form-label" for="edit_password_confirmation">

                                Konfirmasi Password Baru

                            </label>

                            <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                class="form-control" minlength="8" autocomplete="new-password"
                                placeholder="Ulangi password baru">

                        </div>


                        {{-- STATUS AKUN --}}

                        <div class="form-group full-width">

                            <label class="form-label">
                                Status Akun
                            </label>

                            <div class="modal-status">

                                <input type="checkbox" id="edit_status_checkbox" value="1">

                                <label for="edit_status_checkbox">
                                    Akun Aktif
                                </label>

                            </div>

                            <small class="modal-help">
                                Akun nonaktif tidak dapat digunakan untuk login.
                            </small>

                            @error('status_aktif')
                                <small class="modal-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- FOOTER MODAL --}}

                <div class="account-modal-footer">

                    <button type="button" class="btn btn-outline" id="cancelEditAccount">

                        Batal

                    </button>

                    <button type="submit" class="btn btn-primary">

                        <i class="bx bx-save"></i>
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection


{{-- =====================================
     JAVASCRIPT
===================================== --}}

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('editAccountModal');
            const form = document.getElementById('editAccountForm');

            if (!modal || !form) {
                return;
            }

            // Elemen form
            const accountId = document.getElementById('edit_account_id');

            const nama = document.getElementById('edit_nama');
            const username = document.getElementById('edit_nama_pengguna');

            const roleSelect = document.getElementById('edit_peran_id');
            const roleHidden = document.getElementById('edit_role_hidden');

            const statusCheckbox = document.getElementById('edit_status_checkbox');
            const statusHidden = document.getElementById('edit_status_hidden');

            const password = document.getElementById('edit_password');
            const passwordConfirmation = document.getElementById('edit_password_confirmation');

            // Template URL update akun
            const updateUrlTemplate = @json(route('mobil-cinta.admin.akun.update', ['account' => '__ACCOUNT__']));

            let lastTrigger = null;


            // =====================================
            // MEMBUKA MODAL
            // =====================================

            function openModal(data, trigger = null) {

                lastTrigger = trigger || lastTrigger;

                accountId.value = data.id;

                nama.value = data.nama;
                username.value = data.username;

                // Mengisi URL form sesuai akun yang dipilih
                form.action = updateUrlTemplate.replace(
                    '__ACCOUNT__',
                    encodeURIComponent(String(data.id))
                );

                const isSelf = data.isSelf === true;


                // ROLE
                roleSelect.value = data.roleId;

                roleSelect.disabled = isSelf;

                roleHidden.value = data.roleId;
                roleHidden.disabled = !isSelf;


                // STATUS
                statusCheckbox.checked = isSelf || String(data.status) === '1';

                statusCheckbox.disabled = isSelf;

                // Nilai hidden harus selalu mengikuti checkbox
                statusHidden.value = statusCheckbox.checked ? '1' : '0';


                // PASSWORD SELALU KOSONG SAAT MODAL DIBUKA
                password.value = '';
                passwordConfirmation.value = '';


                // Tampilkan modal
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');

                document.body.style.overflow = 'hidden';

                setTimeout(function() {
                    nama.focus();
                }, 100);

            }


            // =====================================
            // MENUTUP MODAL
            // =====================================

            function closeModal() {

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                document.body.style.overflow = '';

                if (lastTrigger) {
                    lastTrigger.focus();
                }

            }


            // =====================================
            // EVENT TOMBOL EDIT
            // =====================================

            document.querySelectorAll('.js-edit-account').forEach(function(button) {

                button.addEventListener('click', function() {

                    openModal({

                        id: this.dataset.accountId,

                        nama: this.dataset.name,

                        username: this.dataset.username,

                        roleId: this.dataset.roleId,

                        status: this.dataset.status,

                        isSelf: this.dataset.isSelf === '1'

                    }, this);

                });

            });


            // =====================================
            // UPDATE STATUS CHECKBOX
            // =====================================

            statusCheckbox.addEventListener('change', function() {

                statusHidden.value = this.checked ? '1' : '0';

            });


            // =====================================
            // TOMBOL CLOSE DAN BATAL
            // =====================================

            document.getElementById('closeEditAccount')
                .addEventListener('click', closeModal);

            document.getElementById('cancelEditAccount')
                .addEventListener('click', closeModal);


            // =====================================
            // KLIK AREA DI LUAR MODAL
            // =====================================

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {
                    closeModal();
                }

            });


            // =====================================
            // TOMBOL ESCAPE
            // =====================================

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {
                    closeModal();
                }

            });


            // =====================================
            // MEMBUKA KEMBALI MODAL JIKA
            // VALIDASI UPDATE GAGAL
            // =====================================

            const oldContext = @json(old('_form_context'));

            const oldId = @json(old('edit_account_id'));

            if (oldContext === 'edit-account' && oldId) {

                const trigger = Array.from(
                    document.querySelectorAll('.js-edit-account')
                ).find(function(button) {

                    return button.dataset.accountId === String(oldId);

                });


                if (trigger) {

                    const isSelf = trigger.dataset.isSelf === '1';

                    // Buka modal melalui tombol edit yang sesuai
                    trigger.click();


                    // Isi kembali data yang sebelumnya dikirim
                    nama.value = @json(old('nama', ''));

                    username.value = @json(old('nama_pengguna', ''));


                    // Pulihkan role jika bukan akun sendiri
                    const oldRole = @json(old('peran_id'));

                    if (!isSelf && oldRole !== null) {
                        roleSelect.value = oldRole;
                    }


                    // Pulihkan status jika bukan akun sendiri
                    if (!isSelf) {

                        const oldStatus = @json((string) old('status_aktif', '0') === '1');

                        statusCheckbox.checked = oldStatus;

                        statusHidden.value = statusCheckbox.checked ? '1' : '0';

                    }

                    // Password sengaja tidak dipulihkan
                    password.value = '';
                    passwordConfirmation.value = '';

                }

            }

        });

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('accountSearchInput');
            const dropdown = document.getElementById('accountSuggestions');

            if (!input || !dropdown) return;

            const suggestionUrl = @json(route('mobil-cinta.admin.akun.suggest'));

            let searchTimer = null;
            let requestNumber = 0;

            function hideSuggestions() {
                dropdown.hidden = true;
                dropdown.innerHTML = '';
            }

            function showEmptyMessage() {
                dropdown.innerHTML = '';

                const empty = document.createElement('div');
                empty.className = 'account-suggestion-empty';
                empty.textContent = 'Tidak ditemukan akun yang cocok.';

                dropdown.appendChild(empty);
                dropdown.hidden = false;
            }

            function renderSuggestions(accounts) {
                dropdown.innerHTML = '';

                if (!accounts.length) {
                    showEmptyMessage();
                    return;
                }

                accounts.forEach(function(account) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'account-suggestion-item';

                    const name = document.createElement('span');
                    name.className = 'account-suggestion-name';
                    name.textContent = account.nama;

                    const username = document.createElement('span');
                    username.className = 'account-suggestion-username';
                    username.textContent = '@' + account.nama_pengguna;

                    button.appendChild(name);
                    button.appendChild(username);

                    button.addEventListener('click', function() {
                        // Username bersifat unik sehingga hasil pencarian lebih spesifik.
                        input.value = account.nama_pengguna;

                        hideSuggestions();

                        if (input.form) {
                            input.form.requestSubmit();
                        }
                    });

                    dropdown.appendChild(button);
                });

                dropdown.hidden = false;
            }

            input.addEventListener('input', function() {
                clearTimeout(searchTimer);

                const keyword = input.value.trim();
                const currentRequest = ++requestNumber;

                if (!keyword) {
                    hideSuggestions();
                    return;
                }

                searchTimer = setTimeout(async function() {
                    try {
                        const response = await fetch(
                            suggestionUrl + '?q=' + encodeURIComponent(keyword), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                        if (!response.ok) {
                            throw new Error('Gagal mengambil saran akun.');
                        }

                        const accounts = await response.json();

                        // Abaikan respons lama jika user sudah mengetik lagi.
                        if (currentRequest !== requestNumber) return;

                        renderSuggestions(accounts);

                    } catch (error) {
                        console.error(error);
                        hideSuggestions();
                    }
                }, 250);
            });

            // Tutup dropdown ketika klik di luar kolom pencarian.
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.account-search')) {
                    hideSuggestions();
                }
            });

            // Tutup dropdown dengan tombol Escape.
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideSuggestions();
                }
            });
        });
    </script>
@endpush
