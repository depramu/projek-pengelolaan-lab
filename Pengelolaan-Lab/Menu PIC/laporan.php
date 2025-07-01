<?php

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<!-- Konten Utama Halaman Laporan -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <h3 class="fw-semibold mb-3">Laporan</h3>
  <div class="mb-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <!-- Pastikan path BASE_URL benar jika Anda memanggilnya dari header.php -->
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
      </ol>
    </nav>
  </div>

  <!-- Filter Laporan Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Laporan</h5>
      <div class="row g-3 align-items-end">
        <!-- Dropdown Jenis Laporan -->
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
        <!-- Dropdown Bulan -->
        <div class="col-md-3">
          <label for="bulanLaporan" class="form-label">Bulan</label>
          <select class="form-select" id="bulanLaporan">
            <option selected disabled value="">Pilih Bulan...</option>
            <!-- Opsi bulan dari Januari hingga Desember -->
            <option value="01">Januari</option>
            <option value="02">Februari</option>
            <option value="03">Maret</option>
            <option value="04">April</option>
            <option value="05">Mei</option>
            <option value="06">Juni</option>
            <option value="07">Juli</option>
            <option value="08">Agustus</option>
            <option value="09">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>
        </div>
        <!-- Dropdown Tahun -->
        <div class="col-md-3">
          <label for="tahunLaporan" class="form-label">Tahun</label>
          <select class="form-select" id="tahunLaporan">
            <option selected disabled value="">Pilih Tahun...</option>
            <!-- Tahun akan diisi secara dinamis oleh JavaScript -->
          </select>
        </div>
        <!-- Tombol Tampilkan Laporan -->
        <div class="col-md-2 d-flex">
          <button class="btn btn-primary w-100" id="tampilkanLaporanBtn"><i class="bi bi-search me-1"></i> Tampilkan</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Filter Laporan Card -->

  <!-- Area untuk menampilkan konten laporan (judul, tombol export, tabel) -->
  <div id="areaKontenLaporan" style="display: none;"> <!-- Awalnya disembunyikan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 id="judulKontenLaporan" class="mb-0"></h4> <!-- Judul laporan dinamis -->
      <button class="btn btn-success" id="exportExcelBtn"> <!-- Tombol Export ke Excel -->
        <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
      </button>
    </div>
    <div id="wadahLaporan" class="table-responsive">
      <!-- Tabel data laporan akan dimuat di sini oleh JavaScript -->
    </div>
  </div>

  <!-- Kontrol Paginasi (navigasi halaman tabel) -->
  <div id="paginationControlsContainer" class="mt-3" style="display: none;"> <!-- Awalnya disembunyikan -->
    <nav aria-label="Page navigation">
      <ul class="pagination" id="paginationUl">
        <!-- Tombol paginasi (Previous, 1, 2, ..., Next) akan digenerate oleh JavaScript -->
      </ul>
    </nav>
  </div>
</main>
<!-- Akhir Konten Utama Halaman Laporan -->

<!-- Modal HTML untuk Validasi -->
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
        <!-- Pesan error validasi akan muncul di sini -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>


<?php
// Include file footer template (yang akan menutup tag body, html, dan memuat script JS global jika ada).
include '../templates/footer.php';
?>