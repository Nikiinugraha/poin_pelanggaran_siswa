<?php
//menentukan lokasi root folder proyek dii server
DEFINE('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

//menghubungkan ke file konfigurasi (koneksi database)
include  ROOTPATH . '/config/config.php';


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if($action == 'add'){
        $jenis = $_POST['jenis'];
        $poin = $_POST['poin'];

        $query = mysqli_query($conn, "INSERT INTO jenis_pelanggaran (jenis, poin) VALUES ('$jenis', '$poin')");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php");
        } else {
            echo "Gagal Menambah Data Jenis Pelanggaran";
        }
    }

    if($action == 'delete') {
        $id = $_POST['id'];

        $query = mysqli_query($conn, "DELETE FROM jenis_pelanggaran WHERE id_jenis_pelanggaran = '$id'");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php");
        } else {
            echo "Gagal Menghapus Data Jenis Pelanggaran";
        }
    }

    if($action == 'edit'){
        $id = $_POST['id_jenis_pelanggaran'];
        $jenis = $_POST['jenis'];
        $poin = $_POST['poin'];

        $query = mysqli_query($conn, "UPDATE jenis_pelanggaran SET jenis = '$jenis', poin = '$poin' WHERE id_jenis_pelanggaran = '$id'");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php");
        } else {
            echo "Gagal Mengedit Data Jenis Pelanggaran";
        }
    }
}
