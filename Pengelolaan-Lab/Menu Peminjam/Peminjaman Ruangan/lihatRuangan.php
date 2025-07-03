<?php
require_once __DIR__ . '/../../function/auth.php';
authorize_role(['Peminjam']);
include '../../templates/header.php';

$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data ruangan yang tersedia
$countQuery = "SELECT COUNT(*) AS total FROM Ruangan WHERE ketersediaan = 'Tersedia'";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage);

// Ambil data ruangan sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan 
          FROM Ruangan 
          WHERE ketersediaan = 'Tersedia'
          ORDER BY idRuangan
          OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $perPage];
$result = sqlsrv_query($conn, $query, $params);

require_once '../../function/pagination.php';
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lihat Ruangan</li>
            </ol>
        </nav>
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
                if ($result === false) {
                    echo '<tr><td colspan="5" class="text-center text-danger">Gagal mengambil data dari database</td></tr>';
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hasData = true;
                ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($row['idRuangan'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['namaRuangan'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kondisiRuangan'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['ketersediaan'] ?? '') ?></td>
                            <td class="td-aksi text-center align-middle">
                                <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanRuangan.php?idRuangan=<?= urlencode($row['idRuangan']) ?>" class="d-inline-block">
                                    <img src="<?= BASE_URL ?>/icon/tandaplus.svg" class="plus-tambah w-25" alt="Tambah Peminjaman Ruangan" style="display: inline-block; vertical-align: middle;">
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                    if (!$hasData) {
                        echo '<tr><td colspan="5" class="text-center">Tidak ada ruangan yang tersedia</td></tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    // Tampilkan pagination jika lebih dari 1 halaman
    if ($totalPages > 1) {
        generatePagination($page, $totalPages);
    }
    ?>
</main>

<?php
include '../../templates/footer.php';
?>