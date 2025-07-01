<?php
include '../../koneksi.php';

$showModal = false;
$idPeminjamanRuangan = $_GET['id'] ?? '';
$error = null;
$kondisiError = '';
$catatanError = '';

// Proses POST untuk simpan ke database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idPeminjamanRuangan) {
    $kondisiRuangan = $_POST['kondisiRuangan'] ?? '';
    $catatanPengembalianRuangan = $_POST['catatanPengembalianRuangan'] ?? '';

    // Validasi harus diisi
    $valid = true;
    if (empty($kondisiRuangan)) {
        $kondisiError = "*Harus diisi";
        $valid = false;
    }
    if (empty($catatanPengembalianRuangan)) {
        $catatanError = "*Harus diisi";
        $valid = false;
    }

    if ($valid) {
        // Cek apakah sudah ada data pengembalian untuk id ini
        $cekSql = "SELECT COUNT(*) as jumlah FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
        $cekParams = [$idPeminjamanRuangan];
        $cekStmt = sqlsrv_query($conn, $cekSql, $cekParams);
        $sudahAda = false;
        if ($cekStmt && ($cekRow = sqlsrv_fetch_array($cekStmt, SQLSRV_FETCH_ASSOC))) {
            $sudahAda = $cekRow['jumlah'] > 0;
        }

        if ($sudahAda) {
            // Update
            $sqlSave = "UPDATE Pengembalian_Ruangan SET kondisiRuangan = ?, catatanPengembalianRuangan = ? WHERE idPeminjamanRuangan = ?";
            $paramsSave = [$kondisiRuangan, $catatanPengembalianRuangan, $idPeminjamanRuangan];
        } else {
            // Insert
            $sqlSave = "INSERT INTO Pengembalian_Ruangan (idPeminjamanRuangan, kondisiRuangan, catatanPengembalianRuangan) VALUES (?, ?, ?)";
            $paramsSave = [$idPeminjamanRuangan, $kondisiRuangan, $catatanPengembalianRuangan];
        }

        $stmtSave = sqlsrv_query($conn, $sqlSave, $paramsSave);

        if ($stmtSave) {
            // Update statusPeminjaman menjadi 'Telah Dikembalikan'
            $sqlUpdateStatus = "UPDATE Peminjaman_Ruangan SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
            $paramsUpdateStatus = ['Telah Dikembalikan', $idPeminjamanRuangan];
            $stmtUpdateStatus = sqlsrv_query($conn, $sqlUpdateStatus, $paramsUpdateStatus);

            if ($stmtUpdateStatus) {
                // Ambil idRuangan dari peminjaman untuk update ketersediaan ruangan
                $sqlGetRuangan = "SELECT idRuangan FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan = ?";
                $paramsGetRuangan = [$idPeminjamanRuangan];
                $stmtGetRuangan = sqlsrv_query($conn, $sqlGetRuangan, $paramsGetRuangan);
                $idRuangan = null;
                if ($stmtGetRuangan && ($rowRuangan = sqlsrv_fetch_array($stmtGetRuangan, SQLSRV_FETCH_ASSOC))) {
                    $idRuangan = $rowRuangan['idRuangan'];
                }

                if ($idRuangan) {
                    // Update ketersediaan ruangan menjadi 'Tersedia'
                    $sqlUpdateKetersediaan = "UPDATE Ruangan SET ketersediaan = ? WHERE idRuangan = ?";
                    $paramsUpdateKetersediaan = ['Tersedia', $idRuangan];
                    $stmtUpdateKetersediaan = sqlsrv_query($conn, $sqlUpdateKetersediaan, $paramsUpdateKetersediaan);
                }

                $showModal = true;
            } else {
                $error = "Gagal mengubah status peminjaman.";
            }
        } else {
            $error = "Gagal menyimpan data pengembalian ruangan.";
        }
    }
}

// Ambil data pengembalian ruangan dan dokumentasi
$data = null;
$dokSebelum = null;
$dokSesudah = null;
if ($idPeminjamanRuangan) {
    $sql = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan, p.statusPeminjaman,
                peng.kondisiRuangan, peng.catatanPengembalianRuangan
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Pengembalian_Ruangan peng ON p.idPeminjamanRuangan = peng.idPeminjamanRuangan
            WHERE 
                p.idPeminjamanRuangan = ?";
    $params = [$idPeminjamanRuangan];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
        $data = $row;
    }

    // Ambil dokumentasi sebelum dan sesudah dari database
    $sqlDok = "SELECT dokumentasiSebelum, dokumentasiSesudah FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
    $paramsDok = [$idPeminjamanRuangan];
    $stmtDok = sqlsrv_query($conn, $sqlDok, $paramsDok);
    if ($stmtDok && ($rowDok = sqlsrv_fetch_array($stmtDok, SQLSRV_FETCH_ASSOC))) {
        $dokSebelum = $rowDok['dokumentasiSebelum'] ?? null;
        $dokSesudah = $rowDok['dokumentasiSesudah'] ?? null;
    }
}

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Pengembalian Ruangan</h3>

    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengembalian Ruangan </li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Pengembalian Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formPengembalianRuangan" autocomplete="off">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="idPeminjamanRuangan" class="form-label fw-semibold">ID Peminjaman</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= isset($idPeminjamanRuangan) ? htmlspecialchars($idPeminjamanRuangan) : '' ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="kondisiRuangan" class="form-label fw-semibold d-flex align-items-center">
                                        Kondisi Ruangan
                                        <span id="kondisiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;">
                                            <?= !empty($kondisiError) ? htmlspecialchars($kondisiError) : '' ?>
                                        </span>
                                    </label>
                                    <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                        <option value="" disabled <?= (empty($data['kondisiRuangan'])) ? 'selected' : '' ?>>Pilih Kondisi Ruangan</option>
                                        <option value="Baik" <?= (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                        <option value="Rusak" <?= (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="catatanPengembalianRuangan" class="form-label fw-semibold d-flex align-items-center">
                                    Catatan Pengembalian
                                    <span id="catatanError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;">
                                        <?= !empty($catatanError) ? htmlspecialchars($catatanError) : '' ?>
                                    </span>
                                </label>
                                <textarea type="text" class="form-control" id="catatanPengembalianRuangan" name="catatanPengembalianRuangan" rows="3" style="resize: none;" placeholder="Masukkan catatan pengembalian.."><?= isset($data['catatanPengembalianRuangan']) ? htmlspecialchars($data['catatanPengembalianRuangan']) : '' ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="dokumentasiSebelum" class="fw-semibold">Dokumentasi sebelum pemakaian</label><br>
                                <?php if (!empty($dokSebelum)): ?>
                                    <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($dokSebelum) ?>" target="_blank">Lihat Dokumentasi</a>
                                <?php else: ?>
                                    <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <label for="dokumentasiSesudah" class="fw-semibold">Dokumentasi sesudah pemakaian</label><br>
                                <?php if (!empty($dokSesudah)): ?>
                                    <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($dokSesudah) ?>" target="_blank">Lihat Dokumentasi</a>
                                <?php else: ?>
                                    <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>

<?php include '../../templates/footer.php'; ?>