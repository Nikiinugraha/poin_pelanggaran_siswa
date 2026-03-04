<?php

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] .  '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

if($_POST['action'] == 'delete'){
    $id_kelas = $_POST['id_kelas'];
    $query = "DELETE FROM kelas WHERE id_kelas = $id_kelas";
    $result = mysqli_query($conn, $query);
    if($result){
        header("Location: /poin_pelanggaran_siswa/pages/kelas/list.php");
    }
}
?>