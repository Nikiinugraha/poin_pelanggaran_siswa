<?php
define ('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . "/config/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['id'];
    $action = $_POST['action'];
    $name = $_POST['nama_siswa'];
    
    
    if ($action == 'add') {
        $query = "INSERT INTO siswa (nis, nama_siswa,jenis_kelamin, alamat, nama_ayah, nama_ibu, pekerjaan_ayah, pekerjaan_ibu, pekerjaan_wali, no_telp_ayah, no_telp_ibu, no_telp_wali, alamat_ayah, alamat_ibu, alamat_wali, id_kelas), VALUES ('$nis', '$name', '$jenis_kelamin', '$alamat', '$nama_ayah', '$nama_ibu', '$pekerjaan_ayah', '$pekerjaan_ibu', '$pekerjaan_wali', '$no_telp_ayah', '$no_telp_ibu', '$no_telp_wali', '$alamat_ayah', '$alamat_ibu', '$alamat_wali', '$id_kelas')";
        mysqli_query($conn, $query);
    } elseif ($action == 'edit') {
        $query = "UPDATE siswa SET nama_siswa='$name' WHERE id=$id";
        mysqli_query($conn, $query);
    } elseif ($action == 'hapus') {
        $query = "DELETE FROM siswa WHERE id=$id";
        mysqli_query($conn, $query);
    }

    header("Location: ../pages/siswa/list.php");
    exit;
} 