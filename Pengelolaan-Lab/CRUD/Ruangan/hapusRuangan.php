<?php
include '../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idRuangan = $_POST['idRuangan'] ?? null;

    if ($idRuangan) {
        $query = "DELETE FROM Ruangan WHERE idRuangan = ?";
        $stmt = sqlsrv_query($conn, $query, [$idRuangan]);

        if ($stmt) {
            header("Location: ../../Menu PIC/manajemenRuangan.php");
            exit;
        } else {
            echo "<script>
                alert('Gagal menghapus Ruangan. Silahkan coba lagi.');
                window.location.href = '../../Menu PIC/manajemenRuangan.php';
            </script>";
            exit;
        }
    }
}

header("Location: ../../Menu PIC/manajemenRuangan.php");
include '../../templates/header.php';
exit;
