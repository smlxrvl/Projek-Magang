<?php 
global $title;
require_once('../../config.php') 
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

	<title><?= $title?></title>

	
		<link rel="icon" href="./favicon-dev.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="./favicon-dev.ico" type="image/x-icon" />
	


	<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?= base_url('assets/libs/jsvectormap/dist/jsvectormap.css?1751425666')?>" rel="stylesheet"/>
<!-- END PAGE LEVEL STYLES -->


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
	
<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- FONT AWESOME -->


	<!-- BEGIN CUSTOM FONT -->
	<style>
		@import url('https://rsms.me/inter/inter.css');
	</style>
	<!-- END CUSTOM FONT -->
</head>


<body>
	<!-- BEGIN GLOBAL THEME SCRIPT -->
	<script src="assets/js/tabler-theme.js"></script>
	<!-- END GLOBAL THEME SCRIPT -->
<div class="page">
		<!-- BEGIN NAVBAR  -->
	<header class="navbar navbar-expand-md d-print-none" >
		<div class="container-xl">
			<!-- BEGIN NAVBAR TOGGLER -->
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
</button>
<!-- END NAVBAR TOGGLER -->
				<!-- BEGIN NAVBAR LOGO --><div  class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
	<a href="." aria-label="BSG">
        <img src="<?= base_url('assets/img/logo-bsg.png')?>" width="110" height="32" alt="BSG Logo" class="navbar-brand-image">
</a>
</div><!-- END NAVBAR LOGO -->
			

			

<?php
$id = $_SESSION['id_magang'];
$q = mysqli_query($connection, "SELECT foto FROM peserta WHERE id = '$id'");
$foto = 'default.png';
if($row = mysqli_fetch_assoc($q)) {
    $foto = $row['foto'] ?: 'default.png';
}
?>
<div class="navbar-nav flex-row order-md-last">
    <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
            <img src="<?= base_url('assets/img/foto_peserta/' . $foto) ?>"
                 alt="Foto Profil"
                 style="width: 32px; height: 32px; object-fit: cover; border-radius: 100%;">
            <div class="d-none d-xl-block ps-2">
                <div><?= $_SESSION['nama'] ?></div>
                <div class="mt-1 small text-secondary"><?= $_SESSION['divisi'] ?></div>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="<?= base_url('peserta/dll/profile.php') ?>" class="dropdown-item">Profile</a>
            <a href="<?= base_url('peserta/dll/ubah_pass.php') ?>" class="dropdown-item">Ubah Password</a>
            <a href="<?= base_url('auth/logout.php')?>" class="dropdown-item">Logout</a>
        </div>
    </div>
</div>


			
		</div>
	</header>

	
	<header class="navbar-expand-md">
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="navbar">
				<div class="container-xl">
					<div class="row flex-column flex-md-row flex-fill align-items-center">
						<div class="col">
							<!-- BEGIN NAVBAR MENU -->






<ul class="navbar-nav">
	
		
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url('peserta/home/home.php')?>" >
				<span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg></span>
				<span class="nav-link-title">
					Home
				</span>
			</a>
		</li>
		
		<li class="nav-item">
			<a class="nav-link" href="<?= base_url('peserta/presensi/rekap_presensi.php') ?>" >
				<span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
				<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-checklist"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8" /><path d="M14 19l2 2l4 -4" /><path d="M9 8h4" /><path d="M9 12h2" /></svg></span>
				<span class="nav-link-title">
					Rekap Presensi
				</span>
			</a>
		</li>

		
<li class="nav-item">
    <a class="nav-link" href="<?= base_url('peserta/ketidakhadiran/ketidakhadiran.php') ?>">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <!-- Download SVG icon from http://tabler.io/icons/icon/user-x -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-x">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                <path d="M22 22l-5 -5" />
                <path d="M17 22l5 -5" />
            </svg>
        </span>
        <span class="nav-link-title">
            Ketidakhadiran
        </span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="<?= base_url('auth/logout.php')?>">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <!-- Download SVG icon from http://tabler.io/icons/icon/logout -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                <path d="M9 12h12l-3 -3" />
                <path d="M18 15l3 -3" />
            </svg>
        </span>
        <span class="nav-link-title">
            Logout
        </span>
    </a>
</li></ul>
<!-- END NAVBAR MENU -->
						</div>	
					</div>
				</div>
			</div>
		</div>
	</header>
	


		<!-- END NAVBAR  -->
	

	<div class="page-wrapper">
		<!-- BEGIN PAGE HEADER -->








<div class="page-header d-print-none" aria-label="Page header">
	<div class="container-xl">
		<div class="row g-2 align-items-center">
			<div class="col">
				
				<!-- Page pre-title -->				
				<h2 class="page-title">
					<?= $title ?>
				</h2>

				
			</div>

			
	</div>
</div>

<!-- END PAGE HEADER -->