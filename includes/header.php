<?php
// Memulai session untuk pengecekan login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengecekan session - Jika tidak ada session username, berarti belum login
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Anda harus login terlebih dahulu"); window.location.href="/poin_pelanggaran_siswa/login.php";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Poin Pelanggaran Siswa</title>
    <link rel="stylesheet" href="/poin_pelanggaran_siswa/css/components/header.css">
</head>
<body>
    <header>
        <h1>Aplikasi Poin Pelanggaran Siswa</h1>
        <nav>
            <ul>
                <li><a href="/poin_pelanggaran_siswa/pages/index.php">Dashboard</a></li>

                <!-- Menu Khusus Guru / Guru BK (Siswa tidak boleh melihat ini) -->
                <?php if ($_SESSION['role'] != 'siswa'): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Data Master</a>
                        <ul class="dropdown-menu">
                            <li><a href="/poin_pelanggaran_siswa/pages/siswa/list.php">Data Siswa</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/guru/list.php">Data Guru</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php">Data Pelanggaran</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/kelas/list.php">Data Kelas</a></li>
                        </ul>
                    </li>
                    <li><a href="/poin_pelanggaran_siswa/pages/pelanggaran/add.php">Entri Pelanggaran</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Laporan</a>
                        <ul class="dropdown-menu">
                            <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pelanggaran.php">Laporan Pelanggaran Siswa</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_panggilan_ortu.php">Laporan Surat Panggilan Ortu</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_perjanjian.php">Laporan Surat Perjanjian</a></li>
                            <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pindah.php">Laporan Surat Pindah Sekolah</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><a href="/poin_pelanggaran_siswa/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
