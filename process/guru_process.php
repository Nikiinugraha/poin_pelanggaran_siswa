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

        $query = mysqli_query($conn, "INSERT INTO guru (kode_guru, nama_pengguna, username, role, password, aktif, jabatan, telp) VALUES ('$kode_guru', '$nama_pengguna', '$username', '$role', '$password_input', 'Y', '$jabatan', '$telp')");

        if ($query) {
            header("Location: ../pages/guru/list.php");
        } else {
            echo "Gagal Menambah Data Guru";
        }
    }
}

// EDIT GURU

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $action = $_POST['action'];
    $kode_guru = $_POST['kode_guru'];

    if($action == 'edit') {
        $nama_pengguna = $_POST['nama_pengguna'];
        $username = $_POST['username'];
        $jabatan = $_POST['jabatan'];
        $telp = $_POST['telp'];
        $role = $_POST['role'];
        $aktif = $_POST['aktif'];

        $query = mysqli_query($conn, "UPDATE guru SET nama_pengguna = '$nama_pengguna', username = '$username', jabatan = '$jabatan', telp = '$telp', role = '$role', aktif = '$aktif' WHERE kode_guru = '$kode_guru'");

        if ($query) {
            header("Location: ../pages/guru/list.php");
        } else {
            echo "Gagal Mengedit Data Guru";
        }
    }


}
