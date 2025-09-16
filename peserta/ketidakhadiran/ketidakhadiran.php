<?php
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
    header('Location: ../../auth/login.php?pesan=belum_login');
    exit; // Tambahkan exit
}elseif($_SESSION['role'] != 'peserta') {
    header('Location: ../../auth/login.php?pesan=tolak_akses');
    exit; // Tambahkan exit
}

$title = "Ketidakhadiran";
include_once ('../layout/header.php');
include_once ('../../config.php');

$id_peserta_session = $_SESSION['id'] ?? null; // Ambil ID peserta dari sesi, inisialisasi dengan null

// Inisialisasi $result dengan null atau query kosong default
$result = null;

if ($id_peserta_session) {
    // Gunakan Prepared Statement untuk keamanan
    $query_select = "SELECT * FROM ketidakhadiran WHERE id_peserta = ? ORDER BY id DESC";
    $stmt = mysqli_prepare($connection, $query_select);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_peserta_session); // 'i' untuk integer
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        // Tidak perlu mysqli_stmt_close($stmt) di sini jika $result masih akan digunakan
        // dan Anda tidak akan melakukan query lain dengan $stmt yang sama.
    } else {
        // Tangani error jika prepared statement gagal
        $_SESSION['gagal_presensi'] = "Error saat menyiapkan query data ketidakhadiran: " . mysqli_error($connection);
        // Set $result ke query yang tidak mengembalikan baris untuk menghindari error di mysqli_num_rows
        $result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE 1=0");
    }
} else {
    // Jika ID peserta tidak ada di sesi
    $_SESSION['gagal_presensi'] = "ID Peserta tidak ditemukan. Silakan login kembali.";
    // Set $result ke query yang tidak mengembalikan baris
    $result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE 1=0");
}
?>

<div class="page-body">
    <div class="container-xl">
        
        <a href="<?= base_url('peserta/ketidakhadiran/pengajuan.php') ?>" class="btn btn-primary">
            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
            </svg>
            Ajukan Ketidakhadiran
        </a>

        <?php if(mysqli_num_rows($result) === 0) { ?>
            <div class="d-flex justify-content-center align-items-center mt-3" style="min-height: 200px;">
                <div class="text-center">
                    <div class="alert alert-warning d-inline-block px-5 py-4">
                        <i class="fa-solid fa-circle-exclamation fa-3x text-warning mb-3"></i>
                        <h4 class="text-dark">Tidak ada data ketidakhadiran</h4>
                        <p class="text-muted mb-0">Belum ada data ketidakhadiran yang tersedia untuk ditampilkan.</p>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th class="fw-bolder text-capitalize text-black">No.</th>
                            <th class="fw-bolder text-capitalize text-black">Tanggal</th>
                            <th class="fw-bolder text-capitalize text-black">Keterangan</th>
                            <th class="fw-bolder text-capitalize text-black">Deskripsi</th>
                            <th class="fw-bolder text-capitalize text-black">File Bukti</th> <!-- Ubah header kolom -->
                            <th class="fw-bolder text-capitalize text-black">Status</th>
                            <th class="fw-bolder text-capitalize text-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while($data = mysqli_fetch_array($result)) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= htmlspecialchars(date('d F Y', strtotime($data['tanggal']))) ?></td>
                                <td class="text-center"><?= htmlspecialchars($data['keterangan']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($data['deskripsi']) ?></td>
                                <td class="text-center"> <!-- Ubah d-flex ke text-center jika hanya satu item -->
                                    <?php if (!empty($data['surat'])): ?> <!-- Asumsi kolom file bernama 'file' -->
                                        <a target="_blank" href="<?= base_url('assets/file_absen/'. htmlspecialchars($data['surat'])) ?>" class="badge badge-pill bg-blue p-1" style="color: white;">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 11l5 5l5 -5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                            Download
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $badge_class = 'bg-secondary'; // Default
                                    if ($data['status'] == 'PENDING') {
                                        $badge_class = 'bg-warning';
                                    } elseif ($data['status'] == 'APPROVED') {
                                        $badge_class = 'bg-success';
                                    } elseif ($data['status'] == 'REJECTED') {
                                        $badge_class = 'bg-danger';
                                    }
                                    ?>
                                    <span style="color: #fff;" class="p-1 badge <?= $badge_class ?>"><?= htmlspecialchars($data['status']) ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Tombol Edit hanya jika status PENDING -->
                                        <?php if ($data['status'] == 'PENDING'): ?>
                                            <a href="edit.php?id=<?= htmlspecialchars($data['id']) ?>" class="badge badge-pill bg-success p-1" style="color: #fff;">Edit</a>
                                            <a href="hapus.php?id=<?= htmlspecialchars($data['id']) ?>" class="badge badge-pill bg-danger p-1 hapus-btn" style="color: #fff;">Hapus</a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span> <!-- Tampilkan strip jika tidak bisa diedit/hapus -->
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

    </div>
</div>

<?php include_once '../layout/footer.php'; ?>
