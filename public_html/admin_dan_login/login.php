<?php
// Tampilkan pesan error jika ada
$error_message = '';
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = '<p class="error-message">Username atau password salah!</p>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JDIH Admin</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="assets/video/background.mp4" type="video/mp4">
            Browser Anda tidak mendukung tag video.
        </video>
    </div>

    <div class="login-container">
        <div class="login-box">
            <img src="assets/images/logo-dprd-surabaya.png" alt="Logo DPRD" class="login-logo">
            <h2>LOGIN ADMIN JDIH</h2>

            <?php echo $error_message; // Menampilkan pesan error di sini ?>

            <form action="proses_login.php" method="post" class="login-form">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye password-toggle"></i>
                </div>
                <button type="submit" name="submit">
                    <i class="fas fa-sign-in-alt"></i> LOGIN
                </button>
            </form>
        </div>
    </div>

<script src="js/login.js" defer></script>
</body>
</html>