<?php
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Detail Data Peserta";
include_once '../layout/header.php';
require_once('../../config.php');

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT users.id_magang, users.username, users.password, users.role, users.divisi, users.status, peserta.* 
FROM users INNER JOIN peserta ON users.id_magang = peserta.id WHERE peserta.id=$id");
?>

<?php while($peserta = mysqli_fetch_array($result)) : ?>
    <div class="page-body">
        <div class="container-xl">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><?= $peserta['nama'] ?></td>
                                </tr>
                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>:</td>
                                    <td><?= $peserta['jenis_kelamin'] ?></td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>:</td>
                                    <td><?= $peserta['alamat'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nomor HP</td>
                                    <td>:</td>
                                    <td><?= $peserta['no_handphone'] ?></td>
                                </tr>
                                <tr>
                                    <td>Universitas</td>
                                    <td>:</td>
                                    <td><?= $peserta['universitas'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nomor Rekening</td>
                                    <td>:</td>
                                    <td><?= $peserta['no_rek'] ?></td>
                                </tr>
                                <tr>
                                    <td>Unit Kerja</td>
                                    <td>:</td>
                                    <td><?= $peserta['divisi'] ?></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>:</td>
                                    <td><?= $peserta['status'] ?></td>
                                </tr>
                                <tr>
                                    <td>NIM</td>
                                    <td>:</td>
                                    <td><?= $peserta['nim'] ?></td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>:</td>
                                    <td><?= $peserta['username'] ?></td>
                                </tr>
                                <tr>
                                    <td>Role</td>
                                    <td>:</td>
                                    <td><?= $peserta['role'] ?></td>
                                </tr>
                                <tr>
                                    <td>Lokasi Presensi</td>
                                    <td>:</td>
                                    <td><?= $peserta['lokasi_presensi'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <img style="width: 480px; height: 480px; object-fit: cover; border-radius: 12px" src="<?= base_url('assets/img/foto_peserta/'.$peserta['foto']) ?>" alt="Foto Peserta">
                </div>

            </div>

        </div>
    </div>
<?php endwhile; ?>

<?php include_once '../layout/footer.php'; ?>