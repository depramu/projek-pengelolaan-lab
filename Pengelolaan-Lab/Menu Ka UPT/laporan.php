<?php

require_once __DIR__ . '/../function/auth.php';
authorize_role('KA UPT');

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<!-- Area Konten Utama Halaman Laporan -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <div class="mb-3"> <!-- Container untuk breadcrumb, dengan margin bawah -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <!-- Breadcrumb navigasi, link ke dashboard KaUPT dan halaman saat ini -->
        <li class="breadcrumb-item"><a href="dashboardKAUPT.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
      </ol>
    </nav>
  </div>

  <!-- Card untuk Filter Laporan -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Laporan</h5>
      <!-- Baris yang berisi semua elemen filter -->
      <div class="row g-3 align-items-end">
        <!-- Dropdown untuk memilih Jenis Laporan -->
        <div class="col-md-4">
          <label for="jenisLaporan" class="form-label">Jenis Laporan</label>
          <select class="form-select" id="jenisLaporan">
            <option selected disabled value="">Pilih Jenis Laporan...</option>
            <option value="dataBarang">Data Barang</option>
            <option value="dataRuangan">Data Ruangan</option>
            <option value="peminjamSeringMeminjam">Peminjam yang Sering Meminjam</option>
            <option value="barangSeringDipinjam">Barang yang Sering Dipinjam</option>
            <option value="ruanganSeringDipinjam">Ruangan yang Sering Dipinjam</option>
          </select>
        </div>
        <!-- Dropdown untuk memilih Bulan -->
        <div class="col-md-3">
          <label for="bulanLaporan" class="form-label">Bulan</label>
          <select class="form-select" id="bulanLaporan">
            <option selected disabled value="">Pilih Bulan...</option>
            <option value="01">Januari</option> <option value="02">Februari</option> <option value="03">Maret</option>
            <option value="04">April</option> <option value="05">Mei</option> <option value="06">Juni</option>
            <option value="07">Juli</option> <option value="08">Agustus</option> <option value="09">September</option>
            <option value="10">Oktober</option> <option value="11">November</option> <option value="12">Desember</option>
          </select>
        </div>
        <!-- Dropdown untuk memilih Tahun -->
        <div class="col-md-3">
          <label for="tahunLaporan" class="form-label">Tahun</label>
          <select class="form-select" id="tahunLaporan">
            <option selected disabled value="">Pilih Tahun...</option>
            <!-- Opsi tahun akan diisi secara dinamis oleh JavaScript -->
          </select>
        </div>
        <!-- Tombol untuk memicu pengambilan dan penampilan laporan -->
        <div class="col-md-2 d-flex">
          <button class="btn btn-primary w-100" id="tampilkanLaporanBtn"><i class="bi bi-search me-1"></i> Tampilkan</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Akhir Card Filter Laporan -->

  <!-- Area untuk menampilkan konten laporan (judul, tombol export, dan tabel data) -->
  <!-- Awalnya disembunyikan (display: none;) dan akan ditampilkan oleh JavaScript jika ada data -->
  <div id="areaKontenLaporan" style="display: none;"> 
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 id="judulKontenLaporan" class="mb-0"></h4> <!-- Judul laporan dinamis -->
      <button class="btn btn-success" id="exportExcelBtn"> <!-- Tombol untuk export data ke Excel -->
        <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
      </button>
    </div>
    <div id="wadahLaporan" class="table-responsive">
      <!-- Tabel data laporan akan dirender di sini oleh JavaScript -->
    </div>
  </div>
  <!-- Elemen duplikat ini sepertinya tidak perlu dan bisa dihapus jika sudah ada di atas -->

  <!-- Kontrol Paginasi untuk tabel laporan -->
  <!-- Awalnya disembunyikan dan akan ditampilkan oleh JavaScript jika data melebihi satu halaman -->
  <div id="paginationControlsContainer" class="mt-3" style="display: none;"> 
    <nav aria-label="Page navigation">
      <ul class="pagination" id="paginationUl">
        <!-- Tombol-tombol paginasi (Previous, 1, 2, ..., Next) akan digenerate oleh JavaScript -->
      </ul>
    </nav>
  </div>
  <!-- Akhir Kontrol Paginasi -->

</main>
<!-- Akhir Area Konten Utama -->

<!-- Modal HTML untuk Validasi Input Pengguna -->
<!-- Digunakan untuk menampilkan pesan error jika filter belum lengkap atau jika tidak ada data laporan -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="validationModalLabel">
          <i><img src="<?= BASE_URL ?>/icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>
          PERINGATAN
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="validationMessage">
        <!-- Pesan validasi dinamis akan ditampilkan di sini oleh JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>


<?php
include '../templates/footer.php';
?>