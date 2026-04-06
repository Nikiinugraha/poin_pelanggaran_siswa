<?php
//menentukan lokasi root folder proyek dii server
DEFINE('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

//menghubungkan ke file konfigurasi (koneksi database)
include  ROOTPATH . '/config/config.php';


// RBAC Protection: Guru and Wakasek cannot perform CRUD on violation categories
session_start();
if (in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])) {
    header("Location: /poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php?error=Akses Ditolak! Anda tidak memiliki izin untuk melakukan operasi ini.");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if($action == 'add'){
        $jenis = $_POST['jenis'];
        $poin = $_POST['poin'];

        $query = mysqli_query($conn, "INSERT INTO jenis_pelanggaran (jenis, poin) VALUES ('$jenis', '$poin')");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php?success=Jenis pelanggaran berhasil ditambahkan!");
        } else {
            header("Location: ../pages/jenis_pelanggaran/list.php?error=Gagal menambah jenis pelanggaran.");
        }
    }

    if($action == 'delete') {
        $id = $_POST['id'];

        // Cek apakah jenis pelanggaran sudah digunakan pada data pelanggaran siswa
        $check_usage = mysqli_query($conn, "SELECT id_jenis_pelanggaran FROM pelanggaran_siswa WHERE id_jenis_pelanggaran = '$id' LIMIT 1");
        
        if (mysqli_num_rows($check_usage) > 0) {
            header("Location: ../pages/jenis_pelanggaran/list.php?error=Data jenis pelanggaran tidak dapat dihapus dikarenakan sudah digunakan pada data pelanggaran siswa!");
            exit;
        }

        $query = mysqli_query($conn, "DELETE FROM jenis_pelanggaran WHERE id_jenis_pelanggaran = '$id'");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php?success=Jenis pelanggaran berhasil dihapus!");
        } else {
            header("Location: ../pages/jenis_pelanggaran/list.php?error=Gagal menghapus jenis pelanggaran.");
        }
    }

    if($action == 'edit'){
        $id = $_POST['id_jenis_pelanggaran'];
        $jenis = $_POST['jenis'];
        $poin = $_POST['poin'];

        $query = mysqli_query($conn, "UPDATE jenis_pelanggaran SET jenis = '$jenis', poin = '$poin' WHERE id_jenis_pelanggaran = '$id'");

        if($query){
            header("Location: ../pages/jenis_pelanggaran/list.php?success=Perubahan jenis pelanggaran berhasil disimpan!");
        } else {
            header("Location: ../pages/jenis_pelanggaran/list.php?error=Gagal mengupdate jenis pelanggaran.");
        }
    }
}
