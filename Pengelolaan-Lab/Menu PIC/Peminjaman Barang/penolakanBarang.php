<?php
require_once __DIR__ . '/../../auth.php'; // Muat fungsi otorisasi
authorize_role('PIC Aset'); // Lindungi halaman ini untuk role 'Peminjam'
include '../../templates/header.php';
include '../../koneksi.php';

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

    // FIX: Add error handling for query failure
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
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
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
            $showModal = true; // Tampilkan modal sukses
            // Langsung redirect, tidak perlu modal
            // header("Location: peminjamanBarang.php?status=tolak&id=" . urlencode($idPeminjamanBrg));
            //exit();
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal memproses penolakan: ";
            foreach ($errors as $err) {
                $error .= $err['message'] . "; ";
            }
        }
    } else {
        $error = "Form tidak boleh kosong.";
    }
}
// include '../../templates/header.php';

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Penolakan Peminjaman Barang</h3>
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
                        <span class="fw-semibold">Penolakan Peminjaman Barang</span>
                    </div>

                    <!-- Error Message Display -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idBarang" class="form-label fw-bold">ID Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                        <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanBrg" class="form-label fw-bold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">NIM / NPK</label>
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
                                        <label for="alasanPeminjamanBrg" class="form-label fw-bold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-bold">ID Peminjaman Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-bold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaPeminjam" class="form-label fw-bold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaPeminjam) ?></div>
                                        <input type="hidden" class="form-control" id="namaPeminjam" name="namaPeminjam" value="<?= htmlspecialchars($namaPeminjam) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label fw-bold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                </div>
                            </div>


                            <!-- Alasan Penolakan -->
                            <div class="mb-3">
                                <label for="alasanPenolakan" class="form-label">Alasan Penolakan
                                    <span id="alasanError" class="text-danger ms-2" style="font-size:0.95em; display:none;">*Harus Diisi</span>
                                </label>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;"><?= htmlspecialchars($_POST['alasanPenolakan'] ?? '') ?></textarea>
                            </div>

                            <!-- Tombol -->
                            <div class="d-flex justify-content-between">
                                <a href="pengajuanBarang.php?id=<?= urlencode($idPeminjamanBrg) ?>" class="btn btn-secondary">Kembali</a> <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>

                        <script>
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const alasanField = document.getElementById('alasanPenolakan');
                                const errorField = document.getElementById('alasanError');
                                let valid = true;

                                if (alasanField.value.trim() === '') {
                                    errorField.textContent = '*Harus Diisi';
                                    errorField.style.display = 'inline';
                                    valid = false;
                                } else {
                                    errorField.textContent = '';
                                    errorField.style.display = 'none';
                                }

                                if (!valid) {
                                    e.preventDefault();
                                }
                            });
                        </script>

                        <!-- Modal Berhasil -->
                        <?php if ($showModal): ?>
                            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header"> <!-- <- di sini sudah diubah -->
                                            <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            PIC menolak peminjaman barang <strong><?= htmlspecialchars($idPeminjamanBrg) ?></strong>.
                                        </div>
                                        <div class="modal-footer">
                                            <a href="peminjamanBarang.php" class="btn btn-primary">OK</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                // Otomatis tampilkan modal jika $showModal true
                                document.addEventListener('DOMContentLoaded', function() {
                                    var modal = new bootstrap.Modal(document.getElementById('successModal'));
                                    modal.show();
                                });
                            </script>
                        <?php endif; ?>

                        <?php include '../../templates/footer.php'; ?>