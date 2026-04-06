<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// RBAC Protection: Guru and Wakasek cannot edit students
if (in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])) {
    echo "<script>alert('Akses Ditolak! Guru dan Wakasek tidak diizinkan mengubah data siswa.'); window.location.href='list.php';</script>";
    exit();
}

// Mendapatkan NIS dari URL
$nis = isset($_GET['nis']) ? $_GET['nis'] : 0;
$siswa = null;

if ($nis > 0) {
    // Query lengkap untuk mengambil data siswa, orang tua, dan detail kelas
    $sql = "SELECT s.*, o.*, k.rombel, t.tingkat, p.program_keahlian 
            FROM siswa s 
            LEFT JOIN ortu_wali o ON s.id_ortu_wali = o.id_ortu_wali
            LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
            LEFT JOIN tingkat t ON k.id_tingkat = t.id_tingkat
            LEFT JOIN program_keahlian p ON k.id_program_keahlian = p.id_program_keahlian
            WHERE s.nis = '$nis'";
    
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $siswa = mysqli_fetch_assoc($result);
    }
}

if (!$siswa) {
    echo '<div class="container" style="text-align: center; margin-top: 50px;">
            <div class="form-card">
                <i class="fas fa-circle-exclamation" style="font-size: 3rem; color: #ef4444; margin-bottom: 20px;"></i>
                <h2>Data Siswa tidak ditemukan.</h2>
                <a href="list.php" class="btn-primary" style="display: inline-flex; margin-top: 20px;">Kembali ke Daftar</a>
            </div>
          </div>';
    include ROOTPATH . '/includes/footer.php';
    exit;
}

// Format string kelas untuk input list
$current_class = (isset($siswa['tingkat'])) ? $siswa['tingkat'] . ' ' . $siswa['program_keahlian'] . ' ' . $siswa['rombel'] : '';
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/siswa/edit_siswa.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-edit"></i> Edit Data Siswa</h2>
        <a href="list.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="POST">
            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="old_nis" value="<?php echo $siswa['nis']; ?>" />
            <input type="hidden" name="id_ortu_wali" value="<?php echo $siswa['id_ortu_wali']; ?>" />

            <!-- Bagian 1: Identitas Utama Siswa -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-id-card"></i> Identitas Utama
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>NIS (Nomor Induk Siswa)</label>
                        <input type="number" class="form-control" name="nis" value="<?php echo $siswa['nis']; ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="Laki - Laki" <?php echo ($siswa['jenis_kelamin'] == 'Laki - Laki') ? 'selected' : ''; ?>>Laki - Laki</option>
                            <option value="Perempuan" <?php echo ($siswa['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas Terpadu</label>
                        <datalist id="kelas_list">
                            <?php
                            $q_kelas = mysqli_query($conn, "SELECT * FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat)");
                            while ($k = mysqli_fetch_assoc($q_kelas)) {
                                echo "<option value='" . $k['tingkat'] . ' ' . $k['program_keahlian'] . ' ' . $k['rombel'] . "'></option>";
                            }
                            ?>
                        </datalist>
                        <input list="kelas_list" class="form-control" name="kelas" value="<?php echo $current_class; ?>" placeholder="Pilih Kelas..." autocomplete="off" required />
                    </div>
                    <div class="form-group">
                        <label>Status Keaktifan</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" <?php echo ($siswa['status'] == 'aktif') ? 'selected' : ''; ?>>Siswa Aktif</option>
                            <option value="tidak_aktif" <?php echo ($siswa['status'] == 'tidak_aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                            <option value="lulus" <?php echo ($siswa['status'] == 'lulus') ? 'selected' : ''; ?>>Lulus Alumni</option>
                            <option value="pindah" <?php echo ($siswa['status'] == 'pindah') ? 'selected' : ''; ?>>Mutasi / Pindah</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nama Lengkap Siswa</label>
                        <input type="text" class="form-control" name="nama_siswa" value="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>" placeholder="Gelar Opsional..." required />
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Domisili & Kontak -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-location-dot"></i> Domisili & Kontak Siswa
                </div>
                <div class="form-group" style="margin-right: 0;">
                    <label>Alamat Lengkap (KTP / Domisili)</label>
                    <textarea name="alamat_siswa" class="form-control" placeholder="Jalan, No Rumah, RT/RW, Kec..." required><?php echo htmlspecialchars($siswa['alamat']); ?></textarea>
                </div>
            </div>

            <!-- Bagian 3: Data Orang Tua / Wali -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-users"></i> Informasi Keluarga / Wali
                </div>

                <!-- Sub-grid Ayah -->
                <div class="parent-sub-grid">
                    <div class="parent-sub-grid-title"><i class="fas fa-user-tie"></i> Data Ayah Kandung</div>
                    <div class="form-group">
                        <label>Nama Ayah</label>
                        <input type="text" class="form-control" name="ayah" value="<?php echo htmlspecialchars($siswa['ayah']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>No. Telp Ayah</label>
                        <input type="text" class="form-control" name="telp_ayah" value="<?php echo $siswa['no_telp_ayah']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Ayah</label>
                        <input type="text" class="form-control" name="pekerjaan_ayah" value="<?php echo htmlspecialchars($siswa['pekerjaan_ayah']); ?>" />
                    </div>
                    <div class="form-group" style="grid-column: span 1.5;">
                        <label>Alamat Ayah</label>
                        <input type="text" class="form-control" name="alamat_ayah" value="<?php echo htmlspecialchars($siswa['alamat_ayah']); ?>" />
                    </div>
                </div>

                <!-- Sub-grid Ibu -->
                <div class="parent-sub-grid">
                    <div class="parent-sub-grid-title"><i class="fas fa-person-breastfeeding"></i> Data Ibu Kandung</div>
                    <div class="form-group">
                        <label>Nama Ibu</label>
                        <input type="text" class="form-control" name="ibu" value="<?php echo htmlspecialchars($siswa['ibu']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>No. Telp Ibu</label>
                        <input type="text" class="form-control" name="telp_ibu" value="<?php echo $siswa['no_telp_ibu']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Ibu</label>
                        <input type="text" class="form-control" name="pekerjaan_ibu" value="<?php echo htmlspecialchars($siswa['pekerjaan_ibu']); ?>" />
                    </div>
                    <div class="form-group" style="grid-column: span 1.5;">
                        <label>Alamat Ibu</label>
                        <input type="text" class="form-control" name="alamat_ibu" value="<?php echo htmlspecialchars($siswa['alamat_ibu']); ?>" />
                    </div>
                </div>

                <!-- Sub-grid Wali -->
                <div class="parent-sub-grid">
                    <div class="parent-sub-grid-title"><i class="fas fa-user-shield"></i> Data Wali (Opsional)</div>
                    <div class="form-group">
                        <label>Nama Wali</label>
                        <input type="text" class="form-control" name="wali" value="<?php echo htmlspecialchars($siswa['wali']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>No. Telp Wali</label>
                        <input type="text" class="form-control" name="telp_wali" value="<?php echo $siswa['no_telp_wali']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Wali</label>
                        <input type="text" class="form-control" name="pekerjaan_wali" value="<?php echo htmlspecialchars($siswa['pekerjaan_wali']); ?>" />
                    </div>
                    <div class="form-group" style="grid-column: span 1.5;">
                        <label>Alamat Wali</label>
                        <input type="text" class="form-control" name="alamat_wali" value="<?php echo htmlspecialchars($siswa['alamat_wali']); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>