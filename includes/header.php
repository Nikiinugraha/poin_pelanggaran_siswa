<?php
/** 
 * CEK AKSES & SIDEBAR UI - RBAC SYSTEM
 * Sistem ini membedakan tampilan menu antara Guru/Staf dan Siswa.
 */

// Pengecekan Session: Redirect ke Login jika belum ada User
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Sesi Anda berakhir, silakan masuk kembali."); window.location.href="/poin_pelanggaran_siswa/login.php";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/poin_pelanggaran_siswa/css/components/header.css">
</head>
<body class="sidebar-layout">
    
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <span>SPPS DIGITAL</span>
        </div>

        <!-- Identitas User di Sidebar -->
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Tanpa Nama'); ?></p>
                <p class="user-role"><?php echo ucfirst(htmlspecialchars($_SESSION['role'] ?? 'Peran Tidak Diketahui')); ?></p>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Navigasi Utama</li>
            <li>
                <a href="/poin_pelanggaran_siswa/pages/index.php">
                    <i class="fas fa-house"></i>
                    <span>Dashboard Utama</span>
                </a>
            </li>

            <!-- MENU KHUSUS GURU, BK, DAN STAF (RBAC GURU FULL ACCESS) -->
            <?php if ($_SESSION['role'] !== 'siswa'): ?>
                
                <li class="menu-label">Manajemen Kedisiplinan</li>
                <li>
                    <a href="/poin_pelanggaran_siswa/pages/pelanggaran/add.php">
                        <i class="fas fa-file-circle-plus"></i>
                        <span>Input Pelanggaran</span>
                    </a>
                </li>

                <!-- Data Master -->
                <li class="menu-label">Master Data</li>
                <li><a href="/poin_pelanggaran_siswa/pages/siswa/list.php"><i class="fas fa-users"></i> Data Siswa</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/guru/list.php"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/kelas/list.php"><i class="fas fa-school"></i> Data Kelas</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php"><i class="fas fa-list-check"></i> Kategori Pelanggaran</a></li>

                <!-- Laporan & Surat -->
                <li class="menu-label">Pusat Laporan</li>
                <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pelanggaran.php"><i class="fas fa-clipboard-list"></i> Rekap Pelanggaran</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_panggilan_ortu.php"><i class="fas fa-envelope-open-text"></i> Panggilan Ortu</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_perjanjian.php"><i class="fas fa-file-contract"></i> Surat Perjanjian</a></li>
                <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pindah.php"><i class="fas fa-file-export"></i> Surat Pindah</a></li>
                
            <?php endif; ?>

            <!-- MENU UNTUK SEMUA ROLE (Siswa & Guru) -->
            <li class="menu-label">Pengaturan Akun</li>
            <li>
                <a href="/poin_pelanggaran_siswa/process/profil_process.php?action=edit">
                    <i class="fas fa-user-gear"></i>
                    <span>Ubah Profil / Sandi</span>
                </a>
            </li>
            <li>
                <a href="/poin_pelanggaran_siswa/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar / Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <header class="top-bar">
            <h2><?php echo $page_title ?? 'Beranda'; ?></h2>
        </header>
        <main>
