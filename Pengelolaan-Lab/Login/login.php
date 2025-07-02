<?php

require_once __DIR__ . '/../config.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    session_start();
}
include '../koneksi.php';

$error_message = '';
$role = $_GET['role'] ?? 'Peminjam';

// Tentukan judul dan label form berdasarkan peran
$pageTitle = "Login";
$identifierLabel = "NIM / NPK";
$identifierPlaceholder = "Masukkan NIM / NPK Anda";

if ($role === 'PIC Aset') {
    $pageTitle = "Login PIC Aset";
    $identifierLabel = "NPK";
    $identifierPlaceholder = "Masukkan NPK Anda";
} elseif ($role === 'KA UPT') {
    $pageTitle = "Login KA UPT";
    $identifierLabel = "NPK";
    $identifierPlaceholder = "Masukkan NPK Anda";
} elseif ($role === 'Peminjam') {
    $pageTitle = "Login Peminjam";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier'];
    $kataSandi = $_POST['kataSandi'];

    // Ambil role dari $_GET['role'] pada saat POST juga
    $role = $_GET['role'] ?? 'Peminjam';

    // Hilangkan validasi: langsung proses login tanpa cek kosong
    switch ($role) {
        case 'Peminjam':
            // Coba login sebagai Mahasiswa
            $query_mhs = "SELECT nim, kataSandi, nama FROM Mahasiswa WHERE nim = ?";
            $stmt_mhs = sqlsrv_query($conn, $query_mhs, [$identifier]);
            $row_mhs = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC);

            if ($row_mhs) {
                if ($kataSandi === $row_mhs['kataSandi']) {
                    $_SESSION['user_id'] = $row_mhs['nim'];
                    $_SESSION['user_nama'] = $row_mhs['nama'];
                    $_SESSION['user_role'] = 'Mahasiswa';
                    $_SESSION['nim'] = $row_mhs['nim'];
                    header('Location: ../Menu Peminjam/dashboardPeminjam.php');
                    exit;
                } else {
                    break;
                }
            }

            // Jika gagal, coba login sebagai Karyawan (Peminjam)
            $query_kry = "SELECT npk, kataSandi, nama, jenisRole FROM Karyawan WHERE npk = ?";
            $stmt_kry = sqlsrv_query($conn, $query_kry, [$identifier]);
            $row_kry = sqlsrv_fetch_array($stmt_kry, SQLSRV_FETCH_ASSOC);

            if ($row_kry) {
                if ($kataSandi === $row_kry['kataSandi']) {
                    $_SESSION['user_id'] = $row_kry['npk'];
                    $_SESSION['user_nama'] = $row_kry['nama'];
                    $_SESSION['user_role'] = 'Karyawan';
                    $_SESSION['npk'] = $row_kry['npk'];
                    header('Location: ../Menu Peminjam/dashboardPeminjam.php');
                    exit;
                } else {
                    break;
                }
            }

            $error_message = 'akun tidak terdafrar';
            break;

        case 'PIC Aset':
        case 'KA UPT':
            $expectedRole = ($role === 'PIC Aset') ? 'PIC Aset' : 'KA UPT';
            $redirectPath = ($role === 'PIC Aset') ? '../Menu PIC/dashboardPIC.php' : '../Menu Ka UPT/dashboardKaUPT.php';

            // Ambil user berdasarkan NPK
            $query = "SELECT npk, kataSandi, nama, jenisRole FROM Karyawan WHERE npk = ?";
            $stmt = sqlsrv_query($conn,  $query, [$identifier]);
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            if ($row) {
                // Cek password dan role HARUS sesuai
                if ($kataSandi === $row['kataSandi'] && isset($row['jenisRole']) && $row['jenisRole'] === $expectedRole) {
                    // Login berhasil -> STANDARISASI SESSION
                    $_SESSION['user_id'] = $row['npk'];
                    $_SESSION['user_nama'] = $row['nama'];
                    $_SESSION['user_role'] = $row['jenisRole'];
                    $_SESSION['npk'] = $row['npk'];
                    header('Location: ' . $redirectPath);
                    exit;
                } elseif ($kataSandi === $row['kataSandi']) {
                    // Password benar tapi role salah
                    $error_message = "Anda tidak memiliki hak akses sebagai $expectedRole.";
                } else {
                    // Password salah
                    $error_message = 'NPK atau Kata Sandi salah.';
                }
            } else {
                // NPK tidak ditemukan
                $error_message = 'NPK atau Kata Sandi salah.';
            }
            $error_message = 'akun tidak terdafrar';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Salin semua CSS dari salah satu file login lama Anda ke sini */
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }

        .container-login {
            display: flex;
            height: 100vh;
            width: 100%;
            box-sizing: border-box;
        }

        .login-left {
            background: #fff;
            padding: 48px;
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .role-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #065ba6;
            margin-bottom: 24px;
        }

        .role-illustration {
            width: 260px;
            min-width: 160px;
            margin-bottom: 0;
        }

        .icon-role-img {
            width: 180px;
            margin-top: 18px;
        }

        img {
            width: 2rem;
        }

        .login-right {
            background: #065ba6;
            flex-basis: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            box-sizing: border-box;
        }

        .login-form-container {
            width: 100%;
            max-width: 380px;
            margin-top: 40px;
            /* atau padding-top: 40px; */
        }


        .login-form-title {
            color: #fff;
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 35px;
            text-align: center;
        }

        .form-label {
            color: #e0e0e0;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #fff;
            border: none;
            border-radius: 8px 0 0 8px;
            padding: 0 15px;
            color: #6c757d;
        }

        .input-group-text i {
            font-size: 1.2rem;
        }

        .form-control {
            background-color: #fff;
            border: none;
            border-radius: 0 8px 8px 0;
            height: 50px;
            padding-left: 15px;
            font-size: 1rem;
            color: #333;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            background-color: #fff;
            border: none;
        }

        .forgot-link {
            color: #bfe4ff;
            font-size: 0.9rem;
            text-align: right;
            display: block;
            margin-top: -10px;
            margin-bottom: 30px;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* Perubahan pada tombol dan link */
        .btn-login-submit {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 0;
            font-size: 1rem;
            font-weight: 600;
            width: 150px;
            display: block;
            margin: 0 auto 15px auto;
            transition: background-color 0.3s ease;
        }


        .btn-login-submit:hover {
            background-color: #218838;
        }

        .alert-danger {
            font-size: 0.9rem;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .container-login {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                flex-basis: auto;
                width: 100%;
            }

            .login-left {
                padding: 40px 20px;
                text-align: center;
                height: auto;
                min-height: 300px;
            }

            .login-right {
                padding: 30px 20px;
                min-height: 350px;
            }
        }
    </style>
</head>

<body>
    <div class="container-login">
        <div class="login-left">
            <div class="w-100 mb-4">
                <img src="../icon/logo-astratech.png" alt="Logo Astra" style="width:60px; margin-bottom:12px; display:block;">
            </div>
            <div class="d-flex align-items-center justify-content-center w-100 mb-2" style="gap: 32px;">
                <img src="../icon/atoyRole.png" alt="Ilustrasi" class="role-illustration">
                <div class="d-flex flex-column align-items-start">
                    <div class="role-title text-start">Sistem<br>Pengelolaan<br>Laboratorium</div>
                    <img src="../icon/iconRole.png" alt="Icon Role" class="icon-role-img">
                </div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-form-container">
                <h3 class="login-form-title"><?php echo htmlspecialchars($pageTitle); ?></h3>
                <form action="login.php?role=<?php echo htmlspecialchars($role); ?>" method="POST" id="loginForm">
                    <?php if (!empty($error_message)): ?>
                        <div id="server-error" class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="identifier" class="form-label d-flex align-items-start">
                            <span><?php echo htmlspecialchars($identifierLabel); ?></span>
                            <span id="identifier-error" class="text-danger" style="font-size: 0.9rem; padding-left: 10px;"></span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><img src="../icon/iconID.svg" alt=""></span>
                            <input type="text" class="form-control" id="identifier" name="identifier" placeholder="<?php echo htmlspecialchars($identifierPlaceholder); ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="kataSandi" class="form-label d-flex align-items-start">
                            <span>Kata Sandi</span>
                            <span id="password-error" class="text-danger" style="font-size: 0.9rem; padding-left: 10px;"></span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><img src="../icon/iconPass.svg" alt=""></span>
                            <input type="password" class="form-control" id="kataSandi" name="kataSandi" placeholder="Masukkan Kata Sandi Anda">
                        </div>
                    </div>

                    <a href="LupaSandi.php" class="forgot-link text-white">Lupa Kata Sandi?</a>

                    <div class="d-flex justify-content-center" style="gap: 12px;">
                        <button type="button" class="btn-login-submit w-50" style="background-color: #6c757d; text-align: center; line-height: normal;" onclick="window.location.href='../index.php'">
                            Kembali
                        </button>
                        <button type="submit" class="btn-login-submit w-100" style="max-width: 300px;">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    include '../templates/footer.php';
    ?>