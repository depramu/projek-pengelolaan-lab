<?php
include '../../templates/header.php';

// Ambil id barang dari parameter GET
$idBarang = $_GET['id'] ?? null;
$error = '';
$showModal = false;

if (!$idBarang) {
    header('Location: ../../Menu PIC/manajemenBarang.php');
    exit;
}

// Ambil data barang berdasarkan id
$query = "SELECT * FROM Barang WHERE idBarang = ?";
$stmt = sqlsrv_query($conn, $query, [$idBarang]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {
    header('Location: ../../Menu PIC/manajemenBarang.php?error=notfound');
    exit;
}

// Ambil daftar lokasi (idRuangan) dari tabel Ruangan
$lokasiList = [];
$sqlLokasi = "SELECT idRuangan FROM Ruangan";
$stmtLokasi = sqlsrv_query($conn, $sqlLokasi);
if ($stmtLokasi) {
    while ($row = sqlsrv_fetch_array($stmtLokasi, SQLSRV_FETCH_ASSOC)) {
        $lokasiList[] = $row['idRuangan'];
    }
}

// Proses form jika ada submit POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'] ?? '';
    $stokBarang = $_POST['stokBarang'] ?? '';
    $lokasiBarang = $_POST['lokasiBarang'] ?? '';

    // Validasi
    if (trim($namaBarang) === '' || $stokBarang === '' || !is_numeric($stokBarang) || intval($stokBarang) < 0 || empty($lokasiBarang)) {
        $error = "Semua field harus diisi dengan benar. Stok tidak boleh kurang dari 0.";
    } else {
        $updateQuery = "UPDATE Barang SET namaBarang = ?, stokBarang = ?, lokasiBarang = ? WHERE idBarang = ?";
        $params = [$namaBarang, $stokBarang, $lokasiBarang, $idBarang];
        $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($updateStmt) {
            $showModal = true;
            // Refresh data setelah update
            $stmt = sqlsrv_query($conn, $query, [$idBarang]);
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            $error = "Gagal mengubah data barang.";
        }
    }
}

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenBarang.php">Manajemen Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-right: 1.5rem;">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Edit Barang</span>
                    </div>
                    <div class="card-body">
                        <form id="formEditBarang" method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="idBarang" class="form-label fw-semibold d-flex align-items-center">ID Barang</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idBarang" name="idBarang" value="<?= htmlspecialchars($data['idBarang']) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label fw-semibold d-flex align-items-center">
                                        Nama Barang
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="namaBarang" name="namaBarang" value="<?= isset($data['namaBarang']) ? htmlspecialchars($data['namaBarang']) : '' ?>" placeholder="Masukkan nama barang..">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="stokBarang" class="form-label fw-semibold d-flex align-items-center">
                                        Stok Barang
                                        <span id="stokError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <div class="input-group" style="max-width: 180px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input type="text" class="form-control text-center" id="stokBarang" name="stokBarang"
                                            min="0" style="max-width: 70px;"
                                            value="<?= isset($data['stokBarang']) ? htmlspecialchars($data['stokBarang']) : '0' ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="lokasiBarang" class="form-label fw-semibold d-flex align-items-center">
                                        Lokasi Barang
                                        <span id="lokasiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="lokasiBarang" name="lokasiBarang">
                                        <option value="" disabled <?= !isset($data['lokasiBarang']) || $data['lokasiBarang'] == '' ? 'selected' : '' ?>>Pilih Lokasi</option>
                                        <?php foreach ($lokasiList as $lokasi) : ?>
                                            <option value="<?= htmlspecialchars($lokasi) ?>"
                                                <?= (isset($data['lokasiBarang']) && $data['lokasiBarang'] == $lokasi) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($lokasi) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
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