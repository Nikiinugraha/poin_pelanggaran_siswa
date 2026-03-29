<?php
define("ROOTPATH", $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';
?>
<fieldset>
    <legend>Tambah Data Pelanggaran Siswa</legend>
    <center>
        <form action="/poin_pelanggaran_siswa/process/pelanggaran_process.php" method="POST">
            <table cellpadding="10">
                <tr>
                    <td><input type="hidden" name="action" value="add"></td>
                </tr>
                <tr>
                    <td>
                        <label for="nis">NIS Siswa</label>
                    </td>

                    <td>
                        <datalist id="nis">
                            <?php
                            $query_siswa = mysqli_query($conn, "SELECT * FROM siswa");
                            while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                                echo "<option value='" . $siswa['nis'] . "'>" . $siswa['nama_siswa'] . "</option>";
                            }
                            ?>
                        </datalist>
                        <input type="text" list="nis" name="nis" placeholder="NIS Siswa" autocomplete="off" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="jenis_pelanggaran">Jenis Pelanggaran</label>
                    </td>
                    <td>
                        <datalist id="jenis_pelanggaran">
                            <?php
                            $query_pelanggaran = mysqli_query($conn, "SELECT * FROM jenis_pelanggaran");
                            while ($pelanggaran = mysqli_fetch_assoc($query_pelanggaran)) {
                                echo "<option value='" . $pelanggaran['jenis'] . "'>" . $pelanggaran['poin'] . "</option>";
                            }
                            ?>
                        </datalist>
                        <input type="text" list="jenis_pelanggaran" name="jenis_pelanggaran" id="jenis_pelanggaran" placeholder="Jenis Pelanggaran" autocomplete="off" required>
                    </td>
                </tr>
                <tr>
                    <td><label for="poin">Keterangan</label></td>
                    <td><textarea name="keterangan" id="keterangan" placeholder="Keterangan" autocomplete="off" required></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Tambah"></td>
                </tr>
            </table>    
        </form>
    </center>

</fieldset>

<?php
    include ROOTPATH .'/includes/footer.php';
?>