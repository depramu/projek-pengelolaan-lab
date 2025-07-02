/**
 * =================================================================
 * PENGELOLAAN LAB - SCRIPT UTAMA (Versi Final Gabungan)
 * Dibuat oleh: Partner Koding
 * Deskripsi: Menggabungkan semua fungsionalitas JavaScript untuk
 * halaman login, laporan, peminjaman, CRUD, dan lainnya
 * ke dalam satu file yang terstruktur.
 * =================================================================
 */

// =================================================================
// #0: HELPER & FUNGSI UMUM
// =================================================================

/**
 * @object dateTimeHelpers
 * @description Kumpulan fungsi untuk mengelola input tanggal dan waktu.
 */
const dateTimeHelpers = {
  isLeapYear: function (year) {
    return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
  },

  updateDays: function (dayId, monthId, yearId) {
    const bulan = parseInt(document.getElementById(monthId).value);
    const tahun = parseInt(document.getElementById(yearId).value);
    const hariSelect = document.getElementById(dayId);
    if (!hariSelect || isNaN(bulan) || isNaN(tahun)) return;

    const prevHari = hariSelect.value;
    let days = 31;
    if ([4, 6, 9, 11].includes(bulan)) days = 30;
    else if (bulan === 2) days = this.isLeapYear(tahun) ? 29 : 28;

    hariSelect.innerHTML = "";
    for (let i = 1; i <= days; i++) {
      hariSelect.innerHTML += `<option value="${String(i).padStart(
        2,
        "0"
      )}">${i}</option>`;
    }

    if (prevHari && parseInt(prevHari) <= days) {
      hariSelect.value = String(prevHari).padStart(2, "0");
    }
  },

  fillSelects: function (dayId, monthId, yearId) {
    const tahunSelect = document.getElementById(yearId);
    const bulanSelect = document.getElementById(monthId);
    const hariSelect = document.getElementById(dayId);
    if (!tahunSelect || !bulanSelect || !hariSelect) return;

    const now = new Date();
    tahunSelect.innerHTML = "";
    for (let y = now.getFullYear(); y <= now.getFullYear() + 1; y++) {
      tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }

    bulanSelect.innerHTML = "";
    for (let m = 1; m <= 12; m++) {
      bulanSelect.innerHTML += `<option value="${m}">${String(m).padStart(
        2,
        "0"
      )}</option>`;
    }

    // Set default value
    tahunSelect.value = now.getFullYear();
    bulanSelect.value = now.getMonth() + 1;
    this.updateDays(dayId, monthId, yearId);
    hariSelect.value = String(now.getDate()).padStart(2, "0");

    // Tambahkan event listener
    bulanSelect.addEventListener("change", () =>
      this.updateDays(dayId, monthId, yearId)
    );
    tahunSelect.addEventListener("change", () =>
      this.updateDays(dayId, monthId, yearId)
    );
  },

  fillTimeSelects: function (hourId, minuteId) {
    const fill = (elId, max) => {
      const el = document.getElementById(elId);
      if (!el) return;
      el.innerHTML = '<option value="">--</option>';
      for (let i = 0; i < max; i++) {
        const val = String(i).padStart(2, "0");
        el.innerHTML += `<option value="${val}">${val}</option>`;
      }
    };
    fill(hourId, 24);
    fill(minuteId, 60);
  },
};

/**
 * @function setupStockStepper
 * @description Menangani tombol +/- untuk input numerik.
 * @param {string} containerId - ID dari container yang membungkus input dan tombol.
 * @param {string} inputId - ID dari input field.
 * @param {string} maxLimitId - (Opsional) ID dari elemen yang menyimpan nilai batas maksimal.
 */
function setupStockStepper(containerId, inputId, maxLimitId = null) {
  // Versi sederhana: gunakan fungsi global changeStok(val) saja
  // Tidak perlu event delegation, cukup panggil dari onclick di HTML
  // Fungsi ini tetap ada agar tidak error jika dipanggil dari form lain
  window.changeStok = function (val) {
    const stokInput = document.getElementById(inputId);
    if (!stokInput) return;
    let current = parseInt(stokInput.value) || 0;
    let next = current + val;
    if (next < 0) next = 0;
    stokInput.value = next;
  };
}

/**
 * @function setupKondisiRuanganLogic
 * @description Jika kondisi ruangan 'Rusak', ketersediaan otomatis menjadi 'Tidak Tersedia'.
 */
function setupKondisiRuanganLogic() {
  const kondisiSelect = document.getElementById("kondisiRuangan");
  const ketersediaanSelect = document.getElementById("ketersediaan");
  if (!kondisiSelect || !ketersediaanSelect) return;

  const updateKetersediaan = () => {
    if (kondisiSelect.value === "Rusak") {
      ketersediaanSelect.value = "Tidak Tersedia";
      ketersediaanSelect.disabled = true;
    } else {
      ketersediaanSelect.disabled = false;
    }
  };

  kondisiSelect.addEventListener("change", updateKetersediaan);
  updateKetersediaan(); // Panggil saat init
}

// =================================================================
// #1: INISIALISASI UTAMA SAAT HALAMAN DIMUAT
// =================================================================

document.addEventListener("DOMContentLoaded", function () {
  /**
   * Panggil semua fungsi setup.
   * Setiap fungsi akan memeriksa keberadaan elemennya sendiri sebelum berjalan.
   */

  // Halaman Otentikasi
  setupLoginForm();
  setupLupaSandiForm();

  // Halaman Admin & Operator
  setupLaporanPage();
  setupPenolakanBarang();
  setupPengembalianBarangPage();
  setupPengembalianRuanganPage();
  setupDetailRiwayatForm();

  // Form CRUD
  setupFormTambahBarang();
  setupFormEditBarang();
  setupFormTambahRuangan();
  setupFormEditRuangan();
  setupFormTambahAkunMhs();
  setupFormEditAkunMhs();
  setupFormTambahAkunKry();
  setupFormEditAkunKry();

  // Form Peminjaman & Cek Ketersediaan
  setupCekKetersediaanBarangPage();
  setupCekKetersediaanRuanganPage();
  setupFormTambahPeminjamanBrg();
  setupFormTambahPeminjamanRuangan();

  // Fitur Tambahan
  setupSidebarPersistence();
  setupInputProtection();
  setupModalChaining();
  setupSuccessModalFromPHP();
});

function setupSuccessModalFromPHP() {
  const successModalElement = document.getElementById("successModal");
  if (
    typeof showSuccessModalOnLoad !== "undefined" &&
    showSuccessModalOnLoad &&
    successModalElement
  ) {
    new bootstrap.Modal(successModalElement).show();
  }
}

// =================================================================
// #2: HALAMAN OTENTIKASI (LOGIN & LUPA SANDI)
// =================================================================

function setupLoginForm() {
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return;

  // Pastikan error span selalu terlihat (jika ada error)
  const idError = document.getElementById("identifier-error");
  const passError = document.getElementById("password-error");

  loginForm.addEventListener("submit", function (e) {
    const idInput = document.getElementById("identifier");
    const passInput = document.getElementById("kataSandi");
    let isValid = true;

    // Reset pesan error
    if (idError) idError.textContent = "";
    if (passError) passError.textContent = "";

    // Validasi identifier
    if (!idInput.value.trim()) {
      if (idError) idError.textContent = "*NIM/NPK tidak boleh kosong.";
      isValid = false;
    } else if (!/^\d+$/.test(idInput.value.trim())) {
      if (idError) idError.textContent = "*NIM/NPK harus berupa angka.";
      isValid = false;
    }

    // Validasi password
    if (!passInput.value.trim()) {
      if (passError) passError.textContent = "*Kata Sandi tidak boleh kosong.";
      isValid = false;
    }

    // Jika tidak valid, cegah submit dan pastikan error terlihat
    if (!isValid) {
      e.preventDefault();
      if (idError) idError.style.display = "inline";
      if (passError) passError.style.display = "inline";
    }
  });

  // Tampilkan error dari server jika ada
  const serverError = document.getElementById("server-error");
  if (serverError && serverError.textContent.trim() !== "") {
    const errorMessage = serverError.textContent.trim().toLowerCase();
    serverError.classList.add("d-none");

    if (idError) idError.textContent = "";
    if (passError) passError.textContent = "";

    if (
      errorMessage.includes("akun tidak terdafrar") ||
      errorMessage.includes("akun_tidak_terdaftar")
    ) {
      if (idError) idError.textContent = "*Akun tidak terdaftar*";
    } else if (errorMessage.includes("kata_sandi_salah")) {
      if (passError) passError.textContent = "*Kata sandi salah*";
    } else {
      if (idError) idError.textContent = serverError.textContent.trim();
    }
    if (idError) idError.style.display = "inline";
    if (passError) passError.style.display = "inline";
  }
}

function setupLupaSandiForm() {
  const lupaSandiForm = document.getElementById("lupaSandiForm"); // Gunakan ID spesifik
  if (!lupaSandiForm) return;

  document.querySelector(".btn-back").onclick = () =>
    (window.location.href = "Login/login.php");

  lupaSandiForm.addEventListener("submit", function (e) {
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    let isValid = true;
    emailError.style.display = "none";

    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      emailError.textContent = "*Format email tidak valid";
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      emailError.style.display = "inline";
    }
  });
}

// =================================================================
// #3: HALAMAN LAPORAN
// =================================================================

function setupLaporanPage() {
  const btnTampilkan = document.getElementById("tampilkanLaporanBtn");
  if (!btnTampilkan) return;

  const jenisLaporanSelect = document.getElementById("jenisLaporan");
  const bulanLaporanSelect = document.getElementById("bulanLaporan");
  const tahunLaporanSelect = document.getElementById("tahunLaporan");
  const areaKontenLaporanDiv = document.getElementById("areaKontenLaporan");
  const judulKontenLaporanSpan = document.getElementById("judulKontenLaporan");
  const wadahLaporanDiv = document.getElementById("wadahLaporan");
  const exportExcelBtn = document.getElementById("exportExcelBtn");
  const validationModalElement = document.getElementById("validationModal");
  const validationModal = validationModalElement
    ? new bootstrap.Modal(validationModalElement)
    : null;
  const validationMessageEl = document.getElementById("validationMessage");
  const paginationControlsContainer = document.getElementById(
    "paginationControlsContainer"
  );
  const paginationUl = document.getElementById("paginationUl");

  let currentPage = 1;
  const rowsPerPage = 5;
  let currentTableFullData = [];
  let currentReportTypeForPaging = "";

  const currentYear = new Date().getFullYear();
  for (let i = 0; i < 5; i++) {
    const year = currentYear - i;
    const option = document.createElement("option");
    option.value = year;
    option.textContent = year;
    tahunLaporanSelect.appendChild(option);
  }

  btnTampilkan.addEventListener("click", function () {
    const jenisLaporan = jenisLaporanSelect.value;
    const bulan = bulanLaporanSelect.value;
    const tahun = tahunLaporanSelect.value;

    if (!jenisLaporan || !bulan || !tahun) {
      if (validationModal && validationMessageEl) {
        validationMessageEl.textContent =
          "Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).";
        validationModal.show();
      } else {
        alert(
          "Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun)."
        );
      }
      return;
    }

    wadahLaporanDiv.innerHTML =
      '<p class="text-center py-5"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat data...</p>';
    paginationControlsContainer.style.display = "none";
    areaKontenLaporanDiv.style.display = "none";
    currentPage = 1;

    const fetchUrl = `../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${jenisLaporan}&bulan=${bulan}&tahun=${tahun}`;

    fetch(fetchUrl)
      .then((response) => {
        if (!response.ok) {
          return response.text().then((text) => {
            throw new Error(
              `HTTP error! status: ${response.status}, message: ${text}`
            );
          });
        }
        return response.json();
      })
      .then((result) => {
        wadahLaporanDiv.innerHTML = "";
        if (result.status === "success") {
          currentTableFullData = result.data || [];
          currentReportTypeForPaging = jenisLaporan;
          const namaBulanDipilih =
            bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
          const tahunDipilih = tahunLaporanSelect.value;

          if (currentTableFullData.length > 0) {
            areaKontenLaporanDiv.style.display = "block";
            let judulText = `Laporan (${
              jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text
            }) - ${namaBulanDipilih} ${tahunDipilih}`;
            // ... (logika penentuan judul tetap sama)
            judulKontenLaporanSpan.textContent = judulText;
            displayPage(currentPage);
            setupPagination();
          } else {
            areaKontenLaporanDiv.style.display = "none";
            paginationControlsContainer.style.display = "none";
            wadahLaporanDiv.innerHTML = "";
            if (validationModal && validationMessageEl) {
              validationMessageEl.textContent =
                "Tidak Ada Data Laporan untuk periode yang dipilih.";
              validationModal.show();
            } else {
              alert("Tidak Ada Data Laporan untuk periode yang dipilih.");
            }
          }
        } else {
          areaKontenLaporanDiv.style.display = "block";
          judulKontenLaporanSpan.textContent = "Kesalahan Sistem";
          wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Gagal memuat data: ${result.message}</p>`;
          console.error("Server Error:", result.message);
          paginationControlsContainer.style.display = "none";
        }
      })
      .catch((error) => {
        areaKontenLaporanDiv.style.display = "block";
        console.error("Fetch Error:", error);
        judulKontenLaporanSpan.textContent = "Kesalahan Jaringan";
        wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Terjadi kesalahan saat mengambil data. Detail: ${error.message}</p>`;
        paginationControlsContainer.style.display = "none";
      });
  });

  function displayPage(page) {
    currentPage = page;
    wadahLaporanDiv.innerHTML = "";
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const paginatedItems = currentTableFullData.slice(startIndex, endIndex);

    if (
      paginatedItems.length === 0 &&
      currentTableFullData.length > 0 &&
      currentPage > 1
    ) {
      displayPage(currentPage - 1);
      return;
    }
    if (paginatedItems.length === 0) return;

    const table = document.createElement("table");
    table.className = "table table-striped table-bordered table-hover";
    let headers = [],
      dataKeys = [];
    // ... (Logika penentuan header dan keys tabel tetap sama)
    if (currentReportTypeForPaging === "dataBarang") {
      table.id = "tabelLaporanDataBarang";
      headers = ["ID Barang", "Nama Barang", "Stok Barang", "Lokasi Barang"];
      dataKeys = ["idBarang", "namaBarang", "stokBarang", "lokasiBarang"];
    } else if (currentReportTypeForPaging === "dataRuangan") {
      table.id = "tabelLaporanDataRuangan";
      headers = [
        "ID Ruangan",
        "Nama Ruangan",
        "Kondisi Ruangan",
        "Ketersediaan",
      ];
      dataKeys = ["idRuangan", "namaRuangan", "kondisiRuangan", "ketersediaan"];
    } else if (currentReportTypeForPaging === "peminjamSeringMeminjam") {
      table.id = "tabelLaporanPeminjamSeringMeminjam";
      headers = [
        "ID Peminjam",
        "Nama Peminjam",
        "Jenis Peminjam",
        "Jumlah Peminjaman",
      ];
      dataKeys = [
        "IDPeminjam",
        "NamaPeminjam",
        "JenisPeminjam",
        "JumlahPeminjaman",
      ];
    } else if (currentReportTypeForPaging === "barangSeringDipinjam") {
      table.id = "tabelLaporanBarangSeringDipinjam";
      headers = ["ID Barang", "Nama Barang", "Total Kuantitas Dipinjam"];
      dataKeys = ["idBarang", "namaBarang", "TotalKuantitasDipinjam"];
    } else if (currentReportTypeForPaging === "ruanganSeringDipinjam") {
      table.id = "tabelLaporanRuanganSeringDipinjam";
      headers = ["ID Ruangan", "Nama Ruangan", "Jumlah Dipinjam"];
      dataKeys = ["idRuangan", "namaRuangan", "JumlahDipinjam"];
    } else {
      wadahLaporanDiv.innerHTML =
        '<p class="text-center">Tampilan tabel untuk jenis laporan ini belum didukung.</p>';
      return;
    }

    const thead = table.createTHead();
    const headerRow = thead.insertRow();
    headers.forEach((text) => {
      let th = document.createElement("th");
      th.textContent = text;
      headerRow.appendChild(th);
    });

    const tbody = table.createTBody();
    paginatedItems.forEach((item) => {
      const row = tbody.insertRow();
      dataKeys.forEach((key) => {
        row.insertCell().textContent = item[key] != null ? item[key] : "";
      });
    });
    wadahLaporanDiv.appendChild(table);
    updatePaginationButtonsActiveState();
  }

  function setupPagination() {
    paginationUl.innerHTML = "";
    const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);

    if (pageCount <= 1) {
      paginationControlsContainer.style.display = "none";
      return;
    }
    paginationControlsContainer.style.display = "block";

    // ... (Logika pembuatan tombol paginasi tetap sama)
    let prevLi = document.createElement("li");
    prevLi.className = "page-item";
    let prevLink = document.createElement("a");
    prevLink.className = "page-link";
    prevLink.href = "#";
    prevLink.innerHTML = "«";
    prevLink.addEventListener("click", (e) => {
      e.preventDefault();
      if (currentPage > 1) displayPage(currentPage - 1);
    });
    prevLi.appendChild(prevLink);
    paginationUl.appendChild(prevLi);

    for (let i = 1; i <= pageCount; i++) {
      let pageLi = document.createElement("li");
      pageLi.className = "page-item";
      pageLi.dataset.page = i;
      let pageLink = document.createElement("a");
      pageLink.className = "page-link";
      pageLink.href = "#";
      pageLink.textContent = i;
      pageLink.addEventListener("click", (e) => {
        e.preventDefault();
        displayPage(parseInt(e.target.closest("li").dataset.page));
      });
      pageLi.appendChild(pageLink);
      paginationUl.appendChild(pageLi);
    }

    let nextLi = document.createElement("li");
    nextLi.className = "page-item";
    let nextLink = document.createElement("a");
    nextLink.className = "page-link";
    nextLink.href = "#";
    nextLink.innerHTML = "»";
    nextLink.addEventListener("click", (e) => {
      e.preventDefault();
      if (currentPage < pageCount) displayPage(currentPage + 1);
    });
    nextLi.appendChild(nextLink);
    paginationUl.appendChild(nextLi);

    updatePaginationButtonsActiveState();
  }

  function updatePaginationButtonsActiveState() {
    const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);
    const pageItems = paginationUl.querySelectorAll(".page-item");

    pageItems.forEach((item) => {
      item.classList.remove("active", "disabled");
      const link = item.querySelector(".page-link");
      const pageNumData = item.dataset.page;

      if (link) {
        if (link.innerHTML.includes("«")) {
          // Previous
          if (currentPage === 1) item.classList.add("disabled");
        } else if (link.innerHTML.includes("»")) {
          // Next
          if (currentPage === pageCount || pageCount === 0)
            item.classList.add("disabled");
        } else if (pageNumData && parseInt(pageNumData) === currentPage) {
          // Nomor halaman
          item.classList.add("active");
        }
      }
    });

    paginationControlsContainer.style.display =
      pageCount <= 1 ? "none" : "block";
  }

  exportExcelBtn.addEventListener("click", function () {
    if (!currentTableFullData || currentTableFullData.length === 0) {
      alert("Tidak ada data untuk diexport.");
      return;
    }

    const jenisLaporan = jenisLaporanSelect.value;
    const bulanText =
      bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
    const tahunText = tahunLaporanSelect.value;
    const dataToExport = currentTableFullData;

    let headersDisplay = [],
      dataKeysForExport = [],
      fileName = "Laporan.xlsx",
      sheetName = "Laporan";
    // ... (Logika penentuan data export tetap sama)
    if (jenisLaporan === "dataBarang") {
      headersDisplay = [
        "ID Barang",
        "Nama Barang",
        "Stok Barang",
        "Lokasi Barang",
      ];
      dataKeysForExport = [
        "idBarang",
        "namaBarang",
        "stokBarang",
        "lokasiBarang",
      ];
      fileName = `Laporan_Data_Barang_${bulanText}_${tahunText}.xlsx`;
      sheetName = "Data Barang";
    } else if (jenisLaporan === "dataRuangan") {
      headersDisplay = [
        "ID Ruangan",
        "Nama Ruangan",
        "Kondisi Ruangan",
        "Ketersediaan",
      ];
      dataKeysForExport = [
        "idRuangan",
        "namaRuangan",
        "kondisiRuangan",
        "ketersediaan",
      ];
      fileName = `Laporan_Data_Ruangan_${bulanText}_${tahunText}.xlsx`;
      sheetName = "Data Ruangan";
    } else if (jenisLaporan === "peminjamSeringMeminjam") {
      headersDisplay = [
        "ID Peminjam",
        "Nama Peminjam",
        "Jenis Peminjam",
        "Jumlah Peminjaman",
      ];
      dataKeysForExport = [
        "IDPeminjam",
        "NamaPeminjam",
        "JenisPeminjam",
        "JumlahPeminjaman",
      ];
      fileName = `Laporan_Peminjam_Sering_Meminjam_${bulanText}_${tahunText}.xlsx`;
      sheetName = "Peminjam Sering Meminjam";
    } else if (jenisLaporan === "barangSeringDipinjam") {
      headersDisplay = ["ID Barang", "Nama Barang", "Total Kuantitas Dipinjam"];
      dataKeysForExport = ["idBarang", "namaBarang", "TotalKuantitasDipinjam"];
      fileName = `Laporan_Barang_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
      sheetName = "Barang Sering Dipinjam";
    } else if (jenisLaporan === "ruanganSeringDipinjam") {
      headersDisplay = ["ID Ruangan", "Nama Ruangan", "Jumlah Dipinjam"];
      dataKeysForExport = ["idRuangan", "namaRuangan", "JumlahDipinjam"];
      fileName = `Laporan_Ruangan_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
      sheetName = "Ruangan Sering Dipinjam";
    } else {
      alert("Jenis laporan tidak dikenal untuk export.");
      return;
    }

    const ws_data = [headersDisplay];
    dataToExport.forEach((item) => {
      const rowData = dataKeysForExport.map((key) =>
        item[key] != null ? item[key] : ""
      );
      ws_data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(ws_data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, sheetName);
    XLSX.writeFile(wb, fileName);
  });
}

// =================================================================
// #4: HALAMAN CEK KETERSEDIAAN (BARANG & RUANGAN)
// =================================================================

function setupCekKetersediaanBarangPage() {
  const form = document.getElementById("formCekKetersediaanBarang");
  if (!form) return;

  // Inisialisasi date picker
  dateTimeHelpers.fillSelects("tglHari", "tglBulan", "tglTahun");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    const hari = document.getElementById("tglHari").value;
    const bulan = document.getElementById("tglBulan").value;
    const tahun = document.getElementById("tglTahun").value;
    const errorTanggal = document.getElementById("error-message");

    if (!hari || !bulan || !tahun) {
      isValid = false;
      errorTanggal.textContent = "*Harus Diisi";
    } else {
      const inputDate = new Date(
        `${tahun}-${String(bulan).padStart(2, "0")}-${String(hari).padStart(
          2,
          "0"
        )}`
      );
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (inputDate < today) {
        isValid = false;
        errorTanggal.textContent = "*Input tanggal sudah lewat";
      }
    }

    if (!isValid) {
      errorTanggal.style.display = "inline";
      event.preventDefault();
    } else {
      errorTanggal.style.display = "none";
      // hidden input untuk dikirim ke PHP
      const tglPeminjamanInput = document.getElementById("tglPeminjamanBrg");
      if (tglPeminjamanInput) {
        tglPeminjamanInput.value = `${String(hari).padStart(2, "0")}-${String(
          bulan
        ).padStart(2, "0")}-${tahun}`;
      }
    }
  });
}

function setupCekKetersediaanRuanganPage() {
  const form = document.getElementById("formCekKetersediaanRuangan");
  if (!form) return;

  // Inisialisasi date & time picker
  dateTimeHelpers.fillSelects("tglHari", "tglBulan", "tglTahun");
  dateTimeHelpers.fillTimeSelects("jam_dari", "menit_dari");
  dateTimeHelpers.fillTimeSelects("jam_sampai", "menit_sampai");

  form.addEventListener("submit", function (e) {
    const hari = document.getElementById("tglHari").value;
    const bulan = document.getElementById("tglBulan").value;
    const tahun = document.getElementById("tglTahun").value;
    const jamDari = document.getElementById("jam_dari").value;
    const menitDari = document.getElementById("menit_dari").value;
    const jamSampai = document.getElementById("jam_sampai").value;
    const menitSampai = document.getElementById("menit_sampai").value;

    const errorMsg = document.getElementById("error-message");
    const errorWaktu = document.getElementById("error-waktu");
    const errorWaktuMulai = document.getElementById("error-waktu-mulai");
    const errorWaktuSelesai = document.getElementById("error-waktu-selesai");

    let isValid = true;

    // Validasi Tanggal
    if (!hari || !bulan || !tahun) {
      errorMsg.textContent = "*Harus Diisi";
      isValid = false;
    } else {
      const inputDate = new Date(
        `${tahun}-${String(bulan).padStart(2, "0")}-${String(hari).padStart(
          2,
          "0"
        )}`
      );
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (inputDate < today) {
        errorMsg.textContent = "*Input tanggal sudah lewat";
        isValid = false;
      }
    }
    errorMsg.style.display = isValid ? "none" : "inline";
    if (!isValid) e.preventDefault();

    // Validasi Waktu
    let isTimeValid = true;
    let isStartTimeFilled = jamDari !== "" && menitDari !== "";
    let isEndTimeFilled = jamSampai !== "" && menitSampai !== "";

    errorWaktuMulai.style.display = isStartTimeFilled ? "none" : "inline";
    errorWaktuSelesai.style.display = isEndTimeFilled ? "none" : "inline";

    if (!isStartTimeFilled || !isEndTimeFilled) {
      isTimeValid = false;
    } else {
      const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
      const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);
      const selectedDate = new Date(
        `${tahun}-${String(bulan).padStart(2, "0")}-${String(hari).padStart(
          2,
          "0"
        )}`
      );
      const now = new Date();

      if (endMinutes <= startMinutes) {
        errorWaktu.textContent =
          "*Waktu selesai harus lebih besar dari waktu mulai";
        isTimeValid = false;
      } else if (
        selectedDate.toDateString() === now.toDateString() &&
        startMinutes < now.getHours() * 60 + now.getMinutes()
      ) {
        errorWaktu.textContent =
          "*Waktu mulai tidak boleh lebih kecil dari waktu sekarang";
        isTimeValid = false;
      }
    }

    errorWaktu.style.display = isTimeValid ? "none" : "block";
    if (!isTimeValid) {
      isValid = false;
      e.preventDefault();
    }

    // Jika semua valid, set hidden input
    if (isValid) {
      const tglPeminjamanInput = document.getElementById(
        "tglPeminjamanRuangan"
      );
      if (tglPeminjamanInput) {
        tglPeminjamanInput.value = `${String(hari).padStart(2, "0")}-${String(
          bulan
        ).padStart(2, "0")}-${tahun}`;
      }
    }
  });
}

// =================================================================
// #5: HALAMAN LAINNYA (DETAIL, PENGEMBALIAN, PENGAJUAN)
// =================================================================

function setupDetailRiwayatForm() {
  const form = document.getElementById("formDetail");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.heif|\.heic)$/i;

    const validateFile = (inputId, errorId) => {
      const fileInput = document.getElementById(inputId);
      const errorSpan = document.getElementById(errorId);
      if (!fileInput) return;

      errorSpan.textContent = "";
      if (fileInput.files.length === 0) {
        errorSpan.textContent = "File wajib diupload.";
        isValid = false;
      } else if (!allowedExtensions.exec(fileInput.value)) {
        errorSpan.textContent = "Format file tidak valid.";
        isValid = false;
      }
    };

    validateFile("dokSebelum", "dokSebelumError");
    validateFile("dokSesudah", "dokSesudahError");

    if (!isValid) event.preventDefault();
  });
}

function setupPenolakanBarang() {
  // Cek kedua kemungkinan id form
  const form =
    document.getElementById("formPenolakanBarang") ||
    document.getElementById("formPenolakanRuangan");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;
    const alasanInput = document.getElementById("alasanPenolakan");
    const alasanError = document.getElementById("alasanPenolakanError");

    // Selalu tampilkan error span, kosongkan dulu
    if (alasanError) {
      alasanError.textContent = "";
      alasanError.style.display = "inline";
    }

    if (!alasanInput || !alasanInput.value.trim()) {
      if (alasanError) {
        alasanError.textContent = "*Harus diisi";
        alasanError.style.display = "inline";
      }
      isValid = false;
    } else {
      if (alasanError) {
        alasanError.textContent = "";
        alasanError.style.display = "none";
      }
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

function setupPengembalianBarangPage() {
  const form = document.getElementById("formPengembalianBarang");
  if (!form) return;

  // Gunakan helper stepper yang sudah dibuat
  setupStockStepper("stepperContainer", "jumlahPengembalian", "sisaPinjaman");

  form.addEventListener("submit", function (e) {
    let isValid = true;

    const jumlahInput = document.getElementById("jumlahPengembalian");
    const jumlahError = document.getElementById("jumlahError");
    const sisaPinjaman = parseInt(
      document.getElementById("sisaPinjaman")?.value || "0",
      10
    );

    if (!jumlahInput.value || parseInt(jumlahInput.value, 10) <= 0) {
      jumlahError.textContent = "*Jumlah harus lebih dari 0.";
      isValid = false;
    } else if (parseInt(jumlahInput.value, 10) > sisaPinjaman) {
      jumlahError.textContent = "*Melebihi sisa pinjaman.";
      isValid = false;
    }
    jumlahError.style.display =
      jumlahError.textContent !== "" ? "inline" : "none";

    const kondisiSelect = document.getElementById("txtKondisi");
    const kondisiError = document.getElementById("kondisiError");
    if (
      !kondisiSelect.value ||
      kondisiSelect.value === "Pilih Kondisi Barang"
    ) {
      kondisiError.textContent = "*Harus Dipilih";
      isValid = false;
    }
    kondisiError.style.display =
      !kondisiSelect.value || kondisiSelect.value === "Pilih Kondisi Barang"
        ? "inline"
        : "none";

    const catatanInput = document.getElementById("catatanPengembalianBarang");
    const catatanError = document.getElementById("catatanError");
    if (!catatanInput.value.trim()) {
      catatanError.textContent = "*Harus Diisi";
      isValid = false;
    }
    catatanError.style.display = !catatanInput.value.trim() ? "inline" : "none";

    if (!isValid) e.preventDefault();
  });
}

function setupPengembalianRuanganPage() {
  const form = document.getElementById("formPengembalianRuangan");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    // Tambahkan logika validasi untuk pengembalian ruangan jika ada
  });
}

// =================================================================
// #6: FITUR TAMBAHAN (SIDEBAR, PROTEKSI INPUT, MODAL)
// =================================================================

function setupSidebarPersistence() {
  const sidebar = document.querySelector(".sidebar, .offcanvas-body");
  if (!sidebar) return;

  const storageKey = "sidebar_active_menus";
  const getActiveMenus = () =>
    JSON.parse(localStorage.getItem(storageKey)) || [];
  const setActiveMenus = (menus) =>
    localStorage.setItem(storageKey, JSON.stringify(menus));

  // Pulihkan state saat load
  getActiveMenus().forEach((menuId) => {
    const menuElement = document.getElementById(menuId);
    if (menuElement) {
      const collapseInstance = new bootstrap.Collapse(menuElement, {
        toggle: false,
      });
      collapseInstance.show();
    }
  });

  // Tambahkan event listener
  sidebar.querySelectorAll(".collapse").forEach((menu) => {
    menu.addEventListener("show.bs.collapse", function () {
      let activeMenus = getActiveMenus();
      if (!activeMenus.includes(this.id)) {
        activeMenus.push(this.id);
        setActiveMenus(activeMenus);
      }
    });
    menu.addEventListener("hide.bs.collapse", function () {
      let activeMenus = getActiveMenus();
      const index = activeMenus.indexOf(this.id);
      if (index > -1) {
        activeMenus.splice(index, 1);
        setActiveMenus(activeMenus);
      }
    });
  });
}

function setupInputProtection() {
  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", () => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });
}

function setupModalChaining() {
  const confirmYesButton = document.getElementById("confirmYes");
  if (!confirmYesButton) return;

  confirmYesButton.addEventListener("click", function () {
    const confirmModalElement = document.getElementById("confirmModal");
    const successModalElement = document.getElementById("successModal");

    if (confirmModalElement && successModalElement) {
      const confirmModalInstance =
        bootstrap.Modal.getInstance(confirmModalElement);
      if (confirmModalInstance) confirmModalInstance.hide();

      const successModalInstance = new bootstrap.Modal(successModalElement);
      successModalInstance.show();
    }
  });
}

// =================================================================
// #7: VALIDASI SEMUA FORM CRUD
// =================================================================

function setupFormTambahBarang() {
  const form = document.getElementById("formTambahBarang");
  if (!form) return;

  setupStockStepper("stepperContainer", "stokBarang");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi nama barang
    const namaBarang = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (!namaBarang.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi stok barang
    const stokBarang = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    if (!stokBarang.value || stokBarang.value <= 0) {
      stokError.textContent = "*Harus diisi dan minimal 1";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi lokasi barang
    const lokasiBarang = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiBarang.value) {
      lokasiError.textContent = "*Harus dipilih";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "menambah barang";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditBarang() {
  const form = document.getElementById("formEditBarang");
  if (!form) return;

  setupStockStepper("stepperContainer", "stokBarang");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi nama barang
    const namaBarang = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (!namaBarang.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi stok barang (boleh 0 untuk edit)
    const stokBarang = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    if (stokBarang.value === "" || stokBarang.value < 0) {
      stokError.textContent = "*Harus diisi dan minimal 0";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi lokasi barang
    const lokasiBarang = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiBarang.value) {
      lokasiError.textContent = "*Harus dipilih";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data barang";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormTambahRuangan() {
  const form = document.getElementById("formTambahRuangan");
  if (!form) return;

  setupKondisiRuanganLogic();

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const namaRuangan = document.getElementById("namaRuangan");
    const kondisiRuangan = document.getElementById("kondisiRuangan");
    const ketersediaan = document.getElementById("ketersediaan");
    const namaError = document.getElementById("namaError");
    const kondisiError = document.getElementById("kondisiError");
    const ketersediaanError = document.getElementById("ketersediaanError");

    // Validasi nama ruangan
    if (!namaRuangan.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi kondisi ruangan
    if (!kondisiRuangan.value) {
      kondisiError.textContent = "*Harus dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    } else {
      kondisiError.style.display = "none";
    }

    // Validasi ketersediaan
    if (!ketersediaan.value) {
      ketersediaanError.textContent = "*Harus dipilih";
      ketersediaanError.style.display = "inline";
      isValid = false;
    } else {
      ketersediaanError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "menambah ruangan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditRuangan() {
  const form = document.getElementById("formEditRuangan");
  if (!form) return;

  setupKondisiRuanganLogic();

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const namaRuangan = document.getElementById("namaRuangan");
    const kondisiRuangan = document.getElementById("kondisiRuangan");
    const ketersediaan = document.getElementById("ketersediaan");
    const namaError = document.getElementById("namaError");
    const kondisiError = document.getElementById("kondisiError");
    const ketersediaanError = document.getElementById("ketersediaanError");

    // Reset error messages
    namaError.style.display = "none";
    kondisiError.style.display = "none";
    ketersediaanError.style.display = "none";

    // Validasi nama ruangan
    if (!namaRuangan.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    }

    // Validasi kondisi ruangan
    if (!kondisiRuangan.value) {
      kondisiError.textContent = "*Harus dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    }

    // Validasi ketersediaan
    if (!ketersediaan.value) {
      ketersediaanError.textContent = "*Harus dipilih";
      ketersediaanError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data ruangan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormTambahAkunMhs() {
  const form = document.getElementById("formTambahAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;
    let nim = document.getElementById("nim").value.trim();
    let nama = document.getElementById("nama").value.trim();
    let email = document.getElementById("email").value.trim();
    let jenisRole = document.getElementById("jenisRole").value;
    let pass = document.getElementById("kataSandi").value;
    let conf = document.getElementById("konfirmasiSandi").value;

    let nimError = document.getElementById("nimError");
    let namaError = document.getElementById("namaError");
    let emailError = document.getElementById("emailError");
    let roleError = document.getElementById("roleError");
    let passError = document.getElementById("passError");
    let confPassError = document.getElementById("confPassError");
    let passPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

    // Reset error messages
    nimError.style.display = "none";
    namaError.style.display = "none";
    emailError.style.display = "none";
    roleError.style.display = "none";
    passError.style.display = "none";
    confPassError.style.display = "none";

    if (nim === "") {
      nimError.textContent = "*Harus diisi";
      nimError.style.display = "inline";
      isValid = false;
    } else if (!/^\d+$/.test(nim)) {
      nimError.textContent = "*Harus berupa angka";
      nimError.style.display = "inline";
      isValid = false;
    }

    if (nama === "") {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else if (/\d/.test(nama)) {
      namaError.textContent = "*Harus berupa huruf";
      namaError.style.display = "inline";
      isValid = false;
    }

    if (email === "") {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    if (jenisRole === "") {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    }

    if (pass === "") {
      passError.textContent = "*Harus diisi";
      passError.style.display = "inline";
      isValid = false;
    } else if (pass.length > 0 && pass.length < 8) {
      passError.textContent = "*Minimal 8 karakter";
      passError.style.display = "inline";
      isValid = false;
    } else if (!passPattern.test(pass)) {
      passError.textContent = "*Harus mengandung huruf dan angka";
      passError.style.display = "inline";
      isValid = false;
    }

    if (conf === "") {
      confPassError.textContent = "*Harus diisi";
      confPassError.style.display = "inline";
      isValid = false;
    } else if (pass !== "" && conf !== "" && pass !== conf) {
      confPassError.textContent = "*Tidak sesuai";
      confPassError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "menambah akun mahasiswa";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditAkunMhs() {
  const form = document.getElementById("formEditAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi email
    const email = document.getElementById("email").value.trim();
    const emailError = document.getElementById("emailError");
    if (!email) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data akun mahasiswa";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });

  // Setup input protection untuk field yang tidak boleh diubah
  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", () => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });

  // Setup password visibility toggle
  const passInput = document.getElementById("kataSandi");
  if (passInput) {
    passInput.addEventListener("mouseenter", function () {
      passInput.type = "text";
    });
    passInput.addEventListener("mouseleave", function () {
      passInput.type = "password";
    });
  }
}

function setupFormTambahAkunKry() {
  const form = document.getElementById("formTambahAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const npk = document.getElementById("npk").value.trim();
    const nama = document.getElementById("nama").value.trim();
    const email = document.getElementById("email").value.trim();
    const jenisRole = document.getElementById("jenisRole").value;
    const pass = document.getElementById("kataSandi").value;
    const conf = document.getElementById("konfirmasiSandi").value;

    const npkError = document.getElementById("npkError");
    const namaError = document.getElementById("namaError");
    const emailError = document.getElementById("emailError");
    const roleError = document.getElementById("roleError");
    const passError = document.getElementById("passError");
    const confPassError = document.getElementById("confPassError");
    const passPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

    // Reset error messages
    npkError.style.display = "none";
    namaError.style.display = "none";
    emailError.style.display = "none";
    roleError.style.display = "none";
    passError.style.display = "none";
    confPassError.style.display = "none";

    // Validasi NPK
    if (!npk) {
      npkError.textContent = "*Harus diisi";
      npkError.style.display = "inline";
      isValid = false;
    } else if (!/^\d+$/.test(npk)) {
      npkError.textContent = "*Harus berupa angka";
      npkError.style.display = "inline";
      isValid = false;
    }

    // Validasi nama
    if (!nama) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else if (/\d/.test(nama)) {
      namaError.textContent = "*Harus berupa huruf";
      namaError.style.display = "inline";
      isValid = false;
    }

    // Validasi email
    if (!email) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    // Validasi role
    if (!jenisRole) {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    }

    // Validasi password
    if (!pass) {
      passError.textContent = "*Harus diisi";
      passError.style.display = "inline";
      isValid = false;
    } else if (pass.length < 8) {
      passError.textContent = "*Minimal 8 karakter";
      passError.style.display = "inline";
      isValid = false;
    } else if (!passPattern.test(pass)) {
      passError.textContent = "*Harus mengandung huruf dan angka";
      passError.style.display = "inline";
      isValid = false;
    }

    // Validasi konfirmasi password
    if (!conf) {
      confPassError.textContent = "*Harus diisi";
      confPassError.style.display = "inline";
      isValid = false;
    } else if (pass && conf && pass !== conf) {
      confPassError.textContent = "*Tidak sesuai";
      confPassError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "menambah akun karyawan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditAkunKry() {
  const form = document.getElementById("formEditAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;
    let email = document.getElementById("email").value.trim();
    let emailError = document.getElementById("emailError");

    // Reset error messages
    emailError.style.display = "none";

    if (email === "") {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data akun karyawan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });

  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", (e) => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });

  const passInput = document.getElementById("kataSandi");
  passInput.addEventListener("mouseenter", function () {
    passInput.type = "text";
  });
  passInput.addEventListener("mouseleave", function () {
    passInput.type = "password";
  });
}

function setupFormTambahPeminjamanBrg() {
  const form = document.getElementById("formTambahPeminjamanBrg");
  if (!form) return;

  setupStockStepper("stepperContainerPeminjaman", "jumlahBrg", "stokTersedia");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const jumlahInputEl = document.getElementById("jumlahBrg");
    const alasanInputEl = document.getElementById("alasanPeminjamanBrg");
    const jumlahError = document.getElementById("jumlahError");
    const alasanError = document.getElementById("alasanError");

    // Reset error messages
    jumlahError.style.display = "none";
    alasanError.style.display = "none";

    // Validasi jumlah barang
    let jumlahValue = parseInt(jumlahInputEl.value, 10) || 0;
    if (!jumlahInputEl.value.trim()) {
      jumlahError.textContent = "*Harus diisi";
      jumlahError.style.display = "inline";
      isValid = false;
    }

    // Validasi alasan peminjaman
    if (!alasanInputEl.value.trim()) {
      alasanError.textContent = "*Harus diisi";
      alasanError.style.display = "inline";
      isValid = false;
    }

    // Ambil stok tersedia dari elemen (misal hidden input atau data attribute)
    let stokTersedia = 0;
    const stokElem = document.getElementById("stokBarang");
    if (stokElem) {
      stokTersedia = parseInt(
        stokElem.value || stokElem.textContent || "0",
        10
      );
    } else if (window.stokTersedia !== undefined) {
      stokTersedia = parseInt(window.stokTersedia, 10);
    } else {
      stokTersedia = parseInt(
        jumlahInputEl.getAttribute("data-stok") || "0",
        10
      );
    }
    if (isNaN(stokTersedia)) stokTersedia = 0;

    // Validasi jumlah terhadap stok
    if (jumlahValue <= 0) {
      jumlahError.textContent = "*Jumlah harus lebih dari 0.";
      jumlahError.style.display = "inline";
      isValid = false;
    } else if (jumlahValue > stokTersedia) {
      jumlahError.textContent = "*Jumlah melebihi stok tersedia.";
      jumlahError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "menambah peminjaman barang";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormTambahPeminjamanRuangan() {
  const form = document.getElementById("formTambahPeminjamanRuangan");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;
    const alasanInput = document.getElementById("alasanPeminjamanRuangan");
    const alasanError = document.getElementById("error-message");

    if (!alasanInput.value.trim()) {
      alasanError.textContent = "*Harus Diisi";
      alasanError.style.display = "inline";
      isValid = false;
    } else {
      alasanError.style.display = "none";
    }

    if (!isValid) event.preventDefault();
  });
}
