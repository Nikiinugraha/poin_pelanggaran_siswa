<?php
use Vtiful\Kernel\Format;
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

$id_kelas = $_GET['id_kelas'];
$query = mysqli_query($conn, "SELECT * FROM kelas JOIN tingkat USING(id_tingkat) JOIN program_keahlian USING(id_program_keahlian) JOIN guru USING(kode_guru) WHERE id_kelas = '$id_kelas'");
$row = mysqli_fetch_assoc($query);


?>