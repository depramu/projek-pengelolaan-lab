<?php
require_once '../../auth.php';
authorize_role('PIC Aset');
include '../../templates/header.php';
include '../../koneksi.php';

$idPeminjamanRuangan = $_GET['id'] ?? '';
$data = null;
$error = '';
$showModal = false;

// Ambil data peminjaman beserta nama peminjam (Mahasiswa/Karyawan) dan info nim/npk
if (!empty($idPeminjamanRuangan)) {
    $_SESSION['idPeminjamanRuangan'] = $idPeminjamanRuangan;

    $query = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan, p.statusPeminjaman,
                COALESCE(m.nama, k.nama) AS namaPeminjam
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Mahasiswa m ON p.nim = m.nim
            LEFT JOIN 
                Karyawan k ON p.npk = k.npk
            WHERE 
                p.idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $error_details = sqlsrv_errors();
        $error_message = "Error saat mengambil data peminjaman. ";
        if ($error_details) {
            foreach ($error_details as $err) {
                $error_message .= $err['message'] . " ";
            }
        }
        die($error_message);
    }

    if (sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Data peminjaman tidak ditemukan untuk ID: " . htmlspecialchars($idPeminjamanRuangan);
    }
} else {
    $error = "ID Peminjaman tidak valid.";
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['setuju'])) {
        // Setujui peminjaman
        $query = "UPDATE Peminjaman_Ruangan 
                  SET statusPeminjaman = 'Sedang Dipinjam'
                  WHERE idPeminjamanRuangan = ?";
        $params = array($idPeminjamanRuangan);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal melakukan pengajuan ruangan.";
            exit;
        }
    }
}
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <!-- Pengajuan Peminjaman Ruangan -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Pengajuan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST" id="formPengajuan">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanRuangan" class="form-label fw-semibold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanRuangan) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime) {
                                                echo htmlspecialchars($data['tglPeminjamanRuangan']->format('d-m-y'));
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?php
                                                                                                                                                if ($data && isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime) {
                                                                                                                                                    echo htmlspecialchars($data['tglPeminjamanRuangan']->format('d-m-y'));
                                                                                                                                                }
                                                                                                                                                ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && !empty($data['nim'])) {
                                                echo htmlspecialchars($data['nim']);
                                            } elseif ($data && !empty($data['npk'])) {
                                                echo htmlspecialchars($data['npk']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= $data && isset($data['nim']) ? htmlspecialchars($data['nim']) : '' ?>">
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= $data && isset($data['npk']) ? htmlspecialchars($data['npk']) : '' ?>">
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idRuangan" class="form-label fw-semibold">ID Ruangan</label>
                                        <div class="form-control-plaintext"><?= $data && isset($data['idRuangan']) ? htmlspecialchars($data['idRuangan']) : '' ?></div>
                                        <input type="hidden" class="form-control" id="idRuangan" name="idRuangan" value="<?= $data && isset($data['idRuangan']) ? htmlspecialchars($data['idRuangan']) : '' ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="waktuMulai" class="form-label fw-semibold">Waktu Mulai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if ($data && isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuMulai']->format('H:i'));
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" class="form-control" id="waktuMulai" name="waktuMulai" value="<?php
                                                                                                                                if ($data && isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime) {
                                                                                                                                    echo htmlspecialchars($data['waktuMulai']->format('H:i'));
                                                                                                                                }
                                                                                                                                ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="waktuSelesai" class="form-label fw-semibold">Waktu Selesai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if ($data && isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuSelesai']->format('H:i'));
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?php
                                                                                                                                    if ($data && isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime) {
                                                                                                                                        echo htmlspecialchars($data['waktuSelesai']->format('H:i'));
                                                                                                                                    }
                                                                                                                                    ?>">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="alasanPeminjaman" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && isset($data['alasanPeminjamanRuangan'])) {
                                                echo nl2br(htmlspecialchars($data['alasanPeminjamanRuangan']));
                                            }
                                            ?>
                                        </div>
                                        <textarea class="form-control w-100" id="alasanPeminjaman" name="alasanPeminjaman" hidden rows="3" style="background: #f5f5f5;"><?php
                                                                                                                                                                        if ($data && isset($data['alasanPeminjamanRuangan'])) {
                                                                                                                                                                            echo htmlspecialchars($data['alasanPeminjamanRuangan']);
                                                                                                                                                                        }
                                                                                                                                                                        ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <div class="align-self-start">
                                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/penolakanRuangan.php?id=<?= urlencode($idPeminjamanRuangan) ?>" class="btn btn-danger" id="btnTolak">Tolak</a>
                                    <button type="submit" name="setuju" class="btn btn-primary">Setuju</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>