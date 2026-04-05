<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/poin_pelanggaran_siswa');
include ROOTPATH . '/config/config.php';
include ROOTPATH . '/includes/header.php';

$result = mysqli_query($conn, 'SELECT * FROM guru WHERE aktif = "Y"');
$result_nonaktif = mysqli_query($conn, 'SELECT * FROM guru WHERE aktif = "N"');
?>

<link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/guru/list_guru.css">

<div class="container">
    <!-- Header: Active Teachers -->
    <div class="header-table">
        <h2><i class="fas fa-chalkboard-user"></i> Daftar Guru Aktif</h2>
        <a href="add.php" class="btn-add">
            <i class="fas fa-plus-circle"></i> Tambah Data Guru
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Kode Guru</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Telepon</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td><span class="kode-badge"><?= htmlspecialchars($row['kode_guru']); ?></span></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_pengguna']); ?></strong>
                                <div class="jabatan-text"><?= htmlspecialchars($row['jabatan']); ?></div>
                            </td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><span class="role-badge"><?= htmlspecialchars($row['role']); ?></span></td>
                            <td><?= htmlspecialchars($row['telp']); ?></td>
                            <td class="action-buttons" style="justify-content: center;">
                                <a href="edit.php?kode_guru=<?= $row['kode_guru'] ?>" class="btn-edit" title="Edit Data">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Non-Active Teachers -->
    <div class="section-divider">
        <h3><i class="fas fa-user-slash"></i> Daftar Guru Non-Aktif</h3>
    </div>

    <div class="table-wrapper">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Kode Guru</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Telepon</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no_na = 1;
                    while ($row = mysqli_fetch_assoc($result_nonaktif)) {
                    ?>
                        <tr class="inactive-row">
                            <td style="text-align: center;"><?= $no_na++; ?></td>
                            <td><span class="kode-badge" style="background:#f1f5f9; color:#94a3b8;"><?= htmlspecialchars($row['kode_guru']); ?></span></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama_pengguna']); ?></strong>
                                <div class="jabatan-text"><?= htmlspecialchars($row['jabatan']); ?></div>
                            </td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><span class="role-badge"><?= htmlspecialchars($row['role']); ?></span></td>
                            <td><?= htmlspecialchars($row['telp']); ?></td>
                            <td class="action-buttons" style="justify-content: center;">
                                <a href="edit.php?kode_guru=<?= $row['kode_guru'] ?>" class="btn-edit" style="background:#f1f5f9; color:#94a3b8;" title="Edit Data">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
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
            <p id="info-message" style="margin: 20px 0; font-size: 1rem; color: #334155;">Pesan diproses di sini.</p>
            <button class="btn-primary-block" onclick="closeModal('infoModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
function showDeleteModal(kode, name) {
    document.getElementById('del-kode-guru').value = kode;
    document.getElementById('del-guru-name').innerText = name;
    document.getElementById('deleteModal').style.display = "block";
}

function showInfoModal(message, type = 'success') {
    const title = type === 'success' ? 'Berhasil' : 'Peringatan';
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