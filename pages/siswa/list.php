<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru

include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/siswa/list_siswa.css">

<?php
$result = mysqli_query($conn, 'SELECT * FROM siswa 
JOIN ortu_wali USING(id_ortu_wali)
JOIN kelas USING(id_kelas)
JOIN tingkat ON kelas.id_tingkat = tingkat.id_tingkat
JOIN program_keahlian ON kelas.id_program_keahlian = program_keahlian.id_program_keahlian
JOIN guru USING(kode_guru)');
?>

<div class="container">
    <div class="header-table">
        <h2>Daftar Siswa</h2>
        <a href="add.php">Tambah Data Siswa</a>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>L/P</th>
                        <th>Alamat Siswa</th>
                        <th style="text-align: center;">Kontak Ortu</th>
                        <th>Kelas</th>
                        <th>Wali Kelas</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $jk_icon = ($row['jenis_kelamin'] == 'Laki - Laki') ? '<i class="fas fa-mars male"></i>' : '<i class="fas fa-venus female"></i>';
                        
                        // Prepare data for modal
                        $parent_data = htmlspecialchars(json_encode([
                            'nama_siswa' => $row['nama_siswa'],
                            'ayah' => $row['ayah'],
                            'pekerjaan_ayah' => $row['pekerjaan_ayah'],
                            'no_telp_ayah' => $row['no_telp_ayah'],
                            'alamat_ayah' => $row['alamat_ayah'],
                            'ibu' => $row['ibu'],
                            'pekerjaan_ibu' => $row['pekerjaan_ibu'],
                            'no_telp_ibu' => $row['no_telp_ibu'],
                            'alamat_ibu' => $row['alamat_ibu'],
                            'wali' => $row['wali'],
                            'pekerjaan_wali' => $row['pekerjaan_wali'],
                            'no_telp_wali' => $row['no_telp_wali'],
                            'alamat_wali' => $row['alamat_wali']
                        ]));
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><span class="nis-badge"><?php echo htmlspecialchars($row['nis']); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['nama_siswa']); ?></strong></td>
                            <td class="text-center" title="<?= $row['jenis_kelamin'] ?>"><?= $jk_icon ?></td>
                            <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                            <td style="text-align: center;">
                                <button class="btn-quickview" onclick='showParentDetail(<?= $parent_data ?>)'>
                                    <i class="fas fa-house-user"></i> Detail Ortu
                                </button>
                            </td>
                            <td><span class="badge" style="background:#f1f5f9; padding:5px 10px; border-radius:6px; font-weight:600;"><?php echo htmlspecialchars($row['tingkat'] . ' ' . $row['program_keahlian'] . ' ' . $row['rombel']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                            <td class="action-buttons">
                                <a href="edit.php?nis=<?= $row['nis'] ?>" class="btn-edit" title="Edit Profil">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn-delete" title="Hapus Data" onclick="showDeleteModal('<?= $row['nis'] ?>', '<?= htmlspecialchars($row['nama_siswa']) ?>')">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Ortu -->
<div id="parentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-users-viewfinder"></i> Detail Orang Tua / Wali</h3>
            <span class="close-btn" onclick="closeModal('parentModal')">&times;</span>
        </div>
        <div class="modal-body">
            <h4 id="m-siswa-name" style="color: #1C6EA4; margin-bottom: 20px;">Nama Siswa</h4>
            <div class="parent-grid">
                <div class="parent-card">
                    <h5><i class="fas fa-user-tie"></i> Ayah</h5>
                    <p><strong>Nama:</strong> <span id="m-ayah-nama">-</span></p>
                    <p><strong>Pekerjaan:</strong> <span id="m-ayah-kerja">-</span></p>
                    <p><strong>Telepon:</strong> <span id="m-ayah-telp">-</span></p>
                    <p><strong>Alamat:</strong> <span id="m-ayah-alamat">-</span></p>
                </div>
                <div class="parent-card">
                    <h5><i class="fas fa-user-dress"></i> Ibu</h5>
                    <p><strong>Nama:</strong> <span id="m-ibu-nama">-</span></p>
                    <p><strong>Pekerjaan:</strong> <span id="m-ibu-kerja">-</span></p>
                    <p><strong>Telepon:</strong> <span id="m-ibu-telp">-</span></p>
                    <p><strong>Alamat:</strong> <span id="m-ibu-alamat">-</span></p>
                </div>
                <div class="parent-card">
                    <h5><i class="fas fa-user-shield"></i> Wali</h5>
                    <p><strong>Nama:</strong> <span id="m-wali-nama">-</span></p>
                    <p><strong>Pekerjaan:</strong> <span id="m-wali-kerja">-</span></p>
                    <p><strong>Telepon:</strong> <span id="m-wali-telp">-</span></p>
                    <p><strong>Alamat:</strong> <span id="m-wali-alamat">-</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal">
    <div class="modal-content small-modal">
        <div class="modal-header danger-header">
            <h3><i class="fas fa-triangle-exclamation"></i> Konfirmasi Hapus</h3>
            <span class="close-btn" onclick="closeModal('deleteModal')">&times;</span>
        </div>
        <div class="modal-body text-center">
            <div class="danger-icon-large">
                <i class="fas fa-trash-arrow-up"></i>
            </div>
            <p>Apakah Anda yakin ingin menghapus data siswa:</p>
            <h4 id="del-siswa-name" style="color: #e11d48; margin: 10px 0;">NAMA SISWA</h4>
            <p style="font-size: 0.85rem; color: #64748b;">Tindakan ini tidak dapat dibatalkan.</p>
            <form action="/poin_pelanggaran_siswa/process/siswa_process.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="nis" id="del-nis">
                <div class="modal-actions-centered">
                    <button type="button" class="btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn-danger-large">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Informasi (Sukses/Gagal) -->
<div id="infoModal" class="modal">
    <div class="modal-content small-modal">
        <div id="info-header" class="modal-header">
            <h3><i id="info-icon" class="fas fa-circle-check"></i> <span id="info-title">Informasi</span></h3>
            <span class="close-btn" onclick="closeModal('infoModal')">&times;</span>
        </div>
        <div class="modal-body text-center">
            <div id="info-visual" class="info-visual">
                <i class="fas fa-check-circle"></i>
            </div>
            <p id="info-message" style="margin: 20px 0; font-size: 1rem; color: #334155;">Pesan sukses atau gagal akan muncul di sini.</p>
            <button class="btn-primary-block" onclick="closeModal('infoModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
function showParentDetail(data) {
    document.getElementById('m-siswa-name').innerText = "Orang Tua dari: " + data.nama_siswa;
    document.getElementById('m-ayah-nama').innerText = data.ayah || '-';
    document.getElementById('m-ayah-kerja').innerText = data.pekerjaan_ayah || '-';
    document.getElementById('m-ayah-telp').innerText = data.no_telp_ayah || '-';
    document.getElementById('m-ayah-alamat').innerText = data.alamat_ayah || '-';
    document.getElementById('m-ibu-nama').innerText = data.ibu || '-';
    document.getElementById('m-ibu-kerja').innerText = data.pekerjaan_ibu || '-';
    document.getElementById('m-ibu-telp').innerText = data.no_telp_ibu || '-';
    document.getElementById('m-ibu-alamat').innerText = data.alamat_ibu || '-';
    document.getElementById('m-wali-nama').innerText = data.wali || '-';
    document.getElementById('m-wali-kerja').innerText = data.pekerjaan_wali || '-';
    document.getElementById('m-wali-telp').innerText = data.no_telp_wali || '-';
    document.getElementById('m-wali-alamat').innerText = data.alamat_wali || '-';
    document.getElementById('parentModal').style.display = "block";
}

function showDeleteModal(nis, name) {
    document.getElementById('del-nis').value = nis;
    document.getElementById('del-siswa-name').innerText = name;
    document.getElementById('deleteModal').style.display = "block";
}

function showInfoModal(message, type = 'success') {
    const title = type === 'success' ? 'Berhasil' : 'Kesalahan';
    const header = document.getElementById('info-header');
    const visual = document.getElementById('info-visual');
    const icon = document.getElementById('info-icon');
    
    document.getElementById('info-title').innerText = title;
    document.getElementById('info-message').innerText = message;
    
    if (type === 'error') {
        header.className = 'modal-header danger-header';
        visual.className = 'info-visual danger-text';
        visual.innerHTML = '<i class="fas fa-circle-xmark"></i>';
        icon.className = 'fas fa-triangle-exclamation';
    } else {
        header.className = 'modal-header success-header';
        visual.className = 'info-visual success-text';
        visual.innerHTML = '<i class="fas fa-circle-check"></i>';
        icon.className = 'fas fa-circle-check';
    }
    
    document.getElementById('infoModal').style.display = "block";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
}

window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}

// Auto-show info modal if URL has success/error params
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showInfoModal(urlParams.get('success'), 'success');
    } else if (urlParams.has('error')) {
        showInfoModal(urlParams.get('error'), 'error');
    }
}
</script>
