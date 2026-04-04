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

// Cek apakah role adalah siswa
if ($_SESSION['role'] == 'siswa') {
    // Jika siswa mencoba akses halaman guru, tampilkan pesan dan tendang ke dashboard
    echo "<script>
            alert('Akses Ditolak! Siswa hanya bisa melihat halaman Dashboard.');
            window.location.href='/poin_pelanggaran_siswa/pages/index.php';
          </script>";
    exit();
}
?>
