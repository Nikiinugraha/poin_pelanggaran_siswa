<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// Mendapatkan ID Kelas dari URL
$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : 0;
$kelas = null;

if ($id_kelas > 0) {
    // Query dengan JOIN untuk mendapatkan nama teks agar prapengisian datalist akurat
    $sql = "SELECT * FROM kelas 
            JOIN tingkat USING(id_tingkat) 
            JOIN program_keahlian USING(id_program_keahlian) 
            JOIN guru USING(kode_guru) 
            WHERE id_kelas = '$id_kelas'";
    $result_kelas = mysqli_query($conn, $sql);
    if ($result_kelas && mysqli_num_rows($result_kelas) > 0) {
        $kelas = mysqli_fetch_assoc($result_kelas);
    }
}

if (!$kelas) {
    echo '<div class="container"><h2>Data Kelas tidak ditemukan.</h2></div>';
    include ROOTPATH . '/includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/kelas/edit_kelas.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-school-circle-check"></i> Edit Data Kelas</h2>
        <a href="/poin_pelanggaran_siswa/pages/kelas/list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/kelas_process.php" method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="id_kelas" value="<?= $kelas['id_kelas'] ?>" />

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-pen-nib"></i> Ubah Konfigurasi Kelas
                </div>

                <div class="form-grid">
                    <!-- Column 1 -->
                    <div class="form-group">
                        <label>Tingkat (Grade)</label>
                        <datalist id="list-tingkat">
                            <?php
                            $query_tingkat = mysqli_query($conn, "SELECT * FROM tingkat");
                            while ($tingkat = mysqli_fetch_assoc($query_tingkat)) {
                                echo "<option value='" . $tingkat['tingkat'] . "'></option>";
                            }
                            ?>
                        </datalist>
                        <input list="list-tingkat" class="form-control" name="tingkat" value="<?= htmlspecialchars($kelas['tingkat']) ?>" autocomplete="off" required />
                    </div>

                    <div class="form-group">
                        <label>Rombel (Rombongan Belajar)</label>
                        <input type="number" class="form-control" name="rombel" value="<?= htmlspecialchars($kelas['rombel']) ?>" autocomplete="off" required />
                    </div>

                    <!-- Column 2 -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Program Keahlian (Jurusan)</label>
                        <datalist id="list-pk">
                            <?php
                            $query_pk = mysqli_query($conn, "SELECT * FROM program_keahlian");
                            while ($pk = mysqli_fetch_assoc($query_pk)) {
                                echo "<option value='" . $pk['program_keahlian'] . "'></option>";
                            }
                            ?>
                        </datalist>
                        <input list="list-pk" class="form-control" name="program_keahlian" value="<?= htmlspecialchars($kelas['program_keahlian']) ?>" autocomplete="off" required />
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Wali Kelas Terpilih</label>
                        <datalist id="list-guru">
                            <?php
                            $query_guru = mysqli_query($conn, "SELECT * FROM guru WHERE aktif = 'Y'");
                            while ($guru = mysqli_fetch_assoc($query_guru)) {
                                echo "<option value='" . $guru['nama_pengguna'] . "'></option>";
                            }
                            ?>
                        </datalist>
                        <input list="list-guru" class="form-control" name="walikelas" value="<?= htmlspecialchars($kelas['nama_pengguna']) ?>" autocomplete="off" required />
                        <div class="search-hint">
                            <i class="fas fa-info-circle"></i> Hanya menampilkan guru yang berstatus aktif saat ini.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-save"></i> Simpan Perubahan Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>