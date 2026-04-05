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
    <title>Sistem Poin Pelanggaran Siswa</title>
    <!-- Memanggil library ikon FontAwesome (v6+) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/poin_pelanggaran_siswa/css/components/header.css">
</head>
<body class="sidebar-layout">
    
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <span>SPPS</span>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo $_SESSION['nama']; ?></p>
                <p class="user-role"><?php echo ucfirst($_SESSION['role']); ?></p>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Menu Utama</li>
            <li>
                <a href="/poin_pelanggaran_siswa/pages/index.php">
                    <i class="fas fa-house-chimney"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Menu Khusus Guru / Guru BK -->
            <?php if ($_SESSION['role'] != 'siswa'): ?>
                <li class="menu-label">Manajemen Data</li>
                <li class="has-submenu">
                    <a href="javascript:void(0)" class="submenu-toggle">
                        <i class="fas fa-database"></i>
                        <span>Data Master</span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/poin_pelanggaran_siswa/pages/siswa/list.php">Data Siswa</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/guru/list.php">Data Guru</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php">Data Pelanggaran</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/kelas/list.php">Data Kelas</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/poin_pelanggaran_siswa/pages/pelanggaran/add.php">
                        <i class="fas fa-file-circle-plus"></i>
                        <span>Entri Pelanggaran</span>
                    </a>
                </li>
                <li class="has-submenu">
                    <a href="javascript:void(0)" class="submenu-toggle">
                        <i class="fas fa-file-lines"></i>
                        <span>Laporan</span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pelanggaran.php">Laporan Pelanggaran</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_panggilan_ortu.php">Panggilan Ortu</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_perjanjian.php">Surat Perjanjian</a></li>
                        <li><a href="/poin_pelanggaran_siswa/pages/laporan/list_pindah.php">Surat Pindah</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="menu-label">Akun</li>
            <li>
                <a href="/poin_pelanggaran_siswa/process/profil_process.php?action=edit">
                    <i class="fas fa-user-gear"></i>
                    <span>Pengaturan Profil</span>
                </a>
            </li>
            <li>
                <a href="/poin_pelanggaran_siswa/logout.php" class="logout-link">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Keluar dari Sistem</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <header class="top-bar">
            <h2><?php echo isset($page_title) ? $page_title : 'Beranda'; ?></h2>
        </header>
        <main>
        
        <!-- Sidebar Interaction Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const submenuToggles = document.querySelectorAll('.submenu-toggle');
                
                submenuToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        const parent = this.parentElement;
                        const isOpening = !parent.classList.contains('active');
                        
                        // Close other sibling submenus
                        const siblings = parent.parentElement.querySelectorAll('.has-submenu');
                        siblings.forEach(sibling => {
                            if (sibling !== parent) sibling.classList.remove('active');
                        });
                        
                        // Toggle current submenu
                        parent.classList.toggle('active');
                    });
                });
            });
        </script>
