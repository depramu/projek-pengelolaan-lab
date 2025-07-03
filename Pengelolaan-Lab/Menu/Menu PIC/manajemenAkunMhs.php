<?php
require_once __DIR__ . '/../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset'); 

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Mahasiswa";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT nim, nama, email, jenisRole FROM Mahasiswa ORDER BY nim OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Mahasiswa</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Akun Mahasiswa</li>
            </ol>
        </nav>
    </div>

    <!-- Table Manajemen Akun Mahasiswa -->
    <div class="d-flex justify-content-start mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Akun/tambahAkunMhs.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="Tambah Akun" class="me-2">
            Tambah Akun
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true;
                ?>
                    <tr>
                        <td class="text-center"><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['jenisRole']) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/CRUD/Akun/editAkunMhs.php?id=<?= $row['nim'] ?>"><img src="<?= BASE_URL ?>/icon/edit.svg" alt="editAkun" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['nim'] ?>"><img src="<?= BASE_URL ?>/icon/hapus.svg" alt="hapusAkun" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>

                            <!-- delete -->
                            <div class="modal fade" id="deleteModal<?= $row['nim'] ?>"
                                tabindex="-1" aria-labelledby="modalLabel<?= $row['nim'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="../CRUD/Akun/hapusAkunMhs.php" method="POST">
                                        <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['nim'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus akun <br>"<strong><?= htmlspecialchars($row['nama']) ?></strong>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Ya, hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                if (!$hasData) {
                    echo '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
                }
                ?>
            </tbody>
        </table>



    </div>
    <?php
    if ($totalPages > 1) {
        generatePagination($page, $totalPages);
    }
    ?>
</main>

<?php

include '../../templates/footer.php';
