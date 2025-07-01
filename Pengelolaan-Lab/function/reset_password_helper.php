<?php
// Helper functions for password reset
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
require_once __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateSecurePassword(int $length = 8): string
{
    // Generates a random 8-character alphanumeric mixed-case password
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $maxIdx = strlen($chars) - 1;
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $idx = random_int(0, $maxIdx);
        $result .= $chars[$idx];
    }
    return $result;
}

/**
 * Attempt to reset user password for Mahasiswa or Karyawan.
 * Returns [bool success, string message]
 */
function resetUserPassword($conn, string $email): array
{
    // First, attempt to locate the user in Mahasiswa
    $sqlMhs = "SELECT nim AS id, nama AS nama FROM Mahasiswa WHERE email = ?";
    $stmtMhs = sqlsrv_query($conn, $sqlMhs, [$email]);
    if ($stmtMhs === false) {
        error_log('SQLSRV Mahasiswa query error: '.print_r(sqlsrv_errors(), true));
        return [false, 'Terjadi kesalahan database.'];
    }
    $row = sqlsrv_fetch_array($stmtMhs, SQLSRV_FETCH_ASSOC);

    $table = null;
    $idCol = null;
    $idVal = null;
    $namaLengkap = null;

    if ($row) {
        $table = 'Mahasiswa';
        $idCol = 'nim';
        $idVal = $row['id'];
        $namaLengkap = $row['nama'];
    } else {
        // Check Karyawan by email
        $sqlKry = "SELECT npk AS id, nama AS nama FROM Karyawan WHERE email = ?";
        $stmtKry = sqlsrv_query($conn, $sqlKry, [$email]);
        if ($stmtKry === false) {
            error_log('SQLSRV Karyawan query error: '.print_r(sqlsrv_errors(), true));
            return [false, 'Terjadi kesalahan database.'];
        }
        $rowK = sqlsrv_fetch_array($stmtKry, SQLSRV_FETCH_ASSOC);
        if ($rowK) {
            $table = 'Karyawan';
            $idCol = 'npk';
            $idVal = $rowK['id'];
            $namaLengkap = $rowK['nama'];
        }
    }

    if ($table === null) {
        return [false, 'Email tidak ditemukan.'];
    }

    $newPass = generateSecurePassword();

    $updateSql = "UPDATE $table SET kataSandi = ?, nama = ? WHERE $idCol = ?";
    $updateStmt = sqlsrv_query($conn, $updateSql, [$newPass, $namaLengkap, $idVal]);
    if ($updateStmt === false) {
        error_log('SQLSRV Update query error: '.print_r(sqlsrv_errors(), true));
        error_log('SQLSRV ERROR (Update): '.print_r(sqlsrv_errors(), true));
        return [false, 'Gagal memperbarui kata sandi.'];
    }

    // Kirim email menggunakan PHPMailer SMTP
    $configMail = require __DIR__ . '/config_email.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $configMail['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $configMail['username'];
        $mail->Password   = $configMail['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $configMail['port'];

        $mail->setFrom($configMail['from_email'], $configMail['from_name']);
        $mail->addAddress($email, $namaLengkap);

        $mail->Subject = 'Reset Kata Sandi - Sistem Pengelolaan Laboratorium';
        $mail->Body    = "Halo $namaLengkap,\n\nKata sandi sementara Anda: $newPass\n\nSegera ganti setelah login.";

        $mail->send();
        return [true, 'Kata sandi sementara telah dikirim ke email Anda.'];
    } catch (Exception $e) {
        return [true, 'Kata sandi sementara berhasil dibuat, namun email gagal dikirim: ' . $mail->ErrorInfo];
    }
}