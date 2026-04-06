<?php
/** 
 * KONFIGURASI PERSISTENT SESSION (30 HARI)
 * Harus dipanggil sebelum session_start()
 */
$session_lifetime = 86400 * 30;
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "poin_pelanggaran_siswa";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}