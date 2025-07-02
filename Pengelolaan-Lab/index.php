<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f7f8fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .role-container {
            height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        .role-card {
            background: #fff;
            overflow: hidden;
            display: flex;
            max-width: 100vw;
            height: 100vh;
            width: 100%;
            animation: slideIn 1s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-left {
            background: #fff;
            padding: 48px;
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeInLeft 1.2s ease-out 0.3s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .role-logo {
            width: 60px;
            margin-bottom: 16px;
            animation: bounceIn 1s ease-out 0.6s both;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .role-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #065ba6;
            margin-bottom: 24px;
            text-align: center;
            animation: fadeInUp 1s ease-out 0.9s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-illustration {
            width: 260px;
            min-width: 160px;
            margin-bottom: 0;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .icon-role-img {
            width: 180px;
            margin-top: 18px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .role-right {
            background: #065ba6;
            color: #fff;
            flex-basis: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeInRight 1.2s ease-out 0.3s both;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .role-right h3 {
            font-weight: 600;
            margin-bottom: 32px;
            font-size: 2rem;
            animation: fadeInDown 1s ease-out 0.6s both;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-btn {
            width: 25rem;
            margin-bottom: 22px;
            font-weight: 500;
            font-size: 1.2rem;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
            animation: slideInUp 0.8s ease-out both;
        }

        .role-btn:nth-child(2) {
            animation-delay: 0.9s;
        }

        .role-btn:nth-child(3) {
            animation-delay: 1.1s;
        }

        .role-btn:nth-child(4) {
            animation-delay: 1.3s;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .role-card {
                flex-direction: column;
                max-width: 99vw;
                height: 100vh;
                min-height: 0;
            }

            .role-left,
            .role-right {
                border-radius: 0;
                padding: 32px 10px;
                height: 50vh;
                min-height: 0;
            }

            .role-right {
                border-radius: 0 0 24px 24px;
            }

            .role-title {
                font-size: 1.2rem;
            }

            .role-btn {
                width: 100%;
            }
        }
    </style>
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