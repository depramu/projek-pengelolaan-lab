<?php
// export_laporan_excel_kaupt.php

// SERTAKAN FILE koneksi.php ANDA!
// Sesuaikan path include jika lokasi koneksi.php berbeda dari '../koneksi.php'
// relatif terhadap file export_laporan_excel_kauph.php ini.
// Jika 'CRUD/Laporan/' adalah subfolder dari 'Menu PIC/', dan 'koneksi.php' ada di root, maka:
include '../../koneksi.php'; // Asumsi koneksi.php ada dua level di atas.

// Ambil parameter dari URL (sama seperti di get_laporan_data.php)
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : null; // Tetap konversi ke int
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : null; // Tetap konversi ke int

// Tentukan nama file Excel yang akan diunduh
$fileName = "Laporan_";
if ($jenisLaporan) {
    $fileName .= str_replace(' ', '_', ucwords(preg_replace('/(?<!^)[A-Z]/', '_$0', $jenisLaporan))); // Membuat nama lebih deskriptif
}
// Tambahkan bulan dan tahun ke nama file JIKA ADA dan jenis laporan memerlukannya
$laporanDenganFilterWaktu = ['peminjamSeringMeminjam', 'barangSeringDipinjam', 'ruanganSeringDipinjam'];
if (in_array($jenisLaporan, $laporanDenganFilterWaktu) && $bulan && $tahun) {
    $fileName .= "_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
} elseif (($jenisLaporan === 'dataBarang' || $jenisLaporan === 'dataRuangan') && $bulan && $tahun) {
    // Untuk dataBarang/dataRuangan, meskipun query utama tidak filter waktu, nama file bisa mencantumkan jika dipilih
    $fileName .= "_Periode_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
}
$fileName .= ".xls";

// Set header HTTP untuk memberitahu browser bahwa ini adalah file Excel yang akan diunduh
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\""); // Nama file saat diunduh
header("Pragma: no-cache"); // Mencegah caching
header("Expires: 0");       // Mencegah caching

// Memulai output tabel HTML (ini yang akan diinterpretasikan Excel)
echo "<table border='1'>";
echo "<thead>"; // Tambahkan thead untuk konsistensi

// Variabel untuk menyimpan statement SQL dan hasil
$stmt = null;
$params = []; // Array untuk parameter query (jika ada)
$headers = []; // Array untuk header kolom tabel HTML

// Logika untuk memilih query dan header berdasarkan jenisLaporan
if ($conn && $jenisLaporan) {
    try {
        // ----------------- Data Barang -----------------
        if ($jenisLaporan === 'dataBarang') {
            $headers = ['ID Barang', 'Nama Barang', 'Stok Barang', 'Lokasi Barang'];
            // Query ini mengambil semua data barang, tidak difilter waktu.
            // Jika Anda ingin memfilter berdasarkan waktu aktivitas barang, query ini perlu diubah.
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            $stmt = sqlsrv_query($conn, $query);
        } 
        // ----------------- Data Ruangan -----------------
        else if ($jenisLaporan === 'dataRuangan') {
            $headers = ['ID Ruangan', 'Nama Ruangan', 'Kondisi Ruangan', 'Ketersediaan'];
            // Query ini mengambil semua data ruangan, tidak difilter waktu.
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);
        }
        // ----------------- Peminjam Sering Meminjam -----------------
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            if ($bulan !== null && $tahun !== null) { // WAJIB ADA FILTER WAKTU
                $headers = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
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
                echo "<tr><td colspan='4'>Filter Bulan dan Tahun wajib dipilih untuk laporan Peminjam Sering Meminjam.</td></tr>";
            }
        }
        // ----------------- Barang Sering Dipinjam -----------------
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan !== null && $tahun !== null) { // WAJIB ADA FILTER WAKTU
                $headers = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
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
                echo "<tr><td colspan='3'>Filter Bulan dan Tahun wajib dipilih untuk laporan Barang Sering Dipinjam.</td></tr>";
            }
        }
        // ----------------- Ruangan Sering Dipinjam -----------------
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
            if ($bulan !== null && $tahun !== null) { // WAJIB ADA FILTER WAKTU
                $headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
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
                echo "<tr><td colspan='3'>Filter Bulan dan Tahun wajib dipilih untuk laporan Ruangan Sering Dipinjam.</td></tr>";
            }
        }
        // ----------------- Default jika jenis laporan tidak dikenal -----------------
        else {
            echo "<tr><td colspan='1'>Jenis laporan tidak dikenal atau belum diimplementasikan untuk export.</td></tr>";
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
                // Loop berdasarkan urutan header untuk menjaga konsistensi kolom
                // (Asumsi nama kolom/alias di SELECT query = kunci di $headers jika case sensitive, atau disesuaikan)
                // Cara sederhana: print semua value dalam $row. Ini kurang ideal jika urutan SELECT tidak sama dengan headers.
                // Untuk lebih presisi, kita seharusnya punya array $dataKeys lagi seperti di JS
                // Namun, mengikuti contoh PDF yang sederhana, kita cetak semua nilai
                foreach ($row as $key => $cell_value) {
                    // Untuk export, mungkin lebih baik mencetak semua yang di-select
                    // atau memetakan sesuai $headers dengan asumsi $dataKeys mirip dengan JavaScript
                    echo "<td>" . htmlspecialchars($cell_value ?? '') . "</td>";
                }
                echo "</tr>";
            }

            if (!$adaData && !empty($headers)) {
                echo "<tr><td colspan='" . count($headers) . "' align='center'>Tidak ada data untuk laporan ini pada periode yang dipilih.</td></tr>";
            }
            if ($stmt) sqlsrv_free_stmt($stmt);

        } elseif (empty($headers) && !$jenisLaporan) { // jika jenis laporan tidak valid dari awal
             echo "<tr><td colspan='1'>Jenis laporan tidak valid.</td></tr>";
        }
         elseif ($stmt === false && $jenisLaporan && ($bulan !== null || $tahun !== null)) { // jika query gagal
             echo "<tr><td colspan='1'>Gagal menjalankan query.</td></tr>";
        }
         // (Jika ada pesan error spesifik dari bagian filter bulan/tahun, itu sudah dicetak)


    } catch (Exception $e) {
        echo "<tr><td colspan='1'>Terjadi kesalahan server: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    // Jika koneksi gagal atau $jenisLaporan tidak ada dari awal.
    $colCount = !empty($headers) ? count($headers) : 1; // Jumlah kolom default jika header tidak ada
    if (!$conn) {
        echo "<tr><td colspan='".$colCount."'>Koneksi database gagal.</td></tr>";
    } else {
        echo "<tr><td colspan='".$colCount."'>Parameter jenis laporan tidak disediakan.</td></tr>";
    }
}

echo "</tbody></table>"; // Menutup tbody dan tabel HTML
exit; // Pastikan tidak ada output lain setelah ini

?>