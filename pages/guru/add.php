<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// Generate Kode Guru Otomatis
$result = mysqli_query($conn, "SELECT kode_guru FROM guru ORDER BY kode_guru DESC LIMIT 1");
$row = mysqli_fetch_assoc($result);
$last_kode = $row['kode_guru'];
$parts = explode(".", $last_kode);
$next_number = str_pad($parts[1] + 1, 3, "0", STR_PAD_LEFT);
$new_kode = "0021." . $next_number;
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/guru/add_guru.css">

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-user-plus"></i> Tambah Data Guru</h2>
        <a href="list.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/guru_process.php" method="POST">
            <input type="hidden" name="action" value="add">

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-id-badge"></i> Identitas & Akun Guru
                </div>
                
                <div class="form-grid">
                    <!-- Row 1 -->
                    <div class="form-group">
                        <label>Kode Guru (Otomatis)</label>
                        <input type="text" class="form-control" name="kode_guru" value="<?= $new_kode ?>" readonly />
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username" autocomplete="off" required />
                    </div>
                    <div class="form-group">
                        <label>Role / Hak Akses</label>
                        <select name="role" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="bk">BK (Bimbingan Konseling)</option>
                            <option value="guru">Guru (Mata Pelajaran)</option>
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nama Lengkap Guru</label>
                        <input type="text" class="form-control" name="nama_pengguna" placeholder="Masukkan nama lengkap beserta gelar" autocomplete="off" required />
                    </div>
                    <div class="form-group">
                        <label>No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telp" placeholder="Contoh: 081234567890" autocomplete="off" required />
                    </div>

                    <!-- Row 3 -->
                    <div class="form-group" style="grid-column: span 3;">
                        <label>Jabatan Struktur</label>
                        <select name="jabatan" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Jabatan --</option>
                            <option value="Guru Mapel">Guru Mapel</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Waka Kurikulum">Waka Kurikulum</option>
                            <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                            <option value="Waka Sarana Prasarana">Waka Sarana Prasarana</option>
                            <option value="Waka Humas">Waka Humas</option>
                            <option value="Komka AN">Komka AN</option>
                            <option value="Komka RPL">Komka RPL</option>
                            <option value="Komka DKV">Komka DKV</option>
                            <option value="Komka TKJ">Komka TKJ</option>
                            <option value="Komka BD">Komka BD</option>
                            <option value="Guru BK XII">Guru BK XII</option>
                            <option value="Guru BK XI">Guru BK XI</option>
                            <option value="Guru BK X">Guru BK X</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-large">
                    <i class="fas fa-save"></i> Simpan Data Guru
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>