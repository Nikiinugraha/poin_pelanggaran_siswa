<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database
include ROOTPATH . '/config/config.php';

// Mengecek apakah permintaan berasal dari metode POST (bukan GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $action = $_POST['action'];  // Jenis aksi (add, edit, delete)
    $nis = $_POST['nis'];  // NIS siswa

    // Jika aksi adalah "add", maka tambahkan data siswa baru ke tabel
    if ($action == 'add') {
        $nama_siswa = $_POST['nama_siswa'];  // Nama siswa
        $jenis_kelamin = $_POST['jenis_kelamin'];  // Jenis kelamin siswa
        $alamat = $_POST['alamat_siswa'];  // Alamat siswa
        $kelas = $_POST['kelas'];  // Kelas siswa

        // kode untuk memecah string kelas menjadi array (contoh "XII RPL 1" menjadi array ["XII", "RPL", "1"])
        $kelas = explode(' ', $kelas);
        $tingkat = $kelas[0];  // XII
        $program_keahlian = $kelas[1];  // RPL
        $rombel = $kelas[2];  // 1

        $query_kelas = mysqli_query($conn, "SELECT id_kelas FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat) WHERE tingkat = '$tingkat' AND program_keahlian = '$program_keahlian' AND rombel = '$rombel'");
        $id_kelas = mysqli_fetch_assoc($query_kelas)['id_kelas'];  // mengambil id kelas

        $ayah = $_POST['ayah'];  // Ayah siswa
        $ibu = $_POST['ibu'];  // Ibu siswa
        $wali = $_POST['wali'];  // Wali siswa
        $pekerjaan_ayah = $_POST['pekerjaan_ayah'];  // Pekerjaan ayah
        $pekerjaan_ibu = $_POST['pekerjaan_ibu'];  // Pekerjaan ibu
        $pekerjaan_wali = $_POST['pekerjaan_wali'];  // Pekerjaan wali
        $telp_ayah = $_POST['telp_ayah'];  // no telp ayah
        $telp_ibu = $_POST['telp_ibu'];  // no telp ibu
        $telp_wali = $_POST['telp_wali'];  // no telp wali
        $alamat_ayah = $_POST['alamat_ayah'];  // Alamat ayah
        $alamat_ibu = $_POST['alamat_ibu'];  // Alamat ibu
        $alamat_wali = $_POST['alamat_wali'];  // Alamat wali

        // Insert data ortu_wali
        $query_ortu = "INSERT INTO ortu_wali (ayah, ibu, wali, pekerjaan_ayah, pekerjaan_ibu, pekerjaan_wali, no_telp_ayah, no_telp_ibu, no_telp_wali, alamat_ayah, alamat_ibu, alamat_wali) 
        VALUES ('$ayah', '$ibu', '$wali', '$pekerjaan_ayah', '$pekerjaan_ibu', '$pekerjaan_wali', '$telp_ayah', '$telp_ibu', '$telp_wali', '$alamat_ayah', '$alamat_ibu', '$alamat_wali')";
        mysqli_query($conn, $query_ortu);

        // Mengambil ID terakhir yang di-generate oleh tabel ortu_wali
        $id_ortu_wali = mysqli_insert_id($conn);

        // Insert data siswa
        $password_enkripsi = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO siswa (nis, nama_siswa, jenis_kelamin, alamat, password, id_ortu_wali, id_kelas) 
        VALUES ('$nis', '$nama_siswa', '$jenis_kelamin', '$alamat', '$password_enkripsi', '$id_ortu_wali', '$id_kelas')";
        mysqli_query($conn, $query);

        // Jika aksi adalah "edit", maka ubah data siswa berdasarkan NIS
    } elseif ($action == 'edit') {
        // belum

        // Jika aksi adalah "delete", maka delete data siswa berdasarkan NIS
    } elseif ($action == 'delete') {
        // Mengambil data siswa yang akan dihapus
        $nis = $_POST['nis'];
        $siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_ortu_wali FROM siswa WHERE nis='$nis'"));

        if ($siswa_data) {
            $id_ortu_wali = $siswa_data['id_ortu_wali'];

            // Hapus data yang memiliki foreign key ke siswa (urutan benar!)
            $query_surat = "DELETE FROM surat_keluar WHERE nis='$nis'";
            mysqli_query($conn, $query_surat);

            $query_pelanggaran = "DELETE FROM pelanggaran_siswa WHERE nis='$nis'";
            mysqli_query($conn, $query_pelanggaran);

            // Hapus siswa (setelah semua child table dihapus)
            $query_siswa = "DELETE FROM siswa WHERE nis='$nis'";
            mysqli_query($conn, $query_siswa);

            // Hapus ortu_wali jika tidak digunakan siswa lain
            $check_ortu = mysqli_query($conn, "SELECT COUNT(*) as count FROM siswa WHERE id_ortu_wali='$id_ortu_wali'");
            $result = mysqli_fetch_assoc($check_ortu);

            if ($result['count'] == 0) {
                $query_ortu = "DELETE FROM ortu_wali WHERE id_ortu_wali='$id_ortu_wali'";
                mysqli_query($conn, $query_ortu);
            }
        }
    }

    // Setelah selesai, arahkan kembali ke halaman daftar siswa
    header('Location: ../pages/siswa/list.php');
    exit;
}
?>

<!-- 
🧠 Penjelasan Singkat:

Kode ini berfungsi sebagai file proses (process file) untuk tabel siswa — menangani semua aksi dari form seperti:
	•	Tambah data (add)
	•	Edit data (edit)
	•	Hapus data (delete)

Setelah aksi dijalankan, pengguna akan otomatis diarahkan kembali ke halaman daftar siswa (list.php).

👉 File ini dipakai dari form add.php(fungsi insert), edit.php(fungsi update), dan list(fungsi delete).php 
-->