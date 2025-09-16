<?php 
session_start();



require_once('../config.php');

if (isset($_POST['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// Query untuk memeriksa kecocokan username dan password
	$query = "SELECT * FROM users JOIN peserta ON users.id_magang = peserta.id WHERE username = '$username'";
	$result = mysqli_query($connection, $query);

	if (mysqli_num_rows($result) === 1) {
		$row = mysqli_fetch_assoc($result);
		
		// Verifikasi password
		if (password_verify($password, $row['password'])){
			if ($row['status'] == 'Aktif') {
				// Set session untuk menyimpan data pengguna
				$_SESSION['login'] = true;
				$_SESSION['id'] = $row['id'];  // peserta.id
				$_SESSION['id_magang'] = $row['id_magang'];  // users.id_magang
				$_SESSION['nama'] = $row['nama'];
				$_SESSION['nim'] = $row['nim'];
				$_SESSION['divisi'] = $row['divisi'];
				$_SESSION['role'] = $row['role'];
				$_SESSION['lokasi_presensi'] = $row['lokasi_presensi'];
				$_SESSION['status'] = $row['status'];
				
				if($row['role'] === 'admin') {
					header('Location: ../admin/home/home.php');
					exit();
				} else {
					header('Location: ../peserta/home/home.php');
					exit();
				}
				exit();
			} else {
				// Status tidak aktif
				$_SESSION['gagal'] = 'Akun tidak aktif, silakan hubungi admin.';
			}
		} else {
			// Password salah
			$_SESSION['gagal'] = 'Password salah, coba lagi.';
		}		
	} else {
		// Username tidak ditemukan
		$_SESSION['gagal'] = 'Username salah, coba lagi.';
	}
}
?>


<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.3.2
* @link https://tabler.io
* Copyright 2018-2025 The Tabler Authors
* Copyright 2018-2025 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->

<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge"/>

	<title>Sign in.</title>

	
		<link rel="icon" href="./favicon-dev.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="./favicon-dev.ico" type="image/x-icon" />
	


	

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="<?= base_url('assets/css/tabler.css?1751425666')?>" rel="stylesheet" />
<!-- END GLOBAL MANDATORY STYLES -->

<!-- BEGIN PLUGINS STYLES -->
<link href="<?= base_url('assets/css/tabler-flags.css?1751425666')?>" rel="stylesheet"/>
	<link href="<?= base_url('assets/css/tabler-socials.css?1751425666')?>" rel="stylesheet"/>
	<link href="<?= base_url('assets/css/tabler-payments.css?1751425666')?>" rel="stylesheet"/>
	<link href="<?= base_url('assets/css/tabler-vendors.css?1751425666')?>" rel="stylesheet"/>
	<link href="<?= base_url('assets/css/tabler-marketing.css?1751425666')?>" rel="stylesheet"/>
	<link href="<?= base_url('assets/css/tabler-themes.css?1751425666')?>" rel="stylesheet"/>
	<!-- END PLUGINS STYLES -->

<!-- BEGIN DEMO STYLES -->
<link href="<?= base_url('assets/css/demo.scss?1751425666')?>" rel="stylesheet"/>
<!-- END DEMO STYLES -->
	

	<!-- BEGIN CUSTOM FONT -->
	<style>
		@import url('https://rsms.me/inter/inter.css');
	</style>
	<!-- END CUSTOM FONT -->
</head>


<body>
	<!-- BEGIN GLOBAL THEME SCRIPT -->
	<script src="<?= base_url('assets/js/tabler-theme.js')?>"></script>
	<!-- END GLOBAL THEME SCRIPT -->

	<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	
<div class="page page-center">
	<div class="container container-normal py-4">
		

		
<div class="row align-items-center g-4">
	<div class="col-lg">
		<div class="container-tight">
			<div class="text-center mb-4">
				<!-- BEGIN NAVBAR LOGO -->
<!-- END NAVBAR LOGO -->
			</div>

<!-- ALERT GAGAL-->
<?php if(isset($_SESSION['gagal'])): ?>

	<script>
		Swal.fire({
		icon: "error",
		title: "Oops...",
		text: "<?= $_SESSION['gagal']; ?>",
		confirmButtonColor: "#ca2e0fff",
		});
	</script>
	<?php unset($_SESSION['gagal']); ?>

<?php endif; ?>

<?php
	if(isset($GET['pesan'])) {
		if($_GET['pesan'] == "belum_login") {
				$_SESSION['gagal'] = 'Silakan login terlebih dahulu!';
		} else if($_GET['pesan'] == "tolak_akses") {
				$_SESSION['gagal'] = 'Akses ditolak.';
		}
	}
?>


<div class="card card-md">
    <div class="card-body">
        <div class="text-center mb-4">
            <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark d-inline-block" style="margin-bottom: 10px;">
                <img src="<?= base_url('assets/img/logo-bsg-small.png')?>" alt="Tabler" style="height:40px; width:auto;">
            </a>
        </div>
        <h2 class="h2 text-center mb-4">Login to your account</h2>
        <form action="" method="POST" autocomplete="off" novalidate>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" autofocus name="username" placeholder="Username" autocomplete="off">
            </div>
            <div class="mb-2">
                <label class="form-label">
                    Password
                </label>
				<div class="input-group input-group-flat">
					<input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off">
					<span class="input-group-text">
						<a class="link-secondary" id="togglePassword" data-bs-toggle="">
							<svg xmlns="http://www.w3.org/2000/svg" id="icon-eye" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
								<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
								<path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
							</svg>
						</a>
					</span>
				</div>
            </div>
            <div class="form-footer">
                <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
            </div>
        </form>
    </div>
</div>	

<!-- BEGIN PAGE LIBRARIES -->
<script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/jsvectormap.min.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js')?>" defer></script>
<!-- END PAGE LIBRARIES -->

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['gagal'])) { ?>
	<script>
		Swal.fire({
		  	icon: "error",
  			title: "Oops...",
  			text: "<?= $_SESSION['gagal']; ?>",
		});
	</script>

	<?php unset($_SESSION['gagal']); ?>

<?php } ?>
<!-- END SWEET ALERT -->

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="<?= base_url('assets/js/tabler.js')?>" defer></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->

<!-- BEGIN DEMO SCRIPTS -->
<script src="<?= base_url('asets/js/demo.js')?>" defer></script>
<!-- END DEMO SCRIPTS -->

 
<!-- BEGIN PAGE SCRIPTS -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById('password');
    const iconEye = document.getElementById('icon-eye');

    togglePassword.addEventListener('click', function (e) {
        e.preventDefault();
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            iconEye.innerHTML = `
                <circle cx="12" cy="12" r="2"/>
                <path d="M22 12c-2.4 4-5.4 6-10 6s-7.6-2-10-6c2.4-4 5.4-6 10-6s7.6 2 10 6"/>
                <path d="M3 3l18 18"/>
            `;
        } else {
            passwordInput.type = "password";
            iconEye.innerHTML = `
                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
            `;
        }
    });
});
</script>

<!-- END PAGE SCRIPTS -->



</body>
</html>