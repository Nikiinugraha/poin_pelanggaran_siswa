<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database
include ROOTPATH . "/config/config.php";

session_start(); // Memulai session untuk menyimpan data user di server

$username = $_POST["username"];
$password_hash = $_POST["password"];

$query_guru = mysqli_query($conn, "SELECT nama_pengguna, username, password, role FROM guru WHERE username = '$username'");
$query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa, password FROM siswa WHERE nis = '$username'");

if(mysqli_num_rows($query_guru) >= 1){
    $query_guru = mysqli_fetch_assoc($query_guru);
    if(password_verify($password_hash, $query_guru['password'])){
        $_SESSION["nama"] = $query_guru['nama_pengguna'];
        $_SESSION["username"] = $query_guru['username'];
        $_SESSION["role"] = $query_guru['role']; // Role: Guru / Waka Kesiswaan / Kepala Sekolah / dll
        
        header('Location: ../pages/index.php');
        exit;
    }else{
        echo "Password Salah";
    };
}elseif(mysqli_num_rows($query_siswa) >= 1){
    $query_siswa = mysqli_fetch_assoc($query_siswa);
    if(password_verify($password_hash, $query_siswa['password'])){
        $_SESSION["nama"] = $query_siswa['nama_siswa'];
        $_SESSION["username"] = $query_siswa['nis'];
        $_SESSION["role"] = "siswa"; // Default role untuk siswa
        
        header('Location: ../pages/index.php');
        exit;
    }else{
        echo "Password Salah";
    };
}else{
    echo "anda siapa????";
}
?>
