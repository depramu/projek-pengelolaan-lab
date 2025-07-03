<?php

require_once __DIR__ . '/../../function/auth.php';
authorize_role(['Peminjam']);

$showModal = false;

include '../../templates/header.php';
include '../../templates/sidebar.php';

// Auto-generate ID Peminjaman Barang
$idPeminjamanBrg = 'PJB001';
$stmtId = sqlsrv_query($conn, "SELECT TOP 1 idPeminjamanBrg FROM Peminjaman_Barang WHERE idPeminjamanBrg LIKE 'PJB%' ORDER BY idPeminjamanBrg DESC");
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $num = intval(substr($rowId['idPeminjamanBrg'], 3)) + 1;
    $idPeminjamanBrg = 'PJB' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// Validasi ID Barang dari URL
$idBarang = $_GET['idBarang'] ?? null;
if (empty($idBarang)) {
    die("Error: ID Barang tidak ditemukan. Silakan kembali dan pilih barang yang ingin dipinjam.");
}

// Ambil detail barang
$stmtDetail = sqlsrv_query($conn, "SELECT namaBarang, stokBarang FROM Barang WHERE idBarang = ?", [$idBarang]);
if ($stmtDetail && $dataBarang = sqlsrv_fetch_array($stmtDetail, SQLSRV_FETCH_ASSOC)) {
    [$namaBarang, $stokTersedia] = [$dataBarang['namaBarang'], $dataBarang['stokBarang']];
} else {
    die("Error: Data untuk ID Barang '" . htmlspecialchars($idBarang) . "' tidak ditemukan di database.");
}

// Data sesi
[$nim, $npk, $tglPeminjamanBrg] = [
    $_SESSION['nim'] ?? "-",
    $_SESSION['npk'] ?? "-",
    $_SESSION['tglPeminjamanBrg'] ?? "-"
];

/// Inisialisasi variabel
$error = null;
$showModal = false;

//  stok barang
$sqlStok = "SELECT stokBarang FROM Barang WHERE idBarang = ?";
$paramsStok = [$idBarang];
$stmtStok = sqlsrv_query($conn, $sqlStok, $paramsStok);
$stokBarang = sqlsrv_fetch_array($stmtStok, SQLSRV_FETCH_ASSOC)['stokBarang'];

// Proses Peminjaman hanya jika metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanBrg = $_POST['alasanPeminjamanBrg'];
    $jumlahBrg = (int)$_POST['jumlahBrg']; // Pastikan integer

    // Ubah format tanggal sebelum insert
    if ($tglPeminjamanBrg) {
        $dateObj = DateTime::createFromFormat('d-m-Y', $tglPeminjamanBrg);
        $tglPeminjamanBrgSQL = $dateObj ? $dateObj->format('d-m-y') : null;
    } else {
        $tglPeminjamanBrgSQL = null;
    }

    // 1. Insert data peminjaman    
    $queryInsert = "INSERT INTO Peminjaman_Barang (idPeminjamanBrg, idBarang, tglPeminjamanBrg, nim, npk, jumlahBrg, sisaPinjaman, alasanPeminjamanBrg, statusPeminjaman) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $paramsInsert = [$idPeminjamanBrg, $idBarang, $tglPeminjamanBrgSQL, $nim, $npk, $jumlahBrg, $jumlahBrg, $alasanPeminjamanBrg, 'Menunggu Persetujuan'];
    $stmtInsert = sqlsrv_query($conn, $queryInsert, $paramsInsert);

    if ($stmtInsert) {
        // 2. Jika insert berhasil, update stok barang
        $queryUpdate = "UPDATE Barang SET stokBarang = stokBarang - ? WHERE idBarang = ?";
        $paramsUpdate = [$jumlahBrg, $idBarang];
        $stmtUpdate = sqlsrv_query($conn, $queryUpdate, $paramsUpdate);

        if ($stmtUpdate) {
            $showModal = true;
        } else {
            $error = "Peminjaman tercatat, tetapi gagal mengupdate stok. Error: " . print_r(sqlsrv_errors(), true);
        }
    } else {
        $error = "Gagal menambahkan peminjaman barang. Error: " . print_r(sqlsrv_errors(), true);
    }
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php">Cek Barang</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/lihatBarang.php">Lihat Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Peminjaman Barang</span>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                            <form id="formTambahPeminjamanBrg"  method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-semibold">ID Peminjaman</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" id="idPeminjamanBrg" name="idPeminjamanBrg_display" value="<?= $idPeminjamanBrg ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idBarang" class="form-label fw-semibold">ID Barang</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" id="idBarang" name="idBarang_display" value="<?= $idBarang ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" name="namaBarang_display" value="<?= $namaBarang ?>">
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
                                        <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" name="tglDisplay" value="<?php if (!empty($tglPeminjamanBrg)) {
                                                                                                                                            $dateObj = DateTime::createFromFormat('d-m=y', $tglPeminjamanBrg);
                                                                                                                                            echo $dateObj ? $dateObj->format('d-m-Y') : htmlspecialchars($tglPeminjamanBrg);
                                                                                                                                        } ?>">
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
                                <div class="col-md-6">
                                    <label for="jumlahBrg" class="form-label w-100 fw-semibold">
                                        Jumlah Peminjaman
                                        <span id="jumlahError" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Jumlah harus lebih dari 0.</span>
                                    </label>
                                    <div class="input-group" style="max-width: 140px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input class="form-control text-center" id="jumlahBrg" name="jumlahBrg" value="0" min="0" style="max-width: 70px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                    <small class="text-muted">Stok tersedia: <span id="stokBarang"><?= $stokTersedia ?></span></small>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-semibold">
                                            Alasan Peminjaman <span id="alasanError" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                        </label>
                                        <textarea class="form-control" id="alasanPeminjamanBrg" name="alasanPeminjamanBrg" rows="1" placeholder="Masukkan alasan peminjaman.."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/lihatBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>


<?php
include '../../templates/footer.php';

?>