<?php
require_once __DIR__ . '/../config.php';

function authorize_role($allowed_roles) {
    // Pastikan session sudah dimulai
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Ubah $allowed_roles menjadi array jika belum
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    // 1. Cek jika pengguna belum login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'Login/login.php');
        exit;
    }

    // 2. Cek jika role pengguna tidak ada di dalam daftar peran yang diizinkan
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        // Jika tidak diizinkan, arahkan ke halaman utama atau halaman "Akses Ditolak"
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

?>