<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

if (isset($_POST['submit'])) {
    $_SESSION['tglPeminjamanBrg'] = $_POST['tglPeminjamanBrg'] ?? '';
    header('Location: lihatBarang.php');
    exit();
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
$tglPeminjamanBrg = $_SESSION['tglPeminjamanBrg'] ?? '';
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang FROM Barang WHERE stokBarang > 0";
$stmt = sqlsrv_query($conn, $query);
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">    
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Cek Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formCekKetersediaanBarang" action="">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">
                                    Pilih Tanggal Peminjaman <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select id="tglHari" class="form-select" style="width: 80px;"></select>
                                    <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                                    <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                                </div>
                                <input type="hidden" id="tglPeminjamanBrg" name="tglPeminjamanBrg">
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary" name="submit">Cek</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php
include __DIR__ . '/../../../templates/footer.php';
?>