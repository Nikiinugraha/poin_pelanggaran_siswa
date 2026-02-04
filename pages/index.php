<?php
include "../config/config.php";
include "../includes/header.php";

if(isset($_COOKIE['username'])) {
    echo "Hallo ". $_COOKIE['nama'];
    echo "<br>";
    echo "Username " . $_COOKIE['username'];
}

?>

<?php include "../includes/footer.php"; ?>