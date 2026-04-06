<?php
// Memulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['role'])) {
    header("Location: /poin_pelanggaran_siswa/login.php");
    exit();
}

// Cek akses cetak (Role 'Guru' dilarang cetak)
if (trim(strtolower($_SESSION['role'])) === 'guru') {
    echo "<script>
            alert('Akses Ditolak! Guru tidak diizinkan melakukan cetak surat.');
            document.addEventListener('DOMContentLoaded', function() {
                window.history.back();
            });
          </script>";
    exit();
}
?>
