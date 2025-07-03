<?php

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <h3 class="fw-semibold mb-3">Laporan</h3>
  <div class="mb-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
      </ol>
    </nav>
  </div>

  <!-- Filter Laporan Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Laporan</h5>
      <div class="row g-3 align-items-end" id="filterRow">
        <!-- Jenis Laporan -->
        <div class="col-md-4" id="colJenis">
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

        <!-- Bulan -->
        <div class="col-md-3" id="colBulan">
          <label for="bulanLaporan" class="form-label">Bulan</label>
          <select class="form-select" id="bulanLaporan">
            <option selected disabled value="">Pilih Bulan...</option>
            <?php
            $bulan = [
              '01' => 'Januari',
              '02' => 'Februari',
              '03' => 'Maret',
              '04' => 'April',
              '05' => 'Mei',
              '06' => 'Juni',
              '07' => 'Juli',
              '08' => 'Agustus',
              '09' => 'September',
              '10' => 'Oktober',
              '11' => 'November',
              '12' => 'Desember'
            ];
            foreach ($bulan as $num => $nama) {
              echo "<option value=\"{$num}\">{$nama}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Tahun -->
        <div class="col-md-3" id="colTahun">
          <label for="tahunLaporan" class="form-label">Tahun</label>
          <select class="form-select" id="tahunLaporan">
            <option selected disabled value="">Pilih Tahun...</option>
            <?php
            $currentYear = date('Y');
            for ($i = 0; $i < 5; $i++) {
              $y = $currentYear - $i;
              echo "<option value=\"{$y}\">{$y}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Tombol Tampilkan -->
        <div class="col-md-2 d-flex" id="colBtn">
          <button class="btn btn-primary w-100" id="tampilkanLaporanBtn">
            <i class="bi bi-search me-1"></i> Tampilkan
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Filter Card -->

  <!-- Area Konten Laporan -->
  <div id="areaKontenLaporan" style="display:none;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 id="judulKontenLaporan" class="mb-0"></h4>
      <button class="btn btn-success" id="exportExcelBtn">
        <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
      </button>
    </div>
    <div id="wadahLaporan" class="table-responsive"></div>
  </div>

  <!-- Paginasi -->
  <div id="paginationControlsContainer" class="mt-3" style="display:none;">
    <nav aria-label="Page navigation">
      <ul class="pagination" id="paginationUl"></ul>
    </nav>
  </div>
</main>

<!-- Modal Validasi -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="validationModalLabel">
          <img src="<?= BASE_URL ?>/icon/info.svg" alt="" style="width:25px;height:25px;margin-right:10px;"> PERINGATAN
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="validationMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>

<!-- SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const jenisLaporan = document.getElementById('jenisLaporan');
    const colBulan = document.getElementById('colBulan');
    const colTahun = document.getElementById('colTahun');
    const colJenis = document.getElementById('colJenis');
    const colBtn = document.getElementById('colBtn');

    // Penyesuaian tampilan filter
    function adjustFilters() {
      const val = jenisLaporan.value;
      if (val === 'dataBarang' || val === 'dataRuangan') {
        colBulan.style.display = 'none';
        colTahun.style.display = 'none';
        colJenis.className = 'col-md-10';
      } else {
        colBulan.style.display = '';
        colTahun.style.display = '';
        colJenis.className = 'col-md-4';
      }
    }
    jenisLaporan.addEventListener('change', adjustFilters);
    adjustFilters();

    // Inisialisasi state
    const tampilBtn = document.getElementById('tampilkanLaporanBtn');
    const areaKonten = document.getElementById('areaKontenLaporan');
    const wadahLaporan = document.getElementById('wadahLaporan');
    const judulKonten = document.getElementById('judulKontenLaporan');
    const exportBtn = document.getElementById('exportExcelBtn');
    const paginationContainer = document.getElementById('paginationControlsContainer');
    const paginationUl = document.getElementById('paginationUl');
    const validationModalEl = document.getElementById('validationModal');
    const validationModal = validationModalEl ? new bootstrap.Modal(validationModalEl) : null;
    const validationMsg = document.getElementById('validationMessage');

    let currentPage = 1;
    const rowsPerPage = 5;
    let fullData = [];
    let reportType = '';

    // Listen for modal shown event to focus the button accessibly
    if (validationModalEl) {
      validationModalEl.addEventListener('shown.bs.modal', () => {
        const btn = validationModalEl.querySelector('.modal-footer button');
        if (btn) btn.focus();
      });
      // Optional: blur button before modal hides to avoid focus issues
      validationModalEl.addEventListener('hide.bs.modal', () => {
        const btn = validationModalEl.querySelector('.modal-footer button');
        if (btn && document.activeElement === btn) btn.blur();
      });
    }

    tampilBtn.addEventListener('click', () => {
      const type = jenisLaporan.value;
      const bln = document.getElementById('bulanLaporan').value;
      const thn = document.getElementById('tahunLaporan').value;

      // Validasi: hanya jika jenis laporan belum dipilih
      if (!type) {
        validationMsg.textContent = 'Silakan pilih jenis laporan.';
        if (validationModal && !validationModalEl.classList.contains('show')) validationModal.show();
        return;
      }

      // PERBAIKAN UTAMA: Hanya validasi bulan+tahun untuk laporan yang membutuhkannya
      if (type !== 'dataBarang' && type !== 'dataRuangan') {
        if (!bln || !thn) {
          validationMsg.textContent = 'Silakan lengkapi semua filter.';
          if (validationModal && !validationModalEl.classList.contains('show')) validationModal.show();
          return;
        }
      }

      // Reset dan loading
      fullData = [];
      reportType = type;
      currentPage = 1;
      wadahLaporan.innerHTML = '<p class="text-center py-5"><span class="spinner-border spinner-border-sm"></span> Memuat data...</p>';
      areaKonten.style.display = 'none';
      paginationContainer.style.display = 'none';

      // Fetch
      let url = '../CRUD/Laporan/get_laporan_data.php?jenisLaporan=' + type;
      if (type !== 'dataBarang' && type !== 'dataRuangan') url += '&bulan=' + bln + '&tahun=' + thn;

      fetch(url)
        .then(res => {
          if (!res.ok) throw new Error(res.statusText);
          return res.json();
        })
        .then(res => {
          if (res.status === 'success') {
            fullData = res.data || [];
            if (fullData.length) {
              // Set judul
              const namaBulan = document.getElementById('bulanLaporan').selectedOptions[0]?.text;
              judulKonten.textContent = 'Laporan ' + jenisLaporan.selectedOptions[0].text +
                ((type !== 'dataBarang' && type !== 'dataRuangan') ? ' - ' + namaBulan + ' ' + thn : '');
              areaKonten.style.display = 'block';
              renderPage(1);
              setupPagination();
            } else {
              validationMsg.textContent = 'Tidak Ada Data Laporan untuk periode yang dipilih.';
              if (validationModal && !validationModalEl.classList.contains('show')) validationModal.show();
            }
          } else throw new Error(res.message);
        })
        .catch(err => {
          judulKonten.textContent = 'Kesalahan';
          wadahLaporan.innerHTML = '<p class="text-danger text-center">' + err.message + '</p>';
          areaKonten.style.display = 'block';
          paginationContainer.style.display = 'none';
        });
    });

    // Reset modal and page state after modal is closed
    if (validationModalEl) {
      validationModalEl.addEventListener('hidden.bs.modal', () => {
        // Remove any overlays or focus traps if present (Bootstrap should handle this, but for safety)
        document.body.classList.remove('modal-open');
        const modalBackdrops = document.querySelectorAll('.modal-backdrop');
        modalBackdrops.forEach(bd => bd.parentNode.removeChild(bd));
        // Optionally, restore focus to the filter button
        tampilBtn.focus();
      });
    }

    function renderPage(page) {
      currentPage = page;
      const start = (page - 1) * rowsPerPage;
      const slice = fullData.slice(start, start + rowsPerPage);
      if (!slice.length && page > 1) return renderPage(page - 1);

      // Build table
      const tbl = document.createElement('table');
      tbl.className = 'table table-striped table-bordered table-hover';
      let headers = [],
        keys = [];
      switch (reportType) {
        case 'dataBarang':
          headers = ['ID', 'Nama', 'Stok', 'Lokasi'];
          keys = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
          break;
        case 'dataRuangan':
          headers = ['ID', 'Nama', 'Kondisi', 'Ketersediaan'];
          keys = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
          break;
        case 'peminjamSeringMeminjam':
          headers = ['ID Peminjam', 'Nama', 'Jenis', 'Jumlah'];
          keys = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
          break;
        case 'barangSeringDipinjam':
          headers = ['ID Barang', 'Nama', 'Total Dipinjam'];
          keys = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
          break;
        case 'ruanganSeringDipinjam':
          headers = ['ID Ruangan', 'Nama', 'Jumlah Dipinjam'];
          keys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
          break;
      }
      const thead = tbl.createTHead().insertRow();
      headers.forEach(h => thead.insertCell().textContent = h);
      const tbody = tbl.createTBody();
      slice.forEach(item => {
        const r = tbody.insertRow();
        keys.forEach(k => r.insertCell().textContent = item[k] || '');
      });
      wadahLaporan.innerHTML = '';
      wadahLaporan.append(tbl);
    }

    function setupPagination() {
      paginationUl.innerHTML = '';
      const pages = Math.ceil(fullData.length / rowsPerPage);
      if (pages < 2) return paginationContainer.style.display = 'none';
      paginationContainer.style.display = 'block';

      const makeLi = (label, disabled, cb) => {
        const li = document.createElement('li');
        li.className = 'page-item' + (disabled ? ' disabled' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = label;
        a.addEventListener('click', e => {
          e.preventDefault();
          if (!disabled) cb();
        });
        li.append(a);
        paginationUl.append(li);
      };
      makeLi('«', currentPage === 1, () => renderPage(currentPage - 1));
      for (let i = 1; i <= pages; i++) {
        const active = i === currentPage;
        makeLi(i, false, () => renderPage(i));
        paginationUl.lastChild.classList.toggle('active', active);
      }
      makeLi('»', currentPage === pages, () => renderPage(currentPage + 1));
    }

    // Export Excel
    exportBtn.addEventListener('click', () => {
      if (!fullData.length) return alert('Tidak ada data untuk diexport.');
      const wb = XLSX.utils.book_new();
      let hdr = [],
        keys = [];
      switch (reportType) {
        case 'dataBarang':
          hdr = ['ID Barang', 'Nama Barang', 'Stok', 'Lokasi'];
          keys = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
          break;
        case 'dataRuangan':
          hdr = ['ID Ruangan', 'Nama Ruangan', 'Kondisi', 'Ketersediaan'];
          keys = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
          break;
        case 'peminjamSeringMeminjam':
          hdr = ['ID Peminjam', 'Nama', 'Jenis', 'Jumlah'];
          keys = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
          break;
        case 'barangSeringDipinjam':
          hdr = ['ID Barang', 'Nama', 'Total Dipinjam'];
          keys = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
          break;
        case 'ruanganSeringDipinjam':
          hdr = ['ID Ruangan', 'Nama', 'Jumlah Dipinjam'];
          keys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
          break;
      }
      const data = [hdr, ...fullData.map(r => keys.map(k => r[k] || ''))];
      const ws = XLSX.utils.aoa_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Laporan');
      XLSX.writeFile(wb, 'Laporan_' + reportType + '.xlsx');
    });
  });
</script>

<?php include '../templates/footer.php';
