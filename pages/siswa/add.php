<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// RBAC Protection: Guru and Wakasek cannot add students
if (in_array(strtolower($_SESSION['role']), ['wakasek', 'guru'])) {
    echo "<script>alert('Akses Ditolak! Guru dan Wakasek tidak diizinkan menambah data siswa.'); window.location.href='list.php';</script>";
    exit();
}
?>


<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/siswa/add_siswa.css">

<div class="container">
    <div class="page-header" style="max-width: 1000px; margin: 0 auto 30px;">
        <h2><i class="fas fa-user-plus"></i> Tambah Data Siswa</h2>
        <a href="list.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
    </div>

    <div class="form-card">
        <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="POST">
            <input type="hidden" name="action" value="add" />

            <!-- Bagian 1: Data Identitas Siswa -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-id-card"></i> Data Identitas Siswa
                </div>
                <div class="form-grid">
                    <!-- Row 1: NIS, Jenis Kelamin, & Kelas (Compact Fields) -->
                    <div class="form-group">
                        <label>Nomor Induk Siswa (NIS)</label>
                        <input type="number" class="form-control" autocomplete="off" name="nis" placeholder="Masukkan NIS" required />
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki - Laki">Laki - Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <datalist id="kelas">
                            <?php
                            $query_kelas = mysqli_query($conn, "SELECT * FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat)");
                            while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                echo "<option value='" . $kelas['tingkat'] . ' ' . $kelas['program_keahlian'] . ' ' . $kelas['rombel'] . "'></option>";
                            }
                            ?>
                        </datalist>
                        <input list="kelas" class="form-control" id="kelas_input" name="kelas" placeholder="Pilih Kelas" autocomplete="off" required />
                    </div>

                    <!-- Row 2: Nama Lengkap (Full Width) -->
                    <div class="form-group" style="grid-column: span 3;">
                        <label>Nama Lengkap Siswa</label>
                        <input type="text" class="form-control" autocomplete="off" name="nama_siswa" placeholder="Masukkan Nama Lengkap" required />
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label>Alamat Lengkap Siswa</label>
                    <textarea name="alamat_siswa" class="form-control" placeholder="Masukkan Alamat Lengkap" autocomplete="off" required></textarea>
                </div>
            </div>

            <!-- Bagian 2: Data Orang Tua / Wali -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-users"></i> Data Orang Tua / Wali
                </div>

                <!-- Info Ayah -->
                <div class="parent-sub-grid">
                    <div class="form-group">
                        <label>Nama Ayah</label>
                        <input type="text" class="form-control" name="ayah" placeholder="Nama Ayah" />
                    </div>
                    <div class="form-group">
                        <label>No. Telpon Ayah</label>
                        <input type="text" class="form-control" name="telp_ayah" placeholder="08xxx" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Ayah</label>
                        <input type="text" class="form-control" name="pekerjaan_ayah" placeholder="Pekerjaan" />
                    </div>
                    <div class="form-group" style="grid-column: span 1;">
                        <label>Alamat Ayah</label>
                        <input type="text" class="form-control" name="alamat_ayah" placeholder="Alamat " />
                    </div>
                </div>

                <!-- Info Ibu -->
                <div class="parent-sub-grid">
                    <div class="form-group">
                        <label>Nama Ibu</label>
                        <input type="text" class="form-control" name="ibu" placeholder="Nama Ibu" />
                    </div>
                    <div class="form-group">
                        <label>No. Telpon Ibu</label>
                        <input type="text" class="form-control" name="telp_ibu" placeholder="08xxx" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Ibu</label>
                        <input type="text" class="form-control" name="pekerjaan_ibu" placeholder="Pekerjaan" />
                    </div>
                    <div class="form-group">
                        <label>Alamat Ibu</label>
                        <input type="text" class="form-control" name="alamat_ibu" placeholder="Alamat " />
                    </div>
                </div>

                <!-- Info Wali -->
                <div class="parent-sub-grid">
                    <div class="form-group">
                        <label>Nama Wali (Opsional)</label>
                        <input type="text" class="form-control" name="wali" placeholder="Nama Wali" />
                    </div>
                    <div class="form-group">
                        <label>No. Telpon Wali</label>
                        <input type="text" class="form-control" name="telp_wali" placeholder="08xxx" />
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Wali</label>
                        <input type="text" class="form-control" name="pekerjaan_wali" placeholder="Pekerjaan" />
                    </div>
                    <div class="form-group">
                        <label>Alamat Wali</label>
                        <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali" />
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-back" style="background:none; border:none; cursor:pointer;">Reset Form</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ROOTPATH . '/includes/footer.php'; ?>