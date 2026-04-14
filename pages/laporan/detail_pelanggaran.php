<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

$nis = $_GET['nis'];

// mengambil data siswa dari database join ke tabel ortu_wali, kelas, tingkat, program_keahlian, dan guru
$query_siswa = mysqli_query($conn, "SELECT nis, nama_siswa, tingkat, program_keahlian, rombel, deskripsi FROM siswa
LEFT JOIN kelas USING(id_kelas)
LEFT JOIN tingkat USING(id_tingkat)
LEFT JOIN program_keahlian USING(id_program_keahlian)
WHERE nis = '$nis'");
$row_siswa = mysqli_fetch_assoc($query_siswa);

// Validasi jika data siswa tidak ditemukan sama sekali di database
if (!$row_siswa) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.history.back();</script>";
    exit;
}


// Menyertakan tampilan header (bagian atas halaman)
$page_title = "Detail Pelanggaran: " . htmlspecialchars($row_siswa['nama_siswa']);
include ROOTPATH . "/includes/header.php";

?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/laporan/detail_pelanggaran.css">

<?php
// Tentukan halaman tujuan saat tombol "Kembali" diklik. 
// Jika ada parameter 'from' dari URL, gunakan itu. Jika tidak, default ke list_pelanggaran.php
$back_page = isset($_GET['from']) ? $_GET['from'] : 'list_pelanggaran.php';
?>

<!-- tombol kembali -->
<center class="no-print">
     
    <div style="display: flex; justify-content: center; align-items:center; gap:15px; margin: 25px 0;">
        <a href="/poin_pelanggaran_siswa/pages/laporan/<?= $back_page ?>" class="btn-back">
            <svg height="18" width="18" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                <path d="M874.69 495.52c0 11.3-9.17 20.47-20.47 20.47H249.45l188.08 188.08c8 8 8 20.95 0 28.94a20.47 20.47 0 0 1-28.95 0l-223.01-223.01c-3.84-3.84-6-9.05-6-14.47 0-5.43 2.16-10.63 6-14.47l223.02-223.03c8-8 20.96-8 28.95 0a20.47 20.47 0 0 1 0 28.95l-188.07 188.07h604.75c11.3 0 20.47 9.16 20.47 20.47z"></path>
            </svg>
            <span>Kembali</span>
        </a>
        <?php if (strtolower($_SESSION['role']) !== 'guru'): ?>
            <button class="print-btn" onclick="window.print()">
                <span class="printer-wrapper">
                    <span class="printer-container">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75">
                            <path stroke-width="5" stroke="black" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
                            <mask fill="white" id="path-2-inside-1_30_7">
                                <path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path>
                            </mask>
                            <path mask="url(#path-2-inside-1_30_7)" fill="black" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
                            <circle fill="black" r="3" cy="49" cx="78"></circle>
                        </svg>
                    </span>
                    <span class="printer-page-wrapper"><span class="printer-page"></span></span>
                </span>
                <span>&nbsp;&nbsp;Cetak</span>
            </button>
        <?php endif; ?>
    </div>
    
</center>


<div class="page">
    <!-- Header -->
    <div class="header">
        <!-- menampilkan gambar kop surat dari folder gambar-->
        <img src="/poin_pelanggaran_siswa/assets/images/kop.jpg" alt="kepala surat" width="100%">
    </div>
    
    <div class="title">LAPORAN PELANGGARAN SISWA</div>
    <br>
    <div class="content">
        
        <div class="indent">
            <div class="form-row">
                <div class="label">Nama</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['nama_siswa']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">NIS</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['nis']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Kelas</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['tingkat'] . ' ' . $row_siswa['program_keahlian'] . ' ' . $row_siswa['rombel'] ?></div>
            </div>
            <div class="form-row">
                <div class="label">Program Keahlian</div>
                <div class="separator">:</div>
                <div class="field"><?php echo $row_siswa['deskripsi']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Pelanggaran</div>
                <div class="separator">:</div>
            </div>
            <br>
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead align="center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $result_pelanggaran = mysqli_query($conn, "SELECT id_pelanggaran_siswa, tanggal, jenis, keterangan, poin FROM pelanggaran_siswa JOIN siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis'");
                    
                    while ($row_pelanggaran = mysqli_fetch_assoc($result_pelanggaran)){
                    ?>
                    <tr>
                        <td align="center"><?= $no++?></td>
                        <td>
                        
                        <?php
                        // ubah format tanggal dari YYYY-MM-DD H:i:s menjadi DD-MM-YYYY H:i:s
                        $datetime = date("d-m-Y H:i:s", strtotime($row_pelanggaran['tanggal']));
                        // memecah tanggal dan jam
                        $tanggal = explode(" ", $datetime);
                        // memecah jam 
                        $jam = $tanggal[1];
                        // memecah tanggal
                        $tanggal = explode("-", $tanggal[0]);

                        // array bulan dalam bahasa indonesia
                        $bulan = array(
                            "01" => "Januari",
                            "02" => "Pebruari",
                            "03" => "Maret",
                            "04" => "April",
                            "05" => "Mei",
                            "06" => "Juni",
                            "07" => "Juli",
                            "08" => "Agustus",
                            "09" => "September",
                            "10" => "Oktober",
                            "11" => "November",
                            "12" => "Desember"
                        );
                        // menggabungkan tanggal dan bulan dalam bahasa indonesia
                        $tanggal = $tanggal[0] . " " . $bulan[$tanggal[1]] . " " . $tanggal[2];
                        // tampilkan tanggal yang sudah dimodifikasi menjadi bahasa indonesia agar mudah dibaca
                        echo $tanggal;
                        echo "<br>";
                        echo $jam;
                        ?>
                    
                    </td>
                        <td><?= htmlspecialchars($row_pelanggaran['jenis']) ?></td>
                        <td rowspan="2" align="center"><?= htmlspecialchars($row_pelanggaran['poin']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Detail Pelanggaran : <?= htmlspecialchars($row_pelanggaran['keterangan']) ?></td>
                    </tr>
                    <?php
                        } 
                    ?>
                    <tr>
                        <td colspan="3" align="right">Total Poin</td>
                        <td align="center">
                            <?php
                             $total_poin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(poin) FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis'"))['SUM(poin)'];

                            // menampilkan total poin
                            echo $total_poin;
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    

    </div>
</div>






<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
