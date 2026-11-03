<div class="coordinator-assignment-modal" id="coordinatorAssignmentModal" hidden>

    <div class="coordinator-assignment-backdrop" data-close-coordinator-assignment></div>

    <div class="coordinator-assignment-dialog">

        <div class="coordinator-assignment-header">
            <div>
                <span class="assignment-label">
                    <i class="bx bx-car"></i>
                    MOBIL CINTA
                </span>

                <h2 id="coordinatorAssignmentTitle">Penugasan Perjalanan</h2>

                <p>Pilih Sopir dan kendaraan untuk perjalanan pasien.</p>
            </div>

            <button
                type="button"
                class="coordinator-assignment-close"
                data-close-coordinator-assignment
            >
                <i class="bx bx-x"></i>
            </button>
        </div>


        <form
            id="coordinatorAssignmentForm"
            method="POST"
            action=""
            data-swal-title="Konfirmasi Penugasan"
            data-swal-text="Pastikan Sopir dan kendaraan sudah benar."
            data-swal-confirm-text="Ya, simpan penugasan"
            data-swal-cancel-text="Periksa kembali"
        >
            @csrf

            <div class="coordinator-assignment-body">

                <div class="coordinator-assignment-number" id="coordinatorAssignmentNumber">
                    Nomor Perjalanan
                </div>

                <div class="coordinator-assignment-field">
                    <label for="coordinatorAssignmentDriver">
                        Pilih Sopir <span>*</span>
                    </label>

                    <select
                        name="sopir_id"
                        id="coordinatorAssignmentDriver"
                        required
                    >
                        <option value="">Pilih sopir</option>

                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">
                                {{ $driver->nama }}
                            </option>
                        @endforeach

                    </select>
                </div>


                <div class="coordinator-assignment-field">
                    <label for="coordinatorAssignmentVehicle">
                        Pilih Kendaraan <span>*</span>
                    </label>

                    <select
                        name="kendaraan_id"
                        id="coordinatorAssignmentVehicle"
                        required
                    >
                        <option value="">Pilih nomor polisi kendaraan</option>

                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->nomor_polisi }}
                            </option>
                        @endforeach

                    </select>
                </div>


                <div class="coordinator-assignment-note">
                    <i class="bx bx-info-circle"></i>
                    Perubahan penugasan akan memperbarui Sopir dan kendaraan pada perjalanan ini.
                </div>

            </div>


            <div class="coordinator-assignment-footer">

                <button
                    type="button"
                    class="coordinator-assignment-cancel"
                    data-close-coordinator-assignment
                >
                    Batal
                </button>

                <button type="submit" class="coordinator-assignment-save">
                    <i class="bx bx-save"></i>
                    Simpan Penugasan
                </button>

            </div>

        </form>

    </div>

</div>


<style>
    .coordinator-assignment-modal[hidden] {
        display: none !important;
    }

    .coordinator-assignment-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .coordinator-assignment-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(17, 37, 25, .55);
        backdrop-filter: blur(3px);
    }

    .coordinator-assignment-dialog {
        position: relative;
        width: 100%;
        max-width: 510px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e5eee7;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        animation: coordinatorAssignmentIn .22s ease;
    }

    @keyframes coordinatorAssignmentIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .coordinator-assignment-header {
        flex: 0 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        padding: 20px;
        border-bottom: 1px solid #edf2ee;
    }

    .coordinator-assignment-header h2 {
        margin: 0 0 5px;
        color: #174b32;
        font-size: 17px;
        font-weight: 800;
    }

    .coordinator-assignment-header p {
        margin: 0;
        color: #87958b;
        font-size: 11px;
    }

    .assignment-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
        color: #078b48;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .coordinator-assignment-close {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #e5eee7;
        border-radius: 8px;
        background: #fff;
        color: #718078;
        font-size: 21px;
        cursor: pointer;
    }

    .coordinator-assignment-close:hover {
        background: #fff1f0;
        color: #b54848;
    }

    #coordinatorAssignmentForm {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .coordinator-assignment-body {
        padding: 20px;
        overflow-y: auto;
    }

    .coordinator-assignment-number {
        margin-bottom: 19px;
        padding: 10px;
        border-radius: 8px;
        background: #eaf8ef;
        color: #078b48;
        font-size: 11px;
        font-weight: 800;
    }

    .coordinator-assignment-field {
        margin-bottom: 17px;
    }

    .coordinator-assignment-field label {
        display: block;
        margin-bottom: 7px;
        color: #52665a;
        font-size: 11px;
        font-weight: 750;
    }

    .coordinator-assignment-field label span {
        color: #d64b4b;
    }

    .coordinator-assignment-field select {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #dce8df;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #354b3d;
        font-family: inherit;
        font-size: 12px;
    }

    .coordinator-assignment-field select:focus {
        border-color: #08a653;
        box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
    }

    .coordinator-assignment-note {
        display: flex;
        gap: 7px;
        padding: 11px;
        border: 1px solid #dcefe1;
        border-radius: 8px;
        background: #f1faf4;
        color: #587461;
        font-size: 10px;
        line-height: 1.6;
    }

    .coordinator-assignment-note i {
        flex-shrink: 0;
        color: #078b48;
        font-size: 15px;
    }

    .coordinator-assignment-footer {
        flex: 0 0 auto;
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 14px 20px;
        border-top: 1px solid #edf2ee;
        background: #fbfdfb;
    }

    .coordinator-assignment-cancel,
    .coordinator-assignment-save {
        min-height: 39px;
        padding: 0 15px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        cursor: pointer;
    }

    .coordinator-assignment-cancel {
        border: 1px solid #dce8df;
        background: #fff;
        color: #617268;
    }

    .coordinator-assignment-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #08a653;
        background: #08a653;
        color: #fff;
    }

    .coordinator-assignment-save:hover {
        background: #078b48;
    }

    @media(max-width: 600px) {
        .coordinator-assignment-modal {
            padding: 10px;
        }

        .coordinator-assignment-dialog {
            max-height: 94vh;
        }

        .coordinator-assignment-footer {
            flex-direction: column-reverse;
        }

        .coordinator-assignment-footer button {
            width: 100%;
        }
    }
</style>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('coordinatorAssignmentModal');
    const form = document.getElementById('coordinatorAssignmentForm');

    if (!modal || !form) return;

    const driverSelect = document.getElementById('coordinatorAssignmentDriver');
    const vehicleSelect = document.getElementById('coordinatorAssignmentVehicle');
    const numberLabel = document.getElementById('coordinatorAssignmentNumber');
    const title = document.getElementById('coordinatorAssignmentTitle');

    function openAssignment(button) {

        const url = button.dataset.assignmentUrl;

        if (!url) {
            Swal.fire({
                icon: 'error',
                title: 'URL penugasan tidak tersedia',
                confirmButtonColor: '#08A653'
            });
            return;
        }

        const target = new URL(url, window.location.origin);

        if (
            target.origin !== window.location.origin ||
            !target.pathname.includes('/koordinator/perjalanan/')
        ) {
            Swal.fire({
                icon: 'error',
                title: 'URL penugasan tidak valid',
                confirmButtonColor: '#08A653'
            });
            return;
        }

        form.action = target.href;

        driverSelect.value = button.dataset.driverId || '';
        vehicleSelect.value = button.dataset.vehicleId || '';

        numberLabel.textContent =
            button.dataset.number + ' · ' + button.dataset.patient;

        title.textContent =
            button.dataset.driverId
                ? 'Edit Penugasan Perjalanan'
                : 'Penugasan Perjalanan';

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeAssignment() {
        modal.hidden = true;
        document.body.style.overflow = '';
        form.action = '';
    }

    document.addEventListener('click', function (event) {

        const button = event.target.closest('[data-open-coordinator-assignment]');

        if (button) {
            event.preventDefault();
            openAssignment(button);
            return;
        }

        if (event.target.closest('[data-close-coordinator-assignment]')) {
            closeAssignment();
        }

    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeAssignment();
        }
    });

});
</script>
@endpush