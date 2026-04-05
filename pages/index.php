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
// LOGIKA DASHBOARD SISWA (Login sebagai Siswa)
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

    // 3. Tentukan Status Berdasarkan Poin (Contoh Rule: < 50 Bagus, 50-100 Peringatan, > 100 Bahaya)
    if ($total_poin < 50) {
        $status_label = "Sangat Baik";
        $status_class = "status-safe";
    } elseif ($total_poin <= 150) {
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
    $telp_ortu = !empty($data_ortu['no_telp_ayah']) ? $data_ortu['no_telp_ayah'] : (!empty($data_ortu['no_telp_ibu']) ? $data_ortu['no_telp_ibu'] : $data_ortu['no_telp_wali']);

    // 5. Ambil Daftar 5 Pelanggaran Terakhir
    $q_recent = mysqli_query($conn, "
        SELECT p.tanggal, j.jenis, j.poin, p.keterangan 
        FROM pelanggaran_siswa p 
        JOIN jenis_pelanggaran j ON p.id_jenis_pelanggaran = j.id_jenis_pelanggaran 
        WHERE p.nis = '$username' 
        ORDER BY p.tanggal DESC 
    ");
    // 6. Kumpulan Pesan Motivasi (Dynamic Quote dari Tokoh Terkenal)
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
    $random_quote = $quotes[array_rand($quotes)];
?>


<div class="dashboard-container">
    <div class="welcome-banner">
        <div class="welcome-text">
            <h1>Selamat Datang, <?php echo $nama; ?>! 👋</h1>
            <p>Tetaplah mematuhi peraturan sekolah untuk menjaga rekam jejak kedisiplinan Anda.</p>
            
            <!-- Pesan Motivasi Dinamis -->
            <div class="quote-box">
                <i class="fas fa-quote-left"></i>
                <div class="quote-content">
                    <p class="quote-text">"<?php echo $random_quote['text']; ?>"</p>
                    <span class="quote-author">— <?php echo $random_quote['author']; ?></span>
                </div>
            </div>
        </div>
        <!-- Dynamic Calendar Widget (Localized Indonesian) -->
        <?php
            $months = ["Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "Mei", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Agu", "Sep" => "Sep", "Oct" => "Okt", "Nov" => "Nov", "Dec" => "Des"];
            $days = ["Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu"];
            $curr_month = $months[date('M')];
            $curr_day = $days[date('l')];
        ?>
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

    <!-- Statistik Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <span>Total Poin Saat Ini</span>
                <h3><?php echo $total_poin; ?> Poin</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-orange">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <span>Jumlah Kasus</span>
                <h3><?php echo $total_kasus; ?> Kejadian</h3>
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
                <span>Orang Tua / Wali</span>
                <h3><?php echo $nama_ortu ?: 'Belum Terdaftar'; ?></h3>
            </div>
        </div>
    </div>

    <!-- Riwayat Terbaru -->
    <div class="dashboard-grid">
        <div class="card data-table-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Riwayat Pelanggaran Terakhir</h3>            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_recent) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($q_recent)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td><strong><?php echo $row['jenis']; ?></strong></td>
                                    <td><span class="badge badge-red"><?php echo $row['poin']; ?> Poin</span></td>
                                    <td><?php echo $row['keterangan']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Alhamdulillah, tidak ada catatan pelanggaran.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Styling */
.dashboard-container { animation: fadeIn 0.8s ease-out; }

.welcome-banner {
    background: linear-gradient(135deg, #1C6EA4 0%, #144E75 100%);
    color: white;
    padding: 30px;
    border-radius: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.welcome-banner h1 { margin: 0; font-size: 1.8rem; font-weight: 800; }
.welcome-banner p { margin: 10px 0 0; opacity: 0.9; font-size: 1rem; }

/* Dynamic Calendar Widget */
.calendar-widget {
    background: white;
    width: 90px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
    animation: slideInRight 1s ease-out;
}

.cal-header {
    background: #ef4444; /* Calendar red */
    color: white;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 6px 0;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

.cal-body {
    padding: 10px 0;
    display: flex;
    flex-direction: column;
    color: #0f172a;
}

.cal-date {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1;
}

.cal-day {
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    margin-top: 4px;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Quote Box Styling */
.quote-box {
    margin-top: 25px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    padding: 15px 20px;
    border-radius: 15px;
    display: flex;
    gap: 15px;
    align-items: flex-start;
    max-width: 600px;
}

.quote-box i {
    font-size: 1.2rem;
    opacity: 0.6;
    margin-top: 5px;
}

.quote-text {
    margin: 0;
    font-style: italic;
    font-size: 0.95rem;
    line-height: 1.5;
    font-weight: 500;
}

.quote-author {
    display: block;
    margin-top: 8px;
    font-size: 0.75rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 35px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: transform 0.3s ease;
}

.stat-card:hover { transform: translateY(-5px); }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.icon-red { background: #fee2e2; color: #ef4444; }
.icon-orange { background: #ffedd5; color: #f97316; }
.icon-blue { background: #e0f2fe; color: #0ea5e9; }
.icon-green { background: #dcfce7; color: #10b981; }

.stat-info span { font-size: 0.85rem; color: #64748b; font-weight: 500; }
.stat-info h3 { margin: 5px 0 0; font-size: 1.35rem; color: #0f172a; font-weight: 700; }

.status-safe { color: #10b981 !important; }
.status-warning { color: #f59e0b !important; }
.status-danger { color: #ef4444 !important; }

.card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    overflow: hidden;
}

.card-header {
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
}

.card-header h3 { font-size: 1.05rem; margin: 0; color: #0f172a; }
.btn-more { font-size: 0.85rem; text-decoration: none; color: #1C6EA4; font-weight: 600; }

.table { width: 100%; border-collapse: collapse; }
.table th { padding: 15px 25px; text-align: left; background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
.table td { padding: 15px 25px; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; color: #334155; }

.badge { padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; }
.badge-red { background: #fee2e2; color: #ef4444; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 768px) {
    .welcome-banner { flex-direction: column; text-align: center; gap: 20px; }
}
</style>

<?php 
// ---------------------------------------------------------------------------------------
// LOGIKA DASHBOARD GURU / BK
// ---------------------------------------------------------------------------------------
else: 
    // Ambil beberapa statistik sekolah umum untuk guru
    $q_total_siswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE status='aktif'");
    $cnt_siswa = mysqli_fetch_assoc($q_total_siswa)['total'];
    
    $q_total_pelanggaran = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggaran_siswa");
    $cnt_pelanggaran = mysqli_fetch_assoc($q_total_pelanggaran)['total'];
?>

<div class="dashboard-container">
    <div class="welcome-banner">
        <h1>Selamat Datang, <?php echo $nama; ?>!</h1>
        <p>Anda saat ini mengelola dashboard sebagai <?php echo strtoupper($role); ?>.</p>
    </div>

    <!-- Statistik Umum Sekolah -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span>Siswa Aktif</span>
                <h3><?php echo $cnt_siswa; ?> Siswa</h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <span>Total Pelanggaran Tercatat</span>
                <h3><?php echo $cnt_pelanggaran; ?> Kasus</h3>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php include "../includes/footer.php"; ?>