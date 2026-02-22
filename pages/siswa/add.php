<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>


<h2>Tambah Data Siswa</h2>
<form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="POST">
    <fieldset>
        <legend>Data Siswa</legend>
        <table cellpadding="10">
            <input type="hidden" name="action" value="add" />
            <tr>
                <td><label>NIS</label></td>
                <td><input type="number" autocomplete="off" name="nis"/></td>
            </tr>
            <tr>
                <td><label>Nama Siswa</label></td>
                <td><input type="text" autocomplete="off" name="nama_siswa" required /></td>
            </tr>
            <tr>
                <!-- Datalist berisi jenis kelamin -->
                <td><label>Jenis Kelamin</label></td>
                <td><input type="radio" name="jenis_kelamin" value="Laki - Laki" required />Laki - Laki
                    <input type="radio" name="jenis_kelamin" value="Perempuan" required />Perempuan
                </td>
            </tr>
            <tr>
                <td><label>Alamat</label></td>
                <td><textarea name="alamat_siswa" autocomplete="off" required></textarea></td>
            </tr>
            <tr>
                <td><label for="kelas">Kelas</label></td>

                <td>
                    <datalist id="kelas">
                        <?php
                        // Mengambil semua data kelas dari tabel 'kelas' JOIN 'program_keahlian', 'tingkat'
                        $query_kelas = mysqli_query($conn, "SELECT * FROM kelas JOIN program_keahlian USING(id_program_keahlian) JOIN tingkat USING(id_tingkat)");
                        while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                            echo "<option value='" . $kelas['tingkat'] . ' ' . $kelas['program_keahlian'] . ' ' . $kelas['rombel'] . "'></option>";
                        }
                        ?>
                    </datalist>
                    <input list="kelas" id="kelas" name="kelas" placeholder="Kelas" autocomplete="off" required />
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>Data Orang Tua</legend>
        <table cellpadding="10">

            <tr>
                <td><label>Nama Ayah</label></td>
                <td><input type="text" name="ayah" /></td>
                <td><label>No Telpon Ayah</label></td>
                <td><input type="text" name="telp_ayah" /></td>
                <td><label>Alamat Ayah</label></td>
                <td><input type="text" name="alamat_ayah" /></td>
                <td><label>Pekerjaan Ayah</label></td>
                <td><input type="text" name="pekerjaan_ayah" /></td>
            </tr>
            <tr>
                <td><label>Nama Ibu</label></td>
                <td><input type="text" name="ibu" /></td>
                <td><label>No Telpon Ibu</label></td>
                <td><input type="text" name="telp_ibu" /></td>
                <td><label>Alamat Ibu</label></td>
                <td><input type="text" name="alamat_ibu" /></td>
                <td><label>Pekerjaan Ibu</label></td>
                <td><input type="text" name="pekerjaan_ibu" /></td>

            </tr>
            <tr>
                <td><label>Nama Wali</label></td>
                <td><input type="text" name="wali" /></td>
                <td><label>No Telpon Wali</label></td>
                <td><input type="text" name="telp_wali" /></td>
                <td><label>Alamat Wali</label></td>
                <td><input type="text" name="alamat_wali" /></td>
                <td><label>Pekerjaan Wali</label></td>
                <td><input type="text" name="pekerjaan_wali" /></td>
            </tr>
    </fieldset>
    <tr>
        <td>
            <button type="submit" style="float:right">Simpan</button>
        </td>
    </tr>
    </table>


</form>
<?php include ROOTPATH . '/includes/footer.php'; ?>