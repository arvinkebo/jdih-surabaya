<?php
// public_html/index.php

// 1. Ambil path URL yang diminta
$request_uri = trim($_SERVER['REQUEST_URI'], '/');

// 2. Hapus nama subfolder jika berjalan di XAMPP
$base_path = 'jdih-surabaya/public_html';
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}
$request_uri = trim($request_uri, '/');

// 3. Pecah URL menjadi beberapa bagian
$parts = explode('/', $request_uri);
$page = $parts[0] ?? 'home';
$action = $parts[1] ?? null;
$id = $parts[2] ?? null;

// 4. Logika Routing
$file_to_include = null;

switch ($page) {
    case 'home':
    case '':
        $file_to_include = 'beranda/home.php';
        break;

    case 'profil':
        if ($action === 'sejarah') {
            $file_to_include = 'profil/sejarah-jdihn.php';
        } elseif ($action === 'dasar-hukum') {
            $file_to_include = 'profil/dasar-hukum.php';
        }
        break;

    case 'dokumen-hukum':
        if ($action === 'peraturan-daerah') {
            $file_to_include = 'dokumen-hukum/peraturan-daerah.php';
        } elseif ($action === 'peraturan-dprd') {
            $file_to_include = 'dokumen-hukum/peraturan-dprd.php';
        } elseif ($action === 'keputusan-dprd') {
            $file_to_include = 'dokumen-hukum/keputusan-dprd.php';
        } elseif ($action === 'keputusan-sekwan') {
            $file_to_include = 'dokumen-hukum/keputusan-sekwan.php';
        }
        break;

    case 'peraturan':
        if ($action === 'detail' && is_numeric($id)) {
            $_GET['id'] = $id;
            $file_to_include = 'beranda/detail_publik.php';
        }
        break;
}

// 5. Muat file yang sesuai atau tampilkan halaman 404
if ($file_to_include && file_exists($file_to_include)) {
    require $file_to_include;
} else {
    http_response_code(404);
    // Buat file 404.php yang bagus untuk ditampilkan di sini
    echo "<h1>404 Halaman Tidak Ditemukan</h1>";
}
?>
