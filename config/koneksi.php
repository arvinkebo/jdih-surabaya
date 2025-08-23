<?php
$host = "localhost";
$user = "root";
$pass = ""; // Di XAMPP defaultnya kosong
$db   = "db_jdih_surabaya";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
// Set charset
mysqli_set_charset($conn, "utf8");
?>