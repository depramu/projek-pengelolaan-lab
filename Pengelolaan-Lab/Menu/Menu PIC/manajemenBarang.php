<?php
require_once __DIR__ . '/../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset'); 


// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Barang";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);
if ($result === false) {
    echo "Error executing query: <br>";
    die(print_r(sqlsrv_errors(), true));
}

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Barang</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-start mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Barang/tambahBarang.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="tambah" class="me-2">Tambah Barang</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok Barang</th>
                    <th>Lokasi Barang</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true;
                ?>
                    <tr class="text-center">
                        <td><?= htmlspecialchars($row['idBarang']) ?></td>
                        <td><?= htmlspecialchars($row['namaBarang']) ?></td>
                        <td><?= htmlspecialchars($row['stokBarang']) ?></td>
                        <td><?= htmlspecialchars($row['lokasiBarang']) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/CRUD/Barang/editBarang.php?id=<?= $row['idBarang'] ?>"><img src="<?= BASE_URL ?>/icon/edit.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['idBarang'] ?>"><img src="<?= BASE_URL ?>/icon/hapus.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>

                            <div class="modal fade" id="deleteModal<?= $row['idBarang'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['idBarang'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="<?= BASE_URL ?>/CRUD/Barang/hapusBarang.php" method="POST">
                                        <input type="hidden" name="idBarang" value="<?= $row['idBarang'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['idBarang'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus Barang "<strong><?= htmlspecialchars($row['namaBarang']) ?></strong>"?
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
include '../../templates/footer.php';
?>