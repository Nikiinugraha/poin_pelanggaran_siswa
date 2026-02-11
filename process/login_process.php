<?php
// Mendefinisikan path root aplikasi untuk keperluan include file
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menginclude file konfigurasi database
include ROOTPATH . "/config/config.php";

// Kode debugging yang di-comment (untuk testing input dan hash password)
// echo $_POST['username'];
// echo "<br>";
// echo $_POST['password'];
// echo "<br>";
// $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// echo $password_hash;
// echo "<br>";

// Mengambil input username dan password dari form login
$username = $_POST["username"];
$password_hash = $_POST['password']; // Password yang diinput user (belum di-hash)

// Query untuk mencari data guru berdasarkan username
$query_guru = mysqli_query($conn, "SELECT nama_pengguna, username, password FROM guru WHERE username = '$username'");

// Query untuk mencari data siswa berdasarkan NIS (Nomor Induk Siswa)
$query_siswa = mysqli_query($conn, "SELECT nama_siswa, nis, password FROM siswa WHERE nis = '$username'");

// Cek apakah username ditemukan di tabel guru
if (mysqli_num_rows($query_guru) >= 1) {
    // Ambil data guru dari hasil query
    $query_guru = mysqli_fetch_assoc($query_guru);

    // Verifikasi password input dengan password yang sudah di-hash di database
    if (password_verify($password_hash, $query_guru['password'])) {
        // Set cookie untuk session guru (berlaku 1 jam)
        SETCOOKIE("nama", $query_guru['nama_pengguna'], time() + 3600, '/');
        SETCOOKIE("username", $query_guru['username'], time() + 3600, '/');

        // Redirect ke halaman dashboard
        header("Location: ../pages/index.php");
        exit(); // Stop eksekusi script
    } else {
        echo "Password Invalid"; // Password salah
    }
}
// Cek apakah username (NIS) ditemukan di tabel siswa
else if (mysqli_num_rows($query_siswa) >= 1) {
    // Ambil data siswa dari hasil query
    $query_siswa = mysqli_fetch_assoc($query_siswa);

    // Verifikasi password input dengan password yang sudah di-hash di database
    if (password_verify($password_hash, $query_siswa['password'])) {
        // Set cookie untuk session siswa (berlaku 1 jam)
        SETCOOKIE("nama", $query_siswa['nama_siswa'], time() + 3600, '/');
        SETCOOKIE("username", $query_siswa['nis'], time() + 3600, '/');

        // Redirect ke halaman dashboard
        header("Location: ../pages/index.php");
        exit(); // Stop eksekusi script
    } else {
        echo "Password Invalid"; // Password salah
    }
}
// Jika username tidak ditemukan di kedua tabel
else {
    echo "Gagal Login"; // Username tidak terdaftar
}
