<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

// Menyertakan tampilan header (bagian atas halaman)
include ROOTPATH . "/includes/header.php";



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
    $id_surat_pindah = $_POST['id_surat_pindah']; 

    // Pindahkan file foto dari folder sementara (tmp) ke folder gambar
    if (move_uploaded_file($data_file_foto["tmp_name"], $lokasi_file_foto)) {

        $nama_tabel = "surat_pindah";

        // Bersihkan data dari karakter berbahaya sebelum disimpan ke database
        $nama_file_foto_aman  = mysqli_real_escape_string($conn, $nama_file_foto);
        $id_surat_pindah_aman = mysqli_real_escape_string($conn, $id_surat_pindah);

        // Simpan nama foto dan ubah status menjadi "Selesai" di database
        $hasil_update = mysqli_query($conn,
            "UPDATE $nama_tabel
             SET foto_dokumen = '$nama_file_foto_aman', status = 'Selesai'
             WHERE id_surat_pindah = '$id_surat_pindah_aman'"
        );

        if ($hasil_update) {
            // Jika berhasil: tampilkan pesan lalu kembali ke halaman ini
            echo "<script>alert('Berhasil Mengunggah Foto Dokumen');window.location.href='list_pindah.php'</script>";
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
            SELECT siswa.*, sk.tanggal_pembuatan_surat AS tanggal_surat, sp.status AS status_dokumen, sp.foto_dokumen, sk.id_surat_pindah
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN surat_keluar sk ON siswa.nis = sk.nis AND sk.jenis_surat = 'Pindah Sekolah'
            LEFT JOIN surat_pindah sp USING(id_surat_pindah)
            WHERE siswa.status = 'aktif'
              AND (siswa.nama_siswa LIKE '%$kata_cari_siswa%' OR siswa.nis LIKE '%$kata_cari_siswa%')
            GROUP BY siswa.nis, sk.id_surat_pindah, sk.tanggal_pembuatan_surat, sp.status, sp.foto_dokumen
            ORDER BY siswa.nis, sk.tanggal_pembuatan_surat DESC
        ) main

        JOIN (
            -- Bagian luar: hitung TOTAL poin keseluruhan per siswa (semua pelanggaran)
            -- Dipisah agar total_poin tidak terpengaruh oleh GROUP BY tanggal di atas
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin >= 100
    ";

} else {
    // Tidak ada pencarian → tampilkan semua siswa dengan poin 100++
    $sql_calon_perjanjian_siswa = "
        SELECT main.*, sub.total_poin
        FROM (
            SELECT siswa.*, sk.tanggal_pembuatan_surat AS tanggal_surat, sp.status AS status_dokumen, sp.foto_dokumen, sk.id_surat_pindah
            FROM siswa
            JOIN pelanggaran_siswa USING(nis)
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            LEFT JOIN surat_keluar sk ON siswa.nis = sk.nis AND sk.jenis_surat = 'Pindah Sekolah'
            LEFT JOIN surat_pindah sp USING(id_surat_pindah)
            WHERE siswa.status = 'aktif'
            GROUP BY siswa.nis, sk.id_surat_pindah, sk.tanggal_pembuatan_surat, sp.status, sp.foto_dokumen
            ORDER BY siswa.nis, sk.tanggal_pembuatan_surat DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin >= 100
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
// Syarat: poin 100 dan siswa masih aktif
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

        WHERE sub.total_poin >= 100
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
            GROUP BY siswa.nis, po.tanggal, po.status, po.foto_dokumen
            ORDER BY siswa.nis, po.tanggal DESC
        ) main

        JOIN (
            SELECT nis, SUM(poin) AS total_poin
            FROM pelanggaran_siswa
            JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
            GROUP BY nis
        ) sub USING(nis)

        WHERE sub.total_poin >= 100
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

// jika ada(isset) tombol ditekan dengan method GET berisi value cari maka jalankan perintah dalam if
if(isset($_GET['cari'])){
    $cari = $_GET['cari'];
    $result = mysqli_query($conn, "SELECT * FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' AND (nama_siswa like '%".$cari."%' OR nis like '%".$cari."%') ORDER BY tanggal_pembuatan_surat DESC");	
    
// else akan berjalan atau tampil ketika tombol cari belum ditekan 
}else{
    $result = mysqli_query($conn, "SELECT * FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' ORDER BY tanggal_pembuatan_surat DESC");
}

?>
<!-- ICON PRINTER (SVG) disimpan dalam variabel PHP agar tidak perlu ditulis ulang -->
<?php
$ikon_printer = '
<span class="printer-wrapper">
    <span class="printer-container">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75" height="20px" width="20px">
            <path stroke-width="5" stroke="black" d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z"></path>
            <mask fill="white" id="path-2-inside-1_30_7"><path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z"></path></mask>
            <path mask="url(#path-2-inside-1_30_7)" fill="black" d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z"></path>
            <circle fill="black" r="3" cy="49" cx="78"></circle>
        </svg>
    </span>
    <span class="printer-page-wrapper"><span class="printer-page"></span></span>
</span>';
?>



<!-- ═══════════════════════════════════════════════════════════════════
     BAGIAN 1: TABEL CALON PEMBUAT SURAT PERJANJIAN SISWA (100++ poin)
     Siswa yang poinnya sudah 100++ perlu membuat surat pindah sekolah
════════════════════════════════════════════════════════════════════ -->
<center>

    <!-- Tombol untuk langsung mencetak surat perjanjian siswa baru -->
    <button class="print-btn" onclick="window.location.href='/poin_pelanggaran_siswa/pages/cetak/add_pindah_sekolah.php?from=list_pindah.php'">
        <?= $ikon_printer ?>
        &nbsp;&nbsp;Cetak Surat Pindah Sekolah
    </button><br>

    <fieldset style="width: 70%;">
        <legend>Daftar Pelanggaran Per Siswa</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="6" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Siswa di atas 100 Poin</h3>

                            <!-- Form pencarian siswa berdasarkan NIS atau nama -->
                            <form action="list_pindah.php" method="get">

                                <!-- Datalist = daftar pilihan yang muncul saat mengetik di kotak pencarian -->
                                <datalist id="pilihan_siswa_100">
                                    <?php
                                    // Ambil daftar NIS dan nama siswa yang poinnya 100++
                                    // untuk ditampilkan sebagai pilihan autocomplete di kotak pencarian
                                    $query_pilihan_siswa = mysqli_query($conn,
                                        "SELECT nama_siswa, nis
                                         FROM siswa
                                         JOIN pelanggaran_siswa USING(nis)
                                         JOIN jenis_pelanggaran USING(id_jenis_pelanggaran)
                                         LEFT JOIN perjanjian_siswa ps USING(id_pelanggaran_siswa)
                                         WHERE siswa.status = 'aktif'
                                         GROUP BY nis, nama_siswa
                                         HAVING SUM(poin) >= 100"
                                    );
                                    while ($baris_pilihan = mysqli_fetch_assoc($query_pilihan_siswa)) {
                                        // Tampilkan NIS sebagai pilihan
                                        echo "<option value='" . htmlspecialchars($baris_pilihan['nis']) . "'>";
                                        // Tampilkan nama siswa sebagai pilihan
                                        echo "<option value='" . htmlspecialchars($baris_pilihan['nama_siswa']) . "'>";
                                    }
                                    ?>
                                </datalist>

                                <input type="text"
                                       name="cari_daftar_siswa"
                                       value="<?= isset($_GET['cari_daftar_siswa']) ? htmlspecialchars($_GET['cari_daftar_siswa']) : '' ?>"
                                       placeholder="Masukkan NIS / Nama Siswa"
                                       list="pilihan_siswa_100"
                                       style="padding:8px 15px;width:200px;border-radius:5px;"
                                       autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white;font-weight:bold;" value="Cari">
                                <a href="list_pindah.php" class="btn-danger"
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

                    // Cek dulu apakah query berhasil dijalankan (tidak error)
                    // dan apakah ada data yang ditemukan
                    if (!$hasil_calon_perjanjian_siswa || mysqli_num_rows($hasil_calon_perjanjian_siswa) == 0) {
                        echo "<tr><td colspan='6' align='center'>Data Tidak Ditemukan</td></tr>";
                        // Jika query gagal total (misalnya syntax error), tampilkan pesan error
                        if (!$hasil_calon_perjanjian_siswa) {
                            echo "<tr><td colspan='6' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        // Ambil data satu per satu dari hasil query
                        while ($data_siswa = mysqli_fetch_assoc($hasil_calon_perjanjian_siswa)) {
                    ?>
                    <tr>
                        <td align="center"><?= $nomor_urut++ ?></td>
                        <td align="center"><?= htmlspecialchars($data_siswa['nis']) ?></td>
                        <td><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td>
                        <td align="center" width="400px">
                            <?php
                            // Tampilkan semua jenis pelanggaran siswa ini (dipisah koma)
                            // Memanggil fungsi yang sudah dibuat di atas
                            tampilkanJenisPelanggaran($conn, $data_siswa['nis']);
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($data_siswa['total_poin']) ?></td>
                        <td>
                            <?php
                            // Tampilkan tombol aksi berbeda berdasarkan status dokumen perjanjian:
                            // - NULL       = belum ada surat → tampilkan tombol "Detail" dan "Cetak"
                            // - Masih Proses = sudah cetak, belum upload foto → tampilkan tombol upload
                            // - Selesai    = sudah upload foto → tampilkan link lihat gambar

                            if ($data_siswa['status_dokumen'] == NULL) { ?>
                                <!-- Status: Belum ada surat perjanjian -->
                                <button class="btn-primary">
                                    <a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?= $data_siswa['nis'] ?>&tanggal=<?= $data_siswa['tanggal_surat'] ?>&from=list_pindah.php">Detail</a>
                                </button>
                                <hr>
                                <!-- Form untuk mencetak surat perjanjian baru -->
                                <form action="/poin_pelanggaran_siswa/pages/cetak/add_pindah_sekolah.php?from=list_pindah.php" method="post">
                                    <input type="hidden" name="nis" value="<?= $data_siswa['nis'] ?>">
                                    <input type="submit" value="Cetak" style="padding:10px 15px;font-weight:bold;background-color:#fff;border-radius:5px;border:1px solid #ccc;">
                                </form>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Masih Proses") { ?>
                                <!-- Status: Surat sudah dicetak, menunggu upload foto -->
                                <button class="btn-primary">
                                    <a href="/poin_pelanggaran_siswa/pages/cetak/surat_pindah_sekolah.php?nis=<?= $data_siswa['nis'] ?>&from=list_pindah.php">Cetak Surat</a>
                                </button>
                                <hr>
                                <!-- Form upload foto dokumen yang sudah ditandatangani -->
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id_surat_pindah" value="<?= htmlspecialchars($data_siswa['id_surat_pindah']) ?>">
                                    <input type="file" name="foto_dokumen" accept="image/*, application/pdf " required>
                                    <input type="submit" name="upload" value="Upload" class="btn-warning" style="color:white;font-weight:bold;">
                                </form>

                            <?php } elseif ($data_siswa['status_dokumen'] == "Selesai") { ?>
                                <!-- Status: Selesai, foto sudah diupload → tampilkan link foto -->
                                <a href="/poin_pelanggaran_siswa/assets/images/<?= htmlspecialchars($data_siswa['foto_dokumen']) ?>"
                                   target="_blank" class="btn-primary"
                                   style="text-decoration:none;color:white;font-family:'Arial';font-size:13px;">Lihat Gambar</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        } // akhir while
                    } // akhir else
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>

<center>
    
    <fieldset style="width: 70%;">
        <legend>Daftar Surat Pindah</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="8" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Surat Pindah Sekolah</h3>
                            <form action="list_pindah.php" method="get">
                                <!-- menampilkan data nis dan nama siswa -->
                                <datalist id="nama_siswa">
                                    <?php
                                    $result_siswa = mysqli_query($conn, "SELECT nama_siswa, nis FROM surat_keluar JOIN siswa USING(nis) JOIN surat_pindah USING(id_surat_pindah) WHERE jenis_surat = 'Pindah Sekolah' GROUP BY nis");
                                    while ($row_siswa = mysqli_fetch_assoc($result_siswa)) {
                                        echo "<option value='" . $row_siswa['nis'] . "'>";
                                        echo "<option value='" . $row_siswa['nama_siswa'] . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text" value="<?php if(isset($_GET['cari'])) { echo $_GET['cari']; } else { echo ""; } ?>" name="cari" placeholder="Masukkan NIS / Nama Siswa" list="nama_siswa" style="padding: 8px 15px;width: 200px;border-radius: 5px;" autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white; font-weight:bold;" value="Cari">
                                <a href="list_pindah.php" class="btn-danger" style="text-decoration: none; color: white; font-family:'Arial'; font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pembuatan Surat</th>
                        <th>No Surat</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Sekolah Tujuan</th>
                        <th>Alasan Pindah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result)==0){
                        echo "
                        <tr><td colspan='8' align='center'>Data Tidak Ditemukan</td></tr>";
                    }else{
                        while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td align="center"><?= $no++?></td>
                        <td align="center">
                            <?php
                            // ubah format tanggal dari YYYY-MM-DD H:i:s menjadi DD-MM-YYYY H:i:s
                            $datetime = date("d-m-Y", strtotime($row['tanggal_pembuatan_surat']));
                            // memecah tanggal
                            $tanggal = explode("-", $datetime);
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
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($row['no_surat']) ?></td>
                        <td align="center"><?= htmlspecialchars($row['nis']) ?></td>
                        <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($row['sekolah_tujuan']) ?></td>
                        <td><?= htmlspecialchars($row['alasan_pindah']) ?></td>
                        <td>
                            <!-- tombol untuk menampilkan detail pelanggaran dengan mengirim nis terpilih melalui method GET -->
                            <button class="btn-primary"><a href="/poin_pelanggaran_siswa/pages/cetak/surat_pindah_sekolah.php?no_surat=<?=$row['no_surat']?>">Cetak</a></button>
                        </td>
                    </tr>
                    <?php
                        } 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>









<?php 
include "../../includes/footer.php"; 
?>
