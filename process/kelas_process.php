<?php

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] .  '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $action = $_POST['action'];
    
    if ($action == 'delete') {

        $id_kelas = $_POST['id_kelas'];

        // Cek apakah masih ada siswa di kelas ini
        $cek = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE id_kelas = '$id_kelas'");
        $row = mysqli_fetch_assoc($cek);

        if ($row['total'] > 0) {
            // Masih ada siswa, tolak penghapusan
            header("Location: /poin_pelanggaran_siswa/pages/kelas/list.php?error=Kelas tidak bisa dihapus, masih terdapat " . $row['total'] . " siswa di kelas ini!");
            exit();
        } else {
            // Aman dihapus
            mysqli_query($conn, "DELETE FROM kelas WHERE id_kelas = $id_kelas");
            header("Location: /poin_pelanggaran_siswa/pages/kelas/list.php?success=Kelas berhasil dihapus!");
            exit();
        }
    }

    if ($action == 'add') {
        $tingkat = $_POST['tingkat'];
        $program_keahlian = $_POST['program_keahlian'];
        $rombel = $_POST['rombel'];
        $walikelas = $_POST['walikelas'];

        // Menentukan id_tingkat berdasarkan nama tingkat
        $query_tingkat = mysqli_query($conn, "SELECT id_tingkat FROM tingkat WHERE tingkat = '$tingkat'");
        $id_tingkat = mysqli_fetch_assoc($query_tingkat)['id_tingkat'];

        // Menentukan id_prxogram_keahlian berdasarkan nama program keahlian
        $query_pk = mysqli_query($conn, "SELECT id_program_keahlian FROM program_keahlian WHERE program_keahlian = '$program_keahlian'");
        $id_program_keahlian = mysqli_fetch_assoc($query_pk)['id_program_keahlian'];

        // Menentukan kode_guru berdasarkan nama wali kelas
        $query_guru = mysqli_query($conn, "SELECT kode_guru FROM guru WHERE nama_pengguna = '$walikelas'");
        $kode_guru = mysqli_fetch_assoc($query_guru)['kode_guru'];

        // Insert data ke tabel kelas
        $query = "INSERT INTO kelas (id_tingkat, id_program_keahlian, rombel, kode_guru) VALUES ('$id_tingkat', '$id_program_keahlian', '$rombel', '$kode_guru')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: /poin_pelanggaran_siswa/pages/kelas/list.php");
            exit;
        }
    }


    if($action == 'edit'){

        $id_tingkat = $_POST['id_tingkat'];
        $rombel = $_POST['rombel'];
        $kode_guru = $_POST['kode_guru'];
        $id_program_keahlian = $_POST['id_program_keahlian'];

        //query update
        $query = mysqli_query($conn, "UPDATE kelas SET id_tingkat = '$id_tingkat', id_program_keahlian = '$id_program_keahlian', rombel = '$rombel', kode_guru = '$kode_guru' WHERE id_kelas = '$id_kelas'");

        if($query){
            header("Location: /poin_pelanggaran_siswa/pages/kelas/list.php");
            exit;   
        } else {
            echo "Gagal Update";
            exit;
        }

        
    }
}
