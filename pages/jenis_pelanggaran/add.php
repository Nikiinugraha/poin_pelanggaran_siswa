<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/jenis_pelanggaran/add_jenis_pelanggaran.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-triangle-exclamation"></i> Tambah Jenis Pelanggaran</h2>
        <a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="POST">
            <input type="hidden" name="action" value="add" />
            
            <div class="form-section">
                <div class="form-section-title">
                    Detail Pelanggaran
                </div>

                <div class="form-group">
                    <label>Nama Pelanggaran</label>
                    <input type="text" class="form-control" name="jenis" placeholder="Contoh: Merokok di lingkungan sekolah" autocomplete="off" required />
                </div>

                <div class="form-group">
                    <label>Poin Pelanggaran</label>
                    <input type="number" class="form-control" name="poin" placeholder="Contoh: 50" autocomplete="off" required />
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Berikan nilai poin yang sesuai dengan tingkat pelanggaran.
                    </p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-plus-circle"></i> Tambah Jenis Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include ROOTPATH . '/includes/footer.php';
?>