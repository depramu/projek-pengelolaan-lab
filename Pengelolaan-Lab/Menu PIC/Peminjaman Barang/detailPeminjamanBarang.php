    <?php
    // Implement koneksi.php
    include '../../koneksi.php';
    include '../../templates/header.php';
    include '../../templates/sidebar.php';

    $data = null;
    $error_message = null;

    $idPeminjamanBrg = $_GET['id'] ?? '';

    if (!empty($idPeminjamanBrg)) {
        // Query detail peminjaman barang beserta data terkait, join namaBarang dari tabel Barang
        $sql = "SELECT 
                    pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                    pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg, pb.statusPeminjaman,
                    b.namaBarang
                FROM 
                    Peminjaman_Barang pb
                JOIN
                    Barang b ON pb.idBarang = b.idBarang
                WHERE 
                    pb.idPeminjamanBrg = ?";
        $params = [$idPeminjamanBrg];
        $stmt = sqlsrv_query($conn, $sql, $params);

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
        <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
        <div class="mb-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
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


                        <div class="card-body scrollable-card-content" style="max-height: 75vh; overflow-y: auto;">
                            <?php if ($error_message) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_message ?>
                                </div>
                            <?php elseif ($data) : ?>
                                <form id="formDetail" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label fw-semibold">ID Peminjaman</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars($data['idPeminjamanBrg']) ?>
                                            </div>
                                            <input type="hidden" name="idPeminjamanBrg" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanBrg']) ?>">
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label fw-semibold">ID Barang</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars($data['idBarang']) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idBarang']) ?>">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">NIM/NPK</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if (!empty($data['nim'])) {
                                                    echo htmlspecialchars($data['nim']);
                                                } elseif (!empty($data['npk'])) {
                                                    echo htmlspecialchars($data['npk']);
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?php
                                                                                                if (!empty($data['nim'])) {
                                                                                                    echo htmlspecialchars($data['nim']);
                                                                                                } elseif (!empty($data['npk'])) {
                                                                                                    echo htmlspecialchars($data['npk']);
                                                                                                } else {
                                                                                                    echo '-';
                                                                                                }
                                                                                                ?>">
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label fw-semibold">Nama Barang</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars($data['namaBarang']) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars(
                                                    $data['tglPeminjamanBrg'] instanceof DateTime
                                                    ? $data['tglPeminjamanBrg']->format('d-m-y')
                                                    : ''
                                                    ) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= ($data['tglPeminjamanBrg'] instanceof DateTime) ? $data['tglPeminjamanBrg']->format('d F Y') : '' ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Jumlah Barang</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars($data['jumlahBrg']) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['jumlahBrg']) ?>">
                                        </div>
                                        
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars($data['alasanPeminjamanBrg']) ?>
                                            </div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanBrg']) ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Status Peminjaman</label>
                                            <?php
                                            // Tentukan class status
                                            $statusClass = 'text-secondary';
                                            switch ($data['statusPeminjaman']) {
                                                case 'Diajukan':
                                                    $statusClass = 'text-primary';
                                                    break;
                                                case 'Menunggu Persetujuan':
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 'Menunggu Pengecekan':
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
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between mt-3">
                                            <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary me-2">Kembali</a>
                                        </div>
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