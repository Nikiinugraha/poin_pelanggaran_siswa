<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";
include ROOTPATH . "/includes/cek_akses_cetak.php";

// Menyertakan tampilan header (bagian atas halaman)
$page_title = "Surat Pindah Sekolah";
include ROOTPATH . "/includes/header.php";

$nis_query = "";
if(isset($_POST['nis'])) {
    $nis_query = mysqli_real_escape_string($conn, $_POST['nis']);
}
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/cetak/add_pindah_sekolah.css">

<div class="add-pindah-wrapper">
    <header class="page-header-premium">
        <div class="header-top">
            <h2 class="animate-title">Surat Pindah Sekolah</h2>
            <a href="/poin_pelanggaran_siswa/pages/laporan/list_pindah.php" class="btn-back-premium">
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

    <!-- Master Form Start -->
    <div class="animate-slide-up">
        
        <!-- Parent Option Cards -->
        <h3 class="card-title" style="margin-top: 40px;"><i class="fas fa-user-friends"></i> Pilih Penandatangan (Orang Tua / Wali)</h3>
        
        <div class="parent-grid-pindah">
            <?php
            // Helper function to render a parent form card
            function renderParentPindahCard($type, $name, $address, $job, $nis, $id_ortu_wali) {
                $badge_class = "badge-" . strtolower($type);
                $icon = ($type == 'Ayah') ? 'fa-user-tie' : (($type == 'Ibu') ? 'fa-female' : 'fa-user-tag');
                ?>
                <div class="parent-radio-card">
                    <form action="surat_pindah_sekolah.php?from=list_pindah.php" method="post" class="parent-form-pindah">
                        
                        <!-- These hidden fields will be paired with the data from the main section via JavaScript if needed, 
                             or we can just put the main inputs in EACH form. 
                             To keep it simple and working like legacy, we'll use a hidden input pattern. -->
                        
                        <!-- Siswa & Ortu Info -->
                        <input type="hidden" name="nis" value="<?= $nis ?>">
                        <input type="hidden" name="id_ortu_wali" value="<?= $id_ortu_wali ?>">
                        <input type="hidden" name="orang_tua" value="<?= strtolower($type) ?>">
                        
                        <!-- Surat Details (Sync these via JS) -->
                        <input type="hidden" name="no_surat" class="sync-no-surat" value="">
                        <input type="hidden" name="pindah_ke" class="sync-pindah-ke" value="">
                        <input type="hidden" name="alasan_pindah" class="sync-alasan" value="">
                        
                        <div class="parent-card-content">
                            <span class="type-badge <?= $badge_class ?>"><?= $type ?></span>
                            <div class="name"><?= htmlspecialchars($name) ?></div>
                            <div class="address"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($address) ?></div>
                            
                            <div style="margin-top:15px; border-top:1px solid #f1f5f9; padding-top:15px;">
                                <div class="form-group-premium" style="margin-bottom:10px;">
                                    <label>Konfirmasi Nama</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fas fa-user-edit"></i>
                                        <input type="text" name="nama_ortu" value="<?= htmlspecialchars($name) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group-premium" style="margin-bottom:10px;">
                                    <label>Konfirmasi Alamat</label>
                                    <div class="input-wrapper-premium" style="align-items: flex-start;">
                                        <i class="fas fa-map-pin" style="top:12px;"></i>
                                        <textarea name="alamat" required style="min-height: 60px; font-size:0.85rem;"><?= htmlspecialchars($address) ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-print-pindah" onclick="return syncFields(this.form)">
                                <i class="fas fa-print"></i> CETAK SURAT PINDAH
                            </button>
                        </div>
                    </form>
                </div>
                <?php
            }

            // Surat Info Inputs - We'll place them outside but sync their values
            ?>
        </div>

        <div class="card-premium" style="margin-top: 30px; border-top: 4px solid var(--danger);">
            <h3 class="card-title"><i class="fas fa-file-invoice" style="color: var(--danger);"></i> Detail Surat Pindah</h3>
            <div class="info-section-premium">
                <div class="form-group-premium">
                    <label>Nomor Surat</label>
                    <div class="input-wrapper-premium">
                        <i class="fas fa-hashtag"></i>
                        <input type="number" id="master_no_surat" placeholder="Contoh: 123" required>
                    </div>
                </div>
                <div class="form-group-premium">
                    <label>Sekolah Tujuan (Pindah Ke)</label>
                    <div class="input-wrapper-premium">
                        <i class="fas fa-school"></i>
                        <input type="text" id="master_pindah_ke" placeholder="Nama sekolah tujuan..." required>
                    </div>
                </div>
                <div class="form-group-premium full-col">
                    <label>Alasan Pindah</label>
                    <div class="input-wrapper-premium" style="align-items: flex-start;">
                        <i class="fas fa-comment-dots" style="top:15px;"></i>
                        <textarea id="master_alasan_pindah" placeholder="Tuliskan alasan kepindahan siswa..." required></textarea>
                    </div>
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
                <i class="fas fa-info-circle"></i> Isi detail di atas terlebih dahulu, kemudian klik tombol <strong>"Cetak"</strong> pada kartu orang tua/wali di bawah.
            </p>
        </div>

        <div class="parent-grid-pindah">
            <?php
            if(!empty($row_ortu_wali["ayah"])) {
                renderParentPindahCard('Ayah', $row_ortu_wali['ayah'], $row_ortu_wali['alamat_ayah'], $row_ortu_wali['pekerjaan_ayah'], $nis_query, $row_ortu_wali['id_ortu_wali']);
            }
            if(!empty($row_ortu_wali["ibu"])) {
                renderParentPindahCard('Ibu', $row_ortu_wali['ibu'], $row_ortu_wali['alamat_ibu'], $row_ortu_wali['pekerjaan_ibu'], $nis_query, $row_ortu_wali['id_ortu_wali']);
            }
            if(!empty($row_ortu_wali["wali"])) {
                renderParentPindahCard('Wali', $row_ortu_wali['wali'], $row_ortu_wali['alamat_wali'], $row_ortu_wali['pekerjaan_wali'], $nis_query, $row_ortu_wali['id_ortu_wali']);
            }
            if(empty($row_ortu_wali["ayah"]) && empty($row_ortu_wali["ibu"]) && empty($row_ortu_wali["wali"])) {
                // Fallback form
                ?>
                <div class="parent-radio-card" style="grid-column: 1 / -1;">
                    <form action="surat_pindah_sekolah.php?from=list_pindah.php" method="post" class="parent-form-pindah">
                        <input type="hidden" name="nis" value="<?= $nis_query ?>">
                        <input type="hidden" name="id_ortu_wali" value="<?= $row_ortu_wali['id_ortu_wali'] ?>">
                        <input type="hidden" name="no_surat" class="sync-no-surat" value="">
                        <input type="hidden" name="pindah_ke" class="sync-pindah-ke" value="">
                        <input type="hidden" name="alasan_pindah" class="sync-alasan" value="">
                        
                        <div class="parent-card-content">
                            <span class="type-badge" style="background:#f1f5f9; color:#475569;">INPUT MANUAL</span>
                            <div class="form-grid-2-col" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:15px;">
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
                                <div class="form-group-premium full-col">
                                    <label>Alamat Lengkap</label>
                                    <div class="input-wrapper-premium" style="align-items: flex-start;"><i class="fas fa-map-marker-alt" style="top:15px;"></i><textarea name="alamat" required></textarea></div>
                                </div>
                            </div>
                            <button type="submit" class="btn-print-pindah" onclick="return syncFields(this.form)">
                                <i class="fas fa-print"></i> CETAK SURAT PINDAH
                            </button>
                        </div>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
        } else {
            echo "<div style='text-align:center; padding: 50px; background: white; border-radius: 20px; box-shadow: var(--shadow-premium);'><i class='fas fa-exclamation-circle' style='font-size: 3rem; color: var(--danger); margin-bottom: 20px;'></i><h4 style='color: var(--text-main); font-weight: 800;'>Data Siswa Tidak Ditemukan!</h4><p style='color: var(--text-muted);'>Pastikan NIS yang dimasukkan sudah benar.</p></div>";
        }
    }
    ?>
</div>

<script>
function syncFields(form) {
    const noSurat = document.getElementById('master_no_surat').value;
    const pindahKe = document.getElementById('master_pindah_ke').value;
    const alasan = document.getElementById('master_alasan_pindah').value;

    if (!noSurat || !pindahKe || !alasan) {
        alert('Harap isi Detail Surat Pindah (Nomor Surat, Pindah Ke, dan Alasan) terlebih dahulu!');
        return false;
    }

    form.querySelector('.sync-no-surat').value = noSurat;
    form.querySelector('.sync-pindah-ke').value = pindahKe;
    form.querySelector('.sync-alasan').value = alasan;
    
    return true;
}
</script>

<div style="height: 50px;"></div>

<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
