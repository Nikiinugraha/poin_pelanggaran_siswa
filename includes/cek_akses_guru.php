<?php
/** 
 * PROTEKSI AKSES KHUSUS GURU/STAF 
 * File ini mengandalkan config.php untuk session-nya.
 */

// Cek apakah user sudah login dan perannya bukan siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'siswa') {
    echo "<script>
            alert('Akses Ditolak! Halaman ini hanya untuk Guru/BK.');
            window.location.href='/poin_pelanggaran_siswa/pages/index.php';
          </script>";
    exit();
}
?>
