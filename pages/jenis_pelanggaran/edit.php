<?php
// Menentukan path utama proyek (lokasi folder 'indomaret_RPL4' di dalam web server)
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
?>

<?php

// Memanggil file konfigurasi database (berisi koneksi ke MySQL)
include ROOTPATH . '/config/config.php';

// Memanggil file header agar tampilan atas halaman muncul (judul, menu, dll)
include ROOTPATH . '/includes/header.php';

if (isset($_GET['id_jenis_pelanggaran'])) {
    // Jika ada, simpan nilainya ke variabel $kode_guru
    $id_jenis_pelanggaran = $_GET['id_jenis_pelanggaran'];
} else {
    // Jika tidak ada, beri nilai default 0
    $id_jenis_pelanggaran = 0;
}

$jenis_pelanggaran = null;
$result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jenis_pelanggaran WHERE id_jenis_pelanggaran = $id_jenis_pelanggaran"));



?>

<h2>Edit Data Pelanggaran</h2>

<form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="POST">
    <input type="hidden" value="<?= $result['id_jenis_pelanggaran'] ?>" name="id_jenis_pelanggaran">
    <input type="hidden" value="edit" name="action">
    <table cellpadding="10">
        <tr>
            <td>
            <label for="jenis">Jenis Pelanggaran</label>
            </td>
            <td>
                <input type="text" name="jenis" value="<?= $result['jenis'] ?>">
            </td>
        </tr>
        <tr>
            <td>
            <label for="poin">Poin</label>
            </td>
            <td>
                <input type="text" name="poin" value="<?= $result['poin'] ?>">
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="Edit Jenis Pelanggaran">
            </td>
        </tr>

</table>
</form>

<?php include ROOTPATH . '/includes/footer.php'; ?>