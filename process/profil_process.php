<?php
/**
 * Sistem Poin Pelanggaran Siswa
 * File: process/profil_process.php
 * Deskripsi: Menghandle tampilan form edit profil dan proses pembaruannya
 */

// Load konfigurasi database
require_once('../config/config.php');

// Memulai session untuk pengecekan login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengecekan session - Jika tidak ada session username, berarti belum login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil Informasi Dasar dari Session
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$page_title = "Pengaturan Profil";

// ---------------------------------------------------------------------------------------
// LOGIKA 1: PROSES UPDATE (POST)
// ---------------------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input dasar
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    $password_baru = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    $update_fields = [];
    $error_msg = "";
    $success_msg = "";

    // Validasi Password (jika diisi)
    if (!empty($password_baru)) {
        if ($password_baru !== $konfirmasi_password) {
            $error_msg = "Konfirmasi password tidak cocok!";
        } else {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $update_fields[] = "password = '$hashed_password'";
        }
    }

    if (empty($error_msg)) {
        if ($role === 'siswa') {
            // Update Tabel Siswa
            $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
            $update_fields[] = "nama_siswa = '$nama_baru'";
            $update_fields[] = "alamat = '$alamat'";
            $sql = "UPDATE siswa SET " . implode(', ', $update_fields) . " WHERE nis = '$username'";
        } else {
            // Update Tabel Guru / BK
            $telp = mysqli_real_escape_string($conn, $_POST['telp']);
            $update_fields[] = "nama_pengguna = '$nama_baru'";
            $update_fields[] = "telp = '$telp'";
            $sql = "UPDATE guru SET " . implode(', ', $update_fields) . " WHERE username = '$username'";
        }

        if (mysqli_query($conn, $sql)) {
            // Update session data
            $_SESSION['nama'] = $nama_baru;
            $success_msg = "Profil berhasil diperbarui!";
        } else {
            $error_msg = "Terjadi kesalahan saat memperbarui database: " . mysqli_error($conn);
        }
    }
}

// ---------------------------------------------------------------------------------------
// LOGIKA 2: TAMPILAN FORM (GET)
// ---------------------------------------------------------------------------------------

// Tarik data terbaru dari DB untuk ditampilkan di form
if ($role === 'siswa') {
    $qr = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$username'");
    $userData = mysqli_fetch_assoc($qr);
    $currentNama = $userData['nama_siswa'];
    $currentExtraLabel = "Alamat Lengkap";
    $currentExtraValue = $userData['alamat'];
    $currentExtraName = "alamat";
} else {
    $qr = mysqli_query($conn, "SELECT * FROM guru WHERE username = '$username'");
    $userData = mysqli_fetch_assoc($qr);
    $currentNama = $userData['nama_pengguna'];
    $currentExtraLabel = "Nomor Telepon / WhatsApp";
    $currentExtraValue = $userData['telp'];
    $currentExtraName = "telp";
}

// Include Header (Sidebar)
include('../includes/header.php');
?>

<div class="profile-container">
    <div class="row">
        <!-- Bagian Info Avatar -->
        <div class="profile-sidebar">
            <div class="card glass text-center">
                <div class="avatar-big">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3><?php echo $currentNama; ?></h3>
                <p class="text-muted"><?php echo strtoupper($role); ?></p>
                <hr>
                <div class="stats-mini">
                    <div class="stat-item">
                        <span>Username/ID</span>
                        <strong><?php echo $username; ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Form Edit -->
        <div class="profile-main">
            <div class="card main-card">
                <div class="card-header">
                    <h3><i class="fas fa-id-card-clip"></i> Edit Detail Profil</h3>
                    <p>Perbarui informasi akun Anda secara berkala untuk menjaga keamanan.</p>
                </div>

                <?php if (isset($success_msg) && !empty($success_msg)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_msg) && !empty($error_msg)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="profile-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" value="<?php echo $currentNama; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label><?php echo $currentExtraLabel; ?></label>
                            <input type="text" name="<?php echo $currentExtraName; ?>" value="<?php echo $currentExtraValue; ?>" required>
                        </div>

                        <div class="form-divider">Keamanan Akun</div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti">
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="../pages/index.php" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS khusus untuk halaman profil yang premium */
.profile-container {
    max-width: 1000px;
    margin: 0 auto;
}

.row {
    display: flex;
    gap: 30px;
}

.profile-sidebar { flex: 1; }
.profile-main { flex: 2.5; }

.card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.card.glass {
    background: linear-gradient(135deg, #1C6EA4 0%, #144E75 100%);
    color: white;
}

.card.glass .text-muted { color: rgba(255,255,255,0.7); }

.avatar-big {
    font-size: 80px;
    margin-bottom: 20px;
    color: rgba(255,255,255,0.9);
}

.stats-mini {
    margin-top: 15px;
    text-align: left;
    font-size: 0.9rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
}

.card-header {
    margin-bottom: 30px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 15px;
}

.card-header h3 {
    margin: 0;
    font-size: 1.4rem;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header p {
    color: #64748b;
    margin-top: 5px;
    font-size: 0.9rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    color: #334155;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-group input:focus {
    border-color: #33A1E0;
    outline: none;
    box-shadow: 0 0 0 4px rgba(51, 161, 224, 0.1);
}

.form-divider {
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: 1px;
    margin: 30px 0 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid #f1f5f9;
}

.form-actions {
    margin-top: 35px;
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: none;
    transition: all 0.3s;
}

.btn-primary {
    background-color: #1C6EA4;
    color: white;
}

.btn-primary:hover {
    background-color: #144E75;
    transform: translateY(-2px);
}

.btn-light {
    background-color: #f1f5f9;
    color: #475569;
}

.alert {
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
}

.alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

@media (max-width: 768px) {
    .row { flex-direction: column; }
}
</style>

<?php 
// Include Footer
include('../includes/footer.php');
?>
