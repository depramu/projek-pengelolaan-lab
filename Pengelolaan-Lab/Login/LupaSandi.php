<?php
session_start();
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = 'Kolom tidak boleh kosong.';
    } else {
        require_once __DIR__ . '/../koneksi.php';
        require_once __DIR__ . '/../function/reset_password_helper.php';
        [$success, $msg] = resetUserPassword($conn, $email);
        if ($success) {
            $_SESSION['flash_success'] = $msg ?: 'Reset password berhasil dikirim. Silakan cek email Anda.';
            header('Location: LupaSandi.php');
            exit;
        } else {
            $error_message = $msg;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    
</head>
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
            <h2 class="login-form-title">Lupa Kata Sandi</h2>
            <h3 class="text-center mb-4" style="color: #fff;">Silahkan Masukkan Email</h3>

            <?php
            $success_message = $_SESSION['flash_success'] ?? '';
            unset($_SESSION['flash_success']);
            ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group flex-column">
                    <span id="emailError" class="text-danger ms-2" style="display:none;font-size:0.9rem;"></span>
                    <div class="d-flex w-100">
                        <span class="input-group-text"><img src="../icon/mail.svg" alt="Email"></span>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Masukkan Email">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 gap-3">
                    <button type="button" class="btn-login-submit w-50" style="background-color: #6c757d; text-align: center; line-height: normal;" onclick="window.location.href='login.php'">Kembali</button>
                    <button type="submit" class="btn btn-login-submit w-100"  style="max-width: 300px;">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include '../templates/footer.php';

?>