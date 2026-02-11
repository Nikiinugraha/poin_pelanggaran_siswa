<?php

/**
 * Header File - Aplikasi Poin Pelanggaran Siswa
 *
 * Fungsi:
 * 1. Melakukan pengecekan session/login user
 * 2. Menampilkan navigasi utama aplikasi
 * 3. Mengalihkan user yang belum login ke halaman login
 *
 * File ini di-include oleh semua halaman yang memerlukan proteksi login
 */

// Pengecekan session menggunakan cookie
// Jika cookie username tidak ada, berarti user belum login
if (!isset($_COOKIE['username'])) {
    // Tampilkan alert dan redirect ke halaman login
    echo '<script>alert("Anda harus login terlebih dahulu"); window.location.href="/poin_pelanggaran_siswa/login.php";</script>';
    exit();  // Hentikan eksekusi script lebih lanjut
}
// Jika cookie ada, lanjutkan ke halaman yang diminta
?>
<body>
    <header>
        <h1 style="text-align:center;">Aplikasi Poin Pelanggaran Siswa</h1>
        <nav>
            <ul>
                <li><a href="/poin_pelanggaran_siswa/pages/index.php">Dashboard</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/siswa/list.php">Data Siswa</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/data_pelanggaran/list.php">Data Pelanggaran</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/entri_pelanggaran/list.php">Entri Pelanggaran</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/entri_pelanggaran/list.php">Cetak Surat</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/entri_pelanggaran/list.php">Laporan</a></li>
                <li><a href="/poin_pelanggaran_siswa/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>