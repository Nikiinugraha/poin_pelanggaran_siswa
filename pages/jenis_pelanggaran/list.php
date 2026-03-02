<?php
//menentukan lokasi root folder proyek dii server
DEFINE('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

//menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . '/config/config.php';

// menghubungkan ke file header
include ROOTPATH . '/includes/header.php';

$result = mysqli_query($conn, 'SELECT * FROM jenis_pelanggaran');

?>

<!-- membuat tampilann list jenis pelanggaran -->
<center>
    <h2>Data Jenis Pelanggaran</h2>

    <!-- Tombol untuk menuju halaman tambah siswa -->
    <a href="add.php">+ Tambah Data</a><br><br>

    <!-- Memuat table jenis pelanggaran -->

    <table cellpadding="10" cellspacing="0" border="1" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pelanggaran</th>
                <th>Poin</th>
                <th colspan="2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['jenis']) ?></td>
                    <td><?= htmlspecialchars($row['poin']) ?></td>
                    <td>
                        <button class="btn-warning"><a href="edit.php?id_jenis_pelanggaran=<?= $row['id_jenis_pelanggaran'] ?>">Edit</a></button>
                    </td>
                    <td>
                        <form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="post"
                            onsubmit="return confirm('Ingin Menghapus data <?= $row['jenis'] ?>?')">
                            <!-- Kirim id dan action ke file proses -->
                            <input type="hidden" name="id" value="<?= $row['id_jenis_pelanggaran'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button class="btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>