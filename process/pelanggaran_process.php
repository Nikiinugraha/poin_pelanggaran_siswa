<?php
define ('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
   $action = $_POST['action'];

   if($action == 'add'){
    $tanggal = date('Y-m-d H:i:s');
    $nis = $_POST['nis'];
    $jenis_pelanggaran = $_POST['jenis_pelanggaran'];
    
    // Pastikan ID jenis pelanggaran valid
    $query_id = mysqli_query($conn, "SELECT id_jenis_pelanggaran FROM jenis_pelanggaran WHERE jenis = '$jenis_pelanggaran'");
    $row_id = mysqli_fetch_assoc($query_id);
    
    if (!$row_id) {
        header("Location: ../pages/siswa/list.php?error=Jenis pelanggaran tidak valid!");
        exit;
    }
    
    $id_jenis_pelanggaran = $row_id['id_jenis_pelanggaran'];
    $keterangan = $_POST['keterangan'];

    $query = mysqli_query($conn, "INSERT INTO pelanggaran_siswa (tanggal, nis, id_jenis_pelanggaran, keterangan) VALUES ('$tanggal', '$nis', '$id_jenis_pelanggaran', '$keterangan')");

    if($query){
        header("Location: ../pages/laporan/list_pelanggaran.php?success=Data pelanggaran siswa berhasil dicatat!");
        exit;
    } else {
        header("Location: ../pages/laporan/list_pelanggaran.php?error=Gagal mencatat data pelanggaran ke database.");
        exit;
    }
   }
}
?>