<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database
include ROOTPATH . "/config/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        $kode_guru = $_POST['kode_guru'];
        $nama_pengguna = $_POST['nama_pengguna'];
        $username = $_POST['username'];
        $jabatan = $_POST['jabatan'];
        $telp = $_POST['telp'];
        $role = $_POST['role'];
        $password_input = password_hash("Guru12345*!", PASSWORD_DEFAULT);

        $query = mysqli_query($conn, "INSERT INTO guru (kode_guru, nama_pengguna, username, jabatan, telp, role, password) VALUES ('$kode_guru', '$nama_pengguna', '$username', '$jabatan', '$telp', '$role', '$password')");

        if ($query) {
            header("Location: /poin_pelanggaran_siswa/pages/guru/list.php");
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
