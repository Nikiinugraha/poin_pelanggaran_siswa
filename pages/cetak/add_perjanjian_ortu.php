<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

// Menyertakan tampilan header (bagian atas halaman)
$page_title = "Surat Perjanjian Orang Tua";
include ROOTPATH . "/includes/header.php";

$nis_query = "";
if(isset($_POST['nis'])) {
    $nis_query = mysqli_real_escape_string($conn, $_POST['nis']);
}
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/cetak/add_perjanjian_ortu.css">

<div class="add-perjanjian-wrapper">
    <header class="page-header-premium">
        <div class="header-top">
            <h2 class="animate-title">Surat Perjanjian Orang Tua</h2>
            <a href="/poin_pelanggaran_siswa/pages/laporan/list_perjanjian.php" class="btn-back-premium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="header-line"></div>
    </header>

    <div class="card-premium animate-fade-in">
        <h3 class="card-title"><i class="fas fa-search"></i> Pilih Siswa</h3>
        <form action="" method="post" class="selection-form">
            <datalist id="nis_list">
                <?php 
                $result = mysqli_query($conn, "SELECT nis, nama_siswa FROM siswa WHERE status='aktif'");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . htmlspecialchars($row['nis']) . "'>" . htmlspecialchars($row['nis']) . " - " . htmlspecialchars($row['nama_siswa']) . "</option>";
                }
                ?>
            </datalist>
            <div class="search-input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="nis" value="<?= htmlspecialchars($nis_query) ?>" list="nis_list" placeholder="Ketik NIS atau Nama Siswa..." autocomplete="off" required>
            </div>
            <button type="submit" class="btn-check">CEK DATA SISWA</button>
        </form>
    </div>

    <?php
    if(!empty($nis_query)) {
        $result_ortu_wali = mysqli_query($conn, "SELECT * FROM siswa JOIN ortu_wali USING(id_ortu_wali) WHERE nis = '$nis_query'");
        $row_ortu_wali = mysqli_fetch_assoc($result_ortu_wali);
        
        if($row_ortu_wali) {
    ?>
    <div class="student-label animate-fade-in">
        <span>Sedang Memproses:</span>
        <span><?= htmlspecialchars($row_ortu_wali['nama_siswa']) ?> (<?= htmlspecialchars($row_ortu_wali['nis']) ?>)</span>
    </div>

    <div class="parent-forms-grid animate-slide-up">
        <?php
        // Helper function to render parent card
        function renderParentCard($type, $name, $born_place, $born_date, $job, $address, $phone, $nis, $id_ortu_wali) {
            $badge_style = "background: #eff6ff; color: #2563eb;";
            if($type == 'Ibu') $badge_style = "background: #fef2f2; color: #dc2626;";
            if($type == 'Wali') $badge_style = "background: #f0fdf4; color: #16a34a;";
            
            $icon = ($type == 'Ayah') ? 'fa-user-tie' : (($type == 'Ibu') ? 'fa-female' : 'fa-hand-holding-heart');
            ?>
            <div class="parent-card">
                <div class="header-card">
                    <span class="parent-type-badge" style="<?= $badge_style ?>"><?= $type ?></span>
                    <h4 style="margin:0 0 20px 0; font-weight: 800; color: #1e293b;"><i class="fas <?= $icon ?>"></i> Identitas <?= $type ?></h4>
                </div>
                <form action="surat_perjanjian_ortu.php" method="post">
                    <input type="hidden" name="nis" value="<?= $nis ?>">
                    <input type="hidden" name="id_ortu_wali" value="<?= $id_ortu_wali ?>">
                    <input type="hidden" name="orang_tua" value="<?= strtolower($type) ?>">

                    <div class="form-grid-2-col">
                        <div class="form-group-premium full-col">
                            <label>Nama Lengkap</label>
                            <div class="input-wrapper-premium">
                                <i class="fas fa-user-edit"></i>
                                <input type="text" name="nama_ortu" value="<?= htmlspecialchars($name) ?>" required>
                            </div>
                        </div>
                        <div class="form-group-premium">
                            <label>Tempat Lahir</label>
                            <div class="input-wrapper-premium">
                                <i class="fas fa-map-pin"></i>
                                <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($born_place) ?>" required>
                            </div>
                        </div>
                        <div class="form-group-premium">
                            <label>Tanggal Lahir</label>
                            <div class="input-wrapper-premium">
                                <i class="far fa-calendar-alt"></i>
                                <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($born_date) ?>" required>
                            </div>
                        </div>
                        <div class="form-group-premium">
                            <label>Pekerjaan</label>
                            <div class="input-wrapper-premium">
                                <i class="fas fa-briefcase"></i>
                                <input type="text" name="pekerjaan" value="<?= htmlspecialchars($job) ?>" required>
                            </div>
                        </div>
                        <div class="form-group-premium">
                            <label>No. Telp / HP</label>
                            <div class="input-wrapper-premium">
                                <i class="fas fa-phone-alt"></i>
                                <input type="number" name="no_telp" value="<?= htmlspecialchars($phone) ?>" required>
                            </div>
                        </div>
                        <div class="form-group-premium full-col">
                            <label>Alamat Lengkap</label>
                            <div class="input-wrapper-premium" style="align-items: flex-start;">
                                <i class="fas fa-map-marker-alt" style="top: 15px;"></i>
                                <textarea name="alamat" required><?= htmlspecialchars($address) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-print-premium">
                        <i class="fas fa-print"></i> CETAK SURAT PERJANJIAN ORTU
                    </button>
                </form>
            </div>
            <?php
        }

        // Render cards for existing data
        if(!empty($row_ortu_wali["ayah"])) {
            renderParentCard('Ayah', $row_ortu_wali['ayah'], $row_ortu_wali['tempat_lahir_ayah'], $row_ortu_wali['tanggal_lahir_ayah'], $row_ortu_wali['pekerjaan_ayah'], $row_ortu_wali['alamat_ayah'], $row_ortu_wali['no_telp_ayah'], $nis_query, $row_ortu_wali['id_ortu_wali']);
        }
        if(!empty($row_ortu_wali["ibu"])) {
            renderParentCard('Ibu', $row_ortu_wali['ibu'], $row_ortu_wali['tempat_lahir_ibu'], $row_ortu_wali['tanggal_lahir_ibu'], $row_ortu_wali['pekerjaan_ibu'], $row_ortu_wali['alamat_ibu'], $row_ortu_wali['no_telp_ibu'], $nis_query, $row_ortu_wali['id_ortu_wali']);
        }
        if(!empty($row_ortu_wali["wali"])) {
            renderParentCard('Wali', $row_ortu_wali['wali'], $row_ortu_wali['tempat_lahir_wali'], $row_ortu_wali['tanggal_lahir_wali'], $row_ortu_wali['pekerjaan_wali'], $row_ortu_wali['alamat_wali'], $row_ortu_wali['no_telp_wali'], $nis_query, $row_ortu_wali['id_ortu_wali']);
        }

        // Form for fallback if everything empty
        if(empty($row_ortu_wali["ayah"]) && empty($row_ortu_wali["ibu"]) && empty($row_ortu_wali["wali"])) {
            ?>
            <div class="parent-card" style="grid-column: 1 / -1;">
                <span class="parent-type-badge" style="background:#f1f5f9; color:#475569;">LENGKAPI DATA</span>
                <h4 style="margin:0 0 25px 0; font-weight: 800; color: #1e293b;"><i class="fas fa-id-card-alt"></i> Input Manual Data Orang Tua / Wali</h4>
                <form action="surat_perjanjian_ortu.php" method="post">
                    <input type="hidden" name="nis" value="<?= $nis_query ?>">
                    <input type="hidden" name="id_ortu_wali" value="<?= $row_ortu_wali['id_ortu_wali'] ?>">
                    
                    <div class="form-grid-2-col">
                        <div class="form-group-premium">
                            <label>Nama Orang Tua / Wali</label>
                            <div class="input-wrapper-premium"><i class="fas fa-user"></i><input type="text" name="nama_ortu" required></div>
                        </div>
                        <div class="form-group-premium">
                            <label>Hubungan Sebagai</label>
                            <div class="input-wrapper-premium">
                                <i class="fas fa-users"></i>
                                <select name="orang_tua" required style="padding-left: 42px;">
                                    <option value="ayah">Ayah</option>
                                    <option value="ibu">Ibu</option>
                                    <option value="wali">Wali</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group-premium">
                            <label>Tempat Lahir</label>
                            <div class="input-wrapper-premium"><i class="fas fa-map-pin"></i><input type="text" name="tempat_lahir" required></div>
                        </div>
                        <div class="form-group-premium">
                            <label>Tanggal Lahir</label>
                            <div class="input-wrapper-premium"><i class="far fa-calendar-alt"></i><input type="date" name="tanggal_lahir" required></div>
                        </div>
                        <div class="form-group-premium">
                            <label>Pekerjaan</label>
                            <div class="input-wrapper-premium"><i class="fas fa-briefcase"></i><input type="text" name="pekerjaan" required></div>
                        </div>
                        <div class="form-group-premium">
                            <label>No. Telp / HP</label>
                            <div class="input-wrapper-premium"><i class="fas fa-phone"></i><input type="number" name="no_telp" required></div>
                        </div>
                        <div class="form-group-premium full-col">
                            <label>Alamat Lengkap</label>
                            <div class="input-wrapper-premium" style="align-items: flex-start;"><i class="fas fa-map-marker-alt" style="top:15px;"></i><textarea name="alamat" required></textarea></div>
                        </div>
                    </div>
                    <button type="submit" class="btn-print-premium" style="max-width: 350px; margin: 20px auto 0;">
                        <i class="fas fa-print"></i> CETAK SURAT PERJANJIAN ORTU
                    </button>
                </form>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
        } else {
            echo "<div style='text-align:center; padding: 50px; background: white; border-radius: 20px; box-shadow: var(--shadow-premium);'><i class='fas fa-exclamation-circle' style='font-size: 3rem; color: var(--danger); margin-bottom: 20px;'></i><h4 style='color: var(--text-main); font-weight: 800;'>Data Siswa Tidak Ditemukan!</h4><p style='color: var(--text-muted);'>Pastikan NIS yang dimasukkan sudah benar.</p></div>";
        }
    }
    ?>
</div>

<div style="height: 50px;"></div>

<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
