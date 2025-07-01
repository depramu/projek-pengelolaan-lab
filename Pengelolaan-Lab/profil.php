<?php
session_start();
include 'templates/header.php';
include 'templates/sidebar.php';
include 'koneksi.php'; // Pastikan koneksi database di-include

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;
$user_nama = $_SESSION['user_nama'] ?? null;

// Siapkan variabel untuk data profil
$profil = [];
$error_message = '';

if ($user_id && $user_role) {
  if ($user_role === 'Mahasiswa') {
    // Ambil data Mahasiswa
    $query = "SELECT nim, nama, email FROM Mahasiswa WHERE nim = ?";
    $stmt = sqlsrv_query($conn, $query, array($user_id));
    if ($stmt === false) {
      $error_message = "Gagal mengambil data Mahasiswa.";
    } else {
      $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
      if ($profil) {
        $profil['role'] = 'Mahasiswa';
      }
    }
  } elseif ($user_role === 'Karyawan' || $user_role === 'PIC Aset' || $user_role === 'KA UPT') {
    // Ambil data Karyawan
    $query = "SELECT npk, nama, email, jenisRole FROM Karyawan WHERE npk = ?";
    $stmt = sqlsrv_query($conn, $query, array($user_id));
    if ($stmt === false) {
      $error_message = "Gagal mengambil data Karyawan.";
    } else {
      $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
      if ($profil) {
        $profil['role'] = $profil['jenisRole'] ?? 'Karyawan';
      }
    }
  } else {
    $error_message = "Role tidak dikenali.";
  }
} else {
  $error_message = "Anda belum login.";
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
<h3 class="fw-semibold mb-3">Profil Akun</h3>
  <div class="mb-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <?php
          $base_url = 'Menu Peminjam/';
          $dashboard_link = $base_url . 'dashboardPeminjam.php';
          if ($user_role === 'PIC Aset') {
            $base_url = 'Menu PIC/';
            $dashboard_link = $base_url . 'dashboardPIC.php';
          } elseif ($user_role === 'KA UPT') {
            $base_url = 'Menu KA UPT/';
            $dashboard_link = $base_url . 'dashboardKAUPT.php';
          }
          
        ?>
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars($dashboard_link) ?>">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profil Akun</li>
      </ol>
    </nav>
  </div>
  <div class="col-lg-7 col-md-9">
    <h2 class="fw-bold display-5" style=" margin-left: 50px; margin-bottom: -30px;">Data Akun</h2>
    <div class="card-body p-4 p-md-5">
      <div class="d-flex align-items-center mb-3 pb-1">
        <div class="me-4">
          <i class="bi bi-person-circle" style="font-size: 8rem; color: #343a40;"></i>
        </div>
        <h3 class="fw-bold mb-0" style="font-size: 1.75rem; margin-left: 20px;">
          <?= htmlspecialchars($user_nama ?? ($profil['nama'] ?? $profil['namaKry'] ?? '')) ?>
        </h3>
      </div>
      <div class="bg-primary text-white p-3 rounded-3">
        <div class="row gy-2">
          <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
          <?php elseif ($profil): ?>
            <div class="col-12 mb-2">
              <strong>Role:</strong> <?= htmlspecialchars($profil['role']) ?>
            </div>
            <div class="col-12 mb-2">
              <strong><?= isset($profil['nim']) ? 'NIM' : 'NPK' ?>:</strong> <?= htmlspecialchars($profil['nim'] ?? $profil['npk'] ?? '') ?>
            </div>
            <div class="col-12 mb-2">
              <strong>Email:</strong> <?= htmlspecialchars($profil['email'] ?? '-') ?>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">Data profil tidak ditemukan.</div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</main>

<?php 

include 'templates/footer.php';

?>  