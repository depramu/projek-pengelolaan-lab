<?php
$role = $_SESSION['user_role'] ?? '';

$isPeminjam = false;
if ($role === 'Mahasiswa') {
    $isPeminjam = true;
} elseif ($role === 'Karyawan') {
    if (
        !isset($_SESSION['jenisRole']) ||
        $_SESSION['jenisRole'] === '' ||
        is_null($_SESSION['jenisRole'])
    ) {
        $isPeminjam = true;
    }
}

function renderSidebarMenu($role, $isPeminjam, $currentPage)
{
    $submenuFiles = [
        'aset' => ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php', 'hapusBarang.php', 'hapusRuangan.php'],
        'akun' => ['manajemenAkunMhs.php', 'manajemenAkunKry.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'tambahAkunKry.php', 'editAkunKry.php', 'hapusAkunMhs.php', 'hapusAkunKry.php'],
        'pinjam' => [
            'peminjamanBarang.php', 
            'peminjamanRuangan.php', 
            'detailPeminjamanBarang.php', 
            'detailPeminjamanRuangan.php',  
            'pengembalianBarang.php', 
            'pengembalianRuangan.php',
            'pengajuanBarang.php',
            'pengajuanRuangan.php',
            'penolakanBarang.php',
            'penolakanRuangan.php'
        ],
        'peminjaman' => ['cekBarang.php', 'cekRuangan.php', 'lihatBarang.php', 'lihatRuangan.php', 'tambahPeminjamanBrg.php', 'tambahPeminjamanRuangan.php'],
        'riwayat' => ['riwayatBarang.php', 'riwayatRuangan.php', 'formDetailRiwayatBrg.php', 'formDetailRiwayatRuangan.php']
    ];

    ob_start();
    if ($role === 'PIC Aset') :
        $isAsetActive = in_array($currentPage, $submenuFiles['aset']);
        $isAkunActive = in_array($currentPage, $submenuFiles['akun']);
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php" class="nav-link <?= ($currentPage == 'dashboardPIC.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAsetActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/layers0.png" class="sidebar-icon">Manajemen Aset</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="asetSubmenu">
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenBarang.php" class="nav-link <?= ($currentPage == 'manajemenBarang.php' || $currentPage == 'tambahBarang.php' || $currentPage == 'editBarang.php' || $currentPage == 'hapusBarang.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenRuangan.php" class="nav-link <?= ($currentPage == 'manajemenRuangan.php' || $currentPage == 'tambahRuangan.php' || $currentPage == 'editRuangan.php' || $currentPage == 'hapusRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAkunActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/iconamoon-profile-fill0.svg" class="sidebar-icon">Manajemen Akun</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="akunSubmenu">
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunMhs.php" class="nav-link <?= ($currentPage == 'manajemenAkunMhs.php' || $currentPage == 'tambahAkunMhs.php' || $currentPage == 'editAkunMhs.php' || $currentPage == 'hapusAkunMhs.php') ? 'active' : '' ?>">Mahasiswa</a>
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunKry.php" class="nav-link <?= ($currentPage == 'manajemenAkunKry.php' || $currentPage == 'tambahAkunKry.php' || $currentPage == 'editAkunKry.php' || $currentPage == 'hapusAkunKry.php') ? 'active' : '' ?>">Karyawan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPinjamActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/ic-twotone-sync-alt0.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
                <div class="collapse ps-4" id="pinjamSubmenu">
                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" 
                        class="nav-link <?= (
                            $currentPage == 'peminjamanBarang.php' || 
                            $currentPage == 'cekBarang.php' || 
                            $currentPage == 'lihatBarang.php' || 
                            $currentPage == 'tambahPeminjamanBrg.php' || 
                            $currentPage == 'pengembalianBarang.php' || 
                            $currentPage == 'detailPeminjamanBarang.php' || 
                            $currentPage == 'pengajuanBarang.php' || 
                            $currentPage == 'penolakanBarang.php'
                        ) ? 'active' : '' ?>">
                        Barang
                    </a>
                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" 
                        class="nav-link <?= (
                            $currentPage == 'peminjamanRuangan.php' || 
                            $currentPage == 'cekRuangan.php' || 
                            $currentPage == 'lihatRuangan.php' || 
                            $currentPage == 'tambahPeminjamanRuangan.php' || 
                            $currentPage == 'pengembalianRuangan.php' || 
                            $currentPage == 'detailPeminjamanRuangan.php' || 
                            $currentPage == 'pengajuanRuangan.php' || 
                            $currentPage == 'penolakanRuangan.php'
                        ) ? 'active' : '' ?>">
                        Ruangan
                    </a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu PIC/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>">
                <img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan
            </a>
        </li>
    <?php
    elseif ($role === 'KA UPT') :
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/dashboardKaUPT.php" class="nav-link <?= ($currentPage == 'dashboardKaUPT.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda</a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan</a>
        </li>

    <?php
    elseif ($isPeminjam) :
        $isPeminjamanActive = in_array($currentPage, $submenuFiles['peminjaman']);
        $isRiwayatActive = in_array($currentPage, $submenuFiles['riwayat']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php" class="nav-link <?= ($currentPage == 'dashboardPeminjam.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPeminjamanActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="peminjamanSubmenu">
                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link <?= (in_array($currentPage, ['cekBarang.php', 'lihatBarang.php', 'tambahPeminjamanBrg.php', 'pengembalianBarang.php', 'detailPeminjamanBarang.php'])) ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link <?= (in_array($currentPage, ['cekRuangan.php', 'lihatRuangan.php', 'tambahPeminjamanRuangan.php', 'pengembalianRuangan.php', 'detailPeminjamanRuangan.php'])) ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isRiwayatActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/riwayat.svg" class="sidebar-icon" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="riwayatSubmenu">
                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link <?= ($currentPage == 'riwayatBarang.php' || $currentPage == 'formDetailRiwayatBrg.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link <?= ($currentPage == 'riwayatRuangan.php' || $currentPage == 'formDetailRiwayatRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
    <?php
    endif;
    ?>
    <li class="nav-item mt-auto">
        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="<?= BASE_URL ?>/icon/exit.png" class="sidebar-icon">Log Out</a>
    </li>
<?php
    return ob_get_clean();
}
?>

<div class="row flex-grow-1 g-0">
    <nav class="col-auto sidebar d-none d-lg-flex flex-column p-2 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">
            <?= renderSidebarMenu($role, $isPeminjam, $currentPage); ?>
        </ul>
    </nav>

    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <h5 class="offcanvas-title mb-0 ms-2" id="offcanvasSidebarLabel">Sistem Pengelolaan Lab</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav nav-pills flex-column mb-auto">
                <?= renderSidebarMenu($role, $isPeminjam, $currentPage); ?>
            </ul>
        </div>
    </div>