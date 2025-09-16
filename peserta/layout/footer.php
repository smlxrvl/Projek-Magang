<!--  BEGIN FOOTER  -->
<footer class="footer footer-transparent d-print-none">
	<div class="container-xl">
		<div class="row text-center align-items-center flex-row-reverse">
		</div>
	</div>
</footer>
<!--  END FOOTER  -->
  	
<!-- BEGIN PAGE LIBRARIES -->
<script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/jsvectormap.min.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world.js')?>" defer></script>
<script src="<?= base_url('assets/libs/jsvectormap/dist/maps/world-merc.js')?>" defer></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- END PAGE LIBRARIES -->

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ALERT VAL -->
 <?php if(isset($_SESSION['validasi'])): ?>

	<script>
		const Toast = Swal.mixin({
		toast: true,
		position: "bottom-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.onmouseenter = Swal.stopTimer;
			toast.onmouseleave = Swal.resumeTimer;
		}
		});
		Toast.fire({
		icon: "error",
		title: "<?= $_SESSION['validasi'] ?>"
		});
	</script>
	<?php unset($_SESSION['validasi']); ?>

<?php endif; ?>

<!-- ALERT SUCCESS -->
<?php if(isset($_SESSION['berhasil'])): ?>

	<script>
		const Berhasil = Swal.mixin({
		toast: true,
		position: "bottom-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.onmouseenter = Swal.stopTimer;
			toast.onmouseleave = Swal.resumeTimer;
		}
		});
		Berhasil.fire({
		icon: "success",
		title: "<?= $_SESSION['berhasil'] ?>"
		});
	</script>
	<?php unset($_SESSION['berhasil']); ?>

<?php endif; ?>

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

<!-- ALERT GAGAL PRESENSI-->
<?php if(isset($_SESSION['gagal_presensi'])): ?>

	<script>
		Swal.fire({
		icon: "error",
		title: "Oops...",
		text: "<?= $_SESSION['gagal_presensi']; ?>",
		confirmButtonColor: "#ca2e0fff",
		});
	</script>
	<?php unset($_SESSION['gagal_presensi']); ?>

<?php endif; ?>
 
<!-- CONF HAPUS -->
<script>
	$('.hapus-btn').on('click', function() {
		var getLink = $(this).attr('href');
		Swal.fire({
			title: "Hapus data?",
			text: "Data yang dihapus tidak bisa dikembalikan!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "green",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ya, hapus!"
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = getLink;	
			}
		})
		return false;
	});
</script>

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="<?= base_url('assets/js/tabler.js')?>" defer></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->
 
</html>
</body>