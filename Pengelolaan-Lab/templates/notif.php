<?php
require_once __DIR__ . '/../function/init.php';

include __DIR__ . '/header.php';
include __DIR__ . '/sidebar.php';


$notifikasi = isset($_SESSION['notifikasi']) ? $_SESSION['notifikasi'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['baca'])) {
    $notifId = $_POST['notif_id'];
    foreach ($_SESSION['notifikasi'] as &$notif) {
        if ($notif['id'] == $notifId) {
            $notif['status'] = 'Sudah Dibaca';
            break;
        }
    }
    unset($notif);
}

$notifikasi = array_filter(
    isset($_SESSION['notifikasi']) ? $_SESSION['notifikasi'] : [],
    fn($n) => $n['status'] === 'Belum Dibaca'
);

?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Notifikasi</h3>
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>
    </div>
    <div class="container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifikasi as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['waktu']) ?></td>
                            <td><?= htmlspecialchars($row['pesan']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'Belum Dibaca'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="notif_id" value="<?= $row['id']; ?>">
                                        <button type="submit" name="baca" style="background:none; border:none; cursor:pointer;">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

</main>

<?php
include 'footer.php';
?>