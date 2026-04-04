<?php 
session_start();
session_destroy(); // Menghapus data session di server

// Jika ingin membersihkan cookie juga (untuk migrasi sistem)
setcookie("nama", "", time() - 3600, '/');
setcookie("username", "", time() - 3600, '/');
setcookie("role", "", time() - 3600, '/');

header("location:/poin_pelanggaran_siswa/login.php");
exit();
?>
