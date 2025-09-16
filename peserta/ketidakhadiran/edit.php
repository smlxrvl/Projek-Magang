<?php
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header('Location: ../../auth/login.php?pesan=belum_login');
}elseif($_SESSION['role'] != 'peserta') {
	header('Location: ../../auth/login.php?pesan=tolak_akses');
}

$title = "Edit Pengajuan";
include_once ('../layout/header.php');
include_once ('../../config.php');

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    // Ganti 'file_baru' menjadi 'surat_baru'
    if(isset($_FILES['surat_baru']) && $_FILES['surat_baru']['error'] === 4){
        $nama_file = $_POST['surat'];
    } else if(isset($_FILES['surat_baru'])) {
        $file = $_FILES['surat_baru'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_diretori = '../../assets/file_absen/' . $nama_file;

        $get_extension = pathinfo($nama_file, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $max_size = 10 * 1024 * 1024;
        
        move_uploaded_file($file_tmp, $file_diretori);
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($keterangan)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Keterangan harus diisi!";
        }
        if(empty($deskripsi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Deskripsi harus diisi!";
        }
        if(empty($tanggal)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Tanggal harus diisi!";
        }
        if(isset($_FILES['surat_baru']) && $_FILES['surat_baru']['error'] !== 4){
            if(!in_array(strtolower($get_extension), $allowed_extensions)) {
                $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Hanya jpg, jpeg, png, dan pdf yang diperbolehkan.";
            }
        if($file_size > $max_size) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Maksimal ukuran file 10 MB.";
        }
    }

        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $result = mysqli_query($connection, "UPDATE ketidakhadiran SET keterangan='$keterangan', deskripsi='$deskripsi', tanggal='$tanggal', surat='$nama_file' WHERE id=$id");
                             
            $_SESSION['berhasil'] = "Data ketidakhadiran berhasil diedit.";
            header("location: ketidakhadiran.php");
            exit;
        }
    }
}

$id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM ketidakhadiran WHERE id = '$id'");
while($data = mysqli_fetch_assoc($result)) {
    $keterangan = $data['keterangan'];
    $tanggal = $data['tanggal'];
    $deskripsi = $data['deskripsi'];
    $surat = $data['surat'];
}
?>

<div class="page-body">
    <div class="container-xl">
        
    <div class="card col-md-6">
        <div class="card-body">
            <form action="" method='POST' enctype="multipart/form-data">
                <input type="hidden" value="<?= $_SESSION['id'] ?>" name="id_peserta">

                <div class="mb-3">
                    <label for="">Keterangan</label>
                    <select name="keterangan" class="form-control">
                        <option value="">--Pilih Keterangan--</option>
                        <option <?php if($keterangan=='Sakit') {
                            echo 'selected';
                            } ?> value="Sakit">Sakit</option>
                        <option <?php if($keterangan=='Izin') {
                            echo 'selected';
                            } ?> value="Izin">Izin</option>
                        <option <?php if($keterangan=='Cuti') {
                            echo 'selected';
                            } ?> value="Cuti">Cuti</option>
                    </select>
                </div>

                <div class='mb-3'>
                    <label for="">Deskripsi</label>
                    <textarea  class="form-control" name="deskripsi" cols="30" rows="3"><?= $deskripsi ?></textarea>
                </div>

                <div class='mb-3'>
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>">
                </div>

                <div class='mb-3'>
                    <label for="">Surat Keterangan</label>
                    <input type="file" class="form-control" name="surat_baru">
                    <input type="hidden" class="form-control" name="surat" value="<?= $surat ?>">
                </div>

                <input type="hidden" name="id" value='<?= $_GET['id'] ?>'>

                <button type="submit" class="btn btn-primary" name="update">Update</button>
            </form>
        </div>
    </div>

    </div>
</div>





<?php include_once '../layout/footer.php'; ?>