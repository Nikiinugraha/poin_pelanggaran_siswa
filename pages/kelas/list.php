<?php

define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

$result = mysqli_query($conn, "SELECT id_kelas, tingkat, program_keahlian, rombel, nama_pengguna FROM kelas JOIN tingkat using(id_tingkat) JOIN program_keahlian using(id_program_keahlian) JOIN guru using(kode_guru) ORDER BY id_tingkat DESC, id_program_keahlian ASC, rombel ASC");
?>

<h2>Data Kelas</h2>

<a href="/poin_pelanggaran_siswa/pages/kelas/add.php">Tambah Data Kelas</a>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>No</th>
            <th>Kelas</th>
            <th>Wali Kelas</th>
            <th>Guru BK</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?php echo htmlspecialchars($row['tingkat'] . ' ' . $row['program_keahlian'] . ' ' . $row['rombel']); ?> </td>
            <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
            <td><?php
                        if( $row['tingkat'] == 'XII'){
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XII'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }else if( $row['tingkat'] == 'XI'){
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK XI'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }else{
                            $row2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_pengguna FROM guru WHERE jabatan = 'Guru BK X'"));
                            echo htmlspecialchars($row2['nama_pengguna']);
                        }
                        ?></td>
            <td>
                <form action="/poin_pelanggaran_siswa/process/kelas_process.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_kelas" value="<?= $row['id_kelas'] ?>">
                    <input type="submit" value="Delete">
                </form>
                <a href="/poin_pelanggaran_siswa/pages/kelas/edit.php?id_kelas">Edit</a>
            </td>
        </tr>
            <?php
            }
            ?>
    </tbody>
</table>

<?php include ROOTPATH . '/includes/footer.php'; ?>