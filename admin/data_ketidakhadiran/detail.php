<?php
ob_start();
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Detail Ketidakhadiran";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['update'])){
    $id = $_POST['id'];
    $status = $_POST['status'];

    $result = mysqli_query($connection, "UPDATE ketidakhadiran SET status = '$status' WHERE id=$id");

    $_SESSION['berhasil'] = "Data pengajuan berhasil diedit.";
    header("location: ketidakhadiran.php");
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id=$id");

while($data = mysqli_fetch_assoc($result)) {
    $keterangan = $data['keterangan'];
    $tanggal = $data['tanggal'];
    $status = $data['status'];
}

?>

<div class="page-body">
	<div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">

            <form action="" method="POST">
                <div class='mb-3'>
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>" readonly>
                </div>

                <div class='mb-3'>
                    <label for="">Keterangan</label>
                    <input type="text" class="form-control" name="tanggal" value="<?= $keterangan ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="">Status</label>
                    <select name="status" class="form-control">
                        <option value="">--Pilih Status--</option>
                        <option <?php if($status=='PENDING') {
                            echo 'selected';
                            } ?> value="PENDING">PENDING</option>
                        <option <?php if($status=='REJECTED') {
                            echo 'selected';
                            } ?> value="REJECTED">REJECTED</option>
                        <option <?php if($status=='APPROVED') {
                            echo 'selected';
                            } ?> value="APPROVED">APPROVED</option>
                    </select>
                </div>

                <input type="hidden" name="id" value="<?= $id ?>">

                <button type="submit" class="btn btn-primary" name="update">Update</button>

                </form>
            </div>
        </div>

    </div>
</div>

<?php include_once '../layout/footer.php'; ?>