<?php
// ============================================================
// File   : list_panggilan_ortu.php
// Fungsi : Menampilkan daftar siswa yang perlu dipanggil
//          orang tuanya (karena poin pelanggaran sudah 50-100)
//          serta daftar surat panggilan yang sudah dibuat.
// ============================================================

// Langkah 1: Tentukan lokasi folder utama proyek di server
// Ini seperti memberi tahu di mana "alamat rumah" program ini berada
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Langkah 2: Hubungkan ke database (membuka buku catatan data siswa)
include ROOTPATH . "/config/config.php";


// ============================================================
// FUNGSI PEMBANTU: Ubah format tanggal ke Bahasa Indonesia
// Contoh: "2025-03-01 10:00:00" → "01 Maret 2025" (atau beserta jamnya)
// ============================================================
function formatTanggalIndo($tanggal_database, $tampilkan_jam = false) {
    // Daftar nama bulan dalam Bahasa Indonesia
    $nama_bulan = [
        "01" => "Januari",  "02" => "Pebruari", "03" => "Maret",
        "04" => "April",    "05" => "Mei",       "06" => "Juni",
        "07" => "Juli",     "08" => "Agustus",   "09" => "September",
        "10" => "Oktober",  "11" => "November",  "12" => "Desember"
    ];
    
    // Pecah antara tanggal dan waktu (jika ada spasinya)
    $waktu_pecah = explode(" ", $tanggal_database);
    $hanya_tanggal = $waktu_pecah[0]; // Ambil bagian tanggal saja (YYYY-MM-DD)
    
    // Ubah format tanggal dari database (YYYY-MM-DD) menjadi (DD-MM-YYYY) lalu dipisah
    $bagian_tanggal = explode("-", date("d-m-Y", strtotime($hanya_tanggal)));
    
    // Gabungkan: hari + nama bulan (bukan angka) + tahun
    $hasil = $bagian_tanggal[0] . " " . $nama_bulan[$bagian_tanggal[1]] . " " . $bagian_tanggal[2];
    
    // Jika ada perintah untuk menampilkan jam, tambahkan keterangan jam
    if ($tampilkan_jam && isset($waktu_pecah[1])) {
        // Ambil bagian jam (H:i) menghilangkan hitungan detiknya
        $jam = date("H:i", strtotime($waktu_pecah[1]));
        $hasil .= "<br>Jam : " . $jam;
    }
    
    return $hasil;
}

// ============================================================
// FUNGSI PEMBANTU: Tampilkan daftar jenis pelanggaran siswa
// Dibuat agar tidak perlu menulis kode panjang berulang-ulang
// ============================================================
function tampilkanJenisPelanggaran($conn, $nis_siswa) {
    // Cari jenis pelanggaran siswa yang berbeda-beda (DISTINCT supaya tidak kembar)
    $query = mysqli_query($conn, 
        "SELECT DISTINCT jenis 
         FROM pelanggaran_siswa 
         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) 
         WHERE nis = '$nis_siswa'"
    );
    
    // Siapkan kotak (array) kosong untuk menampung nama pelanggaran
    $daftar = [];
    
    // Masukkan hasil pencarian satu per satu ke dalam kotak
    while ($baris = mysqli_fetch_assoc($query)) {
        // htmlspecialchars = membersihkan teks agar aman dipakai di website
        $daftar[] = htmlspecialchars($baris['jenis']);
    }
    
    // Jika kotak tidak kosong, gabungkan pakai koma dan akhiri dengan tanda titik
    if (!empty($daftar)) {
        echo implode(', ', $daftar) . '.';
    }
}


// ============================================================
// PROSES UPLOAD FOTO DOKUMEN (SURAT PERATURAN YANG SUDAH DITANDATANGANI)
// ============================================================
if (isset($_POST['upload']) && isset($_FILES["foto_dokumen"])) {
    // Mengambil nama file gambar yang diupload dan filenya itu sendiri
    $nama_file_foto  = $_FILES["foto_dokumen"]['name'];
    $data_file_foto  = $_FILES["foto_dokumen"];
    
    // Menentukan lokasi folder untuk menyimpan foto (folder "gambar/")
    $folder_tujuan   = ROOTPATH . "/assets/images/";
    $lokasi_simpan   = $folder_tujuan . $nama_file_foto;
    
    // Mengambil tanggal dan jenis uploannya dari form
    $tanggal_surat   = $_POST['tanggal_surat'];
    $jenis_upload    = $_POST['jenis_upload']; // nilai bisa "siswa" atau "perjanjian_orang_tua"

    // Memindahkan file foto dari tempat sementara komputer ke folder yang dituju
    if (move_uploaded_file($data_file_foto["tmp_name"], $lokasi_simpan)) {
        // Tentukan tabel mana yang akan diperbarui berdasarkan jenis laporannya
        if ($jenis_upload == "siswa") {
            $nama_tabel = "perjanjian_siswa";
        } else {
            $nama_tabel = "perjanjian_orang_tua";
        }
        
        // Membersihkan nama file & tanggal sebelum disimpan agar database tidak kena virus (Injection)
        $nama_file_aman  = mysqli_real_escape_string($conn, $nama_file_foto);
        $tanggal_aman    = mysqli_real_escape_string($conn, $tanggal_surat);

        // Perbarui data dengan status 'Selesai' dan simpan nama fotonya
        $query_update = mysqli_query($conn,
            "UPDATE $nama_tabel
             SET foto_dokumen = '$nama_file_aman', status = 'Selesai'
             WHERE tanggal = '$tanggal_aman'"
        );

        // Jika berhasil mengubah data, beritahu pengguna dan ulangi halaman (refresh)
        if ($query_update) {
            echo "<script>alert('Berhasil Mengunggah Foto Dokumen');window.location.href='list_perjanjian.php'</script>";
        } else {
            // Jika gagal, tampilkan pesan error dari komputernya
            echo "Gagal Mengunggah Foto Dokumen: " . mysqli_error($conn);
        }
    }
}

// ============================================================
// QUERY 1: CARI CALON SISWA YANG PERLU DIPANGGIL ORTU NYA (Poin 50-100)
// ============================================================

// Cek jika pengguna mengetik nama siswa di kotak pencarian
if (isset($_GET['cari_daftar_calon_ortu'])) {
    
    // Bersihkan teks kata kunci agar bersih dari huruf terlarang/berbahaya
    $kata_kunci_calon = mysqli_real_escape_string($conn, $_GET['cari_daftar_calon_ortu']);
    
    // Cari siswa berdasarkan nama atau NIS, kelompokkan per nis & tanggal (agar ramah buat web server Nginx)
    $query_calon_panggilan = "
        SELECT main.*, sub.total_poin
        FROM (
            -- Bagian Dalam: Cari data pelanggaran (dipisah per siswa & tanggal) 
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa 
            JOIN pelanggaran_siswa USING(nis) 
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif' 
              AND (siswa.nama_siswa LIKE '%$kata_kunci_calon%' OR siswa.nis LIKE '%$kata_kunci_calon%')
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            -- Subquery Luar: Hitung total semua poin tiap-tiap siswa
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        -- Filter: Hanya tampilkan kalau jumlah akhirnya antara 50 sampai 100
        WHERE sub.total_poin BETWEEN 50 AND 100
    ";
} else {
    // Jika pengguna tidak memakai pencarian, langsung panggil semuanya
    $query_calon_panggilan = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, po.tanggal AS tanggal_surat, po.status AS status_dokumen, po.foto_dokumen
            FROM siswa 
            JOIN pelanggaran_siswa USING(nis) 
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN perjanjian_orang_tua po USING(id_pelanggaran_siswa)
            WHERE siswa.status = 'aktif'
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

// ============================================================
// QUERY 3: CARI SISWA DENGAN POIN RINGAN (1-25)
// ============================================================
$query_ringan = "
    SELECT siswa.*, sub.total_poin
    FROM siswa
    JOIN (
        SELECT nis, SUM(poin) AS total_poin
        FROM pelanggaran_siswa
        JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
        GROUP BY nis
    ) sub USING(nis)
    WHERE siswa.status = 'aktif' AND sub.total_poin BETWEEN 1 AND 25
    ORDER BY sub.total_poin DESC
";
$hasil_ringan = mysqli_query($conn, $query_ringan);
// Jalankan misinya ke database (Kirim pesanan datanya)
$hasil_calon_panggilan = mysqli_query($conn, $query_calon_panggilan);

// Kelompokkan data hasil kueri ke dalam dua list: Belum Selesai dan Selesai
$list_panggilan_proses = [];
$list_panggilan_selesai = [];

if ($hasil_calon_panggilan) {
    while ($row = mysqli_fetch_assoc($hasil_calon_panggilan)) {
        if ($row['status_dokumen'] == 'Selesai') {
            $list_panggilan_selesai[] = $row;
        } else {
            $list_panggilan_proses[] = $row;
        }
    }
}

// ============================================================
// QUERY 2: CARI DATA SURAT PANGGILAN ORTU YANG SUDAH DIBUAT (DI TABEL SURAT_KELUAR)
// ============================================================

// Jika pengguna mengetik di kotak pencarian laporan surat
if (isset($_GET['cari_laporan_panggilan'])) {
    
    // Bersihkan kata kunci yang dimasukkan
    $kata_kunci_laporan = mysqli_real_escape_string($conn, $_GET['cari_laporan_panggilan']);
    
    // Cari daftar surat keluar yang jenisnya 'Panggilan Orang Tua' sesuai pencarian
    $query_laporan = mysqli_query($conn, 
        "SELECT * 
         FROM surat_keluar 
         JOIN siswa USING(nis) 
         WHERE jenis_surat = 'Panggilan Orang Tua' 
           AND (nama_siswa LIKE '%$kata_kunci_laporan%' OR nis LIKE '%$kata_kunci_laporan%') 
         ORDER BY tanggal_pemanggilan DESC"
    );	
    
} else {
    // Jika tidak mencari apa-apa, ambil SEMUA data surat yang jenisnya 'Panggilan Orang Tua'
    $query_laporan = mysqli_query($conn, 
        "SELECT * 
         FROM surat_keluar 
         JOIN siswa USING(nis) 
         WHERE jenis_surat = 'Panggilan Orang Tua' 
         ORDER BY tanggal_pemanggilan DESC"
    );
}


// Langkah terakhir sebelum HTML: tampilkan bagian atas halaman (header)
include ROOTPATH . "/includes/header.php";
?>

<center>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/laporan/list_panggilan_ortu.css">

    <!-- Tombol cetak langsung surat panggilan orang tua kosong / manual -->
    <?php if (trim(strtolower($_SESSION['role'])) !== 'guru'): ?>
        <button class="btn-print" onclick="window.location.href='/poin_pelanggaran_siswa/pages/cetak/add_panggilan_ortu.php'">
            <!-- icon printer (gambar mesin pencetak yang lucu) -->
            <span class="printer-wrapper">
                <span class="printer-container">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75" height="20px" width="20px">
                        <path stroke-width="5" stroke="white" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
                        <mask fill="white" id="path-2-inside-1_30_7"><path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path></mask>
                        <path mask="url(#path-2-inside-1_30_7)" fill="white" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
                        <circle fill="white" r="3" cy="49" cx="78"></circle>
                    </svg>
                </span>
                <span class="printer-page-wrapper"><span class="printer-page"></span></span>
            </span>
            &nbsp;&nbsp;Cetak Surat Panggilan Ortu/Wali
        </button><br><br>
    <?php endif; ?>


    <!-- ═════════════════════════════════════════════════════════
     BAGIAN TAMBAHAN: TABEL SISWA POIN RINGAN (1 - 25)
     Untuk Pembinaan Wali Kelas
     ═════════════════════════════════════════════════════════ -->
<div class="table-container-premium">
    <div class="scroll">
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="5">
                            <div class="table-header-premium" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <h3 class="table-header-title" style="color: white;"><i class="fas fa-leaf"></i> Daftar Siswa Poin Ringan (1 - 25 Poin)</h3>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <span style="background: rgba(255,255,255,0.2); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Pembinaan Wali Kelas</span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 100px;">Total Poin</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no_ringan = 1;
                    if (!$hasil_ringan || mysqli_num_rows($hasil_ringan) == 0) {
                        echo "<tr><td colspan='5' align='center' style='padding: 30px; color: #94a3b8;'>Belum ada siswa di kategori ini</td></tr>";
                    } else {
                        while ($row_ringan = mysqli_fetch_assoc($hasil_ringan)) {
                    ?>
                    <tr>
                        <td align="center"><?= $no_ringan++ ?></td>
                        <td align="center"><span class="nis-badge"><?= htmlspecialchars($row_ringan['nis']) ?></span></td>
                        <td style="font-weight: 700; color: #334155;"><?= htmlspecialchars($row_ringan['nama_siswa']) ?></td>
                        <td align="center">
                            <span class="poin-badge-premium" style="background: #ecfdf5; color: #059669; border: 1px solid #d1fae5;"><?= $row_ringan['total_poin'] ?></span>
                        </td>
                        <td align="center">
                            <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $row_ringan['nis'] ?>&from=list_panggilan_ortu.php" class="btn-primary" style="text-decoration:none; padding: 6px 12px; font-size: 0.85rem; border-radius: 8px;">
                                <i class="fas fa-eye"></i> Pantau
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } 
                    ?>
                </tbody>
            </table>
    </div>
</div>

    <!-- ═════════════════════════════════════════════════════════
         BAGIAN 1: TABEL DAFTAR SISWA YANG BISA DIPANGGIL (Poin 50-100)
         ═════════════════════════════════════════════════════════ -->
<div class="table-container-premium">
    <div class="scroll">
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="6">
                            <div class="table-header-premium">
                                <h3 class="table-header-title">Daftar Siswa di atas 50 Poin (Belum/Sedang Proses)</h3>
                                
                                <!-- Form Pencarian Calon Surat Panggilan -->
                                <form action="list_panggilan_ortu.php" method="get" class="premium-search-form">
                                    <!-- Datalist: memunculkan alat bantu pilih otomatis saat mengetik -->
                                    <datalist id="pilihan_calon_ortu">
                                        <?php
                                        // Ambil saja daftar siswa yang poinnya 50-100 untuk daftar pilihan ketikan cepat
                                        $query_pilihan = mysqli_query($conn,
                                            "SELECT nama_siswa, nis 
                                             FROM siswa 
                                             JOIN pelanggaran_siswa USING(nis) 
                                             JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) 
                                             WHERE siswa.status = 'aktif' 
                                             GROUP BY nis, nama_siswa 
                                             HAVING SUM(poin) BETWEEN 50 AND 100"
                                        );
                                        while ($baris_pilihan = mysqli_fetch_assoc($query_pilihan)) {
                                            echo "<option value='" . htmlspecialchars($baris_pilihan['nis']) . "'>";
                                            echo "<option value='" . htmlspecialchars($baris_pilihan['nama_siswa']) . "'>";
                                        }
                                        ?>
                                    </datalist>
                                    
                                    <div class="search-input-wrapper">
                                        <i class="fas fa-search"></i>
                                        <input type="text"
                                               name="cari_daftar_calon_ortu"
                                               value="<?= isset($_GET['cari_daftar_calon_ortu']) ? htmlspecialchars($_GET['cari_daftar_calon_ortu']) : '' ?>"
                                               placeholder="Masukkan NIS / Nama Siswa"
                                               list="pilihan_calon_ortu"
                                               autocomplete="off">
                                    </div>
                                    <button type="submit" class="btn-search-premium">
                                        <i class="fas fa-filter"></i> Cari
                                    </button>
                                    <a href="list_panggilan_ortu.php" class="btn-reset-premium">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </form>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut = 1;
                    
                    if (empty($list_panggilan_proses)) {
                        echo "<tr><td colspan='6' align='center' style='padding: 30px; color: #94a3b8;'>Semua tugas sudah selesai atau tidak ada data yang perlu diproses.</td></tr>";
                    } else {
                        foreach ($list_panggilan_proses as $baris_calon) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center">
                            <span class="nis-badge"><?= htmlspecialchars($baris_calon['nis']) ?></span>
                        </td>
                        <td style="font-weight: 700; color: #1e293b;">
                            <?= htmlspecialchars($baris_calon['nama_siswa']) ?>
                        </td>
                        <td align="center">
                            <div class="v-summary">
                                <?php tampilkanJenisPelanggaran($conn, $baris_calon['nis']); ?>
                            </div>
                        </td>
                        <td align="center">
                            <span class="poin-badge-premium"><?= htmlspecialchars($baris_calon['total_poin']) ?></span>
                        </td>
                        <td>
                            <div class="action-stack">
                            <?php 
                            if ($baris_calon['status_dokumen'] == NULL) { 
                            ?>
                                <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $baris_calon['nis'] ?>&tanggal=<?= $baris_calon['tanggal_surat'] ?>&from=list_panggilan_ortu.php" class="btn-primary" style="text-decoration:none;">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a> 
                                
                                <?php if ($_SESSION['role'] !== 'guru'): ?>
                                    <?php 
                                    $cek_surat_panggilan = mysqli_query($conn, "SELECT nis FROM surat_keluar WHERE nis = '" . mysqli_real_escape_string($conn, $baris_calon['nis']) . "' AND jenis_surat = 'Panggilan Orang Tua'");
                                    if(mysqli_num_rows($cek_surat_panggilan) == 0){
                                    ?>
                                        <hr>
                                        <form action="/poin_pelanggaran_siswa/pages/cetak/add_panggilan_ortu.php" method="post">
                                            <input type="hidden" name="nis" value="<?= $baris_calon['nis'] ?>">
                                            <button type="submit" class="btn-warning">
                                                <i class="fas fa-print"></i> Cetak Panggilan
                                            </button>
                                        </form> 
                                    <?php } ?>
                                    
                                    <?php 
                                    $cek_surat_perjanjian = mysqli_query($conn, "SELECT nis FROM surat_keluar WHERE nis = '" . mysqli_real_escape_string($conn, $baris_calon['nis']) . "' AND jenis_surat = 'Perjanjian Ortu'");
                                    if(mysqli_num_rows($cek_surat_perjanjian) == 0){ 
                                    ?>
                                        <hr>
                                        <form action="/poin_pelanggaran_siswa/pages/cetak/add_perjanjian_ortu.php" method="post">
                                            <input type="hidden" name="nis" value="<?= $baris_calon['nis'] ?>">
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-file-signature"></i> Cetak Perjanjian
                                            </button>
                                        </form>
                                    <?php } ?>
                                <?php endif; ?>
                                
                            <?php 
                            } elseif ($baris_calon['status_dokumen'] == "Masih Proses") { 
                            ?>
                                <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $baris_calon['nis'] ?>&tanggal=<?= $baris_calon['tanggal_surat'] ?>&from=list_panggilan_ortu.php" class="btn-primary" style="text-decoration:none;">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a>
                                <?php if ($_SESSION['role'] !== 'guru'): ?>
                                    <hr>
                                    <a href="/poin_pelanggaran_siswa/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $baris_calon['nis'] ?>" class="btn-primary" style="text-decoration:none;">
                                        <i class="fas fa-print"></i> Surat TTD Ortu
                                    </a> 
                                <?php endif; ?>
                                <?php if (!in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])): ?>
                                    <form action="" method="post" enctype="multipart/form-data" class="upload-mini">
                                        <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($baris_calon['tanggal_surat']) ?>">
                                        <input type="hidden" name="jenis_upload" value="perjanjian_orang_tua">
                                        <div style="font-size:0.75rem; margin-bottom:5px; font-weight:700; color:#64748b;">Upload Dokumen:</div>
                                        <input type="file" name="foto_dokumen" accept="image/*, application/pdf" required style="font-size:0.8rem; margin-bottom:8px;">
                                        <button type="submit" name="upload" class="btn-warning" style="width:100%;">
                                            <i class="fas fa-camera"></i> Upload
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 10px 0;"><i class="fas fa-lock"></i> Upload hanya untuk Petugas BK</p>
                                <?php endif; ?>
                            
                            <?php 
                            } elseif ($baris_calon['status_dokumen'] == "Selesai") { 
                            ?>
                                <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($baris_calon['foto_dokumen']) ?>" target="_blank" class="btn-primary" style="text-decoration:none;">
                                    <i class="fas fa-image"></i> Lihat Gambar
                                </a>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        } // akhir dari bacaan baris di tabel ini (foreach)
                    } // akhir dari kondisi berhasil ada data (else)
                    ?>
                </tbody>
            </table>
    </div>
</div>

<br><br>

<!-- ═════════════════════════════════════════════════════════
     BAGIAN 1.B: DAFTAR PANGGILAN ORTU YANG SUDAH SELESAI (UPLOADED)
     ═════════════════════════════════════════════════════════ -->
<div class="table-container-premium" style="border-top: 4px solid #10b981;">
    <div class="scroll">
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="6">
                            <div class="table-header-premium">
                                <h3 class="table-header-title"><i class="fas fa-check-circle" style="color: #10b981;"></i> Daftar Panggilan Ortu (Selesai/Sudah di-Upload)</h3>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 120px;">NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th style="width: 80px;">Poin</th>
                        <th style="width: 150px;">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $nomor_urut_selesai = 1;
                    if (empty($list_panggilan_selesai)) {
                        echo "<tr><td colspan='6' align='center' style='padding:30px; color:#94a3b8;'>Belum ada dokumen yang selesai di-upload.</td></tr>";
                    } else {
                        foreach ($list_panggilan_selesai as $baris_selesai) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut_selesai++ ?></td>
                        <td align="center">
                            <span class="nis-badge"><?= htmlspecialchars($baris_selesai['nis']) ?></span>
                        </td>
                        <td style="font-weight: 700; color: #1e293b;">
                            <?= htmlspecialchars($baris_selesai['nama_siswa']) ?>
                        </td>
                        <td align="center">
                            <div class="v-summary">
                                <?php tampilkanJenisPelanggaran($conn, $baris_selesai['nis']); ?>
                            </div>
                        </td>
                        <td align="center">
                            <span class="poin-badge-premium"><?= htmlspecialchars($baris_selesai['total_poin']) ?></span>
                        </td>
                        <td align="center">
                            <div class="action-stack">
                                <span class="badge-success" style="background:#dcfce7; color:#166534; padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:700; margin-bottom:8px; display:inline-block;">
                                    <i class="fas fa-check"></i> Selesai
                                </span>
                                <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($baris_selesai['foto_dokumen']) ?>" target="_blank" class="btn-primary" style="text-decoration:none; padding:6px 12px; font-size:0.8rem;">
                                    <i class="fas fa-image"></i> Lihat Dokumen
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } 
                    ?>
                </tbody>
            </table>
    </div>
</div>


<br><br>



<br><br>

<!-- ═════════════════════════════════════════════════════════
     BAGIAN 2: TABEL SURAT PANGGILAN ORANG TUA (YANG SUDAH DICETAK)
     Dapat dari tabel database bernama: surat_keluar
     ═════════════════════════════════════════════════════════ -->
<div class="table-container-premium">
    <div class="scroll">
            <table width="100%">
                <thead>
                    <tr>
                        <th colspan="8">
                            <div class="table-header-premium">
                                <h3 class="table-header-title">Laporan Data Surat Panggilan Ortu</h3>
                                
                                <!-- Form Pencarian Laporan Panggilan yang Sudah Teregistrasi -->
                                <form action="list_panggilan_ortu.php" method="get" class="premium-search-form">
                                    <!-- Datalist Pilihan Laporan (Bantuan Ketik) -->
                                    <datalist id="pilihan_laporan">
                                        <?php
                                        $query_pilihan_laporan = mysqli_query($conn, 
                                            "SELECT nama_siswa, nis 
                                             FROM surat_keluar 
                                             JOIN siswa USING(nis) 
                                             WHERE jenis_surat = 'Panggilan Orang Tua' 
                                             GROUP BY nis, nama_siswa"
                                        );
                                        while ($baris_laporan = mysqli_fetch_assoc($query_pilihan_laporan)) {
                                            echo "<option value='" . htmlspecialchars($baris_laporan['nis']) . "'>";
                                            echo "<option value='" . htmlspecialchars($baris_laporan['nama_siswa']) . "'>";
                                        }
                                        ?>
                                    </datalist>
                                    
                                    <div class="search-input-wrapper">
                                        <i class="fas fa-search"></i>
                                        <input type="text"
                                               name="cari_laporan_panggilan"
                                               value="<?= isset($_GET['cari_laporan_panggilan']) ? htmlspecialchars($_GET['cari_laporan_panggilan']) : '' ?>"
                                               placeholder="Masukkan NIS / Nama Siswa"
                                               list="pilihan_laporan"
                                               autocomplete="off">
                                    </div>
                                    <button type="submit" class="btn-search-premium">
                                        <i class="fas fa-filter"></i> Cari
                                    </button>
                                    <!-- Tombol Merah buat balikin ke setelan tabel semula tanpa difilter -->
                                    <a href="list_panggilan_ortu.php" class="btn-reset-premium">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </form>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>Tanggal Pemanggilan Ortu/Wali</th>
                        <th>No Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $nomor_urut_laporan = 1;

                    // Menggunakan hasil query tabel surat_keluar yang kita jalankan di paling atas
                    if (!$query_laporan || mysqli_num_rows($query_laporan) == 0) {
                        echo "<tr><td colspan='8' align='center'>Data Tidak Ditemukan</td></tr>";
                    } else {
                        while ($baris_surat = mysqli_fetch_assoc($query_laporan)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut_laporan++ ?></td>
                        
                        <td align="center">
                            <div class="date-box" style="display:flex; flex-direction:column; gap:2px;">
                                <span style="font-weight:700; color:#1e293b; font-size:0.85rem;">
                                    <?= formatTanggalIndo($baris_surat['tanggal_pembuatan_surat'], false) ?>
                                </span>
                            </div>
                        </td>
                        
                        <td align="center">
                            <div class="date-box" style="display:flex; flex-direction:column; gap:2px;">
                                <span style="font-weight:700; color:#1e293b; font-size:0.85rem;">
                                    <?= formatTanggalIndo($baris_surat['tanggal_pemanggilan'], false) ?>
                                </span>
                                <span style="font-size:0.75rem; color:#64748b; font-weight:600;">
                                    <?= date("H:i", strtotime($baris_surat['tanggal_pemanggilan'])) ?> WIB
                                </span>
                            </div>
                        </td>
                        
                        <td align="center" style="font-weight:700; color:#1C6EA4; font-family:'JetBrains Mono', monospace; font-size:0.85rem;">
                            <?= htmlspecialchars($baris_surat['no_surat']) ?>
                        </td>
                        <td align="center">
                            <span class="nis-badge"><?= htmlspecialchars($baris_surat['nis']) ?></span>
                        </td>
                        <td style="font-weight:700; color:#334155;">
                            <?= htmlspecialchars($baris_surat['nama_siswa']) ?>
                        </td>
                        <td style="font-size:0.85rem; color:#64748b; max-width:200px;">
                            <?= htmlspecialchars($baris_surat['keperluan']) ?>
                        </td>
                        
                        <td align="center">
                            <a href="/poin_pelanggaran_siswa/pages/cetak/surat_panggilan_ortu.php?no_surat=<?= urlencode($baris_surat['no_surat']) ?>" class="btn-primary" style="text-decoration:none; border-radius:50%; width:35px; height:35px; padding:0; display:inline-flex; align-items:center; justify-content:center;" title="Cetak Ulang">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                        } // akhir siklus baris laporan 
                    } // akhir kalau sukses nemu data laporan
                    ?>
                </tbody>
            </table>
    </div>
</div>

<?php 
// Pasang bagian paling bawah (kaki) dari website, isinya teks hak cipta dsb (footer)
include ROOTPATH . "/includes/footer.php"; 
?>