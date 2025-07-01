<?php
require_once __DIR__ . '/../../auth.php';
authorize_role('PIC Aset');
include '../../templates/header.php';
include '../../koneksi.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = []; // Inisialisasi $data sebagai array kosong
$error = '';
$showModal = false;

if (!empty($idPeminjamanBrg)) {
    $query = "SELECT
                pb.*,
                b.namaBarang,
                COALESCE(m.nama, k.nama) AS namaPeminjam,
                pb.statusPeminjaman
            FROM
                Peminjaman_Barang pb
            JOIN
                Barang b ON pb.idBarang = b.idBarang
            LEFT JOIN
                Mahasiswa m ON pb.nim = m.nim
            LEFT JOIN
                Karyawan k ON pb.npk = k.npk
            WHERE
                pb.idPeminjamanBrg = ?";          
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        // Ini akan menangkap error dari query SELECT
        $error_details = sqlsrv_errors();
        $error_message = "Error saat mengambil data peminjaman. ";
        if ($error_details) {
            foreach ($error_details as $err) {
                $error_message .= $err['message'] . " ";
            }
        }
        die($error_message); // Hentikan eksekusi dan tampilkan error
    }

    if (sqlsrv_has_rows($stmt)) { // Cek apakah ada baris yang dikembalikan
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Data peminjaman tidak ditemukan untuk ID: " . htmlspecialchars($idPeminjamanBrg);
    }
} else {
    $error = "ID Peminjaman tidak valid.";
}

// Pastikan semua variabel diinisialisasi SETELAH mencoba mengambil data
// Gunakan operator null coalescing (??) untuk default nilai kosong jika $data tidak memiliki kunci
$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$npk = $data['npk'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$namaPeminjam = $data['namaPeminjam'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
$statusPeminjaman = $data['statusPeminjaman'] ?? '';

// Proses form untuk menyetujui peminjaman (tetap seperti sebelumnya, sudah benar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (!empty($idPeminjamanBrg)) {
        sqlsrv_begin_transaction($conn);

        $updateQuery = "UPDATE Peminjaman_Barang
                        SET statusPeminjaman = 'Sedang Dipinjam'
                        WHERE idPeminjamanBrg = ?";
        $updateParams = array($idPeminjamanBrg);
        $stmtUpdate = sqlsrv_query($conn, $updateQuery, $updateParams);

        if ($stmtUpdate) {
            sqlsrv_commit($conn);
            $showModal = true;
            // Penting: Setelah sukses, Anda mungkin perlu memuat ulang data atau mengarahkan pengguna
            // Tapi untuk modal di halaman yang sama, ini sudah cukup.
            // Data di form akan terlihat berubah setelah refresh atau redirect.
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal menyetujui peminjaman barang. Detail: ";
            if ($errors) {
                foreach ($errors as $err) {
                    $error .= $err['message'] . "; ";
                }
            } else {
                $error .= "Kesalahan tidak diketahui.";
            }
        }
    } else {
        $error = "ID Peminjaman tidak ditemukan untuk persetujuan.";
    }
}
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Pengajuan Peminjaman Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                            <div class="row">

                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-2">
                           <label for="idBarang" class="form-label fw-bold">ID Barang</label>
                                <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                        </div>
                        <div class="mb-2">
                            <label for="tglPeminjamanBrg" class="form-label fw-bold">Tanggal Peminjaman</label>
                                <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                        </div>                       
                        <div class="mb-2">
                            <label class="form-label fw-bold">NIM / NPK</label>
                                <div class="form-control-plaintext">
                                    <?php
                                        if (!empty($nim)) {
                                            echo htmlspecialchars($nim);
                                        } elseif (!empty($npk)) {
                                            echo htmlspecialchars($npk);
                                        } else {
                                             echo "-";
                                        }
                                    ?>
                                </div>
                            <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>">
                            <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>">
                        </div>
                        <div class="mb-2">
                            <label for="alasanPeminjamanBrg" class="form-label fw-bold">Alasan Peminjaman</label>
                                <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                        </div>                       
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-2">         
                          <label for="idPeminjamanBrg" class="form-label fw-bold">ID Peminjaman Barang</label>
                                <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                        </div>
                    <div class="mb-2">         
                          <label for="namaBarang" class="form-label fw-bold">Nama Barang</label>
                            <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                            <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                        </div>
                    <div class="mb-2">         
                          <label for="namaPeminjam" class="form-label fw-bold">Nama Peminjam</label>
                            <div class="form-control-plaintext"><?= htmlspecialchars($namaPeminjam) ?></div>
                            <input type="hidden" class="form-control" id="namaPeminjam" name="namaPeminjam" value="<?= htmlspecialchars($namaPeminjam) ?>">
                        </div>
                    <div class="mb-2">         
                          <label for="jumlahBrg" class="form-label fw-bold">Jumlah Barang</label>
                            <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                            <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                    </div>
                    </div>
                    </div>
                             <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between mt-4">
                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                    <div>
                        <a href="penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
                        <button type="submit" name="submit" class="btn btn-primary">Setuju</button>
                    </div>
                    </div>


 <?php if ($showModal): ?>
        <div class="modal fade" id="successModalPersetujuan" tabindex="-1" aria-labelledby="successModalPersetujuanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalPersetujuanLabel">Berhasil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        Peminjaman barang <strong><?= htmlspecialchars($idPeminjamanBrg) ?></strong> telah disetujui.
                    </div>
                    <div class="modal-footer">
                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('successModalPersetujuan'));
                modal.show();
                // Setelah modal ditampilkan, kita bisa menghapus ID dari URL agar tidak muncul lagi saat refresh
                // (Ini opsional, tapi bagus untuk UX)
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.pathname);
                }
            });
        </script>
    <?php endif; ?>

</main>
                  

<?php
include '../../templates/footer.php';
?>