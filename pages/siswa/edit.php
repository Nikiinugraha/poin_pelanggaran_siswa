<?php
// Menentukan path utama proyek (lokasi folder 'indomaret_RPL4' di dalam web server)
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
?>

<?php

// Memanggil file konfigurasi database (berisi koneksi ke MySQL)
include ROOTPATH . '/config/config.php';

// Memanggil file header agar tampilan atas halaman muncul (judul, menu, dll)
include ROOTPATH . '/includes/header.php';
// Mengecek apakah parameter 'nis' dikirim lewat URL
if (isset($_GET['nis'])) {
    // Jika ada, simpan nilainya ke variabel $nis
    $nis = $_GET['nis'];
} else {
    // Jika tidak ada, beri nilai default 0
    $nis = 0;
}

// Menyiapkan variabel $cashier untuk menampung data kasir
$siswa = null;

// Jika nis lebih dari 0, lakukan pencarian data kasir di database
if ($nis > 0) {
    // Jalankan query untuk mengambil data kasir berdasarkan id
    $result = mysqli_query($conn, "SELECT * FROM siswa WHERE nis= $nis");

    // Jika hasil ditemukan dan ada datanya, simpan ke variabel $cashier
    if ($result && mysqli_num_rows($result) > 0) {
        $siswa = mysqli_fetch_assoc($result);
    }
}

// Jika data siswa tidak ditemukan, tampilkan pesan dan hentikan proses
if (!$siswa) {
    echo '<p>Siswa not found.</p>';
    include ROOTPATH . '/includes/footer.php';  // tampilkan footer
    exit;  // hentikan eksekusi kode
}
?>

<!-- Menengahkan seluruh isi halaman -->
<center>

    <!-- Judul halaman form -->
    <h2>Edit Siswa <?php echo htmlspecialchars($siswa['nis']); ?> </h2>

    <!-- Formulir untuk mengedit data kasir -->
    <!-- action: file tujuan yang memproses data -->
    <!-- method="post": mengirim data secara tersembunyi -->
    <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="post">

        <!-- Tabel untuk menata posisi input -->
        <table cellpadding="10">

            <!-- Input tersembunyi untuk memberitahu proses adalah 'edit' -->
            <input type="hidden" name="action" value="edit" />

            <!-- Input tersembunyi untuk mengirim ID kasir yang sedang diedit -->
            <input type="hidden" name="nis" value="<?php echo htmlspecialchars($siswa['nis']); ?>" />

            <tr>
                <td>
                    <label for="nis">NIS</label>
                    <input type="text" name="nis" value="<?php echo htmlspecialchars($siswa['nis']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="nama_siswa">Nama</label>
                    <input type="text" name="nama_siswa" value="<?php echo htmlspecialchars($siswa['nama_siswa']); ?>" required />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                </td>
            </tr>
            <tr>
                <td><input type="radio" name="jenis_kelamin" value="Laki - Laki" <?php echo ($siswa['jenis_kelamin'] == 'Laki - Laki') ? 'checked' : ''; ?> required />Laki - Laki
                    <input type="radio" name="jenis_kelamin" value="Perempuan" <?php echo ($siswa['jenis_kelamin'] == 'Perempuan') ? 'checked' : ''; ?> required />Perempuan
                </td>
            </tr>
            <tr>
                <td>
                    <label for="alamat">Alamat</label>
                    <input type="text" name="alamat" value="<?php echo htmlspecialchars($siswa['alamat']); ?>" required />
                </td>
            </tr>
            <!-- Baris kedua: tombol untuk menyimpan perubahan -->
            <tr>
                <td>
                    <!-- Tombol untuk mengirim data ke file proses -->
                    <button type="submit" style="float:right">Update</button>
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