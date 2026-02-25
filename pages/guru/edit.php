<?php
// Menentukan path utama proyek (lokasi folder 'indomaret_RPL4' di dalam web server)
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
?>

<?php

// Memanggil file konfigurasi database (berisi koneksi ke MySQL)
include ROOTPATH . '/config/config.php';

// Memanggil file header agar tampilan atas halaman muncul (judul, menu, dll)
include ROOTPATH . '/includes/header.php';
// Mengecek apakah parameter 'kode_guru' dikirim lewat URL
if (isset($_GET['kode_guru'])) {
    // Jika ada, simpan nilainya ke variabel $kode_guru
    $kode_guru = $_GET['kode_guru'];
} else {
    // Jika tidak ada, beri nilai default 0
    $kode_guru = 0;
}

// Menyiapkan variabel $cashier untuk menampung data kasir
$guru = null;
$kode_guru = $_GET["kode_guru"];
$result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guru WHERE kode_guru = $kode_guru"));

// Jika kode_guru lebih dari 0, lakukan pencarian data kasir di database
if ($kode_guru > 0) {
    // Jalankan query untuk mengambil data kasir berdasarkan id
    $result = mysqli_query($conn, "SELECT * FROM guru WHERE kode_guru= $kode_guru");

    // Jika hasil ditemukan dan ada datanya, simpan ke variabel $cashier
    if ($result && mysqli_num_rows($result) > 0) {
        $guru = mysqli_fetch_assoc($result);
    }
}

// Jika data siswa tidak ditemukan, tampilkan pesan dan hentikan proses
if (!$guru) {
    echo '<p>Data Guru Tidak Ditemukan</p>';
    include ROOTPATH . '/includes/footer.php';  // tampilkan footer
    exit;  // hentikan eksekusi kode
}
?>

<!-- Menengahkan seluruh isi halaman -->
<center>

    <!-- Judul halaman form -->
    <h2>Edit Data <?php echo htmlspecialchars($guru['kode_guru']); ?> </h2>

    <!-- Formulir untuk mengedit data kasir -->
    <!-- action: file tujuan yang memproses data -->
    <!-- method="post": mengirim data secara tersembunyi -->
    <form action="/poin_pelanggaran_siswa/process/guru_process.php" method="POST">

        <!-- Tabel untuk menata posisi input -->
        <table cellpadding="10">

            <!-- Input tersembunyi untuk memberitahu proses adalah 'edit' -->
            <input type="hidden" name="action" value="edit" />

            <!-- Input tersembunyi untuk mengirim ID kasir yang sedang diedit -->
            <input type="hidden" name="kode_guru" value="<?php echo htmlspecialchars($guru['kode_guru']); ?>" />

            <tr>
                <td>
                    <label>Kode Guru</label>
                </td>
                <td>
                    <input type="text" name="kode_guru" autocomplete="off" readonly value="<?php echo htmlspecialchars($guru['kode_guru']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nama Pengguna</label>
                </td>
                <td>
                    <input type="text" name="nama_pengguna" autocomplete="off" value="<?php echo htmlspecialchars($guru['nama_pengguna']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Username</label>
                </td>
                <td>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($guru['username']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Jabatan</label>
                </td>
                <td>
                    <input type="text" name="jabatan" value="<?php echo htmlspecialchars($guru['jabatan']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Telp</label>
                </td>
                <td>
                    <input type="text" name="telp" value="<?php echo htmlspecialchars($guru['telp']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Role</label>
                </td>
                <td>
                    <select name="role" id="" style="width: 100%;">
                        <option <?php if ($guru['role'] == 'Guru') { echo "selected"; } ?> value="Guru">Guru</option>
                        <option <?php if ($guru['role'] == 'BK') { echo "selected"; } ?> value="BK">BK</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Status</label>
                </td>
                <td>
                    <?php $status = $guru['aktif'] == 'Y' ? 'Aktif' : 'Tidak Aktif'; ?>
                    <select name="aktif" id="" style="width: 100%;">
                        <option value="Y">Aktif</option>
                        <option value="N">Tidak Aktif</option>
                    </select>
                </td>
            </tr>
            <!-- Baris kedua: tombol untuk menyimpan perubahan -->
            <tr>
                <td></td>
                <td>
                    <!-- Tombol untuk mengirim data ke file proses -->
                    <button type="submit" style="float:right">Simpan Perubahan</button>
                </td>
            </tr>
        </table>
    </form>

</center>

<?php
// Menyertakan footer agar bagian bawah halaman tampil
include ROOTPATH . '/includes/footer.php';
?>

<!-- 
💡 Ringkasan Fungsi Kode:
	1.	Bagian awal (PHP atas)
        🔹 Menentukan lokasi proyek, menyambung ke database, dan memanggil header.
        🔹 Mengecek apakah ada parameter id di URL.
        🔹 Mengambil data kasir dari tabel kasir berdasarkan id.
	2.	Bagian HTML (form edit)
        🔹 Menampilkan form dengan data kasir yang sudah ada.
        🔹 User bisa mengubah nama kasir dan menekan tombol Update.
	3.	Bagian akhir (PHP bawah)
        🔹 Menampilkan footer dan mengakhiri halaman.

Dengan struktur ini, halaman edit.php berfungsi untuk menampilkan data kasir yang akan diedit, dan setelah tombol Update ditekan, data dikirim ke cashiers_process.php untuk diproses di server. 
-->