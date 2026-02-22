<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>


<div class="container">
    <div class="header-table">
        <h2>Tambah Data Guru</h2>
        <a href="list.php">Daftar Guru</a>
    </div>

    <form action="/poin_pelanggaran_siswa/process/guru_process.php" method="POST">
        <table cellpadding="10">
            <input type="hidden" name="action" value="add">
            <tr>
                <?php
                $result = mysqli_query($conn, "SELECT kode_guru FROM guru ORDER BY kode_guru DESC LIMIT 1");
                $row = mysqli_fetch_assoc($result);
                $kode_guru = $row['kode_guru'];
                $kode_guru = explode(".", $kode_guru);
                $kode_guru = str_pad($kode_guru[1] + 1, 3, "0", STR_PAD_LEFT);
                ?>
                <td><label for="kode_guru">Kode Guru</label></td>
                <td><input type="text" name="kode_guru" autocomplete="off" value="0021.<?= $kode_guru ?>" required placeholder="Kode Guru" readonly /></td>
            </tr>
            <tr>
                <td><label for="nama_pengguna">Nama Guru</label></td>
                <td><input type="text" name="nama_pengguna" placeholder="Nama Guru"></td>
            </tr>
            <tr>
                <td><label for="username">Username</label></td>
                <td><input type="text" name="username" placeholder="Username"></td>
            </tr>
            <tr>
                <td><label for="jabatan">Jabatan</label></td>
                <td>
                <select name="jabatan" id="" style="width: 100%;">
                    <option value="">Pilih Jabatan</option>
                    <option value="Guru Mapel">Guru Mapel</option>
                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                    <option value="Waka Kurikulum">Waka Kurikulum</option>
                    <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                    <option value="Waka Sarana Prasarana">Waka Sarana Prasarana</option>
                    <option value="Waka Humas">Waka Humas</option>
                    <option value="Komka AN">Komka AN</option>
                    <option value="Komka RPL">Komka RPL</option>
                    <option value="Komka DKV">Komka DKV</option>
                    <option value="Komka TKJ">Komka TKJ</option>
                    <option value="Komka BD">Komka BD</option>
                    <option value="Guru BK XII">Guru BK XII</option>
                    <option value="Guru BK XI">Guru BK XI</option>
                    <option value="Guru BK X">Guru BK X</option>
                </select>
                </td>
            </tr>
            <tr>
                <td><label for="telp">Telepon</label></td>
                <td><input type="text" name="telp" placeholder="Telepon"></td>
            </tr>
            <tr>
                <td><label for="role">Role</label></td>
                <td>
                    <select name="role" id="" style="width: 100%;">
                        <option value="">Pilih Role</option>
                        <option value="bk">BK</option>
                        <option value="guru">Guru</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" style="float:right">Tambah Data Guru</button>
                </td>
            </tr>
        </table>
    </form>


</div>