<?php
// Menentukan path utama proyek agar mudah memanggil file lain
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menyertakan file konfigurasi database
include ROOTPATH . '/config/config.php';

// RBAC Protection: Guru and Wakasek cannot perform CRUD on students
session_start();
if (in_array(trim(strtolower($_SESSION['role'])), ['wakasek', 'guru'])) {
    header("Location: /poin_pelanggaran_siswa/pages/siswa/list.php?error=Akses Ditolak! Anda tidak memiliki izin untuk melakukan operasi ini.");
    exit();
}

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
        // Set password default ke NIS jika tidak ada input password khusus
        $password_default = $nis;
        $password_enkripsi = password_hash($password_default, PASSWORD_DEFAULT);
        $query = "INSERT INTO siswa (nis, nama_siswa, jenis_kelamin, alamat, password, status, id_ortu_wali, id_kelas) 
        VALUES ('$nis', '$nama_siswa', '$jenis_kelamin', '$alamat', '$password_enkripsi', 'Aktif', '$id_ortu_wali', '$id_kelas')";
        mysqli_query($conn, $query);

        // Jika aksi adalah "edit", maka ubah data siswa berdasarkan NIS
    } elseif ($action == 'edit') {
        $old_nis = $_POST['old_nis'];
        $new_nis = $_POST['nis'];
        $nama_siswa = $_POST['nama_siswa'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $alamat_siswa = $_POST['alamat_siswa'];
        $id_ortu_wali = $_POST['id_ortu_wali'];
        $kelas_input = $_POST['kelas'];

        // Update Data Orang Tua / Wali
        $ayah = $_POST['ayah'];
        $ibu = $_POST['ibu'];
        $wali = $_POST['wali'];
        $pekerjaan_ayah = $_POST['pekerjaan_ayah'];
        $pekerjaan_ibu = $_POST['pekerjaan_ibu'];
        $pekerjaan_wali = $_POST['pekerjaan_wali'];
        $no_telp_ayah = $_POST['telp_ayah'];
        $no_telp_ibu = $_POST['telp_ibu'];
        $no_telp_wali = $_POST['telp_wali'];
        $alamat_ayah = $_POST['alamat_ayah'];
        $alamat_ibu = $_POST['alamat_ibu'];
        $alamat_wali = $_POST['alamat_wali'];

        $query_ortu = "UPDATE ortu_wali SET 
            ayah='$ayah', ibu='$ibu', wali='$wali', 
            pekerjaan_ayah='$pekerjaan_ayah', pekerjaan_ibu='$pekerjaan_ibu', pekerjaan_wali='$pekerjaan_wali',
            no_telp_ayah='$no_telp_ayah', no_telp_ibu='$no_telp_ibu', no_telp_wali='$no_telp_wali',
            alamat_ayah='$alamat_ayah', alamat_ibu='$alamat_ibu', alamat_wali='$alamat_wali'
            WHERE id_ortu_wali='$id_ortu_wali'";
        mysqli_query($conn, $query_ortu);

        // Update Data Siswa (dan Kelas)
        $status = $_POST['status'];
        $kelas_parts = explode(' ', $kelas_input);
        if (count($kelas_parts) >= 3) {
            $tingkat = $kelas_parts[0];
            $program = $kelas_parts[1];
            $rombel = $kelas_parts[2];

            $query_kelas = mysqli_query($conn, "SELECT id_kelas FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat) WHERE tingkat = '$tingkat' AND program_keahlian = '$program' AND rombel = '$rombel'");
            $id_elas_row = mysqli_fetch_assoc($query_kelas);
            $id_kelas = $id_elas_row ? $id_elas_row['id_kelas'] : $siswa['id_kelas'];
        }

        $query_siswa = "UPDATE siswa SET 
            nis='$new_nis', 
            nama_siswa='$nama_siswa', 
            jenis_kelamin='$jenis_kelamin', 
            alamat='$alamat_siswa',
            id_kelas='$id_kelas',
            status='$status'
            WHERE nis='$old_nis'";
        mysqli_query($conn, $query_siswa);

        header("Location: /poin_pelanggaran_siswa/pages/siswa/list.php?success=Data siswa dan orang tua berhasil diperbarui!");
        exit;
    } elseif ($action == 'delete') {
        // Mengambil data id_ortu_wali dari siswa yang akan dihapus
        $query_siswa = mysqli_query($conn, "SELECT id_ortu_wali FROM siswa WHERE nis='$nis'");
        $siswa_data = mysqli_fetch_assoc($query_siswa);
        
        if ($siswa_data) {
            $id_ortu_wali = $siswa_data['id_ortu_wali'];
            
            // CEK RESTRIKSI: Jika siswa sudah memiliki data Orang Tua, blokir penghapusan
            if (!empty($id_ortu_wali) && $id_ortu_wali != 0) {
                header("Location: /poin_pelanggaran_siswa/pages/siswa/list.php?error=Data tidak bisa dihapus karena Siswa ini sudah memiliki data Orang Tua/Wali yang terikat!");
                exit();
            }
            
            // Mencoba menghapus data siswa (Hanya jika lolos restriksi di atas)
            $delete_siswa = mysqli_query($conn, "DELETE FROM siswa WHERE nis='$nis'");
            
            if ($delete_siswa) {
                header("Location: /poin_pelanggaran_siswa/pages/siswa/list.php?success=Siswa berhasil dihapus!");
                exit();
            } else {
                header("Location: /poin_pelanggaran_siswa/pages/siswa/list.php?error=Gagal menghapus siswa. Pastikan siswa tidak memiliki catatan pelanggaran yang terikat!");
                exit();
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