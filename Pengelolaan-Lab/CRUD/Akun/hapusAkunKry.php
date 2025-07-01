<?php
include '../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npk = $_POST['npk'] ?? null;
    
    if ($npk) {
        $query = "DELETE FROM Karyawan WHERE npk = ?";
        $stmt = sqlsrv_query($conn, $query, [$npk]);

        if ($stmt) {
            header("Location: ../../Menu PIC/manajemenAkunKry.php");
            exit;
        } else {
            echo "<script>
            alert ('Gagal menghapus akun. Silahkan coba lagi.');
            window.location.href = '../../Menu PIC/manajemenAkunKry.php'
            </script>";
            exit;
        }
    }
}

header("Location: ../../Menu PIC/manajemenAkunKry.php");
include '../../templates/header.php';
exit;
?>