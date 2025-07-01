<?php
include '../templates/header.php';

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Ruangan";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);
if ($result === false) {
    echo "Error executing query: <br>";
    die(print_r(sqlsrv_errors(), true));
}
require_once '../function/pagination.php';

include '../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Ruangan</li>
            </ol>
        </nav>
    </div>

    <!-- Table Manajemen Ruangan -->
    <div class="d-flex justify-content-start mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Ruangan/tambahRuangan.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="tambah" class="me-2">Tambah Ruangan</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>ID Ruangan</th>
                    <th>Nama Ruangan</th>
                    <th>Kondisi</th>
                    <th>Ketersediaan</th>
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
                        <td class="text-center"><?= $row['idRuangan'] ?></td>
                        <td><?= $row['namaRuangan'] ?></td>
                        <td><?= $row['kondisiRuangan'] ?></td>
                        <td><?= $row['ketersediaan'] ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/CRUD/Ruangan/editRuangan.php?id=<?= $row['idRuangan'] ?>"><img src="../icon/edit.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>
                            <a href="<?= BASE_URL ?>CRUD/Ruangan/hapusRuangan.php?id=<?= $row['idRuangan'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['idRuangan'] ?>"><img src="../icon/hapus.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>
                            <!-- delete -->
                            <div class="modal fade" id="deleteModal<?= $row['idRuangan'] ?>"
                                tabindex="-1" aria-labelledby="modalLabel<?= $row['idRuangan'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="../CRUD/Ruangan/hapusRuangan.php" method="POST">
                                        <input type="hidden" name="idRuangan" value="<?= $row['idRuangan'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['idRuangan'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus Ruangan "<strong><?= htmlspecialchars($row['namaRuangan']) ?></strong>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
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
include '../templates/footer.php';
?>