<?php
define ('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . "/config/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $name = $_POST['nama_siswa'];
    
    
    if ($action == 'add') {
        $query = "INSERT INTO siswa (id, nama_siswa) VALUES ('$id', '$name')";
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