<?php
//menentukan lokasi root folder proyek dii server
DEFINE('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

//menghubungkan ke file konfigurasi (koneksi database)
include  ROOTPATH . '/config/config.php';


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if($action == 'add'){
        $pelanggaran = $_POST['pelanggaran'];
        $poin = $_POST['poin'];

        $query = mysqli_query($conn, "INSERT INTO jenis_pelanggaran (jenis, poin) VALUES ('$pelanggaran', '$poin')");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php");
        } else {
            echo "Gagal Menambah Data Jenis Pelanggaran";
        }
    }
}
