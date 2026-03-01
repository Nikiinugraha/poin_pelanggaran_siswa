<?php
// Menentukan path utama proyek (lokasi folder 'indomaret_RPL4' di dalam web server)
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
?>

<?php

// Memanggil file konfigurasi database (berisi koneksi ke MySQL)
include ROOTPATH . '/config/config.php';

// Memanggil file header agar tampilan atas halaman muncul (judul, menu, dll)
include ROOTPATH . '/includes/header.php';

?>
 
<h2>Edit Data <?= htmlspecialchars() ?></h2>