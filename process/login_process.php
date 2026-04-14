<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database (session sudah dimulai di sini)
include ROOTPATH . "/config/config.php";

$username = $_POST["username"];
$password_plain = $_POST["password"];
$role_selected = $_POST["role"];

/** 
 * LOGIKA LOGIN BERDASARKAN ROLE
 * Memastikan user hanya bisa login sesuai kategori yang dipilih di UI.
 */

if ($role_selected === 'siswa') {
    // 1. PROSES LOGIN SISWA
    $stmt_siswa = mysqli_prepare($conn, "SELECT nis, nama_siswa, password FROM siswa WHERE nis = ?");
    mysqli_stmt_bind_param($stmt_siswa, "s", $username);
    mysqli_stmt_execute($stmt_siswa);
    $res_siswa = mysqli_stmt_get_result($stmt_siswa);

    if (mysqli_num_rows($res_siswa) >= 1) {
        $data_siswa = mysqli_fetch_assoc($res_siswa);
        if (password_verify($password_plain, $data_siswa['password'])) {
            $_SESSION["nama"] = $data_siswa['nama_siswa'];
            $_SESSION["username"] = $data_siswa['nis'];
            $_SESSION["role"] = "siswa"; 
            header('Location: ../pages/index.php');
            exit;
        } else {
            echo "<script>alert('Password Salah!'); window.location.href='../login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('NIS Siswa tidak ditemukan!'); window.location.href='../login.php';</script>";
        exit;
    }

} else {
    // 2. PROSES LOGIN GURU / BK
    $stmt_guru = mysqli_prepare($conn, "SELECT nama_pengguna, username, password, role, jabatan FROM guru WHERE username = ?");
    mysqli_stmt_bind_param($stmt_guru, "s", $username);
    mysqli_stmt_execute($stmt_guru);
    $res_guru = mysqli_stmt_get_result($stmt_guru);

    if (mysqli_num_rows($res_guru) >= 1) {
        $data_guru = mysqli_fetch_assoc($res_guru);
        if (password_verify($password_plain, $data_guru['password'])) {
            
            $db_role = strtolower(trim($data_guru['role']));
            $db_jabatan = strtolower(trim($data_guru['jabatan']));
            $actual_role = $db_role;

            // Determinasi role BK dari jabatan jika role-nya masih 'guru'
            if ($db_role === 'guru' && stripos($db_jabatan, 'bk') !== false) {
                $actual_role = 'bk';
            }

            // Validasi: Jika pilih BK di UI, harus benar-benar akun BK
            if ($role_selected === 'bk' && $actual_role !== 'bk') {
                echo "<script>alert('Akun Anda tidak memiliki hak akses sebagai Guru BK!'); window.location.href='../login.php';</script>";
                exit;
            }

            // Validasi: Jika pilih Guru di UI, tapi akunnya adalah BK, tetap diizinkan sebagai BK atau Guru? 
            // Biasanya BK punya akses lebih, jadi kita simpan role aslinya.
            
            $_SESSION["nama"] = $data_guru['nama_pengguna'];
            $_SESSION["username"] = $data_guru['username'];
            $_SESSION["role"] = $actual_role;
            
            header('Location: ../pages/index.php');
            exit;
        } else {
            echo "<script>alert('Password Salah!'); window.location.href='../login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username Guru/BK tidak ditemukan!'); window.location.href='../login.php';</script>";
        exit;
    }
}
