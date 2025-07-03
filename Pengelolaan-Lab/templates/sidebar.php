<?php
// Ambil role dari session, sesuaikan jika struktur session berubah
$role = $_SESSION['user_role'] ?? '';
$jenisRole = $_SESSION['jenisRole'] ?? '';

// Penyesuaian logika untuk menentukan apakah user adalah peminjam
$isPeminjam = ($role === 'Peminjam');

// Fungsi untuk menampilkan menu sidebar sesuai role
function renderSidebarMenu($role, $isPeminjam, $currentPage)
{
    // Daftar file untuk submenu, sesuaikan jika ada penambahan/ubah file
    $submenuFiles = [
        'aset' => [
            'manajemenBarang.php', 'manajemenRuangan.php',
            'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php',
            'hapusBarang.php', 'hapusRuangan.php'
        ],
        'akun' => [
            'manajemenAkunMhs.php', 'manajemenAkunKry.php',
            'tambahAkunMhs.php', 'editAkunMhs.php', 'tambahAkunKry.php', 'editAkunKry.php',
            'hapusAkunMhs.php', 'hapusAkunKry.php'
        ],
        'pinjam' => [
            'peminjamanBarang.php', 'peminjamanRuangan.php',
            'detailPeminjamanBarang.php', 'detailPeminjamanRuangan.php',
            'pengembalianBarang.php', 'pengembalianRuangan.php',
            'pengajuanBarang.php', 'pengajuanRuangan.php',
            'penolakanBarang.php', 'penolakanRuangan.php'
        ],
        'peminjaman' => [
            'cekBarang.php', 'cekRuangan.php', 'lihatBarang.php', 'lihatRuangan.php',
            'tambahPeminjamanBrg.php', 'tambahPeminjamanRuangan.php'
        ],
        'riwayat' => [
            'riwayatBarang.php', 'riwayatRuangan.php',
            'formDetailRiwayatBrg.php', 'formDetailRiwayatRuangan.php'
        ]
    ];

    ob_start();
    // Menu untuk PIC Aset
    if ($role === 'PIC Aset') :
        $isAsetActive = in_array($currentPage, $submenuFiles['aset']);
        $isAkunActive = in_array($currentPage, $submenuFiles['akun']);
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php" class="nav-link <?= ($currentPage == 'dashboardPIC.php') ? 'active' : ''; ?>">
                <img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAsetActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="<?= $isAsetActive ? 'true' : 'false' ?>" aria-controls="asetSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/layers0.png" class="sidebar-icon">Manajemen Aset</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4<?= $isAsetActive ? ' show' : '' ?>" id="asetSubmenu">
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenBarang.php" class="nav-link <?= (in_array($currentPage, ['manajemenBarang.php', 'tambahBarang.php', 'editBarang.php', 'hapusBarang.php'])) ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenRuangan.php" class="nav-link <?= (in_array($currentPage, ['manajemenRuangan.php', 'tambahRuangan.php', 'editRuangan.php', 'hapusRuangan.php'])) ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAkunActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="<?= $isAkunActive ? 'true' : 'false' ?>" aria-controls="akunSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/iconamoon-profile-fill0.svg" class="sidebar-icon">Manajemen Akun</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4<?= $isAkunActive ? ' show' : '' ?>" id="akunSubmenu">
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunMhs.php" class="nav-link <?= (in_array($currentPage, ['manajemenAkunMhs.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'hapusAkunMhs.php'])) ? 'active' : '' ?>">Mahasiswa</a>
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php" class="nav-link <?= (in_array($currentPage, ['manajemenAkunKry.php', 'tambahAkunKry.php', 'editAkunKry.php', 'hapusAkunKry.php'])) ? 'active' : '' ?>">Karyawan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPinjamActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="<?= $isPinjamActive ? 'true' : 'false' ?>" aria-controls="pinjamSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/ic-twotone-sync-alt0.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4<?= $isPinjamActive ? ' show' : '' ?>" id="pinjamSubmenu">
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php" 
                    class="nav-link <?= (
                        in_array($currentPage, [
                            'peminjamanBarang.php', 'cekBarang.php', 'lihatBarang.php', 'tambahPeminjamanBrg.php',
                            'pengembalianBarang.php', 'detailPeminjamanBarang.php', 'pengajuanBarang.php', 'penolakanBarang.php'
                        ])
                    ) ? 'active' : '' ?>">
                    Barang
                </a>
                <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" 
                    class="nav-link <?= (
                        in_array($currentPage, [
                            'peminjamanRuangan.php', 'cekRuangan.php', 'lihatRuangan.php', 'tambahPeminjamanRuangan.php',
                            'pengembalianRuangan.php', 'detailPeminjamanRuangan.php', 'pengajuanRuangan.php', 'penolakanRuangan.php'
                        ])
                    ) ? 'active' : '' ?>">
                    Ruangan
                </a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu/Menu PIC/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>">
                <img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan
            </a>
        </li>
    <?php
    // Menu untuk KA UPT
    elseif ($role === 'KA UPT') :
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/dashboardKaUPT.php" class="nav-link <?= ($currentPage == 'dashboardKaUPT.php') ? 'active' : ''; ?>">
                <img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>">
                <img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan
            </a>
        </li>
    <?php
    // Menu untuk Peminjam (Mahasiswa/Karyawan tanpa jenisRole)
    elseif ($role === 'Peminjam') :
        $isPeminjamanActive = in_array($currentPage, $submenuFiles['peminjaman']);
        $isRiwayatActive = in_array($currentPage, $submenuFiles['riwayat']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php" class="nav-link <?= ($currentPage == 'dashboardPeminjam.php') ? 'active' : ''; ?>">
                <img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Beranda
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPeminjamanActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="<?= $isPeminjamanActive ? 'true' : 'false' ?>" aria-controls="peminjamanSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4<?= $isPeminjamanActive ? ' show' : '' ?>" id="peminjamanSubmenu">
                <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link <?= (in_array($currentPage, ['cekBarang.php', 'lihatBarang.php', 'tambahPeminjamanBrg.php', 'pengembalianBarang.php', 'detailPeminjamanBarang.php'])) ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link <?= (in_array($currentPage, ['cekRuangan.php', 'lihatRuangan.php', 'tambahPeminjamanRuangan.php', 'pengembalianRuangan.php', 'detailPeminjamanRuangan.php'])) ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isRiwayatActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="<?= $isRiwayatActive ? 'true' : 'false' ?>" aria-controls="riwayatSubmenu">
                <span>
                    <img src="<?= BASE_URL ?>/icon/riwayat.svg" class="sidebar-icon" style="width: 28px; height: 28px; object-fit: contain;">
                    Riwayat
                </span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4<?= $isRiwayatActive ? ' show' : '' ?>" id="riwayatSubmenu">
                <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link <?= (in_array($currentPage, ['riwayatBarang.php', 'formDetailRiwayatBrg.php'])) ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link <?= (in_array($currentPage, ['riwayatRuangan.php', 'formDetailRiwayatRuangan.php'])) ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
    <?php
    endif;
    ?>
    <li class="nav-item mt-auto">
        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <img src="<?= BASE_URL ?>/icon/exit.png" class="sidebar-icon">Log Out
        </a>
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