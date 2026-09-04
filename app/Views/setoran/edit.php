<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Edit Setoran</h1>
            <p class="text-muted">Perbarui data setoran pengguna.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= base_url('admin/setoran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Setoran</h6>
                </div>
                <div class="card-body">
                    <form id="formEditSetoran" action="<?= base_url('admin/setoran/' . $setoran['id'] . '/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Program <span class="text-danger">*</span></label>
                                <select name="program_id" id="program_id" class="form-select" required>
                                    <option value="">-- Pilih Program --</option>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['id'] ?>" <?= $setoran['program_id'] == $program['id'] ? 'selected' : '' ?>>
                                            <?= esc($program['nama_program']) ?> (<?= esc($program['kode_program']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="err-program_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Setoran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_setoran" class="form-control" value="<?= esc(date('Y-m-d', strtotime($setoran['tanggal_setoran']))) ?>" required>
                                <div class="invalid-feedback" id="err-tanggal_setoran"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pengguna</label>
                                <input type="text" class="form-control bg-light" value="<?php foreach($users as $u) { if($u['id'] == $setoran['user_id']) echo esc($u['nama']); } ?>" readonly>
                                <small class="text-muted">Pengguna tidak dapat diubah setelah setoran dibuat.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Periode <span class="text-danger">*</span></label>
                                <select name="periode_id" id="periode_id" class="form-select" required>
                                    <option value="">-- Pilih Periode --</option>
                                    <?php foreach ($periodes as $periode): ?>
                                        <option value="<?= $periode['id'] ?>" data-program="<?= $periode['program_id'] ?>" <?= $setoran['periode_id'] == $periode['id'] ? 'selected' : '' ?>>
                                            <?= esc($periode['nama_periode']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback" id="err-periode_id"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal" class="form-control" value="<?= esc($setoran['nominal']) ?>" min="1" required>
                                    <div class="invalid-feedback" id="err-nominal"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status_setoran" class="form-select" required>
                                    <option value="tercatat" <?= $setoran['status_setoran'] == 'tercatat' ? 'selected' : '' ?>>Tercatat</option>
                                    <option value="diverifikasi" <?= $setoran['status_setoran'] == 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                                    <option value="dikoreksi" <?= $setoran['status_setoran'] == 'dikoreksi' ? 'selected' : '' ?>>Dikoreksi</option>
                                    <option value="dibatalkan" <?= $setoran['status_setoran'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                                <div class="invalid-feedback" id="err-status_setoran"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= esc($setoran['keterangan']) ?></textarea>
                            <div class="invalid-feedback" id="err-keterangan"></div>
                        </div>

                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="fas fa-save me-1"></i> Perbarui Setoran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-secondary">Informasi</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 text-muted ps-3">
                        <li class="mb-2"><b>Dibuat pada:</b><br><?= date('d M Y H:i', strtotime($setoran['created_at'])) ?></li>
                        <?php if($setoran['updated_at']): ?>
                        <li class="mb-2"><b>Diperbarui pada:</b><br><?= date('d M Y H:i', strtotime($setoran['updated_at'])) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formEditSetoran').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    const btnSubmit = document.getElementById('btnSubmit');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memperbarui...';
    btnSubmit.disabled = true;

    const formData = new FormData(this);
    
    // Get latest CSRF token from cookie (handles regeneration)
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }
    const csrfToken = getCookie('csrf_cookie_name') || '<?= csrf_hash() ?>';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw data;
        }
        return data;
    })
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: data.message || 'Setoran berhasil diperbarui.',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = '<?= base_url('admin/setoran') ?>';
        });
    })
    .catch(error => {
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;

        if (error.data && typeof error.data === 'object') {
            // Validation errors
            for (const [field, msg] of Object.entries(error.data)) {
                const input = document.querySelector(`[name="${field}"]`);
                const errBox = document.getElementById(`err-${field}`);
                if (input && errBox) {
                    input.classList.add('is-invalid');
                    errBox.innerText = msg;
                }
            }
        } else {
            Swal.fire('Gagal!', error.message || error.error || 'Terjadi kesalahan sistem.', 'error');
        }
    });
});

// Fungsi untuk mengupdate dropdown periode berdasarkan program yang dipilih
document.getElementById('program_id').addEventListener('change', function() {
    const programId = this.value;
    const periodeSelect = document.getElementById('periode_id');
    const allPeriodes = periodeSelect.querySelectorAll('option[data-program]');
    
    // Reset dan filter periode berdasarkan program yang dipilih
    periodeSelect.innerHTML = '<option value="">-- Pilih Periode --</option>';
    
    let selectedPeriodeExists = false;
    allPeriodes.forEach(option => {
        if (option.dataset.program === programId) {
            const newOption = option.cloneNode(true);
            newOption.style.display = '';
            periodeSelect.appendChild(newOption);
            
            // Check if this was the previously selected option
            if (option.selected) {
                selectedPeriodeExists = true;
            }
        }
    });
    
    if (!selectedPeriodeExists && periodeSelect.querySelector('option[selected]')) {
        // Clear selection if previously selected periode doesn't belong to new program
        periodeSelect.value = '';
    }
});

// Initialize the periode dropdown based on current program selection
window.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program_id');
    const periodeSelect = document.getElementById('periode_id');
    
    if (programSelect.value) {
        // Trigger change event to filter periodes
        programSelect.dispatchEvent(new Event('change'));
        
        // Set the periode value if it exists in the filtered options
        const currentPeriodeId = '<?= $setoran["periode_id"] ?>';
        if (currentPeriodeId) {
            // Wait for options to be filtered
            setTimeout(() => {
                periodeSelect.value = currentPeriodeId;
            }, 10);
        }
    }
});
</script>
<?= $this->endSection() ?>
