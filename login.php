<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Poin Pelanggaran Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/pages/login.css">  
</head>

<body>
    <div class="login-card">
        <h1>Welcome Back</h1>
        <p>Silakan login untuk mengakses sistem</p>
        
        <form action="process/login_process.php" method="post" autocomplete="off">
            <!-- Fake inputs to trick browser autofill -->
            <input type="text" name="fake_user" style="display:none" aria-hidden="true">
            <input type="password" name="fake_pass" style="display:none" aria-hidden="true">
            
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="new-password">
                    <i class="fas fa-eye toggle-password" id="eyeIcon"></i>
                </div>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        eyeIcon.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>