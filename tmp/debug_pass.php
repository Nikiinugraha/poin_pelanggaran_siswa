<?php
$hash = '$2y$10$.V6ZwUrLKYZ1hOOLJxylxOsblZuqOiPaHbZvi9aEWlVmRyl.aXgwG';
$passwords = ['123', 'admin', 'siswa', '9124', '123456', 'password'];
foreach($passwords as $p) {
    if(password_verify($p, $hash)) {
        echo "MATCH FOUND: $p\n";
    }
}
?>
