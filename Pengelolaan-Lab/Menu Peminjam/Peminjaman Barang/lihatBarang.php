<?php
include '../../templates/header.php';

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data barang yang tersedia
$countQuery = "SELECT COUNT(*) AS total FROM Barang WHERE stokBarang > 0";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage);

// Ambil data barang sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang 
          FROM Barang 
          WHERE stokBarang > 0
          ORDER BY idBarang
          OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $perPage];
$result = sqlsrv_query($conn, $query, $params);

require_once '../../function/pagination.php';
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php">Cek Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lihat Barang</li>
            </ol>
        </nav>
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
                if ($result === false) {
                    echo '<tr><td colspan="5" class="text-center text-danger">Gagal mengambil data dari database</td></tr>';
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hasData = true;
                ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars(string: $row['idBarang'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['stokBarang'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['lokasiBarang'] ?? '') ?></td>
                            <td class="td-aksi text-center align-middle">
                                <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanBrg.php?idBarang=<?= urlencode($row['idBarang']) ?>" class="d-inline-block">
                                    <img src="<?= BASE_URL ?>/icon/tandaplus.svg" class="plus-tambah w-25" alt="Tambah Peminjaman Barang" style="display: inline-block; vertical-align: middle;">
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                    if (!$hasData) {
                        echo '<tr><td colspan="5" class="text-center">Tidak ada barang yang tersedia</td></tr>';
                    }
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