<?php
require_once __DIR__ . '/../../function/auth.php'; // Muat fungsi otorisasi
authorize_role('PIC Aset'); // Lindungi halaman ini untuk role 'Peminjam'

include '../../templates/header.php';

$showModal = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $cekNpk = sqlsrv_query($conn, "SELECT npk FROM Karyawan WHERE npk = ?", [$npk]);
    if ($cekNpk && sqlsrv_has_rows($cekNpk)) {
        $npkError = "*NPK sudah terdaftar";
    } else {
        $query = "INSERT INTO Karyawan (npk, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
        $params = [$npk, $nama, $email, $jenisRole, $kataSandi];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan akun.";
        }
    }
}

include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Karyawan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun Karyawan</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Tambah Akun Karyawan</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahAkunKry" method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="npk" class="form-label fw-semibold d-flex align-items-center">NPK
                                        <span id="npkError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($npkError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $npkError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" class="form-control" id="npk" name="npk" placeholder="Masukkan NPK.." value="<?= isset($npk) ? htmlspecialchars($npk) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="nama" class="form-label fw-semibold d-flex align-items-center">Nama Lengkap
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap.." value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold d-flex align-items-center">Email
                                        <span id="emailError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email.." value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label fw-semibold d-flex align-items-center">Role
                                        <span id="roleError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="jenisRole" name="jenisRole">
                                        <option value="" disabled selected>Pilih Role</option>
                                        <option value="KA UPT" <?= (isset($jenisRole) && $jenisRole == "KA UPT") ? "selected" : "" ?>>KA UPT</option>
                                        <option value="PIC Aset" <?= (isset($jenisRole) && $jenisRole == "PIC Aset") ? "selected" : "" ?>>PIC Aset</option>
                                        <option value="Peminjam" <?= (isset($jenisRole) && $jenisRole == "Peminjam") ? "selected" : "" ?>>Peminjam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="kataSandi" class="form-label fw-semibold d-flex align-items-center">Kata Sandi
                                    <span id="passError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                </label>
                                <input type="password" class="form-control" id="kataSandi" name="kataSandi" placeholder="Masukkan kata sandi.." value="<?= isset($kataSandi) ? htmlspecialchars($kataSandi) : '' ?>">
                            </div>
                            <div class="mb-2">
                                <label for="konfirmasiSandi" class="form-label fw-semibold d-flex align-items-center">Konfirmasi Kata Sandi
                                    <span id="confPassError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                </label>
                                <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi" placeholder="Masukkan konfirmasi kata sandi.." value="<?= isset($konfirmasiSandi) ? htmlspecialchars($konfirmasiSandi) : '' ?>">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
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