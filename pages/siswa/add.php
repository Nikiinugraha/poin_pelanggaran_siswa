<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>

<center>
    <h2>Tambah Data Siswa</h2>
    <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="post">
        <table cellpadding="10">
            <input type="hidden" name="action" value="add" />
        <tr>
                <td><label>NIS</label></td>
                <td><input type="number" name="id" required /></td>
         </tr>
        <tr>
                <td><label>Nama Siswa</label></td>
                <td><input type="text" name="nama_siswa" required /></td>
         </tr>
            <tr>
                <!-- Datalist berisi jenis kelamin -->
            <td><label>Jenis Kelamin</label></td>
                <td>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Alamat</label></td>
                <td><input type="text" name="alamat" required /></td>
            </tr>
            <tr>
                <td><label>Nama Ayah</label></td>
                <td><input type="text" name="nama_ayah" required /></td>
            </tr>
            <tr>
                <td><label>Nama Ibu</label></td>
                <td><input type="text" name="nama_ibu" required /></td>
            </tr>
            <tr>
                <td><label>Pekerjaan Ayah</label></td>
                <td><input type="text" name="pekerjaan_ayah" required /></td>
            </tr>
            <tr>
                <td><label>Pekerjaan Ibu</label></td>
                <td><input type="text" name="pekerjaan_ibu" required /></td>
            </tr>
            <tr>
                <td><label>Pekerjaan Wali</label></td>
                <td><input type="text" name="pekerjaan_wali" required /></td>
            </tr>
            <tr>
                <td><label>No Telpon Ayah</label></td>
                <td><input type="text" name="no_telp_ayah" required /></td>
            </tr>
            <tr>
                <td><label>No Telpon Ibu</label></td>
                <td><input type="text" name="no_telp_ibu" required /></td>
            </tr>
            <tr>
                <td><label>No Telpon Wali</label></td>
                <td><input type="text" name="no_telp_wali" required /></td>
            </tr>
            <tr>
                <td><label>Alamat Ayah</label></td>
                <td><input type="text" name="alamat_ayah" required /></td>
            </tr>
            <tr>
                <td><label>Alamat Ibu</label></td>
                <td><input type="text" name="alamat_ibu" required /></td>
            </tr>
            <tr>
                <td><label>Alamat Wali</label></td>
                <td><input type="text" name="alamat_wali" required /></td>
            </tr>
            <tr>
                <td><label for="id_kelas">Kelas</label></td>
                <td>
                    <select name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        $qKelas = mysqli_query($conn, 'SELECT k.id_kelas, t.tingkat, p.program_keahlian, k.rombel 
                                                    FROM kelas k 
                                                    JOIN tingkat t ON k.id_tingkat = t.id_tingkat 
                                                    JOIN program_keahlian p ON k.id_program_keahlian = p.id_program_keahlian 
                                                    ORDER BY t.tingkat, p.program_keahlian, k.rombel');
                        while ($k = mysqli_fetch_assoc($qKelas)) {
                            echo "<option value='{$k['id_kelas']}'>{$k['tingkat']} {$k['program_keahlian']} {$k['rombel']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>        
                <td>
                    <button type="submit" style="float:right">Simpan</button>
                </td>
            </tr>
        </table>
             
    </form>

</center>
<?php include ROOTPATH . '/includes/footer.php'; ?>