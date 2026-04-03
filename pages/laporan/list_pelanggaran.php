<?php
// Menentukan lokasi root folder proyek di server
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');

// Menghubungkan ke file konfigurasi (koneksi database)
include ROOTPATH . "/config/config.php";

// Menyertakan tampilan header (bagian atas halaman)
include ROOTPATH . "/includes/header.php";

// jika ada(isset) tombol ditekan dengan method GET berisi value cari maka jalankan perintah dalam if
if(isset($_GET['cari'])){
    $cari = $_GET['cari'];

    // Menggunakan subquery untuk mengambil satu tanggal yang paling terakhir jika ada data tanggal yang lebih dari satu (karena penggunaan GROUP BY nis), kita bisa menggunakan fungsi agregasi SQL yaitu MAX(). Mengganti kolom tanggal menjadi MAX(tanggal) as tanggal. Fungsi ini akan memeriksa seluruh data tanggal dari setiap nis yang sama, dan mengembalikan satu tanggal dengan nilai paling besar (yang berarti tanggal yang paling terakhir atau terbaru) sambil mencari hasil inputan user dicocokkan dengan nama siswa atau nis.
    $result = mysqli_query($conn, "SELECT ps.id_pelanggaran_siswa, s.nama_siswa, ps.tanggal, jp.jenis, ps.nis FROM pelanggaran_siswa ps JOIN siswa s USING(nis) JOIN jenis_pelanggaran jp USING(id_jenis_pelanggaran) WHERE ps.tanggal = (SELECT MAX(tanggal) FROM pelanggaran_siswa WHERE nis = ps.nis) AND (nama_siswa like '%".$cari."%' OR nis like '%".$cari."%') ORDER BY ps.tanggal DESC");	
    
// else akan berjalan atau tampil ketika tombol cari belum ditekan 
}else{
    // Menggunakan subquery untuk mengambil satu tanggal yang paling terakhir jika ada data tanggal yang lebih dari satu (karena penggunaan GROUP BY nis), kita bisa menggunakan fungsi agregasi SQL yaitu MAX(). Mengganti kolom tanggal menjadi MAX(tanggal) as tanggal. Fungsi ini akan memeriksa seluruh data tanggal dari setiap nis yang sama, dan mengembalikan satu tanggal dengan nilai paling besar (yang berarti tanggal yang paling terakhir atau terbaru).
    $result = mysqli_query($conn, "SELECT ps.id_pelanggaran_siswa, s.nama_siswa, ps.tanggal, jp.jenis, ps.nis FROM pelanggaran_siswa ps JOIN siswa s USING(nis) JOIN jenis_pelanggaran jp USING(id_jenis_pelanggaran) WHERE ps.tanggal = (SELECT MAX(tanggal) FROM pelanggaran_siswa WHERE nis = ps.nis) ORDER BY ps.tanggal DESC");
    
}


?>



<center>
    <fieldset style="width: 70%;">
        <legend>Daftar Pelanggaran Per Siswa</legend>
        <div class="scroll">
            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th colspan="7" align="right">
                            <h3 style="float:left; margin: 0;">Daftar Pelanggaran Per Siswa</h3>
                            <form action="list_pelanggaran.php" method="get">
                                <!-- menampilkan data nis dan nama siswa -->
                                <datalist id="nama_siswa">
                                    <?php
                                    $result_siswa = mysqli_query($conn, "SELECT nama_siswa, nis FROM pelanggaran_siswa JOIN siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) GROUP BY nis");
                                    while ($row_siswa = mysqli_fetch_assoc($result_siswa)) {
                                        echo "<option value='" . $row_siswa['nis'] . "'>";
                                        echo "<option value='" . $row_siswa['nama_siswa'] . "'>";
                                    }
                                    ?>
                                </datalist>
                                <input type="text" value="<?php if(isset($_GET['cari'])) { echo $_GET['cari']; } else { echo ""; } ?>" name="cari" placeholder="Masukkan NIS / Nama Siswa" list="nama_siswa" style="padding: 8px 15px;width: 200px;border-radius: 5px;" autocomplete="off">
                                <input type="submit" class="btn-warning" style="color:white; font-weight:bold;" value="Cari">
                                <a href="list_pelanggaran.php" class="btn-danger" style="text-decoration: none; color: white; font-family:'Arial'; font-size:13px;">Reset</a>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Point</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($result)==0){
                        echo "
                        <tr><td colspan='7' align='center'>Data Tidak Ditemukan</td></tr>";
                    }else{
                        while ($row = mysqli_fetch_assoc($result)){
                    ?>
                    <tr>
                        <td align="center"><?= $no++?></td>
                        <td align="center">
                            <?php
                            // ubah format tanggal dari YYYY-MM-DD H:i:s menjadi DD-MM-YYYY H:i:s
                            $datetime = date("d-m-Y H:i:s", strtotime($row['tanggal']));
                            // memecah tanggal dan jam
                            $tanggal = explode(" ", $datetime);
                            // memecah jam
                            $jam = $tanggal[1];
                            // memecah tanggal
                            $tanggal = explode("-", $tanggal[0]);
                            // array bulan dalam bahasa indonesia
                            $bulan = array(
                                "01" => "Januari",
                                "02" => "Pebruari",
                                "03" => "Maret",
                                "04" => "April",
                                "05" => "Mei",
                                "06" => "Juni",
                                "07" => "Juli",
                                "08" => "Agustus",
                                "09" => "September",
                                "10" => "Oktober",
                                "11" => "November",
                                "12" => "Desember"
                            );
                            // menggabungkan tanggal dan bulan dalam bahasa indonesia
                            $tanggal = $tanggal[0] . " " . $bulan[$tanggal[1]] . " " . $tanggal[2];
                            // tampilkan tanggal yang sudah dimodifikasi menjadi bahasa indonesia agar mudah dibaca
                            echo $tanggal;
                            echo "<br>";
                            echo $jam;
                            ?>
                        </td>
                        <td align="center"><?= htmlspecialchars($row['nis']) ?></td>
                        <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                        <td align="center">
                            <?php
                            // 1. Ambil data jenis pelanggaran siswa dari database (gunakan DISTINCT agar jenis yang sama hanya tampil 1x)
                            $query_pelanggaran = mysqli_query($conn, "SELECT DISTINCT jenis FROM pelanggaran_siswa JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$row[nis]'");
                            
                            // 2. Siapkan tempat penampungan (array) kosong untuk menyimpan daftar nama pelanggaran
                            $daftar_pelanggaran = [];
                            
                            // 3. Ambil data satu per satu dan masukkan ke tempat penampungan
                            while($data_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)){
                                // htmlspecialchars digunakan untuk keamanan agar teks aman saat ditampilkan
                                $daftar_pelanggaran[] = htmlspecialchars($data_pelanggaran['jenis']);
                            }
                            
                            // 4. Jika daftar pelanggaran ada (tidak kosong), maka tampilkan ke layar
                            if(!empty($daftar_pelanggaran)){
                                // Gabungkan semua pelanggaran dengan koma dan spasi, lalu akhiri dengan tanda titik
                                echo implode(', ', $daftar_pelanggaran) . '.';
                            }
                            ?>
                        </td>
                        <?php
                        // menghitung total poin dari kolom poin menggunakan fungsi SUM() pada mysql
                        $poin_persiswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(poin) FROM pelanggaran_siswa JOIN siswa USING(nis) JOIN jenis_pelanggaran USING(id_jenis_pelanggaran) WHERE nis = '$row[nis]'"))['SUM(poin)'];
                        ?>
                        <td align="center"><?= htmlspecialchars($poin_persiswa) ?></td>
                        <td>
                            <!-- tombol untuk menampilkan detail pelanggaran dengan mengirim nis terpilih melalui method GET -->
                            <button class="btn-primary"><a href="/poin_pelanggaran_siswa/pages/laporan/detail_pelanggaran.php?nis=<?=$row['nis']?>&from=list_pelanggaran.php">Detail</a></button>
                        </td>
                    </tr>
                    <?php
                        } 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </fieldset>
</center>









<?php 
include "../../includes/footer.php"; 
?>
