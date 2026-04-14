<?php
/**
 * Sistem Poin Pelanggaran Siswa
 * File: pages/index.php
 * Deskripsi: Halaman utama dashboard setelah login
 */

$page_title = "Dashboard Utama";
include "../config/config.php";
include "../includes/header.php";

// Definisikan Informasi Login
$username = $_SESSION['username']; // Ini adalah NIS bagi siswa
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];

// ---------------------------------------------------------------------------------------
// LOGIKA UMUM DASHBOARD (Berlaku untuk semua Role)
// ---------------------------------------------------------------------------------------

// Kumpulan Pesan Motivasi (Dynamic Quote dari Tokoh Terkenal)
$quotes = [
    ["text" => "Kedisiplinan adalah jembatan antara cita-cita dan pencapaian.", "author" => "Jim Rohn"],
    ["text" => "Kejujuran adalah bab pertama dalam buku kebijaksanaan.", "author" => "Thomas Jefferson"],
    ["text" => "Masa depanmu ditentukan oleh apa yang kamu lakukan hari ini, bukan besok.", "author" => "Robert Kiyosaki"],
    ["text" => "Integritas adalah melakukan hal yang benar, bahkan ketika tidak ada orang yang melihat.", "author" => "C.S. Lewis"],
    ["text" => "Pendidikan adalah senjata paling mematikan untuk mengubah dunia.", "author" => "Nelson Mandela"],
    ["text" => "Kita adalah apa yang kita kerjakan berulang-ulang. Keunggulan bukanlah tindakan, melainkan kebiasaan.", "author" => "Aristoteles"],
    ["text" => "Jenius adalah 1% inspirasi dan 99% kerja keras.", "author" => "Thomas Alva Edison"],
    ["text" => "Hiduplah seolah kamu akan mati besok. Belajarlah seolah kamu akan hidup selamanya.", "author" => "Mahatma Gandhi"]
];

//Mengambil index secara acak dengan tiper array : array_rand
$random_quote = $quotes[array_rand($quotes)];

// Data Kalender
$months = ["Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "Mei", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Agu", "Sep" => "Sep", "Oct" => "Okt", "Nov" => "Nov", "Dec" => "Des"];
$days = ["Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu"];

//membuat variabel untuk menampung bulan dan hari
//date('M') untuk mengambil bulan dalam format 3 huruf
//date('l') untuk mengambil hari dalam format lengkap
$curr_month = $months[date('M')];
$curr_day = $days[date('l')];

?>

<!-- Link Dashboard CSS -->
<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/dashboard.css">

<div class="dashboard-container">
    
    <!-- Welcome Section (Global untuk Siswa & Guru) -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <h1>Selamat Datang, <?php echo $nama; ?>! 👋</h1>
            <p>
                <?php if ($role === 'siswa'): ?>
                    Tetaplah mematuhi peraturan sekolah untuk menjaga rekam jejak kedisiplinan Anda.
                <?php else: ?>
                    Mari bersama-sama membangun karakter disiplin dan integritas untuk masa depan siswa yang lebih baik.
                <?php endif; ?>
            </p>
            
            <!-- Pesan Motivasi Dinamis -->
            <div class="quote-box">
                <i class="fas fa-quote-left"></i>
                <div class="quote-content">
                    <p class="quote-text">"<?php echo $random_quote['text']; ?>"</p>
                    <span class="quote-author">— <?php echo $random_quote['author']; ?></span>
                </div>
            </div>
        </div>

        <div class="calendar-widget">
            <div class="cal-header">
                <?php echo $curr_month; ?>
            </div>
            <div class="cal-body">
                <span class="cal-date"><?php echo date('d'); ?></span>
                <span class="cal-day"><?php echo $curr_day; ?></span>
            </div>
        </div>
    </div>

    <?php 
    // ---------------------------------------------------------------------------------------
    // KONTEN DASHBOARD SISWA
    // ---------------------------------------------------------------------------------------
    if ($role === 'siswa'): 
        // 1. Ambil Total Poin Akumulasi
        $q_poin = mysqli_query($conn, "
            SELECT SUM(j.poin) as total 
            FROM pelanggaran_siswa p 
            JOIN jenis_pelanggaran j ON p.id_jenis_pelanggaran = j.id_jenis_pelanggaran 
            WHERE p.nis = '$username'
        ");
        $data_poin = mysqli_fetch_assoc($q_poin);
        $total_poin = $data_poin['total'] ?? 0;

        // 2. Ambil Frekuensi Pelanggaran (Jumlah Kasus)
        $q_kasus = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggaran_siswa WHERE nis = '$username'");
        $data_kasus = mysqli_fetch_assoc($q_kasus);
        $total_kasus = $data_kasus['total'] ?? 0;

        // 3. Tentukan Status Berdasarkan Poin
        if ($total_poin < 50) {
            $status_label = "Sangat Baik";
            $status_class = "status-safe";
        } elseif ($total_poin < 100) {
            $status_label = "Peringatan";
            $status_class = "status-warning";
        } else {
            $status_label = "Sanksi Berat";
            $status_class = "status-danger";
        }

        // 4. Ambil Data Orang Tua / Wali
        $q_ortu = mysqli_query($conn, "
            SELECT o.ayah, o.ibu, o.wali, o.no_telp_ayah, o.no_telp_ibu, o.no_telp_wali 
            FROM siswa s 
            JOIN ortu_wali o ON s.id_ortu_wali = o.id_ortu_wali 
            WHERE s.nis = '$username'
        ");
        $data_ortu = mysqli_fetch_assoc($q_ortu);
        $nama_ortu = !empty($data_ortu['ayah']) ? $data_ortu['ayah'] : (!empty($data_ortu['ibu']) ? $data_ortu['ibu'] : $data_ortu['wali']);

        // 5. Ambil Daftar 5 Pelanggaran Terakhir
        $q_recent = mysqli_query($conn, "
            SELECT p.tanggal, j.jenis, j.poin, p.keterangan 
            FROM pelanggaran_siswa p 
            JOIN jenis_pelanggaran j ON p.id_jenis_pelanggaran = j.id_jenis_pelanggaran 
            WHERE p.nis = '$username' 
            ORDER BY p.tanggal DESC 
            LIMIT 5
        ");
    ?>
        
        <!-- Statistik Cards SISWA -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-red">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <span>Total Poin</span>
                    <h3><?php echo $total_poin; ?> Poin</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-orange">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <span>Jumlah Kejadian</span>
                    <h3><?php echo $total_kasus; ?> Kasus</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-shield-heart"></i>
                </div>
                <div class="stat-info">
                    <span>Status Kedisiplinan</span>
                    <h3 class="<?php echo $status_class; ?>"><?php echo $status_label; ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-info">
                    <span>Orang Tua</span>
                    <h3><?php echo $nama_ortu ?: 'Belum Terdaftar'; ?></h3>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card data-table-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Rekam Jejak Pelanggaran Terakhir</h3>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Deskripsi Pelanggaran</th>
                                <th style="text-align: center;">Poin</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($q_recent) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($q_recent)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><strong><?php echo $row['jenis']; ?></strong></td>
                                        <td align="center"><span class="badge badge-red"><?php echo $row['poin']; ?></span></td>
                                        <td><?php echo $row['keterangan'] ?: '-'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" align="center" style="padding: 40px; color: #94a3b8;">
                                        <i class="fas fa-check-circle" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #10b981;"></i>
                                        Sempurna! Anda belum memiliki catatan pelanggaran.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php 
    
    // ---------------------------------------------------------------------------------------
    // KONTEN DASHBOARD GURU / BK
    // ---------------------------------------------------------------------------------------
    else: 

        // 1. Ambil Statistik Global
        $q_total_siswa = mysqli_query($conn, "SELECT COUNT(*) as total_siswa_aktif FROM siswa WHERE status='aktif'");
        $cnt_siswa = mysqli_fetch_assoc($q_total_siswa)['total_siswa_aktif'];
        
        $q_total_pelanggaran = mysqli_query($conn, "SELECT COUNT(*) as total_pelanggaran FROM pelanggaran_siswa");
        $cnt_pelanggaran = mysqli_fetch_assoc($q_total_pelanggaran)['total_pelanggaran'];

        // Siswa dengan poin >= 100 (Butuh Tindakan)
        $q_warning = mysqli_query($conn, "
            SELECT COUNT(*) as total FROM (
                SELECT nis FROM pelanggaran_siswa 
                JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) 
                GROUP BY nis HAVING SUM(poin) >= 100
            ) as sub
        ");
        $cnt_warning = mysqli_fetch_assoc($q_warning)['total'] ?? 0;

        // Total Jenis Pelanggaran
        $q_jenis = mysqli_query($conn, "SELECT COUNT(*) as total FROM jenis_pelanggaran");
        $cnt_jenis = mysqli_fetch_assoc($q_jenis)['total'] ?? 0;

        // 2. Ambil 5 Pelanggaran Terbaru Global
        $q_global_recent = mysqli_query($conn, "
            SELECT p.tanggal, s.nama_siswa, j.jenis, j.poin, s.nis
            FROM pelanggaran_siswa p 
            JOIN siswa s USING(nis)
            JOIN jenis_pelanggaran j USING(id_jenis_pelanggaran)
            ORDER BY p.tanggal DESC 
            LIMIT 5
        ");
    ?>
        
        <!-- Statistik Cards GURU -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-info">
                    <span>Total Siswa Aktif</span>
                    <h3><?php echo $cnt_siswa; ?> Orang</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-red">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="stat-info">
                    <span>Total Pelanggaran</span>
                    <h3><?php echo $cnt_pelanggaran; ?> Kejadian</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-orange">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <span>Siswa Butuh Perhatian</span>
                    <h3 class="status-danger"><?php echo $cnt_warning; ?> Siswa</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="stat-info">
                    <span>Kategori Pelanggaran</span>
                    <h3><?php echo $cnt_jenis; ?> Jenis</h3>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card data-table-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock-rotate-left"></i> Monitoring Pelanggaran Terbaru</h3>
                    <a href="/poin_pelanggaran_siswa/pages/laporan/list_pelanggaran.php" class="btn-more">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nama Siswa</th>
                                <th>Tindakan Pelanggaran</th>
                                <th style="text-align: center;">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($q_global_recent) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($q_global_recent)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                        <td>
                                            <strong><?php echo $row['nama_siswa']; ?></strong><br>
                                            <small style="color: #64748b;"><?php echo $row['nis']; ?></small>
                                        </td>
                                        <td><?php echo $row['jenis']; ?></td>
                                        <td align="center"><span class="badge badge-red"><?php echo $row['poin']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" align="center" style="padding: 40px; color: #94a3b8;">Belum ada data pelanggaran yang masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>


<?php include "../includes/footer.php"; ?>