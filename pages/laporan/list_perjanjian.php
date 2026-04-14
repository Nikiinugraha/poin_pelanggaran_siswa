<?php
// ============================================================
// File   : list_perjanjian.php
// Fungsi : Menampilkan daftar siswa yang perlu/sudah membuat
//          surat perjanjian, berdasarkan jumlah poin pelanggaran
// ============================================================

// Langkah 1: Tentukan lokasi folder utama proyek di server
// Ini seperti menentukan "alamat rumah" agar file lain mudah ditemukan
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Langkah 2: Hubungkan ke database (seperti membuka buku catatan data siswa)
include ROOTPATH . "/config/config.php";
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru


// ============================================================
// FUNGSI PEMBANTU: Ubah format tanggal ke Bahasa Indonesia
// Contoh: "2025-03-01" → "01 Maret 2025"
// ============================================================
function formatTanggalIndo($tanggal_database) {
    // Daftar nama bulan dalam Bahasa Indonesia
    $nama_bulan = [
        "01" => "Januari",  "02" => "Pebruari", "03" => "Maret",
        "04" => "April",    "05" => "Mei",       "06" => "Juni",
        "07" => "Juli",     "08" => "Agustus",   "09" => "September",
        "10" => "Oktober",  "11" => "November",  "12" => "Desember"
    ];
    // Ubah format tanggal dari database (YYYY-MM-DD) menjadi DD-MM-YYYY
    // lalu pecah berdasarkan tanda "-" menjadi array [hari, bulan, tahun]
    $bagian_tanggal = explode("-", date("d-m-Y", strtotime($tanggal_database)));

    // Gabungkan: hari + nama bulan (bukan angka) + tahun
    return $bagian_tanggal[0] . " " . $nama_bulan[$bagian_tanggal[1]] . " " . $bagian_tanggal[2];
}


// ============================================================
// PROSES UPLOAD FOTO DOKUMEN
// Dijalankan saat guru mengunggah foto surat perjanjian yang
// sudah ditandatangani oleh siswa/orang tua
// ============================================================
if (isset($_POST['upload']) && isset($_FILES["foto_dokumen"])) {

    // Ambil nama file foto yang dikirim lewat form
    $nama_file_foto   = $_FILES["foto_dokumen"]['name'];
    $data_file_foto   = $_FILES["foto_dokumen"];

    // Tentukan folder tujuan penyimpanan foto di server
    $folder_tujuan    = ROOTPATH . "/assets/images/";
    $lokasi_file_foto = $folder_tujuan . $nama_file_foto;

    // Ambil tanggal surat dan jenis upload (siswa atau orang tua)
    $tanggal_surat = $_POST['tanggal_surat'];
    $jenis_upload  = $_POST['jenis_upload']; // nilai: "siswa" atau "perjanjian_orang_tua"

    // Pindahkan file foto dari folder sementara (tmp) ke folder gambar
    if (move_uploaded_file($data_file_foto["tmp_name"], $lokasi_file_foto)) {

        // Tentukan nama tabel yang akan diupdate berdasarkan jenis upload
        // Jika jenis = "siswa" → update tabel perjanjian_siswa
        // Jika jenis lain → update tabel perjanjian_orang_tua
        if ($jenis_upload == "siswa") {
            $nama_tabel = "perjanjian_siswa";
        } else {
            $nama_tabel = "perjanjian_orang_tua";
        }

        // Bersihkan data dari karakter berbahaya sebelum disimpan ke database
        $nama_file_foto_aman  = mysqli_real_escape_string($conn, $nama_file_foto);
        $tanggal_surat_aman   = mysqli_real_escape_string($conn, $tanggal_surat);

        // Simpan nama foto dan ubah status menjadi "Selesai" di database
        $hasil_update = mysqli_query($conn,
            "UPDATE $nama_tabel
             SET foto_dokumen = '$nama_file_foto_aman', status = 'Selesai'
             WHERE tanggal = '$tanggal_surat_aman'"
        );

        if ($hasil_update) {
            // Jika berhasil: tampilkan pesan lalu kembali ke halaman ini
            echo "<script>alert('Berhasil Mengunggah Foto Dokumen');window.location.href='list_perjanjian.php'</script>";
        } else {
            // Jika gagal: tampilkan pesan error dari database
            echo "Gagal Mengunggah Foto Dokumen: " . mysqli_error($conn);
        }
    }
}

// ============================================================
// FUNGSI PEMBANTU: Tampilkan daftar jenis pelanggaran siswa
// Digunakan berulang di beberapa tabel, dibuat fungsi agar
// tidak perlu menulis kode yang sama berkali-kali
// ============================================================
function tampilkanJenisPelanggaran($conn, $nis_siswa) {
    // Ambil jenis pelanggaran yang BERBEDA (DISTINCT) dari database
    // agar pelanggaran yang sama tidak tampil lebih dari 1 kali
    $query = mysqli_query($conn,
        "SELECT DISTINCT jenis
         FROM pelanggaran_siswa
         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
         WHERE nis = '$nis_siswa'"
    );

    // Siapkan tempat penampungan kosong (array) untuk daftar pelanggaran
    $daftar = [];

    // Ambil data satu per satu dan masukkan ke tempat penampungan
    while ($baris = mysqli_fetch_assoc($query)) {
        // htmlspecialchars = membersihkan teks agar aman ditampilkan di halaman web
        $daftar[] = htmlspecialchars($baris['jenis']);
    }

    // Jika daftar tidak kosong, gabungkan dengan koma dan tampilkan
    if (!empty($daftar)) {
        echo implode(', ', $daftar) . '.';
    }
}

// ============================================================
// QUERY 1: Daftar siswa CALON pembuat surat perjanjian SISWA
// Syarat: poin antara 25-50 dan siswa masih aktif
// Satu siswa bisa muncul lebih dari 1 baris jika punya
// perjanjian di tanggal yang berbeda
// ============================================================

// Cek apakah guru sedang mencari siswa tertentu (via form pencarian)
if (isset($_GET['cari_daftar_siswa'])) {

    // Bersihkan kata kunci pencarian dari karakter berbahaya
    $kata_cari_siswa = mysqli_real_escape_string($conn, $_GET['cari_daftar_siswa']);

    // Query dengan filter nama atau NIS siswa
    $sql_calon_perjanjian_siswa = "
        SELECT main.*, sub.total_poin
        FROM (
            -- Bagian dalam: ambil data siswa dikelompokkan per NIS dan tanggal perjanjian
            -- GROUP BY nis, ps.tanggal: satu siswa bisa muncul >1 baris jika beda tanggal perjanjian
            -- Kompatibel dengan ONLY_FULL_GROUP_BY di Nginx/MySQL strict mode
            SELECT siswa.*, ps.tanggal AS tanggal_surat, ps.status AS status_dokumen, ps.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (ps.status IS NULL OR ps.status != 'Selesai')
              AND (siswa.nama_siswa LIKE '%$kata_cari_siswa%' OR siswa.nis LIKE '%$kata_cari_siswa%')
            GROUP BY siswa.nis, ps.tanggal, ps.status, ps.foto_dokumen
            ORDER BY siswa.nis, ps.tanggal DESC
        ) main

        JOIN (
            -- Bagian luar: hitung TOTAL poin keseluruhan per siswa (semua pelanggaran)
            -- Dipisah agar total_poin tidak terpengaruh oleh GROUP BY tanggal di atas
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 25 AND 50
    ";

} else {
    // Tidak ada pencarian → tampilkan semua siswa dengan poin 25-50
    $sql_calon_perjanjian_siswa = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, ps.tanggal AS tanggal_surat, ps.status AS status_dokumen, ps.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (ps.status IS NULL OR ps.status != 'Selesai')
            GROUP BY siswa.nis, ps.tanggal, ps.status, ps.foto_dokumen
            ORDER BY siswa.nis, ps.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 25 AND 50
    ";
}
// Jalankan query ke database dan simpan hasilnya
$hasil_calon_perjanjian_siswa = mysqli_query($conn, $sql_calon_perjanjian_siswa);

// ============================================================
// QUERY 2: Laporan surat perjanjian SISWA yang sudah dicetak
// (isi tabel: perjanjian_siswa)
// ============================================================

if (isset($_GET['cari_laporan_siswa'])) {
    $kata_cari_laporan_siswa = mysqli_real_escape_string($conn, $_GET['cari_laporan_siswa']);
    $sql_laporan_perjanjian_siswa = "
        SELECT
            ps.tanggal   AS tanggal_surat,
            ps.foto_dokumen,
            ps.status    AS status_dokumen,
            ps.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_siswa ps
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND (s.nama_siswa LIKE '%$kata_cari_laporan_siswa%' OR s.nis LIKE '%$kata_cari_laporan_siswa%')
        GROUP BY s.nis, ps.tanggal
        ORDER BY ps.tanggal DESC
    ";
} else {
    $sql_laporan_perjanjian_siswa = "
        SELECT
            ps.tanggal   AS tanggal_surat,
            ps.foto_dokumen,
            ps.status    AS status_dokumen,
            ps.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_siswa ps
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
        GROUP BY s.nis, s.nama_siswa, ps.tanggal, ps.foto_dokumen, ps.status, ps.tingkat
        ORDER BY ps.tanggal DESC
    ";
}
$hasil_laporan_perjanjian_siswa = mysqli_query($conn, $sql_laporan_perjanjian_siswa);

// ============================================================
// QUERY 3: Daftar siswa CALON pembuat surat perjanjian ORTU
// Syarat: poin antara 50-100 dan siswa masih aktif
// ============================================================

if (isset($_GET['cari_daftar_ortu'])) {
    // PENTING: gunakan variabel $kata_cari_ortu (bukan $kata_cari_siswa)
    // agar pencarian di tabel ortu tidak tercampur dengan tabel siswa
    $kata_cari_ortu = mysqli_real_escape_string($conn, $_GET['cari_daftar_ortu']);

    $sql_calon_perjanjian_ortu = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (po.status IS NULL OR po.status != 'Selesai')
              AND (siswa.nama_siswa LIKE '%$kata_cari_ortu%' OR siswa.nis LIKE '%$kata_cari_ortu%')
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 50 AND 100
    ";
} else {
    $sql_calon_perjanjian_ortu = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
              AND (po.status IS NULL OR po.status != 'Selesai')
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin BETWEEN 50 AND 100
    ";
}
$hasil_calon_perjanjian_ortu = mysqli_query($conn, $sql_calon_perjanjian_ortu);

// ============================================================
// QUERY 4: Laporan surat perjanjian ORTU yang sudah dicetak
// (isi tabel: perjanjian_orang_tua)
// ============================================================

if (isset($_GET['cari_laporan_ortu'])) {
    $kata_cari_laporan_ortu = mysqli_real_escape_string($conn, $_GET['cari_laporan_ortu']);
    $sql_laporan_perjanjian_ortu = "
        SELECT
            po.tanggal   AS tanggal_surat,
            po.foto_dokumen,
            po.status    AS status_dokumen,
            po.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_orang_tua po
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
          AND (s.nama_siswa LIKE '%$kata_cari_laporan_ortu%' OR s.nis LIKE '%$kata_cari_laporan_ortu%')
        GROUP BY s.nis, s.nama_siswa, po.tanggal, po.foto_dokumen, po.status, po.tingkat
        ORDER BY po.tanggal DESC
    ";
} else {
    $sql_laporan_perjanjian_ortu = "
        SELECT
            po.tanggal   AS tanggal_surat,
            po.foto_dokumen,
            po.status    AS status_dokumen,
            po.tingkat,
            s.nis,
            s.nama_siswa
        FROM perjanjian_orang_tua po
        JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa)
        JOIN siswa s USING(nis)
        WHERE s.status = 'aktif'
        GROUP BY s.nis, s.nama_siswa, po.tanggal, po.foto_dokumen, po.status, po.tingkat
        ORDER BY po.tanggal DESC
    ";
}
$hasil_laporan_perjanjian_ortu = mysqli_query($conn, $sql_laporan_perjanjian_ortu);


// Langkah terakhir sebelum HTML: pasang header/tampilan atas halaman
$page_title = "Surat Perjanjian";
include ROOTPATH . "/includes/header.php";
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/laporan/list_perjanjian.css">

<div class="container">
    <header class="page-header">
        <div class="header-title-group">
            <h2><i class="fas fa-file-signature"></i> Daftar Surat Perjanjian</h2>
            <p class="header-subtitle">Kelola surat pernyataan kedisiplinan siswa berdasarkan akumulasi poin pelanggaran.</p>
        </div>
        <div class="header-actions">
            <!-- Header actions if any -->
        </div>
    </header>

    <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
        <div class="action-banners">
            <a href="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_siswa.php?from=list_perjanjian.php" class="btn-banner">
                <div class="banner-info">
                    <div class="banner-icon"><i class="fas fa-user-edit"></i></div>
                    <div class="banner-text">
                        <h4>Cetak Perjanjian Siswa</h4>
                        <p>Siswa dengan akumulasi 25-50 poin</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right banner-arrow"></i>
            </a>
            <a href="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_ortu.php" class="btn-banner">
                <div class="banner-info">
                    <div class="banner-icon"><i class="fas fa-users"></i></div>
                    <div class="banner-text">
                        <h4>Cetak Perjanjian Ortu</h4>
                        <p>Siswa dengan akumulasi 50-100 poin</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right banner-arrow"></i>
            </a>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════════════
         BAGIAN 1: TABEL CALON PEMBUAT SURAT PERJANJIAN SISWA (25-50 poin)
    ════════════════════════════════════════════════════════════════════ -->
    <div class="card-section">
        <div class="card-header">
            <h3><i class="fas fa-user-clock"></i> Daftar Siswa di atas 25 Poin</h3>
            
            <form action="list_perjanjian.php" method="get" class="card-actions">
                <datalist id="pilihan_siswa_25_50">
                    <?php
                    $query_pilihan_siswa = mysqli_query($conn, "SELECT nama_siswa, nis FROM siswa JOIN pelanggaran_siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE siswa.status = 'aktif' GROUP BY nis, nama_siswa HAVING SUM(poin) BETWEEN 25 AND 50");
                    while ($baris_pilihan = mysqli_fetch_assoc($query_pilihan_siswa)) {
                        echo "<option value='" . htmlspecialchars($baris_pilihan['nis']) . "'>";
                        echo "<option value='" . htmlspecialchars($baris_pilihan['nama_siswa']) . "'>";
                    }
                    ?>
                </datalist>

                <div class="search-group">
                    <input type="text" name="cari_daftar_siswa" value="<?= isset($_GET['cari_daftar_siswa']) ? htmlspecialchars($_GET['cari_daftar_siswa']) : '' ?>" placeholder="NIS atau Nama Siswa..." list="pilihan_siswa_25_50" autocomplete="off" class="search-input">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                </div>
                <a href="list_perjanjian.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th style="width: 100px; text-align: center;">Poin</th>
                        <th style="width: 200px;">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_calon_perjanjian_siswa || mysqli_num_rows($hasil_calon_perjanjian_siswa) == 0) {
                        echo "<tr><td colspan='6' align='center' style='padding: 40px; color: #94a3b8;'>Data tidak ditemukan</td></tr>";
                    } else {
                        while ($data_siswa = mysqli_fetch_assoc($hasil_calon_perjanjian_siswa)) {
                    ?>
                    <tr>
                        <td style="text-align: center; color: #94a3b8; font-weight: 600;"><?= $nomor_urut++ ?></td>
                        <td><span class="nis-badge"><?= htmlspecialchars($data_siswa['nis']) ?></span></td>
                        <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td>
                        <td><span style="font-size: 0.85rem; color: #64748b;"><?php tampilkanJenisPelanggaran($conn, $data_siswa['nis']); ?></span></td>
                        <td style="text-align: center;"><span class="poin-badge"><?= htmlspecialchars($data_siswa['total_poin']) ?></span></td>
                        <td>
                            <div class="action-stack">
                                <?php if ($data_siswa['status_dokumen'] == NULL) { ?>
                                    <span class="status-badge status-pending"><i class="fas fa-hourglass-start"></i> Belum Ada Surat</span>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&tanggal=<?= $data_siswa['tanggal_surat'] ?>&from=list_perjanjian.php" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i> Detail</a>
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <form action="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_siswa.php?from=list_perjanjian.php" method="post" style="margin: 0;">
                                                <input type="hidden" name="nis" value="<?= $data_siswa['nis'] ?>">
                                                <button type="submit" class="btn-action btn-print-mini"><i class="fas fa-print"></i> Cetak</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                <?php } elseif ($data_siswa['status_dokumen'] == "Masih Proses") { ?>
                                    <span class="status-badge status-process"><i class="fas fa-spinner fa-spin"></i> Menunggu Upload</span>
                                    <div style="display: flex; gap: 5px; margin-bottom: 8px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&tanggal=<?= $data_siswa['tanggal_surat'] ?>&from=list_perjanjian.php" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i></a>
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <a href="/poin_pelanggaran_siswa/pages/cetak/surat_perjanjian_siswa.php?nis=<?= $data_siswa['nis'] ?>&from=list_perjanjian.php" class="btn-action btn-print-mini" title="Cetak Surat"><i class="fas fa-print"></i></a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!in_array(strtolower(trim($_SESSION['role'])), ['wakasek', 'guru'])): ?>
                                        <form action="" method="post" enctype="multipart/form-data" class="upload-mini">
                                            <span>Upload Bukti TTD:</span>
                                            <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_siswa['tanggal_surat']) ?>">
                                            <input type="hidden" name="jenis_upload" value="siswa">
                                            <div style="display: flex; gap: 5px; align-items: center;">
                                                <input type="file" name="foto_dokumen" accept="image/*, application/pdf" required>
                                                <button type="submit" name="upload" class="btn-action btn-upload" style="padding: 5px 10px;"><i class="fas fa-cloud-arrow-up"></i></button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 5px 0;"><i class="fas fa-lock"></i> Upload hanya untuk Petugas BK</p>
                                    <?php endif; ?>

                                <?php } elseif ($data_siswa['status_dokumen'] == "Selesai") { ?>
                                    <span class="status-badge status-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($data_siswa['foto_dokumen']) ?>" target="_blank" class="btn-action btn-view"><i class="fas fa-image"></i> Lihat Dokumen</a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>



<br><br>

    <!-- ═══════════════════════════════════════════════════════════════════
         BAGIAN 2: TABEL CALON PEMBUAT SURAT PERJANJIAN ORTU (50-100 poin)
    ════════════════════════════════════════════════════════════════════ -->
    <div class="card-section">
        <div class="card-header">
            <h3><i class="fas fa-users-cog"></i> Daftar Siswa di atas 50 Poin</h3>
            
            <form action="list_perjanjian.php" method="get" class="card-actions">
                <datalist id="pilihan_siswa_50_100">
                    <?php
                    $query_pilihan_ortu = mysqli_query($conn, "SELECT nama_siswa, nis FROM siswa JOIN pelanggaran_siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE siswa.status = 'aktif' GROUP BY nis, nama_siswa HAVING SUM(poin) BETWEEN 50 AND 100");
                    while ($baris_pilihan_ortu = mysqli_fetch_assoc($query_pilihan_ortu)) {
                        echo "<option value='" . htmlspecialchars($baris_pilihan_ortu['nis']) . "'>";
                        echo "<option value='" . htmlspecialchars($baris_pilihan_ortu['nama_siswa']) . "'>";
                    }
                    ?>
                </datalist>

                <div class="search-group">
                    <input type="text" name="cari_daftar_ortu" value="<?= isset($_GET['cari_daftar_ortu']) ? htmlspecialchars($_GET['cari_daftar_ortu']) : '' ?>" placeholder="NIS atau Nama Siswa..." list="pilihan_siswa_50_100" autocomplete="off" class="search-input">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                </div>
                <a href="list_perjanjian.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th style="width: 100px; text-align: center;">Poin</th>
                        <th style="width: 200px;">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_calon_perjanjian_ortu || mysqli_num_rows($hasil_calon_perjanjian_ortu) == 0) {
                        echo "<tr><td colspan='6' align='center' style='padding: 40px; color: #94a3b8;'>Data tidak ditemukan</td></tr>";
                    } else {
                        while ($data_ortu = mysqli_fetch_assoc($hasil_calon_perjanjian_ortu)) {
                    ?>
                    <tr>
                        <td style="text-align: center; color: #94a3b8; font-weight: 600;"><?= $nomor_urut++ ?></td>
                        <td><span class="nis-badge"><?= htmlspecialchars($data_ortu['nis']) ?></span></td>
                        <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($data_ortu['nama_siswa']) ?></td>
                        <td><span style="font-size: 0.85rem; color: #64748b;"><?php tampilkanJenisPelanggaran($conn, $data_ortu['nis']); ?></span></td>
                        <td style="text-align: center;"><span class="poin-badge"><?= htmlspecialchars($data_ortu['total_poin']) ?></span></td>
                        <td>
                            <div class="action-stack">
                                <?php if ($data_ortu['status_dokumen'] == NULL) { ?>
                                    <span class="status-badge status-pending"><i class="fas fa-hourglass-start"></i> Belum Ada Surat</span>
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_ortu['nis'] ?>&tanggal=<?= $data_ortu['tanggal_surat'] ?>&from=list_perjanjian.php" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i> Detail</a>
                                        
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <?php
                                            $cek_surat_panggilan = mysqli_query($conn, "SELECT nis FROM surat_keluar WHERE nis = '" . mysqli_real_escape_string($conn, $data_ortu['nis']) . "' AND jenis_surat = 'Panggilan Orang Tua'");
                                            if (mysqli_num_rows($cek_surat_panggilan) == 0) { ?>
                                                <form action="/poin_pelanggaran_siswa/pages/cetak/add_panggilan_ortu.php?from=list_perjanjian.php" method="post" style="margin: 0;">
                                                    <input type="hidden" name="nis" value="<?= $data_ortu['nis'] ?>">
                                                    <button type="submit" class="btn-action btn-print-mini" title="Cetak Panggilan"><i class="fas fa-envelope"></i> Panggilan</button>
                                                </form>
                                            <?php } ?>

                                            <form action="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_ortu.php?from=list_perjanjian.php" method="post" style="margin: 0;">
                                                <input type="hidden" name="nis" value="<?= $data_ortu['nis'] ?>">
                                                <button type="submit" class="btn-action btn-print-mini" title="Cetak Perjanjian"><i class="fas fa-file-contract"></i> Perjanjian</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                <?php } elseif ($data_ortu['status_dokumen'] == "Masih Proses") { ?>
                                    <span class="status-badge status-process"><i class="fas fa-spinner fa-spin"></i> Menunggu Upload</span>
                                    <div style="display: flex; gap: 5px; margin-bottom: 8px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_ortu['nis'] ?>&tanggal=<?= $data_ortu['tanggal_surat'] ?>&from=list_perjanjian.php" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i></a>
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <a href="/poin_pelanggaran_siswa/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $data_ortu['nis'] ?>&from=list_perjanjian.php" class="btn-action btn-print-mini" title="Cetak Surat"><i class="fas fa-print"></i></a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!in_array(strtolower(trim($_SESSION['role'])), ['wakasek', 'guru'])): ?>
                                        <form action="" method="post" enctype="multipart/form-data" class="upload-mini">
                                            <span>Upload Bukti TTD Ortu:</span>
                                            <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_ortu['tanggal_surat']) ?>">
                                            <input type="hidden" name="jenis_upload" value="perjanjian_orang_tua">
                                            <div style="display: flex; gap: 5px; align-items: center;">
                                                <input type="file" name="foto_dokumen" accept="image/*, application/pdf" required>
                                                <button type="submit" name="upload" class="btn-action btn-upload"><i class="fas fa-cloud-arrow-up"></i></button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 5px 0;"><i class="fas fa-lock"></i> Upload hanya untuk Petugas BK</p>
                                    <?php endif; ?>

                                <?php } elseif ($data_ortu['status_dokumen'] == "Selesai") { ?>
                                    <span class="status-badge status-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($data_ortu['foto_dokumen']) ?>" target="_blank" class="btn-action btn-view"><i class="fas fa-image"></i> Lihat Dokumen</a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="height: 40px;"></div>
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="color: #64748b; font-size: 1.5rem; font-weight: 800; border-bottom: 2px solid #e2e8f0; display: inline-block; padding-bottom: 10px;">Laporan Riwayat Perjanjian</h2>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════
         BAGIAN 3: LAPORAN - Daftar surat perjanjian SISWA yang sudah dicetak
    ════════════════════════════════════════════════════════════════════ -->
    <div class="card-section" style="border-top: 4px solid #3b82f6;">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> Riwayat Perjanjian Siswa</h3>
            
            <form action="list_perjanjian.php" method="get" class="card-actions">
                <datalist id="pilihan_laporan_siswa">
                    <?php
                    $query_pilihan_laporan_siswa = mysqli_query($conn, "SELECT DISTINCT s.nis, s.nama_siswa FROM perjanjian_siswa ps JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa) JOIN siswa s USING(nis)");
                    while ($baris = mysqli_fetch_assoc($query_pilihan_laporan_siswa)) {
                        echo "<option value='" . htmlspecialchars($baris['nis']) . "'>";
                        echo "<option value='" . htmlspecialchars($baris['nama_siswa']) . "'>";
                    }
                    ?>
                </datalist>

                <div class="search-group">
                    <input type="text" name="cari_laporan_siswa" value="<?= isset($_GET['cari_laporan_siswa']) ? htmlspecialchars($_GET['cari_laporan_siswa']) : '' ?>" placeholder="NIS atau Nama Siswa..." list="pilihan_laporan_siswa" autocomplete="off" class="search-input">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                </div>
                <a href="list_perjanjian.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Tanggal Pembuatan</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 80px; text-align: center;">Tingkat</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_laporan_perjanjian_siswa || mysqli_num_rows($hasil_laporan_perjanjian_siswa) == 0) {
                        echo "<tr><td colspan='6' align='center' style='padding: 30px; color: #94a3b8;'>Belum ada data riwayat</td></tr>";
                    } else {
                        while ($data_laporan_siswa = mysqli_fetch_assoc($hasil_laporan_perjanjian_siswa)) {
                    ?>
                    <tr>
                        <td align="center" style="color: #94a3b8;"><?= $nomor_urut++ ?></td>
                        <td><span style="font-weight: 600; color: #475569;"><i class="far fa-calendar-alt" style="margin-right: 8px; color: #94a3b8;"></i><?= formatTanggalIndo($data_laporan_siswa['tanggal_surat']) ?></span></td>
                        <td><span class="nis-badge"><?= htmlspecialchars($data_laporan_siswa['nis']) ?></span></td>
                        <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($data_laporan_siswa['nama_siswa']) ?></td>
                        <td align="center"><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem;"><?= htmlspecialchars($data_laporan_siswa['tingkat']) ?></span></td>
                        <td>
                            <div class="action-stack">
                                <?php if ($data_laporan_siswa['status_dokumen'] == NULL) { ?>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_laporan_siswa['nis'] ?>&tanggal=<?= $data_laporan_siswa['tanggal_surat'] ?>" class="btn-action btn-detail"><i class="fas fa-eye"></i> Detail</a>
                                        <form action="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_siswa.php" method="post" style="margin: 0;">
                                            <input type="hidden" name="nis" value="<?= $data_laporan_siswa['nis'] ?>">
                                            <button type="submit" class="btn-action btn-print-mini"><i class="fas fa-print"></i> Cetak</button>
                                        </form>
                                    </div>
                                <?php } elseif ($data_laporan_siswa['status_dokumen'] == "Masih Proses") { ?>
                                    <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                                        <a href="/poin_pelanggara_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_laporan_siswa['nis'] ?>&tanggal=<?= $data_laporan_siswa['tanggal_surat'] ?>" class="btn-action btn-detail"><i class="fas fa-eye"></i></a>
                                        <a href="/poin_pelanggaran_siswa/pages/cetak/surat_perjanjian_siswa.php?nis=<?= $data_laporan_siswa['nis'] ?>" class="btn-action btn-print-mini"><i class="fas fa-print"></i></a>
                                    </div>
                                    <form action="" method="post" enctype="multipart/form-data" class="upload-mini">
                                        <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_laporan_siswa['tanggal_surat']) ?>">
                                        <input type="hidden" name="jenis_upload" value="siswa">
                                        <div style="display: flex; gap: 5px;">
                                            <input type="file" name="foto_dokumen" accept="image/*" required>
                                            <button type="submit" name="upload" class="btn-action btn-upload"><i class="fas fa-upload"></i></button>
                                        </div>
                                    </form>
                                <?php } elseif ($data_laporan_siswa['status_dokumen'] == "Selesai") { ?>
                                    <a href="/poin_pelanggaran_siswa/gambar/<?= htmlspecialchars($data_laporan_siswa['foto_dokumen']) ?>" target="_blank" class="btn-action btn-view"><i class="fas fa-image"></i> Lihat Dokumen</a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>



<br><br>

    <!-- ═══════════════════════════════════════════════════════════════════
         BAGIAN 4: LAPORAN - Daftar surat perjanjian ORTU yang sudah dicetak
    ════════════════════════════════════════════════════════════════════ -->
    <div class="card-section" style="border-top: 4px solid #f59e0b;">
        <div class="card-header">
            <h3><i class="fas fa-file-invoice"></i> Riwayat Perjanjian Ortu / Wali</h3>
            
            <form action="list_perjanjian.php" method="get" class="card-actions">
                <datalist id="pilihan_laporan_ortu">
                    <?php
                    $query_pilihan_laporan_ortu = mysqli_query($conn, "SELECT DISTINCT s.nis, s.nama_siswa FROM perjanjian_orang_tua po JOIN pelanggaran_siswa pel USING(id_pelanggaran_siswa) JOIN siswa s USING(nis)");
                    while ($baris = mysqli_fetch_assoc($query_pilihan_laporan_ortu)) {
                        echo "<option value='" . htmlspecialchars($baris['nis']) . "'>";
                        echo "<option value='" . htmlspecialchars($baris['nama_siswa']) . "'>";
                    }
                    ?>
                </datalist>

                <div class="search-group">
                    <input type="text" name="cari_laporan_ortu" value="<?= isset($_GET['cari_laporan_ortu']) ? htmlspecialchars($_GET['cari_laporan_ortu']) : '' ?>" placeholder="NIS atau Nama Siswa..." list="pilihan_laporan_ortu" autocomplete="off" class="search-input">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
                </div>
                <a href="list_perjanjian.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Tanggal Pembuatan</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 80px; text-align: center;">Tingkat</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    if (!$hasil_laporan_perjanjian_ortu || mysqli_num_rows($hasil_laporan_perjanjian_ortu) == 0) {
                        echo "<tr><td colspan='6' align='center' style='padding: 30px; color: #94a3b8;'>Belum ada data riwayat</td></tr>";
                    } else {
                        while ($data_laporan_ortu = mysqli_fetch_assoc($hasil_laporan_perjanjian_ortu)) {
                    ?>
                    <tr>
                        <td align="center" style="color: #94a3b8;"><?= $nomor_urut++ ?></td>
                        <td><span style="font-weight: 600; color: #475569;"><i class="far fa-calendar-alt" style="margin-right: 8px; color: #94a3b8;"></i><?= formatTanggalIndo($data_laporan_ortu['tanggal_surat']) ?></span></td>
                        <td><span class="nis-badge"><?= htmlspecialchars($data_laporan_ortu['nis']) ?></span></td>
                        <td style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($data_laporan_ortu['nama_siswa']) ?></td>
                        <td align="center"><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem;"><?= htmlspecialchars($data_laporan_ortu['tingkat']) ?></span></td>
                        <td>
                            <div class="action-stack">
                                <?php if ($data_laporan_ortu['status_dokumen'] == NULL) { ?>
                                    <div style="display: flex; gap: 5px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_laporan_ortu['nis'] ?>&tanggal=<?= $data_laporan_ortu['tanggal_surat'] ?>&from=list_perjanjian.php" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i> Detail</a>
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <form action="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_ortu.php" method="post" style="margin: 0;">
                                                <input type="hidden" name="nis" value="<?= $data_laporan_ortu['nis'] ?>">
                                                <button type="submit" class="btn-action btn-print-mini"><i class="fas fa-print"></i> Cetak</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php } elseif ($data_laporan_ortu['status_dokumen'] == "Masih Proses") { ?>
                                    <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                                        <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_laporan_ortu['nis'] ?>&tanggal=<?= $data_laporan_ortu['tanggal_surat'] ?>" class="btn-action btn-detail" title="Detail"><i class="fas fa-eye"></i></a>
                                        <?php if (strtolower(trim($_SESSION['role'])) !== 'guru'): ?>
                                            <a href="/poin_pelanggaran_siswa/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $data_laporan_ortu['nis'] ?>" class="btn-action btn-print-mini" title="Cetak Surat"><i class="fas fa-print"></i></a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])): ?>
                                        <form action="" method="post" enctype="multipart/form-data" class="upload-mini">
                                            <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($data_laporan_ortu['tanggal_surat']) ?>">
                                            <input type="hidden" name="jenis_upload" value="perjanjian_orang_tua">
                                            <div style="display: flex; gap: 5px;">
                                                <input type="file" name="foto_dokumen" accept="image/*" required>
                                                <button type="submit" name="upload" class="btn-action btn-upload"><i class="fas fa-upload"></i></button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                <?php } elseif ($data_laporan_ortu['status_dokumen'] == "Selesai") { ?>
                                    <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($data_laporan_ortu['foto_dokumen']) ?>" target="_blank" class="btn-action btn-view"><i class="fas fa-image"></i> Lihat Dokumen</a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div> <!-- End of .container -->

<?php include ROOTPATH . "/includes/footer.php"; ?>
