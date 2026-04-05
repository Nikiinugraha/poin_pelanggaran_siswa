<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/kelas/add_kelas.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-school-flag"></i> Tambah Data Kelas</h2>
        <a href="/poin_pelanggaran_siswa/pages/kelas/list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/kelas_process.php" method="POST">
            <input type="hidden" name="action" value="add" />

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-layer-group"></i> Struktur & Identitas Kelas
                </div>

                <div class="form-grid">
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
                        <input list="list-tingkat" class="form-control" name="tingkat" placeholder="Pilih X, XI, atau XII" autocomplete="off" required />
                        <div class="search-hint">
                            <i class="fas fa-search"></i> Ketik untuk saran tingkat.
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Rombel (Rombongan Belajar)</label>
                        <input type="number" class="form-control" name="rombel" placeholder="Tulis angka (misal: 1)" autocomplete="off" required />
                    </div>

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
                        <input list="list-pk" class="form-control" name="program_keahlian" placeholder="Cari Konsentrasi Keahlian..." autocomplete="off" required />
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
                        <input list="list-guru" class="form-control" name="walikelas" placeholder="Pilih Guru yang Aktif..." autocomplete="off" required />
                        <div class="search-hint">
                            <i class="fas fa-info-circle"></i> Hanya menampilkan daftar guru aktif.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-save"></i> Simpan Konfigurasi Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>