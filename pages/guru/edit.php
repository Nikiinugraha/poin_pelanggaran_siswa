<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// Mendapatkan Kode Guru dari URL
$kode_guru = isset($_GET['kode_guru']) ? $_GET['kode_guru'] : 0;
$guru = null;

if ($kode_guru != "0") {
    // Jalankan query untuk mengambil data guru
    $sql = "SELECT * FROM guru WHERE kode_guru = '$kode_guru'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $guru = mysqli_fetch_assoc($result);
    }
}

if (!$guru) {
    echo '<div class="container"><h2>Guru tidak ditemukan.</h2></div>';
    include ROOTPATH . '/includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/guru/edit_guru.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-pen"></i> Edit Data Guru</h2>
        <a href="list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/guru_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="kode_guru" value="<?= htmlspecialchars($guru['kode_guru']); ?>" />

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-id-badge"></i> Ubah Profil & Akun Guru
                </div>
                
                <div class="form-grid">
                    <!-- Row 1 -->
                    <div class="form-group">
                        <label>Kode Guru (ID)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($guru['kode_guru']); ?>" readonly />
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($guru['username']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Role / Hak Akses</label>
                        <select name="role" class="form-control" required>
                            <option value="guru" <?= ($guru['role'] == 'guru') ? 'selected' : ''; ?>>Guru (Mata Pelajaran)</option>
                            <option value="bk" <?= ($guru['role'] == 'bk') ? 'selected' : ''; ?>>BK (Bimbingan Konseling)</option>
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nama Lengkap Guru</label>
                        <input type="text" class="form-control" name="nama_pengguna" value="<?= htmlspecialchars($guru['nama_pengguna']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label>No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telp" value="<?= htmlspecialchars($guru['telp']); ?>" required />
                    </div>

                    <!-- Row 3 -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Jabatan Struktur</label>
                        <select name="jabatan" class="form-control" required>
                            <?php 
                            $jabatans = ["Guru Mapel", "Kepala Sekolah", "Waka Kurikulum", "Waka Kesiswaan", "Waka Sarana Prasarana", "Waka Humas", "Komka AN", "Komka RPL", "Komka DKV", "Komka TKJ", "Komka BD", "Guru BK XII", "Guru BK XI", "Guru BK X"];
                            foreach($jabatans as $j) {
                                $sel = ($guru['jabatan'] == $j) ? 'selected' : '';
                                echo "<option value='$j' $sel>$j</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Keaktifan</label>
                        <select name="aktif" class="form-control" required>
                            <option value="Y" <?= ($guru['aktif'] == 'Y') ? 'selected' : ''; ?>>Aktif (Bisa Login)</option>
                            <option value="N" <?= ($guru['aktif'] == 'N') ? 'selected' : ''; ?>>Non-Aktif (Akses Dicabut)</option>
                        </select>
                    </div>
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

<?php include ROOTPATH . '/includes/footer.php'; ?>