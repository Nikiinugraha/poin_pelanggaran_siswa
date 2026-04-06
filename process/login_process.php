<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database (session sudah dimulai di sini)
include ROOTPATH . "/config/config.php";

$username = $_POST["username"];
$password_plain = $_POST["password"];

/** 
 * KEAMANAN TINGKAT TINGGI: PREPARED STATEMENTS 
 * Pendekatan ini mencegah SQL Injection - serangan paling umum 
 * yang dilakukan dengan menyuntikkan query jahat di kolom login.
 */

// 1. CEK GURU/BK/WAKASEK
$stmt_guru = mysqli_prepare($conn, "SELECT nama_pengguna, username, password, role, jabatan FROM guru WHERE username = ?");
mysqli_stmt_bind_param($stmt_guru, "s", $username);
mysqli_stmt_execute($stmt_guru);
$res_guru = mysqli_stmt_get_result($stmt_guru);

if(mysqli_num_rows($res_guru) >= 1){
    $data_guru = mysqli_fetch_assoc($res_guru);
    if(password_verify($password_plain, $data_guru['password'])){
        
        $role = $data_guru['role'];
        // Jika data di database sebelumnya masih mencatat role sebagai 'Guru' namun jabatannya merupakan BK,
        // berikan akses role 'bk' agar bisa mengakses ke seluruh fitur page dan sistem.
        if (strtolower(trim($role)) === 'guru' && stripos($data_guru['jabatan'], 'bk') !== false) {
            $role = 'bk';
        }

        // Login Berhasil - Data 'role' disimpan secara AMAN di server (SESSION)
        $_SESSION["nama"] = $data_guru['nama_pengguna'];
        $_SESSION["username"] = $data_guru['username'];
        $_SESSION["role"] = $role;
        
        header('Location: ../pages/index.php');
        exit;
    } else {
        echo "<script>alert('Password Salah!'); window.location.href='../login.php';</script>";
        exit;
    }
}

// 2. CEK SISWA (Hanya dieksekusi jika tidak ditemukan di guru)
$stmt_siswa = mysqli_prepare($conn, "SELECT nis, nama_siswa, password FROM siswa WHERE nis = ?");
mysqli_stmt_bind_param($stmt_siswa, "s", $username);
mysqli_stmt_execute($stmt_siswa);
$res_siswa = mysqli_stmt_get_result($stmt_siswa);

if(mysqli_num_rows($res_siswa) >= 1){
    $data_siswa = mysqli_fetch_assoc($res_siswa);
    if(password_verify($password_plain, $data_siswa['password'])){
        // Login Berhasil - Siswa tidak bisa memalsukan role 'admin/bk' lagi lewat browser
        $_SESSION["nama"] = $data_siswa['nama_siswa'];
        $_SESSION["username"] = $data_siswa['nis'];
        $_SESSION["role"] = "siswa"; 
        
        header('Location: ../pages/index.php');
        exit;
    } else {
        echo "<script>alert('Password Salah!'); window.location.href='../login.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('User tidak ditemukan!'); window.location.href='../login.php';</script>";
    exit;
}
