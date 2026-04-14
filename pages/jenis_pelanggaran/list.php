<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
$page_title = "Kategori Pelanggaran";
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/includes/cek_akses_guru.php'; // Proteksi khusus Guru

$result = mysqli_query($conn, 'SELECT * FROM jenis_pelanggaran');
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/jenis_pelanggaran/list_jenis_pelanggaran.css">

<div class="container">
    <div class="header-table">
        <h2><i class="fas fa-triangle-exclamation"></i> Daftar Jenis Pelanggaran</h2>
        <?php if (!in_array(strtolower($_SESSION['role']), ['wakasek', 'guru'])): ?>
            <a href="add.php" class="btn-add">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </a>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Nama Jenis Pelanggaran</th>
                        <th style="text-align: center;">Poin</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600;"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['jenis']) ?></strong></td>
                            <td style="text-align: center;">
                                <span class="poin-badge"><?= htmlspecialchars($row['poin']) ?></span>
                            </td>
                            <td class="action-buttons">
                                <?php if (!in_array(strtolower($_SESSION['role']), ['wakasek', 'guru'])): ?>
                                    <a href="edit.php?id_jenis_pelanggaran=<?= $row['id_jenis_pelanggaran'] ?>" class="btn-edit" title="Edit Data">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn-delete" title="Hapus Data" onclick="showDeleteModal('<?= $row['id_jenis_pelanggaran'] ?>', '<?= htmlspecialchars($row['jenis']) ?>')">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="badge" style="background:#f8fafc; color:#94a3b8;"><i class="fas fa-lock"></i></span>
                                <?php endif; ?>
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

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash-arrow-up"></i> Konfirmasi Hapus</h3>
            <span class="close-btn" onclick="closeModal('deleteModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="visual-icon danger-text">
                <i class="fas fa-circle-exclamation"></i>
            </div>
            <p>Apakah Anda yakin ingin menghapus jenis pelanggaran:</p>
            <h4 id="del-name" style="color: #e11d48; margin: 10px 0;">NAMA PELANGGARAN</h4>
            <p style="font-size: 0.85rem; color: #64748b;">Tindakan ini tidak dapat dibatalkan.</p>
            <form action="/poin_pelanggaran_siswa/process/jenis_pelanggaran_process.php" method="POST" style="margin-top: 25px;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="del-id">
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn-danger-large">Hapus Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Informasi -->
<div id="infoModal" class="modal">
    <div class="modal-content">
        <div id="info-header" class="modal-header">
            <h3><i id="info-icon" class="fas fa-circle-check"></i> <span id="info-title">Informasi</span></h3>
            <span class="close-btn" onclick="closeModal('infoModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div id="info-visual" class="visual-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <p id="info-message" style="margin: 20px 0; font-size: 1rem; color: #334155;">Pesan diproses di sini.</p>
            <button class="btn-primary-block" onclick="closeModal('infoModal')">Selesai</button>
        </div>
    </div>
</div>

<script>
function showDeleteModal(id, name) {
    document.getElementById('del-id').value = id;
    document.getElementById('del-name').innerText = name;
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
        header.className = 'modal-header';
        visual.className = 'visual-icon danger-text';
        visual.innerHTML = '<i class="fas fa-circle-xmark"></i>';
        icon.className = 'fas fa-triangle-exclamation';
    } else {
        header.className = 'modal-header success';
        visual.className = 'visual-icon success-text';
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

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showInfoModal(urlParams.get('success'), 'success');
    } else if (urlParams.has('error')) {
        showInfoModal(urlParams.get('error'), 'error');
    }
}
</script>

<?php include ROOTPATH . '/includes/footer.php'; ?>