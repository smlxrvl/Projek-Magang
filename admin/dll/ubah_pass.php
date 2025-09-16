<?php
ob_start();
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Ubah Password";
include_once '../layout/header.php';
require_once('../../config.php');


if(isset($_POST['update'])){
    $id = $_SESSION['id'];
    $pass_baru = password_hash($_POST['pass_baru'], PASSWORD_DEFAULT);
    $ulang_pass_baru = password_hash($_POST['ulang_pass_baru'], PASSWORD_DEFAULT);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($_POST['pass_baru'])) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Password baru kosong";
        }
        if(empty($_POST['ulang_pass_baru'])) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Wajib ulangi password";
        }

        if($_POST['pass_baru'] !== $_POST['ulang_pass_baru']) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Password tidak cocok";
        }
        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $peserta = mysqli_query($connection, "UPDATE users SET 
                password = '$pass_baru'
                WHERE id_magang = $id");
                            
            $_SESSION['berhasil'] = "Password berhasil diubah";
            header("location: ../home/home.php");
            exit;
        }
    } 
}
?>


<div class="page-body">
	<div class="container-xl">

        <form action="" method="POST">

            <div class="card col-md-6">
                <div class="card-body">
                <div class="mb-3">
                    <label for="">Password Baru</label>
                    <input type="password" name="pass_baru" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="">Ulangi Password Baru</label>
                    <input type="password" name="ulang_pass_baru" class="form-control">
                </div>
                    <input type="hidden" name="id" value="<?= $_SESSION['id'] ?>">
                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                </div>
            </div>
        </form>

	</div>
</div>

<?php include_once '../layout/footer.php'; ?>