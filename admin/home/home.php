<?php
session_start();
if(!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Home";
include_once '../layout/header.php'; 
require_once('../../config.php');

// Tanggal hari ini
$tgl_hari_ini = date('Y-m-d');

// 1. Peserta aktif (hanya role peserta)
$q_aktif = mysqli_query($connection, "SELECT id_magang FROM users WHERE role = 'peserta' AND status = 'Aktif'");
$peserta_aktif = [];
while($row = mysqli_fetch_assoc($q_aktif)) {
    $peserta_aktif[] = $row['id_magang'];
}
$jlh_peserta = count($peserta_aktif);

// 2. Hadir hari ini (presensi)
$q_hadir = mysqli_query($connection, "SELECT id_peserta FROM presensi WHERE tanggal_datang = '$tgl_hari_ini'");
$peserta_hadir = [];
while($row = mysqli_fetch_assoc($q_hadir)) {
    $peserta_hadir[] = $row['id_peserta'];
}
$jlh_hadir = count($peserta_hadir);

// 3. Cuti/Izin/Sakit hari ini (APPROVED)
$q_cis = mysqli_query($connection, "
    SELECT id_peserta FROM ketidakhadiran 
    WHERE tanggal = '$tgl_hari_ini'
    AND keterangan IN ('Cuti', 'Izin', 'Sakit')
    AND status = 'APPROVED'
");
$peserta_cis = [];
while($row = mysqli_fetch_assoc($q_cis)) {
    $peserta_cis[] = $row['id_peserta'];
}
$jlh_cis = count($peserta_cis);

// 4. Alpha hari ini:
// Peserta aktif yang TIDAK presensi dan TIDAK cuti/izin/sakit (APPROVED)
// Tambahkan peserta yang punya pengajuan ketidakhadiran status REJECTED hari ini
$q_rejected = mysqli_query($connection, "
    SELECT id_peserta FROM ketidakhadiran 
    WHERE tanggal = '$tgl_hari_ini' AND status = 'REJECTED'
");
$peserta_rejected = [];
while($row = mysqli_fetch_assoc($q_rejected)) {
    $peserta_rejected[] = $row['id_peserta'];
}

// Alpha = peserta aktif yang tidak hadir dan tidak cuti/izin/sakit (APPROVED), ditambah peserta yang REJECTED
$alpha_ids = array_diff($peserta_aktif, $peserta_hadir, $peserta_cis);
$total_alpha_ids = array_unique(array_merge($alpha_ids, $peserta_rejected));
$jlh_alpha = count($total_alpha_ids);
?>

<!-- BEGIN PAGE BODY -->
		<div class="page-body">
			
			
			<div class="container-xl">
			
				

<div class="row row-deck row-cards">
	<div class="col-sm-12 col-lg-6">
		<div>
	</div>
</div>
	</div>

	<div class="col-12">
		<div class="row row-cards">
			<div class="col-sm-6 col-lg-3">
				

<div class="card card-sm">
	<div class="card-body">
		<div class="row align-items-center">
			
				<div class="col-auto">
					<span class="bg-blue text-white avatar">
						<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>				
				</div>
			


			<div class="col">
				<div class="font-weight-medium">
					Peserta Aktif
				</div>
				
				<div class="text-secondary">
					<?= $jlh_peserta; ?> Peserta
				</div>
			</div>

			
		</div>
	</div>
</div>

			</div>
			<div class="col-sm-6 col-lg-3">
				

<div class="card card-sm">
	<div class="card-body">
		<div class="row align-items-center">
			
				<div class="col-auto">
					<span class="bg-green text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/shopping-cart -->
					<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /><path d="M15 19l2 2l4 -4" /></svg>
				</div>
			


			<div class="col">
				<div class="font-weight-medium">
					Hadir
					
					
				</div>
				<div class="text-secondary">
					<?= $jlh_hadir; ?> Peserta
				</div>
			</div>

			
		</div>
	</div>
</div>

			</div>
			<div class="col-sm-6 col-lg-3">
				

<div class="card card-sm">
	<div class="card-body">
		<div class="row align-items-center">
			
				<div class="col-auto">
					<span class="bg-red text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/brand-x -->
					<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M22 22l-5 -5" /><path d="M17 22l5 -5" /></svg>				
				</div>
			


			<div class="col">
				<div class="font-weight-medium">
					Alpa
					
					
				</div>
				<div class="text-secondary">
					<?= $jlh_alpha; ?> Peserta
				</div>
			</div>

			
		</div>
	</div>
</div>

			</div>
			<div class="col-sm-6 col-lg-3">
				

<div class="card card-sm">
	<div class="card-body">
		<div class="row align-items-center">
			
				<div class="col-auto">
					<span class="bg-orange text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/brand-facebook -->
					<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-heart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h.5" /><path d="M18 22l3.35 -3.284a2.143 2.143 0 0 0 .005 -3.071a2.242 2.242 0 0 0 -3.129 -.006l-.224 .22l-.223 -.22a2.242 2.242 0 0 0 -3.128 -.006a2.143 2.143 0 0 0 -.006 3.071l3.355 3.296z" /></svg>			
				</div>

			<div class="col">
				<div class="font-weight-medium">
					Cuti/Izin/Sakit
					
					
				</div>
				<div class="text-secondary">
					<?= $jlh_cis; ?> Peserta
				</div>
			</div>
		</div>
	</div>
</div>

			</div>
		</div>
	</div>

				</div>

	
	
			<div class="row">
				<div class="col-auto">


	<div class="col-12">
	</div>

		</tbody>
	</table>
</div>

	</div>
			</div>
		</div>
		<!-- END PAGE BODY -->

<?php include_once '../layout/footer.php'; ?>
