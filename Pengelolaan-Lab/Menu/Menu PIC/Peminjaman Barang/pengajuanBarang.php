<?php
require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset');

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = null; // Inisialisasi $data sebagai null
$error = '';
$showModal = false;

if (!empty($idPeminjamanBrg)) {
    $query = "SELECT
                pb.*,
                b.namaBarang,
                COALESCE(m.nama, k.nama) AS namaPeminjam,
                pb.statusPeminjaman
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
        // Ini akan menangkap error dari query SELECT
        $error_details = sqlsrv_errors();
        $error_message = "Error saat mengambil data peminjaman. ";
        if ($error_details) {
            foreach ($error_details as $err) {
                $error_message .= $err['message'] . " ";
            }
        }
        die($error_message); // Hentikan eksekusi dan tampilkan error
    }

    if (sqlsrv_has_rows($stmt)) { // Cek apakah ada baris yang dikembalikan
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Data peminjaman tidak ditemukan untuk ID: " . htmlspecialchars($idPeminjamanBrg);
    }
} else {
    $error = "ID Peminjaman tidak valid.";
}

// Proses form untuk menyetujui peminjaman 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (!empty($idPeminjamanBrg)) {
        sqlsrv_begin_transaction($conn);

        $updateQuery = "UPDATE Peminjaman_Barang
                        SET statusPeminjaman = 'Sedang Dipinjam'
                        WHERE idPeminjamanBrg = ?";
        $updateParams = array($idPeminjamanBrg);
        $stmtUpdate = sqlsrv_query($conn, $updateQuery, $updateParams);

        if ($stmtUpdate) {
            sqlsrv_commit($conn);
            $showModal = true;
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal menyetujui peminjaman barang. Detail: ";
            if ($errors) {
                foreach ($errors as $err) {
                    $error .= $err['message'] . "; ";
                }
            } else {
                $error .= "Kesalahan tidak diketahui.";
            }
        }
    } else {
        $error = "ID Peminjaman tidak ditemukan untuk persetujuan.";
    }
}
include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Pengajuan Peminjaman Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                            <div class="row">

                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-semibold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['idPeminjamanBrg'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($data['idPeminjamanBrg'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanBrg" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= isset($data['tglPeminjamanBrg']) ? htmlspecialchars($data['tglPeminjamanBrg']->format('d-m-y')) : '' ?></div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= isset($data['tglPeminjamanBrg']) ? htmlspecialchars($data['tglPeminjamanBrg']->format('Y-m-d')) : '' ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if (!empty($data['nim'])) {
                                                echo htmlspecialchars($data['nim']);
                                            } elseif (!empty($data['npk'])) {
                                                echo htmlspecialchars($data['npk']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>">
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($data['npk'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($data['alasanPeminjamanBrg'] ?? '')) ?></div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idBarang" class="form-label fw-semibold">ID Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['idBarang'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($data['idBarang'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['namaBarang'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($data['namaBarang'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaPeminjam" class="form-label fw-semibold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['namaPeminjam'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="namaPeminjam" name="namaPeminjam" value="<?= htmlspecialchars($data['namaPeminjam'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label fw-semibold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['jumlahBrg'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($data['jumlahBrg'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- Tombol Aksi -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                <div>
                                    <a href="penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
                                    <button type="submit" name="submit" class="btn btn-primary">Setuju</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>
<?php include '../../../templates/footer.php'; ?>