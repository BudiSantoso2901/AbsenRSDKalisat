@extends('mobil-cinta.layouts.app')

@section('title', 'Master Kendaraan')

@section('content')

<style>
    .vehicle-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 23px;
    }

    .vehicle-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .vehicle-summary-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #fff;
        border: 1px solid #dceee2;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(8, 100, 55, .04);
    }

    .vehicle-summary-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #eaf8f0;
        color: #078b48;
        font-size: 23px;
    }

    .vehicle-summary-card small {
        display: block;
        color: #718078;
        font-size: 11px;
        font-weight: 700;
    }

    .vehicle-summary-card strong {
        display: block;
        color: #18352a;
        font-size: 23px;
        font-weight: 800;
        line-height: 1.3;
    }

    .vehicle-filter-grid {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) auto;
        align-items: end;
        gap: 12px;
    }

    .vehicle-filter-grid .form-group {
        margin: 0;
    }

    .vehicle-plate {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 118px;
        padding: 7px 13px;
        border: 1px solid #d8e4db;
        border-radius: 8px;
        background: #fbfdfb;
        color: #18352a;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .vehicle-count {
        color: #68786e;
        font-size: 12px;
    }

    .vehicle-action-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .vehicle-btn-sm {
        padding: 7px 10px;
        font-size: 11px;
    }

    .vehicle-delete-disabled {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #87938b;
        font-size: 11px;
        font-weight: 700;
    }

    .vehicle-empty-state {
        padding: 38px 15px;
        text-align: center;
        color: #718078;
    }

    .vehicle-empty-state i {
        display: block;
        margin-bottom: 8px;
        color: #08a653;
        font-size: 38px;
    }

    /* MODAL */

    .vehicle-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 2100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(20, 35, 24, .58);
        backdrop-filter: blur(3px);
    }

    .vehicle-modal-overlay.is-open {
        display: flex;
    }

    .vehicle-modal {
        width: 100%;
        max-width: 480px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dceee2;
        border-radius: 17px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .2);
        animation: vehicleModalFade .2s ease;
    }

    @keyframes vehicleModalFade {
        from {
            opacity: 0;
            transform: translateY(10px) scale(.985);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .vehicle-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px 23px;
        border-bottom: 1px solid #e7efe9;
    }

    .vehicle-modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .vehicle-modal-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #eaf8f0;
        color: #078b48;
        font-size: 21px;
    }

    .vehicle-modal-title {
        margin: 0;
        color: #18352a;
        font-size: 17px;
        font-weight: 800;
    }

    .vehicle-modal-subtitle {
        margin: 2px 0 0;
        color: #718078;
        font-size: 11px;
    }

    .vehicle-modal-close {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dce9df;
        border-radius: 9px;
        background: #fff;
        color: #62756a;
        font-size: 20px;
        cursor: pointer;
        transition: .2s ease;
    }

    .vehicle-modal-close:hover {
        background: #eaf8f0;
        color: #078b48;
        border-color: #08a653;
    }

    .vehicle-modal-body {
        padding: 23px;
    }

    .vehicle-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 16px 23px;
        border-top: 1px solid #e7efe9;
        background: #fbfdfb;
    }

    .vehicle-modal-error {
        display: block;
        margin-top: 6px;
        color: #dc3545;
        font-size: 11px;
    }

    .vehicle-modal-help {
        display: block;
        margin-top: 6px;
        color: #718078;
        font-size: 11px;
    }

    @media (max-width: 760px) {
        .vehicle-summary {
            grid-template-columns: 1fr 1fr;
        }

        .vehicle-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .vehicle-summary {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .vehicle-summary-card {
            padding: 14px;
        }

        .vehicle-modal-overlay {
            padding: 10px;
        }

        .vehicle-modal {
            max-height: calc(100vh - 20px);
        }

        .vehicle-modal-header,
        .vehicle-modal-body {
            padding: 17px;
        }

        .vehicle-modal-footer {
            padding: 14px 17px;
        }
    }
</style>

{{-- HEADER HALAMAN --}}

<div class="vehicle-page-heading">
    <div>
        <span class="role">
            <i class="bx bx-car"></i>
            ADMINISTRASI
        </span>

        <h1 style="margin-top:10px;">Master Kendaraan</h1>

        <p class="page-subtitle">
            Pengelolaan nomor polisi kendaraan operasional Mobil Cinta RSD Kalisat.
        </p>
    </div>

    <button
        type="button"
        class="btn btn-primary"
        id="openCreateVehicle"
    >
        <i class="bx bx-plus-circle"></i>
        Tambah Kendaraan
    </button>
</div>

{{-- RINGKASAN KENDARAAN --}}

<div class="vehicle-summary">

    <div class="vehicle-summary-card">
        <div class="vehicle-summary-icon">
            <i class="bx bx-car"></i>
        </div>

        <div>
            <small>Total Kendaraan</small>
            <strong>
                {{ number_format($totalVehicles, 0, ',', '.') }}
            </strong>
        </div>
    </div>

    <div class="vehicle-summary-card">
        <div class="vehicle-summary-icon">
            <i class="bx bx-history"></i>
        </div>

        <div>
            <small>Pernah Digunakan</small>
            <strong>
                {{ number_format($usedVehicles, 0, ',', '.') }}
            </strong>
        </div>
    </div>

    <div class="vehicle-summary-card">
        <div class="vehicle-summary-icon">
            <i class="bx bx-check-shield"></i>
        </div>

        <div>
            <small>Belum Pernah Digunakan</small>
            <strong>
                {{ number_format($neverUsedVehicles, 0, ',', '.') }}
            </strong>
        </div>
    </div>

</div>

{{-- DAFTAR KENDARAAN --}}

<section class="card card-accent">

    <h2 class="section-heading" style="margin-top:0;">
        <i class="bx bx-list-ul" style="color:#08A653;"></i>
        Daftar Kendaraan
    </h2>

    {{-- PENCARIAN --}}

    <form
        method="GET"
        action="{{ route('mobil-cinta.admin.kendaraan.index') }}"
    >
        <div class="vehicle-filter-grid">

            <div class="form-group">
                <label class="form-label" for="vehicleSearch">
                    Cari Nomor Polisi
                </label>

                <input
                    type="text"
                    id="vehicleSearch"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Contoh: P 1234 ABC"
                    autocomplete="off"
                >
            </div>

            <div class="vehicle-action-group">

                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i>
                    Cari
                </button>

                <a
                    href="{{ route('mobil-cinta.admin.kendaraan.index') }}"
                    class="btn btn-outline"
                >
                    Reset
                </a>

            </div>

        </div>
    </form>

    {{-- TABEL KENDARAAN --}}

    <div class="table-wrap">

        <table>

            <thead>
                <tr>
                    <th width="60">No.</th>
                    <th>Nomor Polisi</th>
                    <th>Riwayat Perjalanan</th>
                    <th width="210">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($vehicles as $vehicle)

                    <tr>

                        <td>
                            {{ $vehicles->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <span class="vehicle-plate">
                                {{ $vehicle->nomor_polisi }}
                            </span>
                        </td>

                        <td>
                            <span class="vehicle-count">
                                {{ number_format($vehicle->journeys_count, 0, ',', '.') }}
                                perjalanan
                            </span>
                        </td>

                        <td>

                            <div class="vehicle-action-group">

                                {{-- EDIT --}}

                                <button
                                    type="button"
                                    class="btn btn-outline vehicle-btn-sm js-edit-vehicle"
                                    data-id="{{ $vehicle->id }}"
                                    data-plate="{{ $vehicle->nomor_polisi }}"
                                >
                                    <i class="bx bx-edit"></i>
                                    Edit
                                </button>

                                {{-- HAPUS --}}

                                @if ((int) $vehicle->journeys_count === 0)

                                    <form
                                        method="POST"
                                        action="{{ route('mobil-cinta.admin.kendaraan.destroy', $vehicle) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus kendaraan {{ $vehicle->nomor_polisi }}?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger vehicle-btn-sm"
                                        >
                                            <i class="bx bx-trash"></i>
                                            Hapus
                                        </button>
                                    </form>

                                @else

                                    <span
                                        class="vehicle-delete-disabled"
                                        title="Kendaraan sudah memiliki riwayat perjalanan"
                                    >
                                        <i class="bx bx-lock-alt"></i>
                                        Terkunci
                                    </span>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="4">
                            <div class="vehicle-empty-state">
                                <i class="bx bx-car"></i>

                                <strong>
                                    Data kendaraan tidak ditemukan
                                </strong>

                                <p>
                                    Tambahkan kendaraan baru atau ubah kata kunci pencarian.
                                </p>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}

    <div class="pagination">
        {{ $vehicles->links() }}
    </div>

    <p class="page-subtitle" style="margin-top:12px;">
        Menampilkan {{ $vehicles->count() }}
        dari {{ $vehicles->total() }} kendaraan.
    </p>

</section>

{{-- MODAL TAMBAH DAN EDIT KENDARAAN --}}

<div
    class="vehicle-modal-overlay"
    id="vehicleModal"
    aria-hidden="true"
>
    <div
        class="vehicle-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="vehicleModalTitle"
    >

        {{-- HEADER MODAL --}}

        <div class="vehicle-modal-header">

            <div class="vehicle-modal-title-wrap">

                <div class="vehicle-modal-icon">
                    <i class="bx bx-car"></i>
                </div>

                <div>
                    <h2 class="vehicle-modal-title" id="vehicleModalTitle">
                        Tambah Kendaraan
                    </h2>

                    <p
                        class="vehicle-modal-subtitle"
                        id="vehicleModalSubtitle"
                    >
                        Masukkan nomor polisi kendaraan.
                    </p>
                </div>

            </div>

            <button
                type="button"
                class="vehicle-modal-close"
                id="closeVehicleModal"
                aria-label="Tutup modal"
            >
                <i class="bx bx-x"></i>
            </button>

        </div>

        {{-- FORM TAMBAH / EDIT --}}

        <form
            id="vehicleForm"
            method="POST"
            action="{{ route('mobil-cinta.admin.kendaraan.store') }}"
        >
            @csrf

            <input
                type="hidden"
                name="_method"
                id="vehicleMethod"
                value="PUT"
                disabled
            >

            <input
                type="hidden"
                name="_form_context"
                id="vehicleFormContext"
                value="vehicle-create"
            >

            <input
                type="hidden"
                name="vehicle_id"
                id="vehicleId"
                value=""
            >

            <div class="vehicle-modal-body">

                <div class="form-group" style="margin-bottom:0;">

                    <label
                        class="form-label"
                        for="vehiclePlateInput"
                    >
                        Nomor Polisi
                    </label>

                    <input
                        type="text"
                        name="nomor_polisi"
                        id="vehiclePlateInput"
                        maxlength="20"
                        placeholder="Contoh: P 1234 ABC"
                        autocomplete="off"
                        style="text-transform:uppercase;"
                        required
                    >

                    <small class="vehicle-modal-help">
                        Nomor polisi otomatis disimpan dalam huruf kapital.
                    </small>

                    @error('nomor_polisi')
                        <small class="vehicle-modal-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

            <div class="vehicle-modal-footer">

                <button
                    type="button"
                    class="btn btn-outline"
                    id="cancelVehicleModal"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    id="saveVehicleButton"
                >
                    <i class="bx bx-save"></i>
                    Simpan Kendaraan
                </button>

            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('vehicleModal');
    const form = document.getElementById('vehicleForm');
    const input = document.getElementById('vehiclePlateInput');

    const methodInput = document.getElementById('vehicleMethod');
    const contextInput = document.getElementById('vehicleFormContext');
    const vehicleIdInput = document.getElementById('vehicleId');

    const title = document.getElementById('vehicleModalTitle');
    const subtitle = document.getElementById('vehicleModalSubtitle');
    const saveButton = document.getElementById('saveVehicleButton');

    if (!modal || !form || !input) {
        return;
    }

    const storeUrl = @json(route('mobil-cinta.admin.kendaraan.store'));

    const updateUrlTemplate = @json(
        route('mobil-cinta.admin.kendaraan.update', ['vehicle' => '__VEHICLE__'])
    );

    let lastTrigger = null;

    /**
     * Buka modal tambah atau edit.
     */
    function openModal(mode, data = {}, trigger = null) {

        lastTrigger = trigger || lastTrigger;

        if (mode === 'edit') {

            title.textContent = 'Edit Kendaraan';

            subtitle.textContent = 'Perbarui nomor polisi kendaraan.';

            saveButton.innerHTML =
                '<i class="bx bx-save"></i> Simpan Perubahan';

            form.action = updateUrlTemplate.replace(
                '__VEHICLE__',
                encodeURIComponent(String(data.id))
            );

            methodInput.disabled = false;
            methodInput.value = 'PUT';

            contextInput.value = 'vehicle-edit';

            vehicleIdInput.value = data.id;

            input.value = data.plate || '';

        } else {

            title.textContent = 'Tambah Kendaraan';

            subtitle.textContent = 'Masukkan nomor polisi kendaraan.';

            saveButton.innerHTML =
                '<i class="bx bx-save"></i> Simpan Kendaraan';

            form.action = storeUrl;

            methodInput.disabled = true;
            methodInput.value = 'PUT';

            contextInput.value = 'vehicle-create';

            vehicleIdInput.value = '';

            input.value = '';
        }

        modal.classList.add('is-open');

        modal.setAttribute('aria-hidden', 'false');

        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            input.focus();
        }, 80);
    }

    /**
     * Tutup modal.
     */
    function closeModal() {

        modal.classList.remove('is-open');

        modal.setAttribute('aria-hidden', 'true');

        document.body.style.overflow = '';

        if (lastTrigger) {
            lastTrigger.focus();
        }
    }

    /**
     * Tombol tambah kendaraan.
     */
    document
        .getElementById('openCreateVehicle')
        ?.addEventListener('click', function () {
            openModal('create', {}, this);
        });

    /**
     * Tombol edit kendaraan.
     */
    document.querySelectorAll('.js-edit-vehicle')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                openModal('edit', {
                    id: this.dataset.id,
                    plate: this.dataset.plate
                }, this);

            });

        });

    /**
     * Tombol tutup dan batal.
     */
    document
        .getElementById('closeVehicleModal')
        ?.addEventListener('click', closeModal);

    document
        .getElementById('cancelVehicleModal')
        ?.addEventListener('click', closeModal);

    /**
     * Klik di luar modal.
     */
    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            closeModal();
        }

    });

    /**
     * Tombol Escape.
     */
    document.addEventListener('keydown', function (event) {

        if (
            event.key === 'Escape' &&
            modal.classList.contains('is-open')
        ) {
            closeModal();
        }

    });

    /**
     * Otomatis ubah input menjadi huruf kapital.
     */
    input.addEventListener('input', function () {

        const cursor = this.selectionStart;

        this.value = this.value.toUpperCase();

        this.setSelectionRange(cursor, cursor);

    });

    /**
     * Buka kembali modal jika validasi server gagal.
     */
    const oldContext = @json(old('_form_context'));

    const oldVehicleId = @json(old('vehicle_id'));

    const oldPlate = @json(old('nomor_polisi', ''));

    if (oldContext === 'vehicle-create') {

        openModal('create');

        input.value = oldPlate;

    } else if (
        oldContext === 'vehicle-edit' &&
        oldVehicleId
    ) {

        const editButton = Array.from(
            document.querySelectorAll('.js-edit-vehicle')
        ).find(function (button) {

            return button.dataset.id === String(oldVehicleId);

        });

        if (editButton) {

            openModal('edit', {
                id: oldVehicleId,
                plate: oldPlate || editButton.dataset.plate
            }, editButton);

        }
    }

});
</script>

@endpush