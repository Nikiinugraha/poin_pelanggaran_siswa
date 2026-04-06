<?php
define('ROOTPATH', 'c:/xampp/htdocs/poin_pelanggaran_siswa');
include ROOTPATH . "/config/config.php";

$query = mysqli_query($conn, "SELECT nis, password FROM siswa LIMIT 3");
while($row = mysqli_fetch_assoc($query)) {
    echo "NIS: " . $row['nis'] . " | Hash: " . $row['password'] . "\n";
}
?>
