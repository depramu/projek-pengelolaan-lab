<?php
require_once __DIR__ . '/../../function/auth.php';
authorize_role('PIC Aset');
include '../../templates/header.php';
require_once '../../koneksi.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];
$alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');

$showModal = false;
$error = '';

if (!empty($idPeminjamanBrg)) {

    $query = "SELECT
            pb.*,
            b.namaBarang,
            COALESCE(m.nama, k.nama) AS namaPeminjam
        FROM
            Peminjaman_Barang pb
        JOIN
            Barang b ON pb.idBarang = b.idBarang
        LEFT JOIN
            Mahasiswa m ON pb.nim = m.nim
        LEFT JOIN
            Karyawan k ON pb.npk = k.npk
        WHERE
            pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$namaPeminjam = $data['namaPeminjam'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('d-m-y') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanBrg) && !empty($alasanPenolakan)) {
        sqlsrv_begin_transaction($conn);

        $updateQuery = "UPDATE Peminjaman_Barang 
                        SET statusPeminjaman = 'Ditolak'
                        WHERE idPeminjamanBrg = ?";
        $updateParams = array($idPeminjamanBrg);
        $updateStmt = sqlsrv_query($conn, $updateQuery, $updateParams);

        $insertQuery = "INSERT INTO Penolakan (idPeminjamanBrg, alasanPenolakan) VALUES (?, ?)";
        $insertParams = array($idPeminjamanBrg, $alasanPenolakan);
        $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

        if ($updateStmt && $insertStmt) {
            sqlsrv_commit($conn);
            $showModal = true;
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal memproses penolakan: ";
            foreach ($errors as $err) {
                $error .= $err['message'] . "; ";
            }
        }
    } else {
    }
}

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item"><a href="pengajuanBarang.php">Pengajuan Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penolakan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <!-- Penolakan -->
    <div class="container mt-4 ">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Penolakan Peminjaman Barang</span>
                    </div>

                    <div class="card-body">
                        <form id="formPenolakanBarang" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-semibold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanBrg" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if (!empty($nim)) {
                                                echo htmlspecialchars($nim);
                                            } elseif (!empty($npk)) {
                                                echo htmlspecialchars($npk);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                <div class="mb-2">
                                        <label for="idBarang" class="form-label fw-semibold">ID Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                        <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaPeminjam" class="form-label fw-semibold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaPeminjam) ?></div>
                                        <input type="hidden" class="form-control" id="namaPeminjam" name="namaPeminjam" value="<?= htmlspecialchars($namaPeminjam) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label fw-semibold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alasanPenolakan" class="form-label fw-semibold">Alasan Penolakan
                                    <span id="alasanPenolakanError" class="fw-normal text-danger ms-2" style="font-size:0.95em; display:none;"></span>
                                </label>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="2" style="resize: none;" placeholder="Masukkan alasan penolakan..."><?= htmlspecialchars($_POST['alasanPenolakan'] ?? '') ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="pengajuanBarang.php?id=<?= urlencode($idPeminjamanBrg) ?>" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../templates/footer.php'; ?>