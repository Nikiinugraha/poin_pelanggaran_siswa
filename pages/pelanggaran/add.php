<?php
define("ROOTPATH", $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
$page_title = "Input Pelanggaran Siswa";
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/pelanggaran/add_entri_pelanggaran.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-file-signature"></i>Catat Pelanggaran Siswa</h2>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/pelanggaran_process.php" method="POST">
            <input type="hidden" name="action" value="add">

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user-tag"></i> Informasi Pelanggar & Kejadian
                </div>

                <!-- Container utama dengan layout grid untuk merapikan elemen formulir -->

                <div class="form-grid">
                    <!-- BAGIAN 1: Pemilihan Siswa -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Pilih Siswa (Berdasarkan NIS/Nama)</label>
                        <!-- Datalist menyediakan fitur pencarian (autocomplete) yang dinamis -->
                        <datalist id="list-nis">
                            <!-- Mengambil data NIS dan Nama dari tabel 'siswa' untuk mengisi pilihan -->
                            <?php
                            $query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa");
                            while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                                // Menampilkan NIS sebagai nilai (value) dan nama siswa sebagai label pilihan
                                echo "<option value='" . $siswa['nis'] . "'>" . htmlspecialchars($siswa['nama_siswa']) . "</option>";
                            }
                            ?>
                        </datalist>
                        <!-- Input untuk kategori pelanggaran, wajib diisi (required) -->
                        <input list="list-nis" class="form-control" name="nis" placeholder="Ketik NIS atau cari nama siswa..." autocomplete="off" required>
                        <div class="search-hint">
                            <i class="fas fa-search"></i> Anda dapat mencari dengan mengetik nomor NIS.
                        </div>
                    </div>

                    <!-- Column 2: Violation Type -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Kategori Jenis Pelanggaran</label>
                        <datalist id="list-jenis">
                            <?php
                            $query_pelanggaran = mysqli_query($conn, "SELECT jenis, poin FROM jenis_pelanggaran");
                            while ($pelanggaran = mysqli_fetch_assoc($query_pelanggaran)) {
                                echo "<option value='" . $pelanggaran['jenis'] . "'>Poin: " . $pelanggaran['poin'] . "</option>";
                            }
                            ?>
                        </datalist>
                        <input list="list-jenis" class="form-control" name="jenis_pelanggaran" placeholder="Pilih kategori pelanggaran..." autocomplete="off" required>
                        <div class="search-hint">
                            <i class="fas fa-triangle-exclamation"></i> Poin akan otomatis terakumulasi ke profil siswa.
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Keterangan Kejadian</label>
                        <textarea name="keterangan" class="form-control" placeholder="Jelaskan detail kejadian, lokasi, atau waktu spesifik..." required></textarea>
                    </div>
                </div>

                <div class="notice-box">
                    <i class="fas fa-circle-info" style="margin-top: 3px;"></i>
                    <div>
                        <strong>Perhatian:</strong> Pastikan data yang dimasukkan sudah benar. Tindakan ini akan langsung menambah poin pelanggaran pada catatan siswa yang bersangkutan.
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-plus-circle"></i> Catat Pelanggaran Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>