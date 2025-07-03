<?php
require_once __DIR__ . '/../../function/auth.php';
authorize_role('PIC Aset');
include '../../templates/header.php';

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT pr.*, b.namaBarang 
          FROM Peminjaman_Barang pr 
          JOIN Barang b ON pr.idBarang = b.idBarang 
          ORDER BY pr.idPeminjamanBrg 
          OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);

require_once '../../function/pagination.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
            </ol>
        </nav>
    </div>


    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>ID Peminjaman</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah Peminjaman</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true;
                    $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                    $idPeminjaman = htmlspecialchars($row['idPeminjamanBrg'] ?? '');

                    if ($statusPeminjaman == 'Menunggu Persetujuan') {
                        $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                        $altText = 'Menunggu Persetujuan oleh PIC';
                        $linkDetail = BASE_URL . '/Menu PIC/Peminjaman Barang/pengajuanBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Sedang Dipinjam') {
                        $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                        $altText = 'Sedang Dipinjam';
                        $linkDetail = BASE_URL . '/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Sebagian Dikembalikan') {
                        $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                        $altText = 'Sebagian Dikembalikan';
                        $linkDetail = BASE_URL . '/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Ditolak') {
                        $iconSrc = BASE_URL . '/icon/silang.svg';
                        $altText = 'Ditolak';
                        $linkDetail = BASE_URL . '/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Telah Dikembalikan') {
                        $iconSrc = BASE_URL . '/icon/centang.svg';
                        $altText = 'Peminjaman Selesai';
                        $linkDetail = BASE_URL . '/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                    } else {
                        $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                        $altText = 'Status Tidak Diketahui';
                        $linkDetail = '#';
                    }
                ?>
                    <tr class="text-center">
                        <td><?= htmlspecialchars($row['idPeminjamanBrg'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['idBarang'] ?? '') ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                        <td><?= ($row['tglPeminjamanBrg'] instanceof DateTime ? $row['tglPeminjamanBrg']->format('d-m-Y') : htmlspecialchars($row['tglPeminjamanBrg'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($row['jumlahBrg'] ?? '') ?></td>
                        <td class="td-aksi">
                            <a href="<?= $linkDetail ?>">
                                <img src="<?= $iconSrc ?>" alt="<?= $altText ?>" class="aksi-icon" title="<?= $altText ?>">
                            </a>
                            <a href="<?= $linkDetail ?>">
                                <img src="<?= BASE_URL ?>/icon/detail.svg" alt="Lihat Detail" class="aksi-icon">
                            </a>
                        </td>
                    </tr>
                <?php
                }

                if (!$hasData) {
                    echo '<tr><td colspan="5" class="text-center">Tidak ada data peminjaman</td></tr>';
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

<?php include '../../templates/footer.php'; ?>