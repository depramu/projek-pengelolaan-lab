<?php

/**
 * Membuat dan menampilkan navigasi pagination.
 *
 * @param int $currentPage Halaman yang sedang aktif.
 * @param int $totalPages Jumlah total semua halaman.
 * @param string $baseUrl URL dasar untuk link pagination, jika diperlukan untuk query string yang kompleks. Defaultnya adalah '?'.
 * @return void Fungsi ini langsung mencetak (echo) output HTML.
 */
function generatePagination($currentPage, $totalPages, $baseUrl = '?')
{
    // Jangan tampilkan pagination jika hanya ada satu halaman
    if ($totalPages <= 1) {
        return;
    }
?>
    <nav aria-label="Page navigation" class="fixed-pagination">
        <ul class="pagination justify-content-end">
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $baseUrl ?>page=<?= $currentPage - 1 ?>" tabindex="-1">&lt;</a>
            </li>

            <?php
            $showPages = 3; // Jumlah halaman yang selalu tampil di awal dan akhir
            $ellipsisShown = false;
            for ($i = 1; $i <= $totalPages; $i++) {
                if (
                    $i <= $showPages || // Selalu tampilkan 3 halaman pertama
                    $i > $totalPages - $showPages || // Selalu tampilkan 3 halaman terakhir
                    abs($i - $currentPage) <= 1 // Tampilkan halaman saat ini, satu sebelum, dan satu sesudah
                ) {
                    $ellipsisShown = false;
            ?>
                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                    </li>
            <?php
                } elseif (!$ellipsisShown) {
                    // Tampilkan elipsis (...) hanya sekali
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    $ellipsisShown = true;
                }
            }
            ?>

            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $baseUrl ?>page=<?= $currentPage + 1 ?>">&gt;</a>
            </li>
        </ul>
    </nav>
<?php
}
?>