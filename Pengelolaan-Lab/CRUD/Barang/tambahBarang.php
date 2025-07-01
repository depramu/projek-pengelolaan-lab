<?php
include '../../templates/header.php';

$showModal = false;

// Generate ID Barang otomatis (BRG001, BRG002, dst)
$idBarang = 'BRG001';
$sqlId = "SELECT TOP 1 idBarang FROM Barang WHERE idBarang LIKE 'BRG%' ORDER BY idBarang DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idBarang']; // contoh: BRG012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idBarang = 'BRG' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'] ?? '';
    $stokBarang = $_POST['stokBarang'] ?? '';
    $lokasiBarang = $_POST['lokasiBarang'] ?? '';

    // Cek apakah nama barang sudah ada
    $cekNamaQuery = "SELECT COUNT(*) AS jumlah FROM Barang WHERE namaBarang = ?";
    $cekNamaParams = [$namaBarang];
    $cekNamaStmt = sqlsrv_query($conn, $cekNamaQuery, $cekNamaParams);
    $cekNamaRow = sqlsrv_fetch_array($cekNamaStmt, SQLSRV_FETCH_ASSOC);

    if ($cekNamaRow['jumlah'] > 0) {
        $namaError = "*Nama barang sudah terdaftar";
    } else {
        $query = "INSERT INTO Barang (idBarang, namaBarang, stokBarang, lokasiBarang) VALUES (?, ?, ?, ?)";
        $params = [$idBarang, $namaBarang, $stokBarang, $lokasiBarang];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan barang.";
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
                <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-right: 1.5rem;">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Tambah Barang</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahBarang" method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="idBarang" class="form-label fw-semibold d-flex align-items-center">ID Barang</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label fw-semibold d-flex align-items-center">
                                        Nama Barang
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($namaError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $namaError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" class="form-control" id="namaBarang" name="namaBarang" value="<?= isset($namaBarang) ? htmlspecialchars($namaBarang) : '' ?>" placeholder="Masukkan nama barang..">
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
                                            value="<?= isset($stokBarang) ? htmlspecialchars($stokBarang) : '0' ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="lokasiBarang" class="form-label fw-semibold d-flex align-items-center">
                                        Lokasi Barang
                                        <span id="lokasiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="lokasiBarang" name="lokasiBarang">
                                        <option value="" disabled <?= !isset($lokasiBarang) || $lokasiBarang == '' ? 'selected' : '' ?>>Pilih Lokasi</option>
                                        <?php foreach ($lokasiList as $lokasi) : ?>
                                            <option value="<?= htmlspecialchars($lokasi) ?>"
                                                <?= (isset($lokasiBarang) && $lokasiBarang == $lokasi) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($lokasi) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include '../../templates/footer.php'; ?>