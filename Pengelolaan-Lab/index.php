<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div class="role-container">
        <div class="role-card">
            <div class="role-left w-100">
                <div class="w-100 mb-4">
                    <img src="icon/logo-astratech.png" alt="Logo Astra" style="width:60px; margin-bottom:12px; display:block;" class="role-logo">
                </div>
                <div class="d-flex align-items-center justify-content-center w-100 mb-2" style="gap: 32px;">
                    <img src="icon/atoyRole.png" alt="Ilustrasi" class="role-illustration">
                    <div class="d-flex flex-column align-items-start">
                        <div class="role-title text-start">Sistem<br>Pengelolaan<br>Laboratorium</div>
                        <img src="icon/iconRole.png" alt="Icon Role" class="icon-role-img">
                    </div>
                </div>
            </div>
            <div class="role-right">
                <h3>Silahkan Pilih Role</h3>
                <a href="Login/login.php?role=Peminjam" class="btn btn-light role-btn fw-bold mb-2 d-flex align-items-center justify-content-center text-center" style="color: #065ba6;">
                    <span class="d-flex align-items-center justify-content-center" style="width: 24px;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <span class="w-100 text-center me-4">Peminjam</span>
                </a>
                <a href="Login/login.php?role=PIC Aset" class="btn btn-light role-btn fw-bold mb-2 d-flex align-items-center justify-content-center text-center" style="color: #065ba6;">
                    <span class="d-flex align-items-center justify-content-center" style="width: 24px;">
                        <i class="bi bi-briefcase-fill"></i>
                    </span>
                    <span class="w-100 text-center me-4">PIC Aset</span>
                </a>
                <a href="Login/login.php?role=KA UPT" class="btn btn-light role-btn fw-bold d-flex align-items-center justify-content-center text-center" style="color: #065ba6;">
                    <span class="d-flex align-items-center justify-content-center" style="width: 24px;">
                        <i class="bi bi-person-badge-fill"></i>
                    </span>
                    <span class="w-100 text-center me-4">KA UPT</span>
                </a>

                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
            </div>
        </div>
    </div>


    <?php

    include 'templates/footer.php';
    ?>