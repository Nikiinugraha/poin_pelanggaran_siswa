<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Metadata dasar halaman: karakter, viewport untuk mobile, dan judul -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem | E-Pooint</title>

    <!-- Memanggil library ikon FontAwesome (v6+) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Memanggil file CSS utama halaman login -->
    <link rel="stylesheet" href="/poin_pelanggaran_siswa/css/pages/login.css">
</head>

<body class="login-body">
    <main class="login-wrapper">
        
        <!-- Sisi Kiri: Branding & Informasi Sistem -->
        <section class="login-left">
            <div class="branding-content">
                <div class="brand-logo">
                    <img src="assets/images/logo ti.PNG" alt="Logo SMKS TI Bali Global Denpasar">
                </div>
                <h1 class="brand-title">Sistem Poin Pelanggaran Siswa</h1>
                <p class="brand-subtitle">
                    Platform Pemantauan Kedisiplinan Siswa yang Terintegrasi, Transparan, dan Modern.
                </p>
                <div class="brand-footer">
                    <p>&copy; 2026 SMKS TI Bali Global Denpasar. Hak Cipta Terlindungi.</p>
                </div>
            </div>
        </section>

        <!-- Sisi Kanan: Autentikasi Pengguna -->
        <section class="login-right">
            <div class="login-card">
                <header class="login-card-header">
                    <h2>Selamat Datang</h2>
                    <p>Silakan masukkan akun Anda untuk mulai mengelola data kedisiplinan.</p>
                </header>

                <div class="login-card-body">
                    <!-- Form Utama Login -->
                    <form action="process/login_process.php" method="POST" autocomplete="off">
                    
                        <!-- Fitur Pengaman Autofill Browser -->
                        <div style="display:none" aria-hidden="true">
                            <input type="text" name="fake_user">
                            <input type="password" name="fake_pass">
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username atau NIS</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <input type="text" id="username" name="username" placeholder="Masukkan Username/NIS" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Kata Sandi</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required autocomplete="new-password">
                                <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan kata sandi">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="login-btn">
                            <span>Masuk Sekarang</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <!-- Logika Interaktivitas: Toggle Visibilitas Password -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.querySelector('#password');
            const toggleBtn = document.querySelector('#togglePassword');
            const eyeIcon = document.querySelector('#eyeIcon');

            toggleBtn.addEventListener('click', function() {
                // Berpindah antara tipe password dan text
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ganti ikon dan Label aksesibilitas
                const isPassword = type === 'password';
                eyeIcon.className = isPassword ? 'fas fa-eye' : 'fas fa-eye-slash';
                this.setAttribute('aria-label', isPassword ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
            });
        });
    </script>
</body>

</html>
