<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

if (isset($_GET['id_kelas'])) {
    // Jika ada, simpan nilainya ke variabel $id_kelas
    $id_kelas = $_GET['id_kelas'];
} else {
    // Jika tidak ada, beri nilai default 0
    $id_kelas = 0;
}

// Lakukan pencarian data kelas berdasarkan id_kelas

$kelas = null;

if ($id_kelas > 0) {
    $result_kelas = mysqli_query($conn, "SELECT * FROM kelas JOIN tingkat USING(id_tingkat) JOIN program_keahlian USING(id_program_keahlian) JOIN guru USING(kode_guru) WHERE id_kelas = '$id_kelas'");
    if ($result_kelas && mysqli_num_rows($result_kelas) > 0) {
        $kelas = mysqli_fetch_assoc($result_kelas);
    }
}

if (!$kelas) {
    echo '<p>Kelas tidak ditemukan.</p>';
    include ROOTPATH . '/includes/footer.php';  // tampilkan footer
    exit;  // hentikan eksekusi kode
}

?>

<form action="/poin_pelanggaran_siswa/process/kelas_process.php" method="POST">

    <table cellpadding="10">
        <input type="text" name="id_kelas" value="<?= $kelas['id_kelas'] ?>" hidden>
        <input type="hidden" name="action" value="edit">

        <td><label>Tingkat</label></td>
        <td>
            <datalist id="tingkat">
                <?php
                // Mengambil semua data kelas dari tabel 'kelas' JOIN 'program_keahlian', 'tingkat'
                $query_tingkat = mysqli_query($conn, "SELECT * FROM tingkat");
                while ($tingkat = mysqli_fetch_assoc($query_tingkat)) {
                    echo "<option value='" . $tingkat['tingkat'] . "'></option>";
                }
                ?>
            </datalist>
            <input list="tingkat" id="tingkat" name="tingkat" placeholder="Tingkat" autocomplete="off" required value="<?= $kelas['tingkat'] ?>" />
        </td>
        </tr>
        <tr>
            <td><label>Program Keahlian</label></td>
            <td>
                <datalist id="program_keahlian">
                    <?php
                    // Mengambil semua data program keahlian dari tabel 'program_keahlian'
                    $query_program_keahlian = mysqli_query($conn, "SELECT * FROM program_keahlian");
                    while ($program_keahlian = mysqli_fetch_assoc($query_program_keahlian)) {
                        echo "<option value='" . $program_keahlian['program_keahlian'] . "'></option>";
                    }
                    ?>
                </datalist>
                <input list="program_keahlian" id="program_keahlian" name="program_keahlian" placeholder="Program Keahlian" autocomplete="off" required value="<?= $kelas['program_keahlian'] ?>" />
            </td>
        </tr>
        <tr>
            <td><label for="rombel">Rombel</label></td>
            <td>
                <input type="number" id="rombel" name="rombel" placeholder="Rombel" autocomplete="off" required value="<?= $kelas['rombel'] ?>" />
            </td>
        </tr>
        <tr>
            <td><label for="walikelas">Wali Kelas</label></td>
            <td>

                <datalist id="walikelas">
                    <?php
                    $query_guru = mysqli_query($conn, "SELECT * FROM guru WHERE aktif LIKE 'Y'");
                    while ($guru = mysqli_fetch_assoc($query_guru)) {
                        echo "<option value='" . $guru['nama_pengguna'] . "'></option>";
                    }
                    ?>
                </datalist>
                <input list="walikelas" id="walikelas" name="walikelas" placeholder="Wali Kelas" autocomplete="off" required value="<?= $kelas['nama_pengguna'] ?>" />
            </td>
        </tr>

        <tr>
            <td>
            <button type="submit">Simpan Perubahan</button>
            </td>
        </tr>
        <tr>
            <td>
                <a href="list.php">Kembali</a>
            </td>
        </tr>
    </table>

</form>