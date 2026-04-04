<?php
include "config/config.php";
$result = mysqli_query($conn, "DESCRIBE surat_pindah");
while($row = mysqli_fetch_assoc($result)){
    print_r($row);
}
?>
