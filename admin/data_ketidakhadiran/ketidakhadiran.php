<?php

session_start();
if(!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Data Ketidakhadiran";
include_once '../layout/header.php';
require_once('../../config.php');

// JOIN ke tabel peserta untuk ambil nama pengaju
$result = mysqli_query($connection, "
    SELECT k.*, p.nama 
    FROM ketidakhadiran k
    LEFT JOIN peserta p ON k.id_peserta = p.id
    ORDER BY k.id DESC
");

?>

<div class="page-body">
    <div class="container-xl">
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th class="fw-bolder text-capitalize text-black">No.</th>
                    <th class="fw-bolder text-capitalize text-black">Nama Pengaju</th>
                    <th class="fw-bolder text-capitalize text-black">Tanggal</th>
                    <th class="fw-bolder text-capitalize text-black">Keterangan</th>
                    <th class="fw-bolder text-capitalize text-black">Deskripsi</th>
                    <th class="fw-bolder text-capitalize text-black">File Bukti</th>
                    <th class="fw-bolder text-capitalize text-black">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while($data = mysqli_fetch_array($result)) : ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= htmlspecialchars($data['nama']) ?></td>
                        <td class="text-center"><?= htmlspecialchars(date('d F Y', strtotime($data['tanggal']))) ?></td>
                        <td class="text-center"><?= htmlspecialchars($data['keterangan']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($data['deskripsi']) ?></td>
                        <td class="text-center">
                            <?php 
                            if (!empty($data['surat'])): ?>
                                <a target="_blank" href="<?= base_url('assets/file_absen/'. htmlspecialchars($data['surat'])) ?>" class="badge badge-pill bg-blue p-1" style="color: white;">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 11l5 5l5 -5" />
                                        <path d="M12 4l0 12" />
                                    </svg>
                                    Download
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                            if($data['status'] == 'PENDING') : ?>
                                <a class="badge badge-pill bg-warning" style="color: #fff;" href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id'])?>">PENDING</a>
                            <?php elseif($data['status'] == 'REJECTED') : ?>
                                <a class="badge badge-pill bg-danger" style="color: #fff;" href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id'])?>">REJECTED</a>
                            <?php else: ?>
                                <a class="badge badge-pill bg-success" style="color: #fff;" href="<?= base_url('admin/data_ketidakhadiran/detail.php?id=' . $data['id'])?>">APPROVED</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../layout/footer.php'; ?>