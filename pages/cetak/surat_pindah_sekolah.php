<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";



if(isset($_GET['no_surat'])){
    $no_surat = $_GET['no_surat'];
}else{
    $nis = $_POST['nis'];
    $no_surat = $_POST['no_surat'];
    

    // ubah format bulan menjadi romawi (untuk bagian no surat)
    $bulan_romawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
    $bulan_romawi = $bulan_romawi[date("n")];
    $no_surat = $no_surat . "/SMK TI/BG/" . $bulan_romawi . "/" . date("Y");
        
    

    // cek apakah data sudah ada di tabel surat_keluar
    $cek_data = mysqli_query($conn, "SELECT no_surat FROM surat_keluar WHERE no_surat = '$no_surat'");
    if(mysqli_num_rows($cek_data) > 0){
        echo "<script>alert('No surat sudah ada di database'); window.location.href = 'add_pindah_sekolah.php';</script>";
    }else{
        $id_ortu_wali = $_POST['id_ortu_wali'];
        $orang_tua = $_POST['orang_tua'];
        $nama_ortu = $_POST['nama_ortu'];
        $alamat_ortu = $_POST['alamat'];
        $jenis_surat = "Pindah Sekolah";

        // update data ortu/wali jika tidak ada di database atau data baru diinput
        $orang_tua = $_POST['orang_tua'];
        $id_ortu_wali = $_POST['id_ortu_wali'];
        if($orang_tua == "ayah"){
            $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ayah = '$nama_ortu', alamat_ayah = '$alamat_ortu' WHERE id_ortu_wali = '$id_ortu_wali'");
        }else if($orang_tua == "ibu"){
            $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET ibu = '$nama_ortu', alamat_ibu = '$alamat_ortu' WHERE id_ortu_wali = '$id_ortu_wali'");
        }else{
            $update_ortu = mysqli_query($conn, "UPDATE ortu_wali SET wali = '$nama_ortu', alamat_wali = '$alamat_ortu' WHERE id_ortu_wali = '$id_ortu_wali'");
        }

        $sekolah_tujuan = $_POST['pindah_ke'];
        $alasan_pindah = $_POST['alasan_pindah'];

        // insert data ke database tabel surat_pindah
        $insert_surat_pindah = mysqli_query($conn, "INSERT INTO surat_pindah VALUES (NULL, '$sekolah_tujuan', '$alasan_pindah', '$nama_ortu', '$alamat_ortu')");

        // Mengambil ID terakhir yang di-generate oleh tabel surat_pindah
        $id_surat_pindah = mysqli_insert_id($conn); 

        
        
        $tanggal_pembuatan_surat = date("Y-m-d");
        $id_profil_sekolah = 1;
        $id_tahun_ajaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_tahun_ajaran FROM tahun_ajaran WHERE aktif = 'Y'"))['id_tahun_ajaran'];
        $tingkat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tingkat FROM siswa JOIN kelas USING(id_kelas) JOIN tingkat USING(id_tingkat) WHERE nis = '$nis'"))['tingkat'];


        // insert data ke database tabel surat_keluar
        $insert_surat_keluar = mysqli_query($conn, "INSERT INTO surat_keluar (no_surat, jenis_surat, id_surat_pindah, nis, tanggal_pembuatan_surat, id_profil_sekolah, id_tahun_ajaran, tingkat) VALUES ('$no_surat', '$jenis_surat', '$id_surat_pindah', '$nis', '$tanggal_pembuatan_surat', '$id_profil_sekolah', '$id_tahun_ajaran', '$tingkat')");
    }
       
}

// mengambil data siswa dari database join ke tabel ortu_wali, kelas, tingkat, dan program_keahlian
$query_siswa = mysqli_query($conn, "SELECT surat_keluar.no_surat, surat_pindah.sekolah_tujuan, surat_pindah.alasan_pindah, siswa.nama_siswa, siswa.nis, siswa.jenis_kelamin, siswa.alamat, surat_pindah.nama_ortu, surat_pindah.alamat_ortu, kelas.rombel, program_keahlian.program_keahlian, surat_keluar.tingkat, surat_keluar.tanggal_pembuatan_surat FROM surat_keluar 
JOIN surat_pindah USING(id_surat_pindah)
JOIN siswa USING(nis)
JOIN kelas USING(id_kelas)
JOIN tingkat USING(id_tingkat)
JOIN program_keahlian USING(id_program_keahlian) WHERE no_surat = '$no_surat'");
$row_siswa = mysqli_fetch_assoc($query_siswa);

// mengambil data wakasek kesiswaan dari database
$waka_kesiswaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Waka Kesiswaan' AND aktif = 'Y'"))['nama_pengguna'];

// mengambil data kepala sekolah dari database
$kepala_sekolah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Kepala Sekolah' AND aktif = 'Y'"))['nama_pengguna'];

// buat array bulan (berfungsi untuk mengubah angka bulan menjadi nama bulan, contoh : 2 menjadi Februari)
$bulan_indo = ["", "Januari", "Pebruari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


// mengubah format tanggal dari database menjadi format tanggal bulan indonesia
$tanggal = explode("-", $row_siswa['tanggal_pembuatan_surat']);
$tanggal_cetak_surat = $tanggal[2] ." ". $bulan_indo[(int)$tanggal[1]] . " " . $tanggal[0];

// Menyertakan tampilan header (bagian atas halaman)
include ROOTPATH . "/includes/header.php";

?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/cetak/surat_pindah_sekolah.css">
<!-- tombol kembali -->
<center class="no-print">
    
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <!-- tombol ini berfungsi untuk kembali ke halaman add_pindah_sekolah.php dan mengirimkan nis yang sudah di cek menggunakan method post -->
        <?php
        if(isset($_GET['no_surat'])){
        ?>
            <form action="/poin_pelanggaran_siswa/pages/laporan/list_pindah.php" style="margin: 0;">
                <button type="submit">
                    <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                        <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                    </svg>
                    <span>Kembali</span>
                </button>
            </form>
        <?php
        }else{
        ?>
            <form action="add_pindah_sekolah.php" method="post" style="margin: 0;">
                <input type="text" name="nis" value="<?= $nis ?>" hidden>
                <button type="submit">
                    <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                        <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                    </svg>
                    <span>Kembali</span>
                </button>
            </form>
        <?php
        }
        ?>
        

        <!-- tombol ini berfungsi untuk print halaman ini -->
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
            <span>&nbsp;&nbsp;Cetak Lagi</span>
        </button>
    </div>
    
</center>









<div class="page">
    <!-- Header -->
    <div class="header">
        <img src="/poin_pelanggaran_siswa/assets/images/kop.jpg" alt="kepala surat" width="100%">
    </div>
    <br>

    <div class="title">
        <u style="text-underline-offset: 3px;">KETERANGAN PINDAH SEKOLAH</u><br>
        <?=$no_surat?>
    </div>
    <br> 
    <div class="content">
        <p>Yang bertandatangan di bawah ini Kepala SMK TI BALI GLOBAL Denpasar, kecamatan Denpasar Selatan, Kota Denpasar, Provinsi Bali, Menerangkan bahwa :</p>
        
        <div class="indent">
            <div class="form-row">
                <div class="label">Nama Siswa</div>
                <div class="separator">:</div>
                <!-- mengambil data siswa dari database line: 16 -->
                <div class="field"><?php echo $row_siswa['nama_siswa']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Kelas/Program</div>
                <div class="separator">:</div>
                <!-- mengambil data siswa dari database line: 16 join ke kelas, tingkat, program_keahlian -->
                <div class="field"><?php echo $row_siswa['tingkat'] . ' ' . $row_siswa['program_keahlian'] . ' ' . $row_siswa['rombel'] ?></div>
            </div>
            <div class="form-row">
                <div class="label">NIS</div>
                <div class="separator">:</div>
                <!-- mengambil data nis siswa dari database line: 16 -->
                <div class="field"><?php echo $row_siswa['nis']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Jenis Kelamin</div>
                <div class="separator">:</div>
                <!-- mengambil data jenis kelamin siswa dari database line: 16 -->
                <div class="field"><?php echo $row_siswa['jenis_kelamin']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Alamat</div>
                <div class="separator">:</div>
                <!-- mengambil data alamat siswa dari database line: 16 -->
                <div class="field"><?php echo $row_siswa['alamat']; ?></div>
            </div>
        </div>

        <p>
            Sesuai dengan surat permohonan pindah sekolah dari Orang Tua/Wali siswa
        </p>

        <div class="indent">
            <div class="form-row">
                <div class="label">Nama</div>
                <div class="separator">:</div>
                <!-- menampilkan nama orang tua dari halaman add_pindah_sekolah line : 11 -->
                <div class="field"><?php echo $row_siswa['nama_ortu']; ?></div>
            </div>
            <div class="form-row">
                <div class="label">Alamat</div>
                <div class="separator">:</div>  
                <!-- menampilkan alamat_ortu dari halaman add_pindah_sekolah line : 12 -->
                <div class="field"><?php echo $row_siswa['alamat_ortu']; ?></div>
            </div>
        </div>

        <p>
            Telah mengajukan surat permohonan pindah ke <?php echo $row_siswa['sekolah_tujuan']; ?>, dengan alasan <?php echo $row_siswa['alasan_pindah']; ?> dan untuk kelengkapan administrasi sudah diselesaikan. <br>
            Demikian surat pindah ini dibuat untuk dipergunakan sebagaimana mestinya.
        </p>
        <br>

        <div class="signature-section">
            <div class="sig-block"></div>
            <div class="sig-block sig-right">
                <div>Denpasar, <?php echo $tanggal_cetak_surat; ?></div>
                <div>
                    Kepala SMK TI Bali Global Denpasar
                </div>
                <!-- menampilkan nama kepala sekolah dari database tabel guru line : 29 -->
                <div class="sig-name-plain"><?=$kepala_sekolah?></div>
            </div>
        </div>

    </div>
</div>









<script>
    // Menyertakan bagian footer (penutup halaman)
    window.onload = function() {
        window.print();
    }
</script>
<?php 
// Menyertakan bagian footer (penutup halaman)
include "../../includes/footer.php"; 
?>
