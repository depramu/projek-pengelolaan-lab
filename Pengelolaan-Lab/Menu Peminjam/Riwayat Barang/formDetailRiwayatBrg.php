<?php

include '../../templates/header.php';
include '../../templates/sidebar.php';

$data = null;
$error_message = null;

if (isset($_GET['idPeminjamanBrg'])) {
    $idPeminjamanBrg = $_GET['idPeminjamanBrg'];
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT 
                pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg, pb.statusPeminjaman,
                b.namaBarang,
                tolak.alasanPenolakan
            FROM 
                Peminjaman_Barang pb
            JOIN 
                Barang b ON pb.idBarang = b.idBarang
            LEFT JOIN 
                Penolakan tolak ON pb.idPeminjamanBrg = tolak.idPeminjamanBrg
            WHERE 
                pb.idPeminjamanBrg = ?";
    $params = [$idPeminjamanBrg];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanBrg) . "' tidak ditemukan.";
        }
    }
} else {
    $error_message = "ID Peminjaman Barang tidak valid atau tidak disertakan.";
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Barang</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php">Riwayat Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Detail Peminjaman Barang</span>
                    </div>
                    <div class="card-body scrollable-card-content">
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php elseif (!empty($data)) : ?>
                            <form id="formDetail" action="#" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">ID Peminjaman Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idPeminjamanBrg']) ?></div>
                                            <input type="hidden" name="idPeminjamanBrg" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanBrg']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">NIM / NPK</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['nim'] ?: $data['npk'] ?: '-') ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['nim'] ?: $data['npk'] ?: '-') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">ID Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idBarang']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idBarang']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaBarang']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars(
                                                    ($data['tglPeminjamanBrg'] instanceof DateTime)
                                                        ? $data['tglPeminjamanBrg']->format('d-m-Y')
                                                        : $data['tglPeminjamanBrg']
                                                ) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars(
                                                ($data['tglPeminjamanBrg'] instanceof DateTime)
                                                    ? $data['tglPeminjamanBrg']->format('d-m-Y')
                                                    : $data['tglPeminjamanBrg']
                                            ) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Jumlah Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['jumlahBrg']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['jumlahBrg']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Peminjaman</label>
                                            <?php
                                            $statusClass = 'text-secondary';
                                            switch ($data['statusPeminjaman']) {
                                                case 'Diajukan':
                                                    $statusClass = 'text-primary';
                                                    break;
                                                case 'Menunggu Persetujuan':
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 'Sedang Dipinjam':
                                                    $statusClass = 'text-info';
                                                    break;
                                                case 'Telah Dikembalikan':
                                                    $statusClass = 'text-success';
                                                    break;
                                                case 'Ditolak':
                                                    $statusClass = 'text-danger';
                                                    break;
                                            }
                                            ?>
                                            <div class="form-control-plaintext <?= $statusClass ?>"><?= htmlspecialchars($data['statusPeminjaman']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['statusPeminjaman']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($data['alasanPeminjamanBrg'])) ?></div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanBrg']) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if ($data['statusPeminjaman'] == 'Ditolak' && !empty($data['alasanPenolakan'])) : ?>
                                    <hr>
                                    <h6 class="mb-3">DETAIL PENOLAKAN</h6>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold text-danger">Alasan Penolakan dari PIC</label>
                                        <div class="form-control-plaintext text-danger"><?= nl2br(htmlspecialchars($data['alasanPenolakan'])) ?></div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="btn btn-secondary me-2">Kembali</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../../templates/footer.php';
?>