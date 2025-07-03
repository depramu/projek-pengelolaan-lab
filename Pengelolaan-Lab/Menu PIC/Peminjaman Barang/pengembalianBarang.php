<?php
require_once __DIR__ . '/../../function/auth.php';
authorize_role('PIC Aset');
require_once '../../koneksi.php';

// Inisialisasi variabel
$showModal = false;
$idPeminjamanBrg = $_GET['id'] ?? '';

if (empty($idPeminjamanBrg)) {
    die("Akses tidak valid. ID Peminjaman tidak ditemukan.");
}

// Ambil data peminjaman dan barang terkait
$data = null;
$jumlahBrg = 0;
$idBarang = null;
$sisaPinjaman = 0;
$namaBarang = '';

$query_get = "SELECT pb.jumlahBrg, pb.sisaPinjaman, pb.idBarang, b.namaBarang
              FROM Peminjaman_Barang pb
              JOIN Barang b ON pb.idBarang = b.idBarang
              WHERE pb.idPeminjamanBrg = ?";
$params_get = [$idPeminjamanBrg];
$stmt_get = sqlsrv_query($conn, $query_get, $params_get);

if ($stmt_get && ($data = sqlsrv_fetch_array($stmt_get, SQLSRV_FETCH_ASSOC))) {
    // Data ditemukan
} else {
    $data = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari $data jika ada
    $jumlahBrg = $data['jumlahBrg'] ?? 0;
    $idBarang = $data['idBarang'] ?? '';
    $sisaPinjaman = $data['sisaPinjaman'] ?? 0;
    $namaBarang = $data['namaBarang'] ?? '';

    $jumlahPengembalian = (int)($_POST['jumlahPengembalian'] ?? 0);
    $catatan = $_POST['catatanPengembalianBarang'] ?? '';
    $kondisiBrg = $_POST['kondisiBrg'] ?? '';

    // Validasi
    if ($jumlahPengembalian <= 0 || $jumlahPengembalian > $sisaPinjaman || empty($kondisiBrg) || $kondisiBrg == 'Pilih Kondisi Barang') {
        $error = "Data tidak valid. Pastikan jumlah pengembalian benar (tidak melebihi sisa pinjaman) dan kondisi barang telah dipilih.";
    } else {
        sqlsrv_begin_transaction($conn);

        // Insert ke pengembalian_barang
        $query_insert_pengembalian = "INSERT INTO pengembalian_barang 
            (idPeminjamanBrg, jumlahPengembalian, kondisiBrg, catatanPengembalianBarang) 
            VALUES (?, ?, ?, ?)";
        $params_insert_pengembalian = [$idPeminjamanBrg, $jumlahPengembalian, $kondisiBrg, $catatan];
        $stmt_insert_pengembalian = sqlsrv_query($conn, $query_insert_pengembalian, $params_insert_pengembalian);

        // Update sisaPinjaman dan statusPeminjaman
        $sisaBaru = $sisaPinjaman - $jumlahPengembalian;
        if ($sisaBaru < 0) $sisaBaru = 0;
        $statusPeminjaman = ($sisaBaru == 0) ? 'Telah Dikembalikan' : 'Sebagian Dikembalikan';

        $query_update_peminjaman = "UPDATE Peminjaman_Barang 
            SET sisaPinjaman = ?, statusPeminjaman = ?
            WHERE idPeminjamanBrg = ?";
        $params_update_peminjaman = [$sisaBaru, $statusPeminjaman, $idPeminjamanBrg];
        $stmt_update_peminjaman = sqlsrv_query($conn, $query_update_peminjaman, $params_update_peminjaman);

        // Update stok barang
        $query_update_stok = "UPDATE Barang SET stokBarang = stokBarang + ? WHERE idBarang = ?";
        $params_update_stok = [$jumlahPengembalian, $idBarang];
        $stmt_update_stok = sqlsrv_query($conn, $query_update_stok, $params_update_stok);

        // Commit/rollback
        if ($stmt_insert_pengembalian && $stmt_update_peminjaman && $stmt_update_stok) {
            sqlsrv_commit($conn);
            header("Location: pengembalianBarang.php?id=" . urlencode($idPeminjamanBrg) . "&success=1");
            exit;
        } else {
            sqlsrv_rollback($conn);
            $error = "Gagal memproses pengembalian barang. Silakan coba lagi.";
        }
    }
} else {
    // GET: set variabel dari $data jika ada
    $jumlahBrg = $data['jumlahBrg'] ?? 0;
    $idBarang = $data['idBarang'] ?? '';
    $sisaPinjaman = $data['sisaPinjaman'] ?? 0;
    $namaBarang = $data['namaBarang'] ?? '';
}

$showModal = isset($_GET['success']);

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengembalian Barang</li>
            </ol>
        </nav>
    </div>

    <!-- <div class="container mt-4 px-3 px-md-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?> -->

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Pengembalian Barang</span>
                    </div>

                    <div class="card-body">
                        <form id="formPengembalianBarang" method="POST">
                            <div class='mb-2 row'>
                                <div class="col-md-6">
                                    <label for="idPeminjamanBrg" class="form-label fw-semibold">ID Peminjaman</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-4">
                                    <label for="jumlahBrg" class="form-label fw-semibold">Jumlah Peminjaman</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="jumlahBrg" name="jumlahBrg" value="<?= $jumlahBrg ?>" readonly>
                                    <input type="hidden" id="sisaPinjaman" value="<?= $sisaPinjaman ?>">
                                    <?php if ($sisaPinjaman == 0): ?>
                                        <span class="text-success small">Semua barang sudah dikembalikan.</span>
                                    <?php else: ?>
                                        <span class="text-primary small">Sisa yang harus dikembalikan: <?= $sisaPinjaman ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlahPengembalian" class="form-label w-100 text-center fw-semibold">Jumlah Pengembalian
                                        <span id="jumlahError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                    </label>
                                    <div class="input-group mx-auto" style="max-width: 140px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input class="form-control text-center" id="jumlahPengembalian" name="jumlahPengembalian" value="0" min="0" max="<?= $sisaPinjaman ?>" style="max-width: 70px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="txtKondisi" class="form-label fw-semibold">Kondisi Barang
                                        <span id="kondisiError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                    </label>
                                    <select class="form-select" id="txtKondisi" name="kondisiBrg">
                                        <option selected>Pilih Kondisi Barang</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak">Rusak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="catatanPengembalianBarang" class="form-label fw-semibold">Catatan Pengembalian
                                    <span id="catatanError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                </label>
                                <textarea type="text" class="form-control" id="catatanPengembalianBarang" name="catatanPengembalianBarang" rows="3" style="resize: none;" placeholder="Masukkan catatan pengembalian.."></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../../templates/footer.php'; ?>