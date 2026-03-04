<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

?>

<h2>Tambah Data Kelas</h2>

<form action="/poin_pelanggaran_siswa/process/kelas_process.php" METHOD="POST">

    <table cellpadding="10">
        <tr>
            <td><input type="hidden" name="action" value="add" /></td>
        </tr>
        <tr>
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
                <input list="tingkat" id="tingkat" name="tingkat" placeholder="Tingkat" autocomplete="off" required />
            </td>
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
                <input list="program_keahlian" id="program_keahlian" name="program_keahlian" placeholder="Program Keahlian" autocomplete="off" required />
            </td>
            <td><label for="rombel">Rombel</label></td>
            <td>
                <input type="number" id="rombel" name="rombel" placeholder="Rombel" autocomplete="off" required />
            </td>

        </tr>
    </table>
</form>