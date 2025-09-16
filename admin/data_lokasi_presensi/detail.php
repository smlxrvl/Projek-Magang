<?php
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Detail Lokasi Presensi";
include_once '../layout/header.php';
require_once('../../config.php');
$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE id=$id");
?>

<style>
.table td {
    white-space: nowrap;
}
.table td.alamat-lokasi {
    white-space: normal;
    word-break: break-word;
    max-width: 200px; /* atur sesuai kebutuhan */
}
</style>

<?php while($lokasi = mysqli_fetch_array($result)) : ?>

<div class="page-body">
    <div class="container-xl">

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Nama Lokasi</td>
                                <td>:</td>
                                <td><?= $lokasi['nama_lokasi'] ?></td>
                            </tr>
                            <tr>
                                <td>Alamat Lokasi</td>
                                <td>:</td>
                                <td class="alamat-lokasi"><?= $lokasi['alamat_lokasi'] ?></td>
                            </tr>
                            <tr>
                                <td>Tipe Lokasi</td>
                                <td>:</td>
                                <td><?= $lokasi['tipe_lokasi'] ?></td>
                            </tr>
                            <tr>
                                <td>Latitude</td>
                                <td>:</td>
                                <td><?= $lokasi['latitude'] ?></td>
                            </tr>
                            <tr>
                                <td>Longitude</td>
                                <td>:</td>
                                <td><?= $lokasi['longitude'] ?></td>
                            </tr>
                            <tr>
                                <td>Radius</td>
                                <td>:</td>
                                <td><?= $lokasi['radius'] ?></td>
                            </tr>
                            <tr>
                                <td>Jam Masuk</td>
                                <td>:</td>
                                <td><?= $lokasi['jam_masuk'] ?></td>
                            </tr>
                            <tr>
                                <td>Jam Pulang</td>
                                <td>:</td>
                                <td><?= $lokasi['jam_pulang'] ?></td>
                            </tr>                        
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d249.27923873771357!2d
                            <?= $lokasi['longitude']?>!3d
                            <?= $lokasi['latitude']?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sid!2sid!4v1751940044779!5m2!1sid!2sid" 
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                    </div>
                </div>
            </div>

        </div>

	</div>
</div>

<?php endwhile; ?>
<?php include_once '../layout/footer.php'; ?>