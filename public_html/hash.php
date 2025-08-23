<?php
$password_asli = '';
$password_hash = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password_to_hash'])) {
    $password_asli = $_POST['password_to_hash'];
    // Membuat hash dari password menggunakan algoritma default yang aman
    $password_hash = password_hash($password_asli, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #f0f2f5; text-align: center;">

    <div style="width: 600px; margin: 80px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2>Password Hash Generator</h2>
        <p>Gunakan alat ini untuk membuat password hash yang aman.</p>
        
        <form action="hash.php" method="post">
            <p>
                <label for="password_to_hash">Masukkan Password Baru:</label><br>
                <input type="text" id="password_to_hash" name="password_to_hash" size="40" required>
            </p>
            <p><button type="submit">Generate Hash</button></p>
        </form>

        <?php if ($password_hash): ?>
            <hr>
            <h3>Hasil:</h3>
            <p>
                <strong>Password Asli:</strong><br>
                <code><?php echo htmlspecialchars($password_asli); ?></code>
            </p>
            <p>
                <strong>Password Hash (Siap di-copy ke database):</strong><br>
                <textarea rows="3" style="width: 100%; font-family: monospace; font-size: 14px;" readonly><?php echo $password_hash; ?></textarea>
            </p>
        <?php endif; ?>

    </div>

</body>
</html>