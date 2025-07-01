<?php
// export_laporan_excel_pic.php

// 1. SERTAKAN FILE koneksi.php ANDA!
// Pastikan path ini benar relatif terhadap lokasi file export_laporan_excel_pic.php ini.
// Jika 'CRUD/Laporan/' adalah subfolder dari 'Menu PIC/', dan 'koneksi.php' ada di root, maka:
// $pathToKoneksi = __DIR__ . '/../../koneksi.php'; // Jika koneksi.php dua level di atas
$pathToKoneksi = '../../koneksi.php'; // Atau sesuaikan dengan struktur Anda

// Coba include dengan path absolut berbasis __DIR__ jika memungkinkan
if (file_exists(__DIR__ . '/../../koneksi.php')) {
    include __DIR__ . '/../../koneksi.php';
} elseif (file_exists($pathToKoneksi)) {
    include $pathToKoneksi;
} else {
    die("File koneksi.php tidak ditemukan. Periksa path: " . $pathToKoneksi . " dan " . __DIR__ . '/../../koneksi.php');
}


// 2. Ambil parameter dari URL (sama seperti di get_laporan_data.php)
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
// Konversi ke integer penting untuk query SQL
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null; 
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;


// 3. Tentukan nama file Excel yang akan diunduh
$fileName = "Laporan_PIC_"; // Awalan untuk membedakan dengan KaUPT jika perlu
if ($jenisLaporan) {
    // Mengubah camelCase atau PascalCase menjadi Title_Case_With_Underscore
    $namaLaporanDeskriptif = ucwords(trim(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $jenisLaporan))));
    $fileName .= str_replace(' ', '_', $namaLaporanDeskriptif);
} else {
    $fileName .= "Tidak_Diketahui";
}

// Tambahkan bulan dan tahun ke nama file jika relevan dan tersedia
// Jenis laporan yang pasti menggunakan filter waktu
$laporanDenganFilterWaktuWajib = ['peminjamSeringMeminjam', 'barangSeringDipinjam', 'ruanganSeringDipinjam'];

if (in_array($jenisLaporan, $laporanDenganFilterWaktuWajib)) {
    if ($bulan && $tahun) {
        $fileName .= "_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
    } else {
        // Jika filter waktu wajib tapi tidak ada, tambahkan penanda di nama file atau handle error
        $fileName .= "_Filter_Tidak_Lengkap"; 
    }
} elseif (($jenisLaporan === 'dataBarang' || $jenisLaporan === 'dataRuangan') && $bulan && $tahun) {
    // Untuk dataBarang/dataRuangan, meskipun query tidak selalu filter waktu, nama file bisa mencerminkan pilihan pengguna
    $fileName .= "_Periode_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
}
$fileName .= ".xls"; // Ekstensi file .xls (Excel lama, sesuai metode PDF)


// 4. Set header HTTP untuk memberitahu browser bahwa ini adalah file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\""); // Gunakan kutip ganda jika nama file mengandung spasi
header("Pragma: no-cache"); // Mencegah caching proxy
header("Expires: 0");       // Mencegah caching browser

// 5. Memulai output tabel HTML
echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
echo "<head><meta charset=\"utf-8\"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>";
echo "<table border='1'>";
echo "<thead>";

// Variabel untuk statement SQL dan hasil
$stmt = null;
$params = [];    // Array untuk parameter query
$headers = [];   // Array untuk header kolom tabel
$dataKeys = [];  // Array untuk kunci data sesuai header (untuk mencetak sel)

// 6. Logika untuk memilih query dan header berdasarkan jenisLaporan
if ($conn && $jenisLaporan) {
    try {
        // --------------- Data Barang ---------------
        if ($jenisLaporan === 'dataBarang') {
            $headers = ['ID Barang', 'Nama Barang', 'Stok Barang', 'Lokasi Barang'];
            $dataKeys = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
            // CATATAN: Query ini MENGAMBIL SEMUA data barang, tidak difilter oleh bulan/tahun
            // Jika ingin difilter waktu berdasarkan aktivitas, query perlu diubah (JOIN dengan Peminjaman_Barang).
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            $stmt = sqlsrv_query($conn, $query);
        } 
        // --------------- Data Ruangan ---------------
        else if ($jenisLaporan === 'dataRuangan') {
            $headers = ['ID Ruangan', 'Nama Ruangan', 'Kondisi Ruangan', 'Ketersediaan'];
            $dataKeys = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
            // CATATAN: Query ini MENGAMBIL SEMUA data ruangan, tidak difilter oleh bulan/tahun
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);
        }
        // --------------- Peminjam Sering Meminjam ---------------
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            if ($bulan !== null && $tahun !== null) { // WAJIB ada filter waktu
                $headers = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
                $dataKeys = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
                $query = "
                    SELECT
                        CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END AS IDPeminjam,
                        CASE WHEN P.nim IS NOT NULL THEN M.namaMhs WHEN P.npk IS NOT NULL THEN K.namaKry END AS NamaPeminjam,
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                        COUNT(P.id_peminjaman) AS JumlahPeminjaman
                    FROM (
                        SELECT idPeminjamanBrg AS id_peminjaman, nim, npk, tglPeminjamanBrg FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                        UNION ALL
                        SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk, tglPeminjamanRuangan FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
                    ) AS P
                    LEFT JOIN Mahasiswa AS M ON P.nim = M.nim
                    LEFT JOIN Karyawan AS K ON P.npk = K.npk
                    GROUP BY CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END,
                             CASE WHEN P.nim IS NOT NULL THEN M.namaMhs WHEN P.npk IS NOT NULL THEN K.namaKry END,
                             CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                    ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                ";
                $params = [$tahun, $bulan, $tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            } else {
                // Jika filter waktu wajib tidak ada, cetak pesan error di tabel
                $headers = ['Pesan Kesalahan']; $dataKeys = ['Pesan Kesalahan']; // Header darurat
                echo "<tr><th>Pesan Kesalahan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Peminjam Sering Meminjam.</td></tr>";
            }
        }
        // --------------- Barang Sering Dipinjam ---------------
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan !== null && $tahun !== null) { // WAJIB ada filter waktu
                $headers = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
                $dataKeys = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
                $query = "
                    SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                    FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                    WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                    GROUP BY PB.idBarang, B.namaBarang
                    ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            } else {
                $headers = ['Pesan Kesalahan']; $dataKeys = ['Pesan Kesalahan'];
                echo "<tr><th>Pesan Kesalahan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Barang Sering Dipinjam.</td></tr>";
            }
        }
        // --------------- Ruangan Sering Dipinjam ---------------
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
             if ($bulan !== null && $tahun !== null) { // WAJIB ada filter waktu
                $headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
                $dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
                $query = "
                    SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                    FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                    GROUP BY PR.idRuangan, R.namaRuangan
                    ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            } else {
                $headers = ['Pesan Kesalahan']; $dataKeys = ['Pesan Kesalahan'];
                echo "<tr><th>Pesan Kesalahan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Ruangan Sering Dipinjam.</td></tr>";
            }
        }
        // --------------- Default jika jenis laporan tidak dikenal ---------------
        else {
            echo "<tr><td colspan='1'>Jenis laporan tidak dikenal atau tidak valid.</td></tr>";
        }

        // Mencetak header tabel jika query berhasil dieksekusi dan ada header yang ditentukan
        if ($stmt && !empty($headers)) {
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            echo "</thead><tbody>"; // Menutup thead dan membuka tbody

            $adaData = false;
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $adaData = true;
                echo "<tr>";
                // Cetak sel berdasarkan urutan dataKeys untuk memastikan konsistensi kolom
                foreach ($dataKeys as $key) {
                    echo "<td>" . htmlspecialchars($row[$key] ?? '') . "</td>";
                }
                echo "</tr>";
            }

            if (!$adaData && !empty($headers)) { // Jika tidak ada baris data tapi header sudah dicetak
                echo "<tr><td colspan='" . count($headers) . "' align='center'>Tidak ada data untuk laporan ini pada periode yang dipilih.</td></tr>";
            }
             if ($stmt) sqlsrv_free_stmt($stmt);

        } elseif ($stmt === false && $jenisLaporan && (count($params) > 0 ? ($bulan !== null && $tahun !== null) : true ) ) { 
            // Jika query gagal DAN jenis laporan valid & filter waktu (jika perlu) ada
            echo "<tr><th>Error</th></tr></thead><tbody><tr><td>Gagal menjalankan query untuk laporan. Periksa log server.</td></tr>";
        }
        // (Pesan error untuk filter waktu yang hilang sudah ditangani di atas)


    } catch (Exception $e) {
        $colCount = !empty($headers) ? count($headers) : 1;
        echo "<tr><th colspan='".$colCount."'>Kesalahan Server</th></tr></thead><tbody><tr><td colspan='".$colCount."'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    // Jika koneksi gagal atau $jenisLaporan tidak valid dari awal.
    echo "<tr><th>Error</th></tr></thead><tbody><tr><td>";
    if (!$conn) {
        echo "Koneksi database gagal.";
    } else {
        echo "Parameter jenis laporan tidak disediakan atau tidak valid.";
    }
    echo "</td></tr>";
}

echo "</tbody></table></body></html>"; // Menutup tbody, table, body, html
exit; // Pastikan tidak ada output lain setelah ini

?>