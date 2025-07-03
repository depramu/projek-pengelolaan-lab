<?php

require_once __DIR__ . '/../config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Gunakan require_once untuk memastikan koneksi hanya di-load sekali.
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Pengelolaan Laboratorium</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>style.css">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="d-flex align-items-center justify-content-between px-3 px-md-5 py-3">
            <div class="d-flex align-items-center">
                <img src="<?= BASE_URL ?>/icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
                <div class="d-none d-md-block ps-3 ps-md-4" style="margin-left: 5vw;">
                    <span class="fw-semibold fs-3">Hello,</span><br>
                    <span class="fw-normal fs-6">
                        <?php
                        if (isset($_SESSION['user_nama'])) {
                            echo htmlspecialchars($_SESSION['user_nama']);
                        } else {
                            echo "Pengguna"; // Default yang lebih generik
                        }
                        if (isset($_SESSION['user_role'])) {
                            echo " (" . htmlspecialchars($_SESSION['user_role']) . ")";
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="<?= BASE_URL ?>/notif.php" class="me-0"><img src="<?= BASE_URL ?>/icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="<?= BASE_URL ?>/profil.php"><img src="<?= BASE_URL ?>/icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>