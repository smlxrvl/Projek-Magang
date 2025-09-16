<?php
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Data Peserta Magang";
include_once '../layout/header.php';
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT users.id_magang, users.username, users.password, users.role, users.divisi, users.status, peserta.* FROM users INNER JOIN peserta ON users.id_magang = peserta.id ORDER BY peserta.id ASC");
?>

<div class="page-body">
	<div class="container-xl">

		<a href="<?= base_url('admin/data_peserta/tambah.php')?>" class="btn btn-primary">
			<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-plus">
				<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
				<path d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
			</svg>
			Tambah Peserta
		</a>
		<table class="table table-bordered mt-3">
			<tr class="text-center">
				<th class="fw-bolder text-capitalize text-black">No.</th>
				<th class="fw-bolder text-capitalize text-black">Kode Magang</th>
				<th class="fw-bolder text-capitalize text-black">NIM</th>
				<th class="fw-bolder text-capitalize text-black">Nama</th>
				<th class="fw-bolder text-capitalize text-black">Username</th>
				<th class="fw-bolder text-capitalize text-black">Unit Kerja</th>
				<th class="fw-bolder text-capitalize text-black">Role</th>
				<th class="fw-bolder text-capitalize text-black">Aksi</th>
			</tr>

			<?php if(mysqli_num_rows($result) === 0) { ?>
				<div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
					<div class="text-center">
						<div class="alert alert-warning d-inline-block px-5 py-4">
							<i class="fa-solid fa-circle-exclamation fa-3x text-warning mb-3"></i>
							<h4 class="text-dark">Tidak ada data peserta</h4>
							<p class="text-muted mb-0">Belum ada data peserta yang tersedia untuk ditampilkan.</p>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<?php $no = 1;
				while($peserta = mysqli_fetch_array($result)) :?>
					<tr>
						<td class="text-center"><?= $no++; ?></td>
						<td class="text-center"><?= $peserta['kode_magang'] ?></td>
						<td class="text-center"><?= $peserta['nim'] ?></td>
						<td class="text-center"><?= $peserta['nama'] ?></td>
						<td class="text-center"><?= $peserta['username'] ?></td>
						<td class="text-center"><?= $peserta['divisi'] ?></td>
						<td class="text-center"><?= $peserta['role'] ?></td>
						<td class="text-center">
							<div class="d-flex justify-content-center gap-2">
								<a href="<?= base_url('admin/data_peserta/detail.php?id=' . $peserta['id'])?>" class="badge badge-pill" style="background-color: #1976d2; color: #fff;">Details</a>
								<a href="<?= base_url('admin/data_peserta/edit.php?id=' . $peserta['id'])?>" class="badge badge-pill bg-primary bg-orange" style="color: #fff;">Edit</a>
								<a href="<?= base_url('admin/data_peserta/hapus.php?id=' . $peserta['id'])?>" class="badge badge-pill bg-danger hapus-btn" style="color: #fff;">Hapus</a>
							</div>
						</td>
					</tr>
				<?php endwhile; ?>
			<?php } ?>
		</table>

	</div>
</div>

<?php include_once '../layout/footer.php'; ?>