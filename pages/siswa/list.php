

<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . "/config/config.php";
include ROOTPATH . "/includes/header.php";

// $result = mysqli_query($conn, "SELECT * FROM siswa

// JOIN ortu_wali ON siswa.Id_Ortu_Wali = ortu_wali.id
// JOIN kelas ON siswa.Id_Kelas = kelas.id
// JOIN tingkat ON kelas.Id_Tingkat = tingkat.id
// JOIN program_keahlian ON kelas.Program_Keahlian = program_keahlian.id
// JOIN guru ON kelas.Kode_Guru = guru.KodeGuru");

// 

// Mengambil semua data siswa dari tabel 'Siswa' JOIN 'Ortu_Wali', 'Kelas', 'Tingkat', 'Program_Keahlian', 'Guru'
$result = mysqli_query($conn, "SELECT * FROM Siswa 
JOIN Ortu_Wali ON Siswa.Id_Ortu_Wali = Ortu_Wali.Id 
JOIN Kelas ON Siswa.Id_Kelas = Kelas.Id 
JOIN Tingkat ON Kelas.Tingkat = Tingkat.Id 
JOIN Program_Keahlian ON Kelas.Program = Program_Keahlian.Id 
JOIN Guru ON Kelas.Kode_Guru = Guru.Kode_Guru");
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
                <td><?php echo htmlspecialchars($row['NIS']); ?></td>
                <td><?php echo htmlspecialchars($row['Nama_Siswa']); ?></td>
                <td><?php echo htmlspecialchars($row['Jenis_Kelamin']); ?></td>
                <td><?php echo htmlspecialchars($row['Alamat']); ?></td>
                <td><?= (empty($row['Ayah']) || $row['Ayah'] == 'NULL') ? '-' : htmlspecialchars($row['Ayah']) ?></td>
                <td><?= (empty($row['Ibu']) || $row['Ibu'] == 'NULL') ? '-' : htmlspecialchars($row['Ibu']) ?></td>
                <td><?= (empty($row['Wali']) || $row['Wali'] == 'NULL') ? '-' : htmlspecialchars($row['Wali']) ?></td>
                  <td><?php echo htmlspecialchars($row['Pekerjaan_Ayah']); ?></td>
                  <td><?php echo htmlspecialchars($row['Pekerjaan_Ibu']); ?></td>
                  <td><?php echo htmlspecialchars($row['Pekerjaan_Wali']); ?></td>
                  <td><?= (empty($row['Alamat_Ayah']) || $row['Alamat_Ayah'] == 'NULL') ? '-' : htmlspecialchars($row['Alamat_Ayah']) ?></td>
                <td><?= (empty($row['Alamat_Ibu']) || $row['Alamat_Ibu'] == 'NULL') ? '-' : htmlspecialchars($row['Alamat_Ibu']) ?></td>
                <td><?= (empty($row['Alamat_Wali']) || $row['Alamat_Wali'] == 'NULL') ? '-' : htmlspecialchars($row['Alamat_Wali']) ?></td>
               <td><?php echo htmlspecialchars($row['Nama_Tingkat'] . ' ' . $row['Program_Keahlian'] . ' ' . $row['Rombel']); ?> </td>
               <td><?php echo htmlspecialchars($row['Nama_Pengguna']); ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>