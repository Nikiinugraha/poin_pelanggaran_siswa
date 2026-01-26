<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "poin_pelanggaran_siswa";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $password, $dbname);
// $conn = mysqli_connect('localhost', 'root', '', 'indomaret_rpl4');


// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>


