<?php 
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header('Location: ../../auth/login.php?pesan=belum_login');
}elseif($_SESSION['role'] != 'peserta') {
	header('Location: ../../auth/login.php?pesan=tolak_akses');
}

$title = "Home";
include_once ('../layout/header.php');
include_once ('../../config.php');

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE nama_lokasi = '$lokasi_presensi'");

if ($lokasi = mysqli_fetch_array($result)) {
	$latitude_kantor = $lokasi['latitude'];
	$longitude_kantor = $lokasi['longitude'];
	$radius = $lokasi['radius'];
	$jam_masuk = $lokasi['jam_masuk'];
	$jam_pulang = $lokasi['jam_pulang'];
}

date_default_timezone_set('Asia/Makassar');
?>

<style>
	.parent_date {
		display: grid;
		grid-template-columns: auto auto auto auto auto;
		font-size: 25px;
		text-align: center;
		justify-content: center;
		font-weight: 300;
	}
	.parent_time {
		display: grid;
		grid-template-columns: auto auto auto auto auto;
		font-size: 30px;
		text-align: center;
		justify-content: center;
		font-weight: bolder;
		gap: 5px;
	}
</style>

<div class="page-body">
	<div class="container-xl">
		<div class="row">
			<div class="col-md-3"></div>
				<div class="col-md-3">
					<div class="card text-center h-100">
						<div class="card-header" style="justify-content: center;">Presensi Datang</div>
						<div class="card-body">

						<?php 
						$id_peserta = $_SESSION['id_magang'];
						$tgl_hari_ini = date('Y-m-d');

						$cek_datang = mysqli_query($connection, "SELECT * FROM presensi WHERE id_peserta = '$id_peserta' AND tanggal_datang = '$tgl_hari_ini'");
					
						?>

						<?php if(mysqli_num_rows($cek_datang) === 0) { ?>
							<div class="parent_date">
								<div id="tanggal_datang"></div>
								<div class="ms-2"></div>
								<div id="bulan_datang"></div>
								<div class="ms-2"></div>
								<div id="tahun_datang"></div>
							</div>
							<div class="parent_time">
								<div id="jam_datang"></div>
								<div>:</div>
								<div id="menit_datang"></div>
								<div>:</div>
								<div id="detik_datang"></div>
							</div>
							<form action="<?= base_url('peserta/presensi/datang.php')?>" method="POST">
								<input type="hidden" name="longitude_peserta" id="longitude_peserta">
								<input type="hidden" name="latitude_peserta" id="latitude_peserta">
								<input type="hidden" name="id_peserta" value="<?= $id_peserta ?>">
								<input type="hidden" value="<?= $latitude_kantor?>" name="latitude_kantor">
								<input type="hidden" value="<?= $longitude_kantor?>" name="longitude_kantor">
								<input type="hidden" value="<?= $radius?>" name="radius">
								<input type="hidden" value="<?= date('Y-m-d')?>" name="tanggal_datang">
								<input type="hidden" value="<?= date('H:i:s')?>" name="jam_datang">
								<button name= "tombol_datang" type="submit" class="btn btn-info bg-success mt-3">Datang</button>
							</form>

							<?php } else{ ?>
								<i class="fa-regular fa-calendar-check fa-4x text-success"></i>
								<h4><br>Anda telah masuk</h4>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card text-center h-100">
						<div class="card-header" style="justify-content: center;">Presensi Pulang</div>
						<div class="card-body">
						<?php
							$id_peserta = $_SESSION['id'];
							$get_data_absen = mysqli_query($connection, "SELECT * FROM presensi WHERE id_peserta = '$id_peserta' AND tanggal_datang = '$tgl_hari_ini'");
							$cur_time = date('H:i:s'); 
							$jumlah_absen = mysqli_num_rows($get_data_absen);
						?>
						<?php 
						// Cek apakah belum absen sama sekali
						if($jumlah_absen == 0 && strtotime($cur_time) >= strtotime($jam_pulang)) { ?>
							<i class="fa-solid fa-clipboard-question fa-4x text-warning"></i>
							<h4><br>Anda belum masuk</h4>
						<?php } elseif(strtotime($cur_time) <= strtotime($jam_pulang)) { ?>
							<i class="fa-solid fa-hourglass-half fa-4x text-warning"></i>
							<h4><br>Belum waktu pulang</h4>
						<?php } else { ?>

							<?php while($cek_pulang = mysqli_fetch_array($get_data_absen)) { ?>

								<?php if(($cek_pulang['tanggal_datang']) && ($cek_pulang['tanggal_pulang'] == '0000-00-00' )) { ?>

								<div class="parent_date">
									<div id="tanggal_pulang"></div>
									<div class="ms-2"></div>
									<div id="bulan_pulang"></div>
									<div class="ms-2"></div>
									<div id="tahun_pulang"></div>
								</div>
								<div class="parent_time">
									<div id="jam_pulang"></div>
									<div>:</div>
									<div id="menit_pulang"></div>
									<div>:</div>
									<div id="detik_pulang"></div>
								</div>
								<form action="<?= base_url('peserta/presensi/pulang.php') ?>" method= "POST">
									<input type="hidden" name="id" value="<?= $cek_pulang['id'] ?>">
									<input type="hidden" name="longitude_peserta" id="longitude_peserta_pulang">
									<input type="hidden" name="latitude_peserta" id="latitude_peserta_pulang">
									<input type="hidden" name="id_peserta" value="<?= $id_peserta ?>">
									<input type="hidden" value="<?= $latitude_kantor?>" name="latitude_kantor">
									<input type="hidden" value="<?= $longitude_kantor?>" name="longitude_kantor">
									<input type="hidden" value="<?= $radius?>" name="radius">
									<input type="hidden" value="<?= date('Y-m-d')?>" name="tanggal_pulang">
									<input type="hidden" value="<?= date('H:i:s')?>" name="jam_pulang">
									<button type="submit" name="tombol_pulang" class="btn btn-info bg-red mt-3">Pulang</button>
								</form>

								<?php }else{ ?>
									<i class="fa-regular fa-calendar-check fa-4x text-success"></i>
									<h4><br>Anda telah keluar</h4>
								<?php } ?>

							<?php } ?>

						<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	</div>
</div>

<script>
	window.setTimeout("waktuDatang()", 1000);
	namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
	function waktuDatang() {
		const waktu = new Date();
		setTimeout(waktuDatang, 1000);
		document.getElementById("tanggal_datang").innerHTML = waktu.getDate();
		document.getElementById("bulan_datang").innerHTML = namaBulan[waktu.getMonth() + 1];
		document.getElementById("tahun_datang").innerHTML = waktu.getFullYear();
		document.getElementById("jam_datang").innerHTML = waktu.getHours();
		document.getElementById("menit_datang").innerHTML = waktu.getMinutes();
		document.getElementById("detik_datang").innerHTML = waktu.getSeconds();
	}

	window.setTimeout("waktuPulang()", 1000);
	namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
	function waktuPulang() {
		const waktu = new Date();
		setTimeout(waktuPulang, 1000);
		document.getElementById("tanggal_pulang").innerHTML = waktu.getDate();
		document.getElementById("bulan_pulang").innerHTML = namaBulan[waktu.getMonth() + 1];
		document.getElementById("tahun_pulang").innerHTML = waktu.getFullYear();
		document.getElementById("jam_pulang").innerHTML = waktu.getHours();
		document.getElementById("menit_pulang").innerHTML = waktu.getMinutes();
		document.getElementById("detik_pulang").innerHTML = waktu.getSeconds();
	}

	getLocation();

	function getLocation() {
		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		}else{
			alert("Browser Anda Tidak Mendukung")
		}
	}

	function showPosition(position) {
		// Untuk form datang
		$('#latitude_peserta').val(position.coords.latitude)
		$('#longitude_peserta').val(position.coords.longitude)
		// Untuk form pulang
		$('#latitude_peserta_pulang').val(position.coords.latitude)
		$('#longitude_peserta_pulang').val(position.coords.longitude)
	}
</script>



				
<?php include_once '../layout/footer.php'; ?>
