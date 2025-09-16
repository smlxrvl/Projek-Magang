<?php
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "";
include_once '../layout/header.php';
require_once('../../config.php');

$id = $_SESSION['id'];
$result = mysqli_query($connection, "SELECT users.id_magang, users.username, users.role, users.divisi, users.status, peserta.* FROM users INNER JOIN peserta ON users.id_magang = peserta.id WHERE peserta.id=$id");
?>

<?php while($peserta = mysqli_fetch_array($result)): ?>

<div class="page-body">
	<div class="container-xl">

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-4 mt-1">
                            <img 
                                src="<?= base_url('assets/img/foto_peserta/' .$peserta['foto']) ?>" 
                                alt="" 
                                style="width: 210px; height: 210px; object-fit: cover; border-radius: 100%;"
                            />
                        </div>
                        <table class="table">
                            <tr>
                                <td>Nama</td>
                                <td>: <?= $peserta['nama'] ?></td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>: <?= $peserta['username'] ?></td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td>: <?= $peserta['jenis_kelamin'] ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: <?= $peserta['alamat'] ?></td>
                            </tr>
                            <tr>
                                <td>No. HP</td>
                                <td>: <?= $peserta['no_handphone'] ?></td>
                            </tr>
                            <tr>
                                <td>Asal Institut</td>
                                <td>: <?= $peserta['universitas'] ?></td>
                            </tr>
                            <tr>
                                <td>Nomor Rekening</td>
                                <td>: <?= $peserta['no_rek'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4"></div>
        </div>

	</div>
</div>

<?php endwhile ?>
<?php include_once '../layout/footer.php'; ?>