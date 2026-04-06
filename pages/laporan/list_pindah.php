<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

// Menyertakan tampilan header (bagian atas halaman)
$page_title = "Laporan Surat Pindah";
include ROOTPATH . "/includes/header.php";

// ============================================================
// FUNGSI PEMBANTU: Ubah format tanggal ke Bahasa Indonesia
// Contoh: "2025-03-01" → "01 Maret 2025"
// ============================================================
function formatTanggalIndo($tanggal_database) {
    if (!$tanggal_database) return "-";
    $nama_bulan = [
        "01" => "Januari",  "02" => "Pebruari", "03" => "Maret",
        "04" => "April",    "05" => "Mei",       "06" => "Juni",
        "07" => "Juli",     "08" => "Agustus",   "09" => "September",
        "10" => "Oktober",  "11" => "November",  "12" => "Desember"
    ];
    $timestamp = strtotime($tanggal_database);
    $bagian_tanggal = explode("-", date("d-m-Y", $timestamp));
    return $bagian_tanggal[0] . " " . $nama_bulan[$bagian_tanggal[1]] . " " . $bagian_tanggal[2];
}

// ============================================================
// PROSES UPLOAD FOTO DOKUMEN
// ============================================================
if (isset($_POST['upload']) && isset($_FILES["foto_dokumen"])) {
    $nama_file_foto   = $_FILES["foto_dokumen"]['name'];
    $data_file_foto   = $_FILES["foto_dokumen"];
    $folder_tujuan    = ROOTPATH . "/assets/images/";
    $lokasi_file_foto = $folder_tujuan . $nama_file_foto;
    $id_surat_pindah = $_POST['id_surat_pindah']; 

    if (move_uploaded_file($data_file_foto["tmp_name"], $lokasi_file_foto)) {
        $nama_file_foto_aman  = mysqli_real_escape_string($conn, $nama_file_foto);
        $id_surat_pindah_aman = mysqli_real_escape_string($conn, $id_surat_pindah);

        $hasil_update = mysqli_query($conn,
            "UPDATE surat_pindah SET foto_dokumen = '$nama_file_foto_aman', status = 'Selesai' WHERE id_surat_pindah = '$id_surat_pindah_aman'"
        );

        if ($hasil_update) {
            echo "<script>alert('Berhasil Mengunggah Foto Dokumen');window.location.href='list_pindah.php'</script>";
        }
    }
}

// ============================================================
// FUNGSI PEMBANTU: Tampilkan daftar jenis pelanggaran siswa
// ============================================================
function tampilkanJenisPelanggaran($conn, $nis_siswa) {
    $query = mysqli_query($conn,
        "SELECT DISTINCT jenis FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$nis_siswa'"
    );
    $daftar = [];
    while ($baris = mysqli_fetch_assoc($query)) {
        $daftar[] = htmlspecialchars($baris['jenis']);
    }
    if (!empty($daftar)) {
        echo implode(', ', $daftar) . '.';
    }
}

// ============================================================
// QUERY 1: Calon Siswa Pindah (Poin >= 100)
// ============================================================
$keyword_calon = isset($_GET['cari_daftar_siswa']) ? mysqli_real_escape_string($conn, $_GET['cari_daftar_siswa']) : '';
$sql_calon = "
    SELECT main.*, sub.total_poin
    FROM (
        SELECT siswa.*, sk.tanggal_pembuatan_surat AS tanggal_surat, sp.status AS status_dokumen, sp.foto_dokumen, sk.id_surat_pindah
        FROM siswa
        JOIN pelanggaran_siswa USING(nis)
        LEFT JOIN surat_keluar sk ON siswa.nis = sk.nis AND sk.jenis_surat = 'Pindah Sekolah'
        LEFT JOIN surat_pindah sp USING(id_surat_pindah)
        WHERE siswa.status = 'aktif'
          AND (siswa.nama_siswa LIKE '%$keyword_calon%' OR siswa.nis LIKE '%$keyword_calon%')
        GROUP BY siswa.nis, sk.id_surat_pindah, sk.tanggal_pembuatan_surat, sp.status, sp.foto_dokumen
        ORDER BY siswa.nis, sk.tanggal_pembuatan_surat DESC
    ) main
    JOIN (
        SELECT nis, SUM(poin) AS total_poin FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) GROUP BY nis
    ) sub USING(nis)
    WHERE sub.total_poin >= 100
";
$hasil_calon = mysqli_query($conn, $sql_calon);

// ============================================================
// QUERY 2: Surat Pindah Sekolah Keluar
// ============================================================
$keyword_surat = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$result_surat_pindah = mysqli_query($conn, 
    "SELECT * FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) 
     WHERE jenis_surat = 'Pindah Sekolah' 
     AND (nama_siswa LIKE '%$keyword_surat%' OR nis LIKE '%$keyword_surat%') 
     ORDER BY tanggal_pembuatan_surat DESC"
);
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/laporan/list_pindah.css">

<div class="list-pindah-wrapper animate-fade-in">
    <!-- Header Section -->
    <header class="page-header-premium">
        <div class="header-info">
            <h2>Laporan Administrasi Pindah Sekolah</h2>
            <p>Kelola daftar siswa yang poinnya mencapai batas maksimal (100+) dan surat pindah keluar.</p>
        </div>
        <?php if (trim(strtolower($_SESSION['role'])) !== 'guru'): ?>
            <a href="/poin_pelanggaran_siswa/pages/cetak/add_pindah_sekolah.php" class="btn-add-pindah">
                <i class="fas fa-file-export"></i>
                Tambah Surat Pindah
            </a>
        <?php endif; ?>
    </header>

    <!-- SECTION 1: CALON SISWA PINDAH (100++ POIN) -->
    <div class="section-header">
        <h3><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Calon Siswa Pindah (Poin 100+)</h3>
        <form action="" method="get" class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" name="cari_daftar_siswa" value="<?= htmlspecialchars($keyword_calon) ?>" placeholder="Cari NIS / Nama Siswa..." autocomplete="off">
        </form>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="120">NIS</th>
                    <th>Nama Siswa</th>
                    <th>Pelanggaran</th>
                    <th width="80">Poin</th>
                    <th width="300">Aksi & Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($hasil_calon) == 0) {
                    echo "<tr><td colspan='6' align='center'>Tidak ada siswa dengan poin 100+ saat ini.</td></tr>";
                } else {
                    while ($data_siswa = mysqli_fetch_assoc($hasil_calon)) {
                        $status_class = ($data_siswa['status_dokumen'] == "Selesai") ? "status-success" : (($data_siswa['status_dokumen'] == "Masih Proses") ? "status-progress" : "status-pending");
                        $status_label = ($data_siswa['status_dokumen'] == "Selesai") ? "Selesai" : (($data_siswa['status_dokumen'] == "Masih Proses") ? "Masih Proses" : "Belum Ada");
                        $status_icon = ($data_siswa['status_dokumen'] == "Selesai") ? "fa-check-circle" : (($data_siswa['status_dokumen'] == "Masih Proses") ? "fa-clock" : "fa-info-circle");
                ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><strong><?= htmlspecialchars($data_siswa['nis']) ?></strong></td>
                    <td><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td>
                    <td style="font-size: 0.85rem; color: #64748b;"><?php tampilkanJenisPelanggaran($conn, $data_siswa['nis']); ?></td>
                    <td align="center"><span style="color: #ef4444; font-weight: 800;"><?= $data_siswa['total_poin'] ?></span></td>
                    <td>
                        <div class="btn-action-group">
                            <span class="status-badge <?= $status_class ?>"><i class="fas <?= $status_icon ?>"></i> <?= $status_label ?></span>
                            
                            <?php if ($data_siswa['status_dokumen'] == NULL) { ?>
                                <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&from=list_pindah.php" class="btn-action btn-detail"><i class="fas fa-eye"></i> Detail</a>
                                <?php if (trim(strtolower($_SESSION['role'])) !== 'guru'): ?>
                                    <form action="/poin_pelanggaran_siswa/pages/cetak/add_pindah_sekolah.php?from=list_pindah.php" method="post" style="display:inline;">
                                        <input type="hidden" name="nis" value="<?= $data_siswa['nis'] ?>">
                                        <button type="submit" class="btn-action btn-print"><i class="fas fa-print"></i> Cetak</button>
                                    </form>
                                <?php endif; ?>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Masih Proses") { ?>
                                <?php if (trim(strtolower($_SESSION['role'])) !== 'guru'): ?>
                                    <a href="/poin_pelanggaran_siswa/pages/cetak/surat_pindah_sekolah.php?nis=<?= $data_siswa['nis'] ?>&from=list_pindah.php" class="btn-action btn-print"><i class="fas fa-print"></i> Lihat Surat</a>
                                <?php endif; ?>
                                <?php if (!in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])): ?>
                                    <form action="" method="post" enctype="multipart/form-data" class="upload-form">
                                        <input type="hidden" name="id_surat_pindah" value="<?= htmlspecialchars($data_siswa['id_surat_pindah']) ?>">
                                        <input type="file" name="foto_dokumen" accept="image/*, application/pdf" required>
                                        <button type="submit" name="upload" class="btn-action btn-upload"><i class="fas fa-upload"></i> Upload</button>
                                    </form>
                                <?php else: ?>
                                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 10px 0;"><i class="fas fa-lock"></i> Upload hanya untuk Petugas BK</p>
                                <?php endif; ?>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Selesai") { ?>
                                <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($data_siswa['foto_dokumen']) ?>" target="_blank" class="btn-action btn-view"><i class="fas fa-image"></i> Lihat Dokumen</a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>

    <!-- SECTION 2: DAFTAR SURAT PINDAH SELESAI -->
    <div class="section-header">
        <h3><i class="fas fa-file-contract" style="color: var(--primary);"></i> Riwayat Surat Pindah Sekolah</h3>
        <form action="" method="get" class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" name="cari" value="<?= htmlspecialchars($keyword_surat) ?>" placeholder="Cari NIS / Nama Siswa..." autocomplete="off">
        </form>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="150">Tanggal</th>
                    <th width="150">No Surat</th>
                    <th>Nama Siswa (NIS)</th>
                    <th>Sekolah Tujuan</th>
                    <th>Alasan</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($result_surat_pindah) == 0) {
                    echo "<tr><td colspan='7' align='center'>Data Surat Pindah tidak ditemukan.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result_surat_pindah)) {
                ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>
                    <td><i class="far fa-calendar-alt" style="color:var(--text-muted); margin-right:5px;"></i> <?= formatTanggalIndo($row['tanggal_pembuatan_surat']) ?></td>
                    <td><span class="status-badge status-progress"><?= htmlspecialchars($row['no_surat']) ?></span></td>
                    <td>
                        <div style="font-weight: 800; color:var(--text-main);"><?= htmlspecialchars($row['nama_siswa']) ?></div>
                        <div style="font-size: 0.8rem; color:var(--text-muted);">NIS: <?= htmlspecialchars($row['nis']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($row['sekolah_tujuan']) ?></td>
                    <td><span style="font-style: italic; color: var(--text-muted);"><?= htmlspecialchars($row['alasan_pindah']) ?></span></td>
                    <td>
                        <?php if (trim(strtolower($_SESSION['role'])) !== 'guru'): ?>
                            <a href="/poin_pelanggaran_siswa/pages/cetak/surat_pindah_sekolah.php?no_surat=<?=$row['no_surat']?>" class="btn-action btn-print">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<div style="height: 40px;"></div>

<?php 
include "../../includes/footer.php"; 
?>
