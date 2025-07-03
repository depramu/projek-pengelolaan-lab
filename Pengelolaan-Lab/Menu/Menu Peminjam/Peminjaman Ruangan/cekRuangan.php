<?php
require_once __DIR__ . '/../../../function/init.php';

authorize_role(['Peminjam']);

if (isset($_POST['submit'])) {
    $_SESSION['tglPeminjamanRuangan'] = $_POST['tglPeminjamanRuangan'] ?? '';
    $_SESSION['waktuMulai'] = $_POST['jam_dari'] . ':' . $_POST['menit_dari'];
    $_SESSION['waktuSelesai'] = $_POST['jam_sampai'] . ':' . $_POST['menit_sampai'];
    header('Location: lihatRuangan.php');
    exit();
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Cek Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formCekKetersediaanRuangan">
                            <div class="mb-2">
                                <label class="form-label fw-semibold" for="tglHari">
                                    Pilih Tanggal Peminjaman
                                    <span id="error-message" style="color: red; display: none; margin-left: 10px;" class="fw-normal"></span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select id="tglHari" name="tglHari" class="form-select" style="width: 80px;"></select>
                                    <select id="tglBulan" name="tglBulan" class="form-select" style="width: 100px;"></select>
                                    <select id="tglTahun" name="tglTahun" class="form-select" style="width: 100px;"></select>
                                </div>
                                <input type="hidden" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="jam_dari">
                                        Waktu Mulai
                                        <span id="error-waktu-mulai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <select id="jam_dari" name="jam_dari" class="form-select" style="width: 100px;"></select>
                                        <select id="menit_dari" name="menit_dari" class="form-select" style="width: 100px;"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="jam_sampai">
                                        Waktu Selesai
                                        <span id="error-waktu-selesai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <select id="jam_sampai" name="jam_sampai" class="form-select" style="width: 100px;"></select>
                                        <select id="menit_sampai" name="menit_sampai" class="form-select" style="width: 100px;"></select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span id="error-waktu" style="color: red; display: none;" class="fw-normal"></span>
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
