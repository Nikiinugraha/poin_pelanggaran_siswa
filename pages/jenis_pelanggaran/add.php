<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>

<h2>Tambah Jenis Pelanggaran</h2>

<a href="/poin_pelanggaran_siswa/pages/jenis_pelanggaran/list.php">Daftar Jenis Pelanggaran</a>

<form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="POST">
  <table cellpadding="10">
    <input type="hidden" name="action" value="add" />
    <tr>
      <td>Nama Pelanggaran</td>
      <td><input type="text" autocomplete="off" name="jenis"></td>
    </tr>
    <tr>
      <td>Poin</td>
      <td><input type="number" autocomplete="off" name="poin"></td>
    </tr>
    <tr>
      <td><input type="submit" value="Tambah"></td>
    </tr>
  </table>
</form>

<?php
include ROOTPATH . '/includes/footer.php'
?>