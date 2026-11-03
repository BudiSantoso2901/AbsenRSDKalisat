
<div class="journey-edit-modal" id="journeyEditModal" hidden>

    <div class="journey-edit-backdrop" data-close-edit></div>

    <div class="journey-edit-dialog" role="dialog" aria-modal="true" aria-labelledby="journeyEditTitle">

        <div class="journey-edit-header">
            <div>
                <span class="journey-edit-label">
                    <i class="bx bx-edit"></i> MOBIL CINTA
                </span>

                <h2 id="journeyEditTitle">Edit Pengajuan Perjalanan</h2>
                <p>Perbarui informasi pengajuan pasien.</p>
            </div>

            <button type="button" class="journey-edit-close" data-close-edit>
                <i class="bx bx-x"></i>
            </button>
        </div>


        <form
            id="journeyEditForm"
            method="POST"
            action=""
            data-swal-confirm="false"
        >
            @csrf
            @method('PUT')

            <div class="journey-edit-body">

                <div class="journey-edit-number" id="journeyEditNumber">
                    Nomor Perjalanan
                </div>

                <div class="journey-edit-section">
                    <i class="bx bx-user"></i>
                    Identitas Pasien
                </div>

                <div class="journey-edit-grid">

                    <div class="journey-edit-field">
                        <label for="edit_ruangan_id">Ruangan <span>*</span></label>

                        <select name="ruangan_id" id="edit_ruangan_id" required>
                            <option value="">Pilih ruangan</option>

                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">
                                    {{ $room->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="journey-edit-field">
                        <label for="edit_nama_pasien">Nama Pasien <span>*</span></label>

                        <input
                            type="text"
                            name="nama_pasien"
                            id="edit_nama_pasien"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="journey-edit-field">
                        <label for="edit_nomor_rekam_medis">
                            Nomor Rekam Medis <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="nomor_rekam_medis"
                            id="edit_nomor_rekam_medis"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="journey-edit-field">
                        <label for="edit_jenis_kelamin">
                            Jenis Kelamin <span>*</span>
                        </label>

                        <select
                            name="jenis_kelamin_pasien"
                            id="edit_jenis_kelamin"
                            required
                        >
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                </div>


                <div class="journey-edit-divider"></div>


                <div class="journey-edit-section">
                    <i class="bx bx-map"></i>
                    Informasi Perjalanan
                </div>

                <div class="journey-edit-grid">

                    <div class="journey-edit-field">
                        <label for="edit_penjamin">Penjamin <span>*</span></label>

                        <select name="penjamin" id="edit_penjamin" required>
                            <option value="">Pilih penjamin</option>
                            <option value="UHC">UHC</option>
                            <option value="PBI">PBI</option>
                            <option value="NON PBI">NON PBI</option>
                        </select>
                    </div>

                    <div class="journey-edit-field">
                        <label for="edit_tujuan">Tujuan Antar <span>*</span></label>

                        <input
                            type="text"
                            name="tujuan"
                            id="edit_tujuan"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="journey-edit-field journey-edit-full">
                        <label for="edit_catatan">Catatan Pengajuan <span>*</span></label>

                        <textarea
                            name="catatan"
                            id="edit_catatan"
                            rows="4"
                            maxlength="5000"
                            required
                        ></textarea>
                    </div>

                </div>


                <div class="journey-edit-note">
                    <i class="bx bx-info-circle"></i>
                    <span>
                        Nomor perjalanan dan identitas pengaju tidak dapat diubah.
                    </span>
                </div>

            </div>


            <div class="journey-edit-footer">

                <button
                    type="button"
                    class="journey-edit-cancel"
                    data-close-edit
                >
                    Batal
                </button>

                <button type="submit" class="journey-edit-save">
                    <i class="bx bx-save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>


<style>
    .journey-edit-modal[hidden] {
        display: none !important;
    }

    .journey-edit-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .journey-edit-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(17, 37, 25, .55);
        backdrop-filter: blur(3px);
    }

    .journey-edit-dialog {
        position: relative;
        width: 100%;
        max-width: 750px;
        max-height: 90vh;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e5eee7;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        animation: journeyModalIn .22s ease;
    }

    @keyframes journeyModalIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .journey-edit-header {
        flex: 0 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        padding: 20px 23px;
        border-bottom: 1px solid #edf2ee;
        background: #fff;
    }

    .journey-edit-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
        color: #078b48;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .journey-edit-header h2 {
        margin: 0 0 5px;
        color: #174b32;
        font-size: 17px;
        font-weight: 800;
    }

    .journey-edit-header p {
        margin: 0;
        color: #87958b;
        font-size: 11px;
    }

    .journey-edit-close {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5eee7;
        border-radius: 8px;
        background: #fff;
        color: #718078;
        font-size: 21px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .journey-edit-close:hover {
        background: #fff1f0;
        color: #b54848;
    }

    #journeyEditForm {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .journey-edit-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        padding: 20px 23px;
        scrollbar-width: thin;
        scrollbar-color: #a8cdb5 #f1f8f3;
    }

    .journey-edit-body::-webkit-scrollbar {
        width: 6px;
    }

    .journey-edit-body::-webkit-scrollbar-track {
        background: #f1f8f3;
    }

    .journey-edit-body::-webkit-scrollbar-thumb {
        background: #a8cdb5;
        border-radius: 10px;
    }

    .journey-edit-number {
        display: inline-block;
        margin-bottom: 20px;
        padding: 7px 10px;
        border-radius: 7px;
        background: #eaf8ef;
        color: #078b48;
        font-size: 11px;
        font-weight: 800;
    }

    .journey-edit-section {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 14px;
        color: #078b48;
        font-size: 12px;
        font-weight: 800;
    }

    .journey-edit-section i {
        font-size: 17px;
    }

    .journey-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .journey-edit-field {
        min-width: 0;
    }

    .journey-edit-field label {
        display: block;
        margin-bottom: 7px;
        color: #52665a;
        font-size: 11px;
        font-weight: 750;
    }

    .journey-edit-field label span {
        color: #d64b4b;
    }

    .journey-edit-field input,
    .journey-edit-field select,
    .journey-edit-field textarea {
        width: 100%;
        min-height: 41px;
        padding: 10px 12px;
        border: 1px solid #dce8df;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #354b3d;
        font-family: inherit;
        font-size: 12px;
        transition: .2s;
    }

    .journey-edit-field textarea {
        min-height: 90px;
        resize: vertical;
        line-height: 1.6;
    }

    .journey-edit-field input:focus,
    .journey-edit-field select:focus,
    .journey-edit-field textarea:focus {
        border-color: #08a653;
        box-shadow: 0 0 0 3px rgba(8, 166, 83, .09);
    }

    .journey-edit-full {
        grid-column: 1 / -1;
    }

    .journey-edit-divider {
        height: 1px;
        margin: 21px 0;
        background: #edf2ee;
    }

    .journey-edit-note {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 17px;
        padding: 11px;
        border: 1px solid #dcefe1;
        border-radius: 8px;
        background: #f1faf4;
        color: #587461;
        font-size: 10px;
        line-height: 1.5;
    }

    .journey-edit-note i {
        flex-shrink: 0;
        color: #078b48;
        font-size: 15px;
    }

    .journey-edit-footer {
        flex: 0 0 auto;
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 15px 23px;
        border-top: 1px solid #edf2ee;
        background: #fbfdfb;
    }

    .journey-edit-cancel,
    .journey-edit-save {
        min-height: 39px;
        padding: 0 16px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 750;
        cursor: pointer;
    }

    .journey-edit-cancel {
        border: 1px solid #dce8df;
        background: #fff;
        color: #617268;
    }

    .journey-edit-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #08a653;
        background: #08a653;
        color: #fff;
    }

    .journey-edit-save:hover {
        background: #078b48;
        border-color: #078b48;
    }

    @media (max-width: 600px) {
        .journey-edit-modal {
            padding: 10px;
        }

        .journey-edit-dialog {
            max-height: 94vh;
        }

        .journey-edit-header,
        .journey-edit-body {
            padding: 16px;
        }

        .journey-edit-grid {
            grid-template-columns: 1fr;
        }

        .journey-edit-full {
            grid-column: auto;
        }

        .journey-edit-footer {
            padding: 12px 16px;
        }

        .journey-edit-cancel,
        .journey-edit-save {
            flex: 1;
        }
    }
</style>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('journeyEditModal');
    const form = document.getElementById('journeyEditForm');

    if (!modal || !form) return;

    const fields = {
        ruangan: document.getElementById('edit_ruangan_id'),
        nama: document.getElementById('edit_nama_pasien'),
        rm: document.getElementById('edit_nomor_rekam_medis'),
        gender: document.getElementById('edit_jenis_kelamin'),
        penjamin: document.getElementById('edit_penjamin'),
        tujuan: document.getElementById('edit_tujuan'),
        catatan: document.getElementById('edit_catatan'),
        nomor: document.getElementById('journeyEditNumber')
    };

    let submitting = false;

    function openModal(button) {

        const updateUrl = button.getAttribute('data-update-url');

        if (!updateUrl || updateUrl.trim() === '' || updateUrl.trim() === '#') {
            Swal.fire({
                icon: 'error',
                title: 'URL pembaruan tidak tersedia',
                text: 'Periksa atribut data-update-url pada tombol Edit.',
                confirmButtonColor: '#08A653'
            });

            return;
        }

        // Pastikan URL tujuan adalah route update perjalanan.
        const targetUrl = new URL(updateUrl, window.location.origin);

        if (
            targetUrl.origin !== window.location.origin ||
            !targetUrl.pathname.includes('/perawat/perjalanan/')
        ) {
            Swal.fire({
                icon: 'error',
                title: 'URL pembaruan tidak valid',
                text: 'Form tidak diarahkan ke alamat pembaruan perjalanan.',
                confirmButtonColor: '#08A653'
            });

            return;
        }

        // Atur URL form secara eksplisit.
        form.setAttribute('action', targetUrl.href);
        form.setAttribute('method', 'POST');

        // Pastikan Laravel menerima method spoofing PUT.
        const methodInput = form.querySelector('input[name="_method"]');

        if (methodInput) {
            methodInput.value = 'PUT';
        }

        fields.ruangan.value = button.dataset.ruanganId || '';
        fields.nama.value = button.dataset.namaPasien || '';
        fields.rm.value = button.dataset.nomorRekamMedis || '';
        fields.gender.value = button.dataset.jenisKelamin || '';
        fields.penjamin.value = button.dataset.penjamin || '';
        fields.tujuan.value = button.dataset.tujuan || '';
        fields.catatan.value = button.dataset.catatan || '';

        fields.nomor.textContent =
            'Nomor Perjalanan: ' + (button.dataset.nomor || '—');

        submitting = false;

        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            fields.nama.focus();
        }, 100);
    }

    function closeModal() {

        if (submitting) return;

        modal.hidden = true;
        document.body.style.overflow = '';
    }

    // Tombol Edit dan tombol penutup modal.
    document.addEventListener('click', function (event) {

        const editButton = event.target.closest('[data-edit-journey]');

        if (editButton) {
            event.preventDefault();
            openModal(editButton);
            return;
        }

        if (event.target.closest('[data-close-edit]')) {
            closeModal();
        }
    });


    // Konfirmasi khusus untuk form Edit.
    // Tidak menggunakan handler konfirmasi global layout.
    form.addEventListener('submit', function (event) {

        event.preventDefault();

        if (submitting) return;

        // Validasi HTML.
        if (!form.reportValidity()) {
            return;
        }

        const actionUrl = form.getAttribute('action');

        if (
            !actionUrl ||
            actionUrl.trim() === '' ||
            actionUrl.includes('/riwayat')
        ) {
            Swal.fire({
                icon: 'error',
                title: 'URL pembaruan tidak benar',
                text: 'Silakan tutup popup, kemudian buka kembali tombol Edit.',
                confirmButtonColor: '#08A653'
            });

            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Simpan perubahan?',
            text: 'Perubahan data pengajuan perjalanan akan disimpan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan perubahan',
            cancelButtonText: 'Periksa kembali',
            confirmButtonColor: '#08A653',
            cancelButtonColor: '#7b8580',
            reverseButtons: true,
            focusCancel: true,
            allowOutsideClick: false
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            submitting = true;

            const saveButton = form.querySelector('.journey-edit-save');

            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML =
                    '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...';
            }

            /*
             * Native submit melewati handler submit global.
             * Form tetap mengirim POST dengan _method=PUT
             * menuju action URL yang telah ditetapkan.
             */
            HTMLFormElement.prototype.submit.call(form);
        });
    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }

    });

});
</script>
@endpush
