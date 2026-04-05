<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

// Menyertakan tampilan header (bagian atas halaman)
$page_title = "Surat Panggilan Orang Tua";
include ROOTPATH . "/includes/header.php";

?>


<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/cetak/add_panggilan_ortu.css">

<div class="add-surat-wrapper">
    <div class="page-header-premium">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="animate-title">Surat Panggilan Orang Tua</h2>
            <a href="/poin_pelanggaran_siswa/pages/laporan/list_perjanjian.php" class="btn-back-premium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="header-line"></div>
    </div>

    <!-- Pilihan Siswa Card (Selalu muncul di awal atau saat belum pilih) -->
    <div class="step-card search-section animate-fade-in">
        <h3><i class="fas fa-search" style="margin-right: 15px; color: var(--primary);"></i> Pilih Siswa</h3>
        <p style="margin-bottom: 25px; color: #6c757d; font-weight: 500;">Silakan pilih siswa yang akan dibuatkan surat panggilan orang tua.</p>
        
        <form action="" method="post" class="premium-form-inline">
            <datalist id="nis_list">
                <?php 
                $result = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa WHERE status='aktif'");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . htmlspecialchars($row['nis']) . "'>" . htmlspecialchars($row['nis']) . " - " . htmlspecialchars($row['nama_siswa']) . "</option>";
                }
                ?>
            </datalist>
            <div class="input-group-search">
                <i class="fas fa-id-card search-icon"></i>
                <input type="text" name="nis" value="<?= isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : '' ?>" list="nis_list" placeholder="Masukkan NIS atau Nama Siswa..." autocomplete="off" required>
            </div>
            <button type="submit" class="btn-primary-premium">
                <span>CEK DATA</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <?php
    if(isset($_POST['nis'])) {
        $nis = mysqli_real_escape_string($conn, $_POST['nis']);
        $result_ortu_wali = mysqli_query($conn, "SELECT * FROM siswa JOIN ortu_wali USING(id_ortu_wali) WHERE nis = '$nis'");
        
        if($row_ortu_wali = mysqli_fetch_assoc($result_ortu_wali)) {
        ?>

        <div class="step-card detail-section animate-slide-up"> 
            <div class="form-header-with-info">
                <h3>Detail Pemanggilan</h3>
                <div class="student-info-bubble">
                    <span class="info-label">Siswa:</span>
                    <span class="info-name"><?= htmlspecialchars($row_ortu_wali['nama_siswa']) ?></span>
                </div>
            </div>

            <form action="/poin_pelanggaran_siswa/pages/cetak/surat_panggilan_ortu.php" method="post" class="detail-form-premium">
                <input type="hidden" name="nis" value="<?php echo $nis; ?>">

                <div class="form-grid-premium">
                    <div class="form-group">
                        <label for="no_surat">Nomor Surat</label>
                        <div class="input-field-wrapper">
                            <i class="fas fa-hashtag"></i>
                            <input type="number" name="no_surat" id="no_surat" required placeholder="001">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Tanggal Pemanggilan</label>
                        <div class="input-field-wrapper">
                            <i class="far fa-calendar-alt"></i>
                            <input type="date" name="tanggal" id="tanggal" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="jam">Waktu / Jam</label>
                        <div class="input-field-wrapper">
                            <i class="far fa-clock"></i>
                            <input type="time" name="jam" id="jam" value="08:00" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="keperluan">Keperluan / Perihal</label>
                        <div class="input-field-wrapper align-top">
                            <i class="fas fa-pen-nib"></i>
                            <textarea name="keperluan" id="keperluan" required placeholder="Contoh: Menindaklanjuti poin pelanggaran siswa..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-print-action">
                        <span class="btn-text">BUAT & CETAK SURAT</span>
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </form>
        </div>

        <?php
        } else {
            echo "<div class='error-toast animate-bounce'><i class='fas fa-exclamation-triangle'></i> Data siswa tidak ditemukan!</div>";
        }
    }
    ?>
</div>


<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
