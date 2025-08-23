<?php
// public_html/buat_hash.php

// --- GANTI DENGAN PASSWORD YANG ANDA INGINKAN ---
$password_saya = 'admin123'; // Ganti 'admin123' dengan password yang aman

// Proses hashing password
$hash = password_hash($password_saya, PASSWORD_DEFAULT);

// Tampilkan hasilnya
echo "<h1>Password Hash Generator</h1>";
echo "<p>Password asli: " . htmlspecialchars($password_saya) . "</p>";
echo "<p><strong>Hash yang dihasilkan:</strong></p>";
echo "<textarea rows='4' cols='80' readonly>" . htmlspecialchars($hash) . "</textarea>";
echo "<p>Salin hash di atas dan tempelkan ke kolom 'password' di tabel 'users' pada database Anda.</p>";

?>
