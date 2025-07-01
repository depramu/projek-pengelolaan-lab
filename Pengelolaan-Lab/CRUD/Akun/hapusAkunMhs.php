<?php
include '../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;
    
    if ($nim) {
        $query = "DELETE FROM Mahasiswa WHERE nim = ?";
        $stmt = sqlsrv_query($conn, $query, [$nim]);
        
        if ($stmt) {
            header("Location: ../../Menu PIC/manajemenAkunMhs.php");
            exit;
        } else {
            echo "<script>
            alert ('Gagal menghapus akun. Silahkan coba lagi.');
            window.location.href = '../../Menu PIC/manajemenAkunMhs.php'
            </script>";
            exit;
        }
    }
}

header("Location: ../../Menu PIC/manajemenAkunMhs.php");
include '../../templates/header.php';
exit;
?>