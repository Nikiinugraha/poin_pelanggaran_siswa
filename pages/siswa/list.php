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
$result = mysqli_query($conn, 'SELECT * FROM siswa 
JOIN ortu_wali USING(id_ortu_wali)
JOIN kelas USING(id_kelas)
JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
JOIN guru USING(kode_guru)');
?>

<div class="container">
    <div class="header-table">
        <h2>Daftar Siswa</h2>
        <a href="add.php">Tambah Data Siswa</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Ayah</th>
                <th>Ibu</th>
                <th>Wali</th>
                <th>Pekerjaan Ayah</th>
                <th>Pekerjaan Ibu</th>
                <th>Pekerjaan Wali</th>
                <th>Alamat Ayah</th>
                <th>Alamat Ibu</th>
                <th>Alamat Wali</th>
                <th>Kelas</th>
                <th>Wali Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                    <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                    <td><?= (empty($row['ayah']) || $row['ayah'] == 'NULL') ? '-' : htmlspecialchars($row['ayah']) ?></td>
                    <td><?= (empty($row['ibu']) || $row['ibu'] == 'NULL') ? '-' : htmlspecialchars($row['ibu']) ?></td>
                    <td><?= (empty($row['wali']) || $row['wali'] == 'NULL') ? '-' : htmlspecialchars($row['wali']) ?></td>
                    <td><?php echo htmlspecialchars($row['pekerjaan_ayah']); ?></td>
                    <td><?php echo htmlspecialchars($row['pekerjaan_ibu']); ?></td>
                    <td><?php echo htmlspecialchars($row['pekerjaan_wali']); ?></td>
                    <td><?= (empty($row['alamat_Ayah']) || $row['alamat_ayah'] == 'NULL') ? '-' : htmlspecialchars($row['alamat_ayah']) ?></td>
                    <td><?= (empty($row['alamat_ibu']) || $row['alamat_ibu'] == 'NULL') ? '-' : htmlspecialchars($row['alamat_ibu']) ?></td>
                    <td><?= (empty($row['alamat_wali']) || $row['alamat_wali'] == 'NULL') ? '-' : htmlspecialchars($row['alamat_wali']) ?></td>
                    <td><?php echo htmlspecialchars($row['tingkat'] . ' ' . $row['program_keahlian'] . ' ' . $row['rombel']); ?> </td>
                    <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                    <td style="text-align: center;">
                        <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="nis" value="<?= $row['nis'] ?>">
                    <input type="submit" value="🗑️ Delete">
                </form>
                    </td>
                    <td>
                        <a href="edit.php?nis=<?= $row['nis'] ?>">Edit</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>