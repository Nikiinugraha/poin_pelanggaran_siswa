<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

// $result = mysqli_query($conn, "SELECT * FROM siswa

// JOIN ortu_wali ON siswa.Id_Ortu_Wali = ortu_wali.id
// JOIN kelas ON siswa.Id_Kelas = kelas.id
// JOIN tingkat ON kelas.Id_Tingkat = tingkat.id
// JOIN program_keahlian ON kelas.Program_Keahlian = program_keahlian.id
// JOIN guru ON kelas.Kode_Guru = guru.KodeGuru");

//

// Mengambil semua data siswa dari tabel 'Siswa' JOIN 'Ortu_Wali', 'Kelas', 'Tingkat', 'Program_Keahlian', 'Guru'
// $result = mysqli_query($conn, "SELECT * FROM siswa
// JOIN ortu_wali USING(id_ortu_wali)
// JOIN kelas USING(id_kelas)
// JOIN tingkat USING(tingkat)
// JOIN program_keahlian USING(program)
// JOIN guru USING(kode_guru)");
//
$result = mysqli_query($conn, 'SELECT * FROM guru WHERE aktif = "Y"');
$result_nonaktif = mysqli_query($conn, 'SELECT * FROM guru WHERE aktif = "N"');
?>

<div class="container">
    <div class="header-table">
        <h2>Daftar Guru</h2>
        <a href="add.php">Tambah Data Guru</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Guru</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Jabatan</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?= htmlspecialchars($no++); ?></td>
                    <td><?= htmlspecialchars($row['kode_guru']); ?></td>
                    <td><?= htmlspecialchars($row['nama_pengguna']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['role']); ?></td>
                    <td><?= htmlspecialchars($row['telp']); ?></td>
                    <td>
                        <a href="edit.php?kode_guru=<?= $row['kode_guru'] ?>">Edit</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>