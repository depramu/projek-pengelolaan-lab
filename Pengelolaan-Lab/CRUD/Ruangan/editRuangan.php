<?php
include '../../templates/header.php';

$idRuangan = $_GET['id'] ?? null;

if (!$idRuangan) {
    header('Location: ../../Menu PIC/manajemenRuangan.php');
    exit;
}

$query = "SELECT * FROM Ruangan WHERE idRuangan = ?";
$stmt = sqlsrv_query($conn, $query, [$idRuangan]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {
    header('Location: ../../Menu PIC/manajemenRuangan.php');
    exit;
}

$showModal = false;
$error = '';
$kondisiRuanganList = ['Baik', 'Rusak'];
$ketersediaanList = ['Tersedia', 'Tidak Tersedia'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaRuangan = $_POST['namaRuangan'] ?? '';
    $kondisiRuangan = $_POST['kondisiRuangan'] ?? '';
    $ketersediaan = $_POST['ketersediaan'] ?? '';

    // Validasi
    if ($namaRuangan === '') {
        $namaError = "*Harus diisi";
    }
    if ($kondisiRuangan === '') {
        $kondisiError = "*Harus diisi";
    }
    if ($ketersediaan === '') {
        $ketersediaanError = "*Harus diisi";
    }

    if (empty($namaError) && empty($kondisiError) && empty($ketersediaanError)) {
        $updateQuery = "UPDATE Ruangan SET namaRuangan = ?, kondisiRuangan = ?, ketersediaan = ? WHERE idRuangan= ?";
        $params = [$namaRuangan, $kondisiRuangan, $ketersediaan, $idRuangan];
        $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($updateStmt) {
            $showModal = true;
            $stmt = sqlsrv_query($conn, $query, [$idRuangan]);
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            $error = "Gagal mengubah data ruangan.";
        }
    }
}
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenRuangan.php">Manajemen Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Ruangan</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Edit Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="idRuangan" class="form-label fw-semibold d-flex align-items-center">ID Ruangan</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="namaRuangan" class="form-label fw-semibold d-flex align-items-center">Nama Ruangan
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($namaError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $namaError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" class="form-control" id="namaRuangan" name="namaRuangan" value="<?= isset($namaRuangan) ? htmlspecialchars($namaRuangan) : htmlspecialchars($data['namaRuangan']) ?>" placeholder="Masukkan nama ruangan..">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="kondisiRuangan" class="form-label fw-semibold d-flex align-items-center">Kondisi Ruangan
                                        <span id="kondisiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($kondisiError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $kondisiError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                        <option value="" disabled <?= (!isset($kondisiRuangan) && (!isset($data['kondisiRuangan']) || $data['kondisiRuangan'] == '')) ? 'selected' : '' ?>>Pilih Kondisi</option>
                                        <?php foreach ($kondisiRuanganList as $kondisi): ?>
                                            <option value="<?= htmlspecialchars($kondisi) ?>" <?= ((isset($kondisiRuangan) && $kondisiRuangan == $kondisi) || (!isset($kondisiRuangan) && isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == $kondisi)) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($kondisi) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="ketersediaan" class="form-label fw-semibold d-flex align-items-center">Ketersediaan Ruangan
                                        <span id="ketersediaanError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($ketersediaanError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $ketersediaanError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <select class="form-select" id="ketersediaan" name="ketersediaan">
                                        <option value="" disabled <?= (!isset($ketersediaan) && (!isset($data['ketersediaan']) || $data['ketersediaan'] == '')) ? 'selected' : '' ?>>Pilih Ketersediaan</option>
                                        <?php foreach ($ketersediaanList as $tersedia): ?>
                                            <option value="<?= htmlspecialchars($tersedia) ?>" <?= ((isset($ketersediaan) && $ketersediaan == $tersedia) || (!isset($ketersediaan) && isset($data['ketersediaan']) && $data['ketersediaan'] == $tersedia)) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tersedia) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenRuangan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>