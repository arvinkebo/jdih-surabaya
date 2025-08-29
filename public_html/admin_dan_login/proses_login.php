<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("location: /login?error=1");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Prepare statement failed: " . $conn->error);
    header("location: /login?error=1");
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        
        // ✅ PERBAIKI REDIRECT INI - ke dashboard admin baru
        header("location: /admin/dashboard");
        exit();
    } else {
        header("location: /login?error=1");
        exit();
    }
} else {
    header("location: /login?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>