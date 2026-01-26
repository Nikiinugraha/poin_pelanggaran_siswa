<?php
include "../../config/config.php";
include "../../includes/header.php";

$result = mysqli_query($conn, "SELECT ");
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
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $row['NIS'] . "</td>";
                echo "<td>" . $row['Nama_Siswa'] . "</td>";
                echo "<td>" . $row['Jenis_Kelamin'] . "</td>";
                echo "<td>" . $row['Alamat'] . "</td>";
                echo "<td>" . $row['Ayah'] . "</td>";
                echo "<td>" . $row['Ibu'] . "</td>";
                echo "<td>" . $row['Wali'] . "</td>";
                echo "<td>" . $row['Pekerjaan_Ibu'] . "</td>";
                echo "<td>" . $row['Pekerjaan_Wali'] . "</td>";
                echo "<td>" . $row['alamat_ayah'] . "</td>";
                echo "<td>" . $row['alamat_ibu'] . "</td>";
                echo "<td>" . $row['alamat_wali'] . "</td>";
                echo "<td>" . $row['kelas'] . "</td>";
                echo "<td>" . $row['wali_kelas'] . "</td>";
                echo "<td>
                    <a href='edit.php?id=" . $row['id'] . "'>Edit</a>
                    <a href='delete.php?id=" . $row['id'] . "'>Hapus</a>
                </td>";
                echo "</tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>