<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . "/config/config.php";
$page_title = "Laporan Pelanggaran";
include ROOTPATH . "/includes/header.php";
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru
    
// Logika Pencarian
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$sql = "SELECT ps.id_pelanggaran_siswa, s.nama_siswa, ps.tanggal, ps.nis 
        FROM pelanggaran_siswa ps 
        JOIN siswa s USING(nis) 
        WHERE ps.tanggal = (SELECT MAX(tanggal) FROM pelanggaran_siswa WHERE nis = ps.nis)";

if (!empty($cari)) {
    $sql .= " AND (nama_siswa LIKE '%$cari%' OR nis LIKE '%$cari%')";
}

$sql .= " ORDER BY ps.tanggal DESC";
$result = mysqli_query($conn, $sql);

// Array bulan dalam bahasa Indonesia
$bulan_id = [
    "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", 
    "05" => "Mei", "06" => "Jun", "07" => "Jul", "08" => "Agu", 
    "09" => "Sep", "10" => "Okt", "11" => "Nov", "12" => "Des"
];

// Definisi Indikator Poin
$point_indicators = [
    ['min' => 100, 'max' => 999, 'class' => 'poin-high', 'label' => 'Sangat Tinggi', 'desc' => 'Surat DO/Dikeluarkan dari Sekolah'],
    ['min' => 50, 'max' => 99, 'class' => 'poin-warning', 'label' => 'Waspada', 'desc' => 'Surat Panggilan Orang Tua'],
    ['min' => 25, 'max' => 49, 'class' => 'poin-med', 'label' => 'Perhatian', 'desc' => 'Surat Perjanjian Siswa'],
    ['min' => 1, 'max' => 24, 'class' => 'poin-low', 'label' => 'Ringan', 'desc' => 'Pembinaan Wali Kelas']
];
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/laporan/list_pelanggaran.css">

<div class="container">
    <div class="report-header">
        <div class="header-main">
            <h2><i class="fas fa-file-circle-exclamation"></i> Laporan Pelanggaran Siswa</h2>
            <p class="header-subtitle">Pantau akumulasi poin pelanggaran dan status kedisiplinan siswa secara real-time.</p>
        </div>
        
        <form action="list_pelanggaran.php" method="GET" class="search-form">
            <datalist id="nama_siswa">
                <?php
                $result_s = mysqli_query($conn, "SELECT nama_siswa, nis FROM siswa");
                while ($rs = mysqli_fetch_assoc($result_s)) {
                    echo "<option value='" . $rs['nis'] . "'>" . htmlspecialchars($rs['nama_siswa']) . "</option>";
                }
                ?>
            </datalist>
            <div class="search-group">
                <input type="text" name="cari" class="search-input" value="<?= htmlspecialchars($cari) ?>" placeholder="NIS atau Nama Siswa..." list="nama_siswa" autocomplete="off">
                <button type="submit" class="btn-search">
                    <i class="fas fa-magnifying-glass"></i> Cari
                </button>
            </div>
            <?php if(!empty($cari)): ?>
                <a href="list_pelanggaran.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Poin Indicator Legend -->
    <div class="legend-card">
        <div class="legend-header">
            <h3><i class="fas fa-circle-info"></i> Indikator Poin Pelanggaran</h3>
        </div>
        <div class="legend-grid">
            <?php foreach($point_indicators as $indicator): ?>
                <div class="legend-item">
                    <div class="legend-badge-wrapper">
                        <span class="legend-badge <?= $indicator['class'] ?>"><?= $indicator['min'] . ($indicator['max'] < 999 ? ' - ' . $indicator['max'] : '+') ?></span>
                    </div>
                    <div class="legend-info">
                        <strong><?= $indicator['label'] ?></strong>
                        <span><?= $indicator['desc'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 140px;">Waktu Terakhir</th>
                        <th>Identitas Siswa</th>
                        <th>Ringkasan Pelanggaran</th>
                        <th style="width: 100px; text-align: center;">Total Poin</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) == 0): ?>
                        <tr><td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;"><i class="fas fa-magnifying-glass" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i> Data tidak ditemukan</td></tr>
                    <?php else: 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                            // Format Tanggal
                            $dt = strtotime($row['tanggal']);
                            $tgl = date("d", $dt) . " " . $bulan_id[date("m", $dt)] . " " . date("Y", $dt);
                            $jam = date("H:i", $dt);

                            // Ambil detail pelanggaran (gabungan)
                            $ps_sql = "SELECT DISTINCT jenis FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$row[nis]'";
                            $ps_res = mysqli_query($conn, $ps_sql);
                            $list_j = [];
                            while($dj = mysqli_fetch_assoc($ps_res)) { $list_j[] = htmlspecialchars($dj['jenis']); }
                            $string_pelanggaran = implode(", ", $list_j);

                            // Hitung Total Poin
                            $poin_sql = "SELECT SUM(poin) as total FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$row[nis]'";
                            $total_poin = mysqli_fetch_assoc(mysqli_query($conn, $poin_sql))['total'];
                            
                            // Poin Level Class
                            $level = 'poin-none';
                            if($total_poin >= 150) $level = 'poin-critical';
                            elseif($total_poin >= 100) $level = 'poin-high';
                            elseif($total_poin >= 50) $level = 'poin-warning';
                            elseif($total_poin >= 25) $level = 'poin-med';
                            elseif($total_poin > 0) $level = 'poin-low';
                    ?>
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600;"><?= $no++ ?></td>
                            <td>
                                <div class="date-box">
                                    <span class="date-text"><?= $tgl ?></span>
                                    <span class="time-text"><i class="far fa-clock"></i> <?= $jam ?> WIB</span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span class="student-name"><?= htmlspecialchars($row['nama_siswa']) ?></span>
                                    <span class="nis-badge" style="width: fit-content; margin-top: 5px;"><?= htmlspecialchars($row['nis']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="violation-list"><?= $string_pelanggaran ?>.</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="poin-badge <?= $level ?>"><?= $total_poin ?></span>
                            </td>
                            <td style="text-align: center;">
                                <a href="detail_pelanggaran.php?nis=<?= $row['nis'] ?>&from=list_pelanggaran.php" class="btn-detail">
                                    <i class="fas fa-circle-info"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
