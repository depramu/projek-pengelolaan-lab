<?php
include '../../templates/header.php';

$idRuangan = $_GET['idRuangan'] ?? null;
if (empty($idRuangan)) {
    die("Error: ID Ruangan tidak ditemukan. Silakan kembali dan pilih ruangan yang ingin dipinjam.");
}

$namaRuangan = '';
$sqlNama = "SELECT namaRuangan FROM Ruangan WHERE idRuangan = ?";
$params = [$idRuangan];
$stmtNama = sqlsrv_query($conn, $sqlNama, $params);

if ($stmtNama === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($rowNama = sqlsrv_fetch_array($stmtNama, SQLSRV_FETCH_ASSOC)) {
    $namaRuangan = $rowNama['namaRuangan'];
} else {
    echo "<div style='color:orange;'>Tidak ada ruangan dengan ID: $idRuangan</div>";
}


// Auto-generate id Peminjaman Ruangan
$idPeminjamanRuangan = 'PJR001';
$sqlId = "SELECT TOP 1 idPeminjamanRuangan FROM Peminjaman_Ruangan ORDER BY idPeminjamanRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idPeminjamanRuangan'];
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idPeminjamanRuangan = 'PJR' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

$showModal = false;
$error = null;
$nim = $_SESSION['nim'] ?? null;
$npk = $_SESSION['npk'] ?? null;

// Ambil data dari session
$tglPeminjamanRuangan = $_SESSION['tglPeminjamanRuangan'] ?? null; // Format: d-m-Y
$waktuMulai = $_SESSION['waktuMulai'] ?? null; // Format: H:i
$waktuSelesai = $_SESSION['waktuSelesai'] ?? null; // Format: H:i

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanRuangan = $_POST['alasanPeminjamanRuangan'];

    if (empty($alasanPeminjamanRuangan)) {
        $error = "Alasan peminjaman ruangan tidak boleh kosong";
    } else {
        $tglForSQL = DateTime::createFromFormat('d-m-Y', $tglPeminjamanRuangan)->format('Y-m-d');
        $waktuMulaiForSQL = DateTime::createFromFormat('H:i', $waktuMulai)->format('H:i:s');
        $waktuSelesaiForSQL = DateTime::createFromFormat('H:i', $waktuSelesai)->format('H:i:s');

        // Query INSERT dengan data yang sudah diformat
        $query = "INSERT INTO Peminjaman_Ruangan (idPeminjamanRuangan, idRuangan, nim, npk, tglPeminjamanRuangan, waktuMulai, waktuSelesai, alasanPeminjamanRuangan, statusPeminjaman) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $idPeminjamanRuangan,
            $idRuangan,
            $nim,
            $npk,
            $tglForSQL, // Gunakan tanggal yang sudah diformat
            $waktuMulaiForSQL, // Gunakan waktu mulai yang sudah diformat
            $waktuSelesaiForSQL, // Gunakan waktu selesai yang sudah diformat
            $alasanPeminjamanRuangan,
            'Menunggu Persetujuan'
        ];
        $stmtPeminjamanRuangan = sqlsrv_query($conn, $query, $params);
        if ($stmtPeminjamanRuangan) {
            // Jika INSERT peminjaman berhasil, baru UPDATE status ruangan
            $ketersediaanQuery = "UPDATE Ruangan SET ketersediaan = 'Tidak Tersedia' WHERE idRuangan = ?";
            $paramsKetersediaan = [$idRuangan];

            $stmtKetersediaan = sqlsrv_query($conn, $ketersediaanQuery, $paramsKetersediaan);

            if ($stmtKetersediaan) {
                // Jika UPDATE juga berhasil, tampilkan modal sukses
                $showModal = true;
            } else {
                // Kondisi jarang: Insert berhasil, tapi update gagal. Beri pesan error.
                $error = "Peminjaman berhasil dicatat, tetapi gagal memperbarui status ruangan. Error: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            // Jika INSERT gagal, berikan pesan error yang jelas
            $error = "Gagal mengajukan peminjaman ruangan. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
    if ($error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
}

include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/lihatRuangan.php">Lihat Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Pengajuan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahPeminjamanRuangan" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanRuangan" class="form-label fw-semibold">ID Peminjaman Ruangan</label>
                                        <input type="text" class="form-control protect-input" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= $idPeminjamanRuangan ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idRuangan" class="form-label fw-semibold">ID Ruangan</label>
                                        <input type="hidden" name="idRuangan" value="<?= $idRuangan ?>">
                                        <input type="text" class="form-control protect-input" id="idRuangan" name="idRuangan" value="<?= $idRuangan ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="namaRuangan" class="form-label fw-semibold">Nama Ruangan</label>
                                        <input type="text" class="form-control protect-input" id="namaRuangan" name="namaRuangan" value="<?= $namaRuangan ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="nim" class="form-label fw-semibold">NIM</label>
                                        <input type="text" class="form-control protect-input" id="nim" name="nim" value="<?= $nim ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control protect-input" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?= $tglPeminjamanRuangan ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="npk" class="form-label fw-semibold">NPK</label>
                                        <input type="text" class="form-control protect-input" id="npk" name="npk" value="<?= $npk ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label for="waktuMulai" class="form-label fw-semibold">Waktu Mulai</label>
                                        <input type="text" class="form-control protect-input" id="waktuMulai" name="waktuMulai" value="<?= $waktuMulai ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label for="waktuSelesai" class="form-label fw-semibold">Waktu Selesai</label>
                                        <input type="text" class="form-control protect-input" id="waktuSelesai" name="waktuSelesai" value="<?= $waktuSelesai ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="alasanPeminjamanRuangan" class="form-label fw-semibold">Alasan Peminjaman</label>
                                    <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                    <textarea class="form-control" id="alasanPeminjamanRuangan" name="alasanPeminjamanRuangan" rows="1" placeholder="Masukkan alasan peminjaman.."></textarea>


                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-5">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/lihatRuangan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                            </div>
                            </fobuat>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>



<?php
include '../../templates/footer.php';
?>

