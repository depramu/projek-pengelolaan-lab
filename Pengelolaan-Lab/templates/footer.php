</div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin <span id="confirmAction"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-primary" id="confirmYes">Ya</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                <a href="<?php
                            $currentFile = basename($_SERVER['PHP_SELF']);
                            if ($currentFile == 'tambahBarang.php' || $currentFile == 'editBarang.php') {
                                echo BASE_URL . '/Menu PIC/manajemenBarang.php';
                            } else if ($currentFile == 'tambahRuangan.php' || $currentFile == 'editRuangan.php') {
                                echo BASE_URL . '/Menu PIC/manajemenRuangan.php';
                            } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Barang/riwayatBarang.php';
                            } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'tambahAkunKry.php' || $currentFile == 'editAkunKry.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunKry.php';
                            } else if ($currentFile == 'tambahAkunMhs.php' || $currentFile == 'editAkunMhs.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunMhs.php';
                            } else if ($currentFile == 'pengajuanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'pengajuanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'pengembalianRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'formDetailRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'formDetailRiwayatRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'pengembalianBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'penolakanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'penolakanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            }
                            ?>"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
            </div>
            <div class="modal-body">
                <p>
                    <?php
                    $currentFile = basename($_SERVER['PHP_SELF']);
                    if ($currentFile == 'tambahBarang.php') {
                        echo 'Data barang berhasil ditambahkan.';
                    } else if ($currentFile == 'editBarang.php') {
                        echo 'Data barang berhasil diperbarui.';
                    } else if ($currentFile == 'tambahRuangan.php') {
                        echo 'Data ruangan berhasil ditambahkan.';
                    } else if ($currentFile == 'editRuangan.php') {
                        echo 'Data ruangan berhasil diperbarui.';
                    } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                        echo 'Data peminjaman barang berhasil ditambahkan.';
                    } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                        echo 'Data peminjaman ruangan berhasil ditambahkan.';
                    } else if ($currentFile == 'editPeminjaman.php') {
                        echo 'Data peminjaman berhasil diperbarui.';
                    } else if ($currentFile == 'tambahPengembalian.php') {
                        echo 'Data pengembalian berhasil ditambahkan.';
                    } else if ($currentFile == 'editPengembalian.php') {
                        echo 'Data pengembalian berhasil diperbarui.';
                    } else if ($currentFile == 'tambahAkunKry.php') {
                        echo 'Data akun karyawan berhasil ditambahkan.';
                    } else if ($currentFile == 'editAkunKry.php') {
                        echo 'Data akun karyawan berhasil diperbarui.';
                    } else if ($currentFile == 'tambahAkunMhs.php') {
                        echo 'Data akun mahasiswa berhasil ditambahkan.';
                    } else if ($currentFile == 'editAkunMhs.php') {
                        echo 'Data akun mahasiswa berhasil diperbarui.';
                    } else if ($currentFile == 'pengajuanBarang.php') {
                        echo 'Peminjaman barang telah disetujui.';
                    } else if ($currentFile == 'pengajuanRuangan.php') {
                        echo 'Peminjaman ruangan telah disetujui.';
                    } else if ($currentFile == 'pengembalianRuangan.php') {
                        echo 'Peminjaman ruangan berhasil dikembalikan.';
                    } else if ($currentFile == 'formDetailRuangan.php') {
                        echo 'Dokumentasi peminjaman ruangan berhasil dikirim.';
                    } else if ($currentFile == 'formDetailRiwayatRuangan.php') {
                        echo 'Dokumentasi peminjaman ruangan berhasil dikirim.';
                    } else if ($currentFile == 'pengembalianBarang.php') {
                        echo 'Data pengembalian barang berhasil ditambahkan.';
                    } else if ($currentFile == 'penolakanBarang.php') {
                        echo 'Peminjaman barang telah ditolak.';
                    } else if ($currentFile == 'penolakanRuangan.php') {
                        echo 'Peminjaman ruangan telah ditolak.';
                    }
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <a href="<?php
                            $currentFile = basename($_SERVER['PHP_SELF']);
                            if ($currentFile == 'tambahBarang.php' || $currentFile == 'editBarang.php') {
                                echo BASE_URL . '/Menu PIC/manajemenBarang.php';
                            } else if ($currentFile == 'tambahRuangan.php' || $currentFile == 'editRuangan.php') {
                                echo BASE_URL . '/Menu PIC/manajemenRuangan.php';
                            } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Barang/riwayatBarang.php';
                            } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'tambahPengembalian.php' || $currentFile == 'editPengembalian.php') {
                                echo BASE_URL . '/Menu PIC/manajemenPengembalian.php';
                            } else if ($currentFile == 'tambahAkunKry.php' || $currentFile == 'editAkunKry.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunKry.php';
                            } else if ($currentFile == 'tambahAkunMhs.php' || $currentFile == 'editAkunMhs.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunMhs.php';
                            } else if ($currentFile == 'pengajuanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'pengajuanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'pengembalianRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'formDetailRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            } else if ($currentFile == 'formDetailRiwayatRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'pengembalianBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'penolakanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'penolakanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            }
                            ?>" class="btn btn-primary">OK</a>
            </div>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel"><i><img src="<?= BASE_URL ?>/icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary ps-4 pe-4">Ya</a>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script>
    // Menyediakan BASE_URL ke main.js menggunakan PHP
    window.BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/../main.js"></script>



<?php if (isset($showModal) && $showModal) : ?>
    <script>
        window.addEventListener('load', function() {
            let modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
<?php endif; ?>

</body>

</html>