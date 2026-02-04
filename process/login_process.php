<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . "/config/config.php";


// echo $_POST['username'];
// echo "<br>";
// echo $_POST['password'];
// echo "<br>";
// $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// echo $password_hash;
// echo "<br>";

$username = $_POST ["username"];
// ambil password dari database
$password_hash = $_POST['password'];

//membandingkan password yang diinput
$query_guru =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM guru WHERE username = '$username'"));
$query_siswa =  mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM siswa WHERE username = '$username'"));




if(mysqli_num_rows($query_guru) > 0 || mysqli_num_rows($query_siswa) > 0) {
    echo "Password benar";
} else {
    echo "Password salah";
}
 ?>