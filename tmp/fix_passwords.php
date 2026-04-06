<?php
include 'config/config.php';

echo "--- PROSES RESET PASSWORD SISWA ---\n";

$query = mysqli_query($conn, "SELECT nis FROM siswa");
$count = 0;

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $nis = $row['nis'];
        
        // Buat hash baru menggunakan NIS sebagai password
        $new_password = password_hash($nis, PASSWORD_DEFAULT);
        
        // Update ke database
        $update = mysqli_query($conn, "UPDATE siswa SET password = '$new_password' WHERE nis = '$nis'");
        
        if ($update) {
            echo "[SUKSES] NIS: $nis | Password di-reset menjadi: $nis\n";
            $count++;
        } else {
            echo "[GAGAL ] NIS: $nis | Error: " . mysqli_error($conn) . "\n";
        }
    }
    echo "------------------------------------\n";
    echo "BERHASIL: $count siswa telah diperbarui.\n";
    echo "Silakan coba login menggunakan NIS sebagai username dan password.\n";
} else {
    echo "Gagal mengambil data siswa: " . mysqli_error($conn) . "\n";
}
?>
