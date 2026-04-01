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
    
    // Menentukan lokasi folder untuk menyimpan foto (folder "assets/images/")
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
// Jalankan misinya ke database (Kirim pesanan datanya)
$hasil_calon_panggilan = mysqli_query($conn, $query_calon_panggilan);


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

    <!-- Tombol cetak langsung surat panggilan orang tua kosong / manual -->
    <button class="print-btn" onclick="window.location.href='/poin_pelanggaran_siswa/pages/cetak/add_panggilan_ortu.php'">
        <!-- icon printer (gambar mesin pencetak yang lucu) -->
        <span class="printer-wrapper">
            <span class="printer-container">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75">
                    <path stroke-width="5" stroke="black" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
                    <mask fill="white" id="path-2-inside-1_30_7"><path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path></mask>
                    <path mask="url(#path-2-inside-1_30_7)" fill="black" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
                    <circle fill="black" r="3" cy="49" cx="78"></circle>
                </svg>
            </span>
            <span class="printer-page-wrapper"><span class="printer-page"></span></span>
        </span>
        &nbsp;&nbsp;Cetak Surat Panggilan Ortu/Wali
    </button><br>

    <!-- ═════════════════════════════════════════════════════════
         BAGIAN 1: TABEL DAFTAR SISWA YANG BISA DIPANGGIL (Poin 50-100)
         ═════════════════════════════════════════════════════════ -->
    <fieldset style="width: 70%;">
        <legend>Daftar Calon Pembuat Surat Panggilan Ortu/Wali</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa di atas 50 Poin (Belum/Sedang Proses)</h3>
                            
                            <!-- Form Pencarian Calon Surat Panggilan -->
                            <form action="list_panggilan_ortu.php" method="get">
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
                                
                                <input type="text"
                                       name="cari_daftar_calon_ortu"
                                       value="<?= isset($_GET['cari_daftar_calon_ortu']) ? htmlspecialchars($_GET['cari_daftar_calon_ortu']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_calon_ortu"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="list_panggilan_ortu.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
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
                    
                    // Pastikan query tidak terkena error dan datanya tidak kosong
                    if (!$hasil_calon_panggilan || mysqli_num_rows($hasil_calon_panggilan) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        if (!$hasil_calon_panggilan) {
                            // Tampilkan jika pencariannya gagal karena kodenya salah
                            echo "<tr><td colspan='6' align='center' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        // Jika ada datanya, perlihatkan sebaris demi sebaris
                        while ($baris_calon = mysqli_fetch_assoc($hasil_calon_panggilan)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center"><?= htmlspecialchars($baris_calon['nis']) ?></td>
                        <td><?= htmlspecialchars($baris_calon['nama_siswa']) ?></td>
                        <td align="center" width="400px">
                            <?php 
                            // Pakai fungsi tampilkanJenisPelanggaran yang kita buat di bagian atas
                            tampilkanJenisPelanggaran($conn, $baris_calon['nis']); 
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($baris_calon['total_poin']) ?></td>
                        <td>
                            <?php 
                            // ===== OPSI TOMBOL TERGANTUNG PROGRESS SURATNYA =====
                            
                            // 1. Jika statusnya KOSONG (NULL) -> Belum ada surat yang dibuat
                            if ($baris_calon['status_dokumen'] == NULL) { 
                            ?>
                                <!-- Tombol untuk melihat detail kesalahannya secara lengkap -->
                                <button class="btn-primary">
                                    <a href="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/laporan/detail_pelanggaran.php?nis=<?= $baris_calon['nis'] ?>&tanggal=<?= $baris_calon['tanggal_surat'] ?>">Detail</a>
                                </button> 
                                
                                <?php 
                                // Cek, apakah guru pernah membuatkan Surat Panggilan untuk anak ini sebelumnya?
                                $cek_surat_panggilan = mysqli_query($conn, 
                                    "SELECT nis FROM surat_keluar 
                                     WHERE nis = '" . mysqli_real_escape_string($conn, $baris_calon['nis']) . "' 
                                     AND jenis_surat = 'Panggilan Orang Tua'"
                                );
                                // Kalau BELUM ADA sama sekali, tampilkan tombol untuk "Cetak Panggilan"
                                if(mysqli_num_rows($cek_surat_panggilan) == 0){
                                ?>
                                    <hr>
                                    <form action="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/cetak/add_panggilan_ortu.php" method="post">
                                        <input type="hidden" name="nis" value="<?= $baris_calon['nis'] ?>">
                                        <input type="submit" value="Cetak Panggilan Ortu" style="padding: 10px 15px;font-weight:bold;background-color: #fff;border-radius: 5px;border: 1px solid #ccc;">
                                    </form> 
                                <?php } ?>
                                
                                <?php 
                                // Cek lagi, apakah sudah ada Surat Perjanjian untuk orang tuanya?
                                $cek_surat_perjanjian = mysqli_query($conn, 
                                    "SELECT nis FROM surat_keluar 
                                     WHERE nis = '" . mysqli_real_escape_string($conn, $baris_calon['nis']) . "' 
                                     AND jenis_surat = 'Perjanjian Ortu'"
                                );
                                // Kalau BELUM ADA, beri tombol "Cetak Perjanjian"
                                if(mysqli_num_rows($cek_surat_perjanjian) == 0){ 
                                ?>
                                    <hr>
                                    <form action="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/cetak/add_perjanjian_ortu.php" method="post">
                                        <input type="hidden" name="nis" value="<?= $baris_calon['nis'] ?>">
                                        <input type="submit" value="Cetak Perjanjian Ortu" style="padding: 10px 15px;font-weight:bold;background-color: #fff;border-radius: 5px;border: 1px solid #ccc;">
                                    </form>
                                <?php } ?>
                                
                            <?php 
                            // 2. Jika statusnya 'Masih Proses' (Sudah dicipatakan kertas surat, menunggu foto surat ditandatangani)
                            } elseif ($baris_calon['status_dokumen'] == "Masih Proses") { 
                            ?>
                                <button class="btn-primary">
                                    <a href="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/laporan/detail_pelanggaran.php?nis=<?= $baris_calon['nis'] ?>&tanggal=<?= $baris_calon['tanggal_surat'] ?>">Detail Pelanggaran</a>
                                </button>
                                <hr>
                                <button class="btn-primary">
                                    <a href="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/cetak/surat_perjanjian_ortu.php?nis=<?= $baris_calon['nis'] ?>">Cetak Surat TTD Ortu</a>
                                </button> 
                                <hr>
                                <!-- Ini adalah area tempat kita mengupload/mengirim foto kertas yang tertanda tangan -->
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="tanggal_surat" value="<?= htmlspecialchars($baris_calon['tanggal_surat']) ?>">
                                    <input type="hidden" name="jenis_upload" value="perjanjian_orang_tua">
                                    <!-- 'accept="image/*"' artinya dia menerima semua jenis gambar (jika memotret dengan HP dsb) -->
                                    <input type="file" name="foto_dokumen" accept="image/*" required>
                                    <input type="submit" name="upload" value="Upload" class="btn-warning" style="color:white;font-weight:bold;margin-top:7px;">
                                </form>
                            
                            <?php 
                            // 3. Jika statusnya 'Selesai' (Sudah diupload gambarnya, maka langsung tampilkan fotonya)
                            } elseif ($baris_calon['status_dokumen'] == "Selesai") { 
                            ?>
                                <!-- '_blank' membuat gambar kebuka di jendela baru browser, sehingga halaman tabel terhindari dari tutup paksa -->
                                <a href="/Poin_Pelanggaran_Siswa_XIIRPL4/gambar/<?= htmlspecialchars($baris_calon['foto_dokumen']) ?>"
                                   target="_blank" class="btn-primary"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        } // akhir dari bacaan baris di tabel ini (while)
                    } // akhir dari kondisi berhasil ada data (else)
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>

<br><br>

<!-- ═════════════════════════════════════════════════════════
     BAGIAN 2: TABEL SURAT PANGGILAN ORANG TUA (YANG SUDAH DICETAK)
     Dapat dari tabel database bernama: surat_keluar
     ═════════════════════════════════════════════════════════ -->
<center>
    <fieldset style="width: 70%;">
        <legend>Laporan Surat Panggilan Ortu/Wali Yang Sudah Ada</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="8" align="right">
                            <h3 style="float:left; margin: 0;">Laporan Data Surat Panggilan Ortu</h3>
                            
                            <!-- Form Pencarian Laporan Panggilan yang Sudah Teregistrasi -->
                            <form action="list_panggilan_ortu.php" method="get">
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
                                
                                <input type="text"
                                       name="cari_laporan_panggilan"
                                       value="<?= isset($_GET['cari_laporan_panggilan']) ? htmlspecialchars($_GET['cari_laporan_panggilan']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_laporan"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <!-- Tombol Merah buat balikin ke setelan tabel semula tanpa difilter -->
                                <a href="list_panggilan_ortu.php" class="btn-danger"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Reset</a>
                            </form>
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
                            <!-- Panggil fungsi tanggal Indonesia yang kita buat di baris paling atas (formatTanggalIndo) -->
                            <!-- (false = tidak perlu nampilkan jamnya) -->
                            <?= formatTanggalIndo($baris_surat['tanggal_pembuatan_surat'], false) ?>
                        </td>
                        
                        <td align="center">
                            <!-- (true = tolong tambahkan jamnya juga di bawah tanggal) -->
                            <?= formatTanggalIndo($baris_surat['tanggal_pemanggilan'], true) ?>
                        </td>
                        
                        <td align="center"><?= htmlspecialchars($baris_surat['no_surat']) ?></td>
                        <td align="center"><?= htmlspecialchars($baris_surat['nis']) ?></td>
                        <td><?= htmlspecialchars($baris_surat['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($baris_surat['keperluan']) ?></td>
                        
                        <td align="center">
                            <!-- Tombol ini berfungsi kalau kertasnya hilang dicetak lagi dari database -->
                            <!-- 'urlencode' biar nomor yang ada logo karakter unik tidak rusak saat tersentuh alamat website -->
                            <button class="btn-primary">
                                <a href="/Poin_Pelanggaran_Siswa_XIIRPL4/pages/cetak/surat_panggilan_ortu.php?no_surat=<?= urlencode($baris_surat['no_surat']) ?>">Cetak Ulang</a>
                            </button>
                        </td>
                    </tr>
                    <?php
                        } // akhir siklus baris laporan 
                    } // akhir kalau sukses nemu data laporan
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>

<?php 
// Pasang bagian paling bawah (kaki) dari website, isinya teks hak cipta dsb (footer)
include ROOTPATH . "/includes/footer.php"; 
?>
