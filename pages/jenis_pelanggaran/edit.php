<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
$page_title = "Edit Jenis Pelanggaran";
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru

// Mendapatkan ID dari URL
$id_jenis_pelanggaran = isset($_GET['id_jenis_pelanggaran']) ? $_GET['id_jenis_pelanggaran'] : 0;
$result_data = null;

if ($id_jenis_pelanggaran > 0) {
    $sql = "SELECT * FROM jenis_pelanggaran WHERE id_jenis_pelanggaran = $id_jenis_pelanggaran";
    $query = mysqli_query($conn, $sql);
    if ($query && mysqli_num_rows($query) > 0) {
        $result_data = mysqli_fetch_assoc($query);
    }
}

if (!$result_data) {
    echo '<div class="container"><h2>Data pelanggaran tidak ditemukan.</h2></div>';
    include ROOTPATH . '/includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/jenis_pelanggaran/edit_jenis_pelanggaran.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-pen-to-square"></i> Edit Jenis Pelanggaran</h2>
        <a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id_jenis_pelanggaran" value="<?= $result_data['id_jenis_pelanggaran'] ?>" />
            
            <div class="form-section">
                <div class="form-section-title">
                    Detail Pelanggaran
                </div>

                <div class="form-group">
                    <label>Nama Pelanggaran</label>
                    <input type="text" class="form-control" name="jenis" value="<?= htmlspecialchars($result_data['jenis']) ?>" autocomplete="off" required />
                </div>

                <div class="form-group">
                    <label>Poin Pelanggaran</label>
                    <input type="number" class="form-control" name="poin" value="<?= htmlspecialchars($result_data['poin']) ?>" autocomplete="off" required />
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Perubahan poin akan memengaruhi perhitungan poin siswa di masa depan.
                    </p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-save"></i> Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include ROOTPATH . '/includes/footer.php';
?>