<?php
    ob_start();
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: ../../auth/login.php?pesan=belum_login');
        exit; // Tambahkan exit
    }elseif($_SESSION['role'] != 'admin') {
        header('Location: ../../auth/login.php?pesan=tolak_akses');
        exit; // Tambahkan exit
    }

    $title = "Rekap Presensi Bulanan";
    include_once ('../layout/header.php');
    include_once ('../../config.php');

    $filter_bulan = isset($_GET['filter_bulan']) ? $_GET['filter_bulan'] : '';
    $filter_tahun = isset($_GET['filter_tahun']) ? $_GET['filter_tahun'] : '';
    
    // Logika filter yang lebih fleksibel
    if(empty($filter_bulan) && empty($filter_tahun)) {
        // Jika tidak ada filter, tampilkan semua
        $result = mysqli_query($connection, "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang FROM presensi JOIN peserta ON presensi.id_peserta = peserta.id ORDER BY tanggal_datang DESC");
    } elseif(!empty($filter_bulan) && empty($filter_tahun)) {
        // Jika hanya bulan yang diisi, tampilkan semua data dengan bulan yang sama dari tahun manapun
        // Gunakan prepared statement untuk keamanan
        $stmt = mysqli_prepare($connection, "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang FROM presensi JOIN peserta ON presensi.id_peserta = peserta.id WHERE MONTH(tanggal_datang) = ? ORDER BY tanggal_datang DESC");
        mysqli_stmt_bind_param($stmt, "s", $filter_bulan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } elseif(empty($filter_bulan) && !empty($filter_tahun)) {
        // Jika hanya tahun yang diisi, tampilkan semua data dari tahun tersebut
        // Gunakan prepared statement untuk keamanan
        $stmt = mysqli_prepare($connection, "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang FROM presensi JOIN peserta ON presensi.id_peserta = peserta.id WHERE YEAR(tanggal_datang) = ? ORDER BY tanggal_datang DESC");
        mysqli_stmt_bind_param($stmt, "s", $filter_tahun);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        // Jika keduanya diisi, filter dengan kombinasi tahun dan bulan
        // Gunakan prepared statement untuk keamanan
        $stmt = mysqli_prepare($connection, "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang FROM presensi JOIN peserta ON presensi.id_peserta = peserta.id WHERE MONTH(tanggal_datang) = ? AND YEAR(tanggal_datang) = ? ORDER BY tanggal_datang DESC");
        mysqli_stmt_bind_param($stmt, "ss", $filter_bulan, $filter_tahun);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
?>

<div class="page-body">
    <div class="container-xl">

        <?php if(mysqli_num_rows($result) === 0) { ?>
            <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                <div class="text-center">
                    <div class="alert alert-warning d-inline-block px-5 py-4">
                        <i class="fa-solid fa-circle-exclamation fa-3x text-warning mb-3"></i>
                        <h4 class="text-dark">Tidak ada data rekapan</h4>
                        <p class="text-muted mb-3">
                            <?php if(!empty($filter_bulan) || !empty($filter_tahun)) { ?>
                                Tidak ada data presensi untuk filter yang dipilih.
                            <?php } else { ?>
                                Belum ada data presensi yang tersedia untuk ditampilkan.
                            <?php } ?>
                        </p>
                        <?php if(!empty($filter_bulan) || !empty($filter_tahun)) { ?>
                            <div class="mt-3">
                                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-primary">
                                    <i class="fa-solid fa-refresh me-2"></i>
                                    Tampilkan Semua Data
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>

            <div class="row">
                <div class="col-md-2 col-12 mb-3">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#exportModal"> <!-- Ubah ID target modal -->
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M8 11h8v7h-8z" />
                            <path d="M8 15h8" />
                            <path d="M11 11v7" />
                        </svg>
                        <span class="d-none d-md-inline">Export Excel</span>
                        <span class="d-md-none">Export</span>
                    </button>
                </div>
                <div class="col-md-5 offset-md-5">
                    <form method="GET">
                        <div class="input-group">
                            <select name="filter_bulan" class="form-control">
                                <option value="">--Pilih Bulan--</option>
                                <option value="01" <?= ($filter_bulan == '01') ? 'selected' : '' ?>>Januari</option>
                                <option value="02" <?= ($filter_bulan == '02') ? 'selected' : '' ?>>Februari</option>
                                <option value="03" <?= ($filter_bulan == '03') ? 'selected' : '' ?>>Maret</option>
                                <option value="04" <?= ($filter_bulan == '04') ? 'selected' : '' ?>>April</option>
                                <option value="05" <?= ($filter_bulan == '05') ? 'selected' : '' ?>>Mei</option>
                                <option value="06" <?= ($filter_bulan == '06') ? 'selected' : '' ?>>Juni</option>
                                <option value="07" <?= ($filter_bulan == '07') ? 'selected' : '' ?>>Juli</option>
                                <option value="08" <?= ($filter_bulan == '08') ? 'selected' : '' ?>>Agustus</option>
                                <option value="09" <?= ($filter_bulan == '09') ? 'selected' : '' ?>>September</option>
                                <option value="10" <?= ($filter_bulan == '10') ? 'selected' : '' ?>>Oktober</option>
                                <option value="11" <?= ($filter_bulan == '11') ? 'selected' : '' ?>>November</option>
                                <option value="12" <?= ($filter_bulan == '12') ? 'selected' : '' ?>>Desember</option>
                            </select>

                            <select name="filter_tahun" class="form-control">
                                <option value="">--Pilih Tahun--</option>
                                <?php 
                                $tahun_sekarang = date('Y');
                                $tahun_mulai = 2023;
                                $tahun_akhir = $tahun_sekarang;
                                
                                for($tahun = $tahun_mulai; $tahun <= $tahun_akhir; $tahun++) {
                                    $selected = ($filter_tahun == $tahun) ? 'selected' : '';
                                    echo "<option value='$tahun' $selected>$tahun</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead >
                    <tr class="text-center">
                        <th class="fw-bolder text-capitalize text-black">No.</th>
                        <th class="fw-bolder text-capitalize text-black">Nama</th>
                        <th class="fw-bolder text-capitalize text-black">Kantor</th>
                        <th class="fw-bolder text-capitalize text-black">Tanggal</th>
                        <th class="fw-bolder text-capitalize text-black">Jam Datang</th>
                        <th class="fw-bolder text-capitalize text-black">Jam Pulang</th>
                        <th class="fw-bolder text-capitalize text-black">Durasi Kerja</th>
                        <th class="fw-bolder text-capitalize text-black">Keterlambatan/Tepat Waktu</th>
                    </tr>
                </thead>
                <tbody>

            <?php $no = 1; while($rekap = mysqli_fetch_array($result)) : 
                
                // CEK APAKAH SUDAH ABSEN PULANG //
                $sudah_pulang = !empty($rekap['tanggal_pulang']) && $rekap['tanggal_pulang'] != '0000-00-00' && $rekap['tanggal_pulang'] != null;
                
                if ($sudah_pulang) {
                    // TOTAL JAM KERJA //
                    $jam_tanggal_datang = date('Y-m-d H:i:s', strtotime($rekap['tanggal_datang'] . ' ' . $rekap['jam_datang']));
                    $jam_tanggal_pulang = date('Y-m-d H:i:s', strtotime($rekap['tanggal_pulang'] . ' ' .  $rekap['jam_pulang'])) ;

                    $timestamp_masuk = strtotime($jam_tanggal_datang);
                    $timestamp_pulang = strtotime($jam_tanggal_pulang);

                    $selisih = $timestamp_pulang - $timestamp_masuk;
                    $ttl_jam_kerja = floor($selisih / 3600);
                    $sisa_detik_kerja = $selisih % 3600; // Sisa detik setelah jam
                    $selisih_mnt_kerja = floor($sisa_detik_kerja / 60);
                } else {
                    // Jika belum absen pulang
                    $ttl_jam_kerja = 0;
                    $selisih_mnt_kerja = 0;
                }

                // TOTAL JAM TERLAMBAT //

                $lokasi_presensi = $rekap['lokasi_presensi'];
                // Gunakan prepared statement untuk mengambil jam masuk kantor
                $jam_datang_ktr = '00:00:00'; // Default jika jam masuk kantor tidak ditemukan
                $lokasi_query = "SELECT jam_masuk FROM lokasi_presensi WHERE nama_lokasi = ?";
                $stmt_lokasi = mysqli_prepare($connection, $lokasi_query);
                if ($stmt_lokasi) {
                    mysqli_stmt_bind_param($stmt_lokasi, "s", $lokasi_presensi);
                    mysqli_stmt_execute($stmt_lokasi);
                    $result_lokasi = mysqli_stmt_get_result($stmt_lokasi);
                    $lokasi_data = mysqli_fetch_assoc($result_lokasi);
                    mysqli_stmt_close($stmt_lokasi);

                    if ($lokasi_data && !empty($lokasi_data['jam_masuk'])) {
                        $jam_datang_ktr = date('H:i:s' , strtotime($lokasi_data['jam_masuk']));
                    }
                }

                $jam_datang = date('H:i:s', strtotime($rekap['jam_datang']));
                $timestamp_masuk_real = strtotime($jam_datang);
                $timestamp_jam_datang_ktr = strtotime($jam_datang_ktr);

                $terlambat = $timestamp_masuk_real - $timestamp_jam_datang_ktr;
                $ttl_jam_terlambat = floor($terlambat / 3600);
                $sisa_detik_terlambat = $terlambat % 3600;
                $selisih_mnt_terlambat = floor($sisa_detik_terlambat / 60);

            ?>

            <tr class="text-center">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($rekap['nama'])  ?></td>
                <td><?= htmlspecialchars($rekap['lokasi_presensi'])  ?></td>
                <td><?= htmlspecialchars(date('d F Y', strtotime($rekap['tanggal_datang']))) ?></td>
                <td><?= htmlspecialchars($rekap['jam_datang']) ?></td>
                <td>
                    <?php if ($sudah_pulang) : ?>
                        <?= htmlspecialchars($rekap['jam_pulang']) ?>
                    <?php else : ?>
                        <span class="text-warning fw-bolder">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($sudah_pulang) : ?>
                        <?= htmlspecialchars($ttl_jam_kerja . ' Jam ' . $selisih_mnt_kerja . ' Menit ') ?>
                    <?php else : ?>
                        <span class="badge bg-warning text-white">Masih Jam Kerja</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($terlambat < 0) : // Jika terlambat < 0, berarti tepat waktu atau lebih cepat ?>
                        <span class="badge bg-success text-white">Tepat Waktu</span>
                    <?php else : ?>
                        <span class="badge bg-danger text-white">Terlambat <?= htmlspecialchars($ttl_jam_terlambat . ' Jam ' . $selisih_mnt_terlambat . ' Menit ') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Export Excel -->
        <div class="modal" id="exportModal" tabindex="-1"> <!-- Ubah ID modal -->
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Rekap Bulanan To Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="exportForm" method="POST" action="<?= base_url('admin/rekap_presensi/rekap_bulanan_xls.php') ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="export_filter_bulan">Bulan</label>
                                <select name="filter_bulan" id="export_filter_bulan" class="form-control"> <!-- Tambahkan ID -->
                                    <option value="">--Pilih Bulan--</option>
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

                            <div class="mb-3">
                                <label for="export_filter_tahun">Tahun</label>
                                <select name="filter_tahun" id="export_filter_tahun" class="form-control"> <!-- Tambahkan ID -->
                                    <option value="">--Pilih Tahun--</option>
                                    <?php 
                                    $tahun_sekarang = date('Y');
                                    $tahun_mulai = 2023;
                                    $tahun_akhir = $tahun_sekarang;
                                    
                                    for($tahun = $tahun_mulai; $tahun <= $tahun_akhir; $tahun++) {
                                        echo "<option value='$tahun'>$tahun</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" id="exportButton" class="btn btn-primary">Export</button> <!-- Ubah type ke button dan tambahkan ID -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php include_once '../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tangani klik tombol Export di dalam modal
    const exportButton = document.getElementById('exportButton');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            const filterBulan = document.getElementById('export_filter_bulan').value;
            const filterTahun = document.getElementById('export_filter_tahun').value;
            const exportForm = document.getElementById('exportForm');

            if (filterBulan === "" || filterTahun === "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Harap pilih Bulan dan Tahun untuk melakukan export.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            } else {
                // Jika validasi berhasil, submit form
                exportForm.submit();
                // Tutup modal secara manual setelah submit
                const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
                if (exportModal) {
                    exportModal.hide();
                }
            }
        });
    }
});
</script>
