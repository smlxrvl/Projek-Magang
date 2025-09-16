<?php
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Edit Data Peserta";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['edit'])) {

    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $nim = htmlspecialchars($_POST['nim']);
    $username = htmlspecialchars($_POST['username']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_handphone = htmlspecialchars($_POST['no_handphone']);
    $universitas = htmlspecialchars($_POST['universitas']);
    $divisi = htmlspecialchars($_POST['divisi']);
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : '';
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = isset($_POST['lokasi_presensi']) ? htmlspecialchars($_POST['lokasi_presensi']) : '';
    $no_rek = isset($_POST['no_rek']) ? $_POST['no_rek'] : '';

    if(empty($_POST['password'])) {
        $password = $_POST['old_password'];
    } else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if($_FILES['foto_baru']['error'] === 4) {
        $nama_file = $_POST['foto_lama'];
    }else {
        if(isset($_FILES['foto_baru'])) {
            $file = $_FILES['foto_baru'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_diretori = '../../assets/img/foto_peserta/' . $nama_file;

            $get_extension = pathinfo($nama_file, PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'png', 'jpeg'];
            $max_size = 10 * 1024 * 1024;
            
            move_uploaded_file($file_tmp, $file_diretori);
        }        
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($nama)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Nama harus diisi";
        }
        if(empty($jenis_kelamin)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Jenis kelamin harus diisi";
        }
        if(empty($alamat)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Alamat harus diisi";
        }
        if(empty($no_handphone)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> No. Handphone harus diisi";
        }
        if(empty($universitas)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Universitas harus diisi";
        }
        if(empty($divisi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Unit Kerja harus diisi";
        }
        if(empty($status)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Status harus diisi";
        }
        if(empty($no_rek)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> No. Rekening harus diisi!";
        }
        if(empty($nim)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> NIM harus diisi";
        }
        if(empty($username)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Username harus diisi";
        }
        if(empty($role)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Role harus diisi";
        }
        if(empty($lokasi_presensi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Lokasi presensi harus diisi";
        }
        if($_POST['password'] !== $_POST['confirm_password']) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Password tidak cocok";
        }
        if($_FILES['foto_baru']['error'] !== 4) {
            if(!in_array(strtolower($get_extension), $allowed_extensions)) {
                $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Format file tidak valid";
            }
            if($file_size > $max_size) {
                $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Ukuran file terlalu besar";
            }
        }

        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $peserta = mysqli_query($connection, "UPDATE peserta SET 
                nim = '$nim', 
                nama = '$nama', 
                jenis_kelamin = '$jenis_kelamin', 
                alamat = '$alamat', 
                no_handphone = '$no_handphone', 
                universitas = '$universitas', 
                lokasi_presensi = '$lokasi_presensi', 
                foto = '$nama_file',
                no_rek = '$no_rek'
                WHERE id = $id");
             
            $user = mysqli_query($connection, "UPDATE users SET 
                username = '$username',
                password = '$password',
                role = '$role', 
                divisi = '$divisi', 
                status = '$status' 
                WHERE id_magang = $id");
                
            $_SESSION['berhasil'] = "Data peserta berhasil diupdate.";
            header("location: peserta.php");
            exit;
        }
    } 
} 

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT users.id_magang, users.username, users.password, users.role, users.divisi, users.status, peserta.* FROM users INNER JOIN peserta ON users.id_magang = peserta.id WHERE peserta.id = $id");

while($peserta = mysqli_fetch_array($result)) {
    $nama = $peserta['nama'];
    $jenis_kelamin = $peserta['jenis_kelamin'];
    $alamat = $peserta['alamat'];
    $no_handphone = $peserta['no_handphone'];
    $universitas = $peserta['universitas'];
    $divisi = $peserta['divisi'];
    $status = $peserta['status'];
    $nim = $peserta['nim'];
    $username = $peserta['username'];
    $password = $peserta['password'];
    $role = $peserta['role'];
    $lokasi_presensi = $peserta['lokasi_presensi'];
    $foto = $peserta['foto'];
    $no_rek = $peserta['no_rek'];
}

?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?= base_url('admin/data_peserta/edit.php')?>" method="POST" enctype="multipart/form-data">
            <div class="row justifty-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                                <div class="mb-3">
                                    <label for="">Nama</label>
                                    <input type="text" name="nama" class="form-control" value="<?= $nama ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option <?php if($jenis_kelamin == 'Laki-laki') {
                                            echo 'selected';
                                            } ?> value="Laki-laki">Laki-laki</option>
                                        <option <?php if($jenis_kelamin == 'Perempuan') {
                                            echo 'selected';
                                            } ?> value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="<?= $alamat ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Nomor HP</label>
                                    <input type="text" name="no_handphone" class="form-control" value="<?= $no_handphone ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Universitas</label>
                                    <input type="text" name="universitas" class="form-control" value="<?= $universitas ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Unit Kerja</label>
                                    <select name="divisi" class="form-control">
                                        <option value="">--Pilih Unit Kerja--</option>
                                        <?php
                                            $get_divisi = mysqli_query($connection, "SELECT * FROM divisi ORDER BY divisi ASC");
                                            while ($row = mysqli_fetch_assoc($get_divisi)) {
                                                $nama_divisi = $row['divisi'];

                                                if($divisi == $nama_divisi) {
                                                    echo '<option value="' .$nama_divisi . '" selected= "selected">' . $nama_divisi.'</option>';
                                                } else {
                                                    echo '<option value="' . $nama_divisi . '">' . $nama_divisi . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">--Pilih Status--</option>
                                        <option <?php if($status == 'Aktif') {
                                            echo 'selected';
                                            } ?> value="Aktif">Aktif</option>
                                        <option <?php if($status == 'Nonaktif') {
                                            echo 'selected';
                                            } ?> value="Nonaktif">nonaktif</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Nomor Rekening</label>
                                    <input type="text" name="no_rek" class="form-control" value="<?= $no_rek ?>">
                                </div>
                                
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                                <div class="mb-3">
                                    <label for="">NIM</label>
                                    <input type="text" name="nim" class="form-control" value="<?= $nim ?>">
                                </div>
                        
                                <div class="mb-3">
                                    <label for="">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= $username ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Password</label>
                                    <input type="hidden" value="<?= $password; ?>" name="old_password">
                                    <input type="password" name="password" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="">Role</label>
                                    <select name="role" class="form-control">
                                        <option value="">--Pilih Role--</option>
                                        <option <?php if($role == 'admin') {
                                            echo 'selected';
                                            } ?> value="admin">Admin</option>
                                        <option <?php if($role == 'peserta') {
                                            echo 'selected';
                                            } ?> value="peserta">Peserta</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Lokasi Presensi</label>
                                    <select name="lokasi_presensi" class="form-control">
                                        <option value="">--Pilih Lokasi Presensi--</option>
                                        <?php
                                            $get_loc_presensi = mysqli_query($connection, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");
                                            while ($lokasi = mysqli_fetch_assoc($get_loc_presensi)) {
                                                $nama_lokasi = $lokasi['nama_lokasi'];
                                                if($lokasi_presensi == $nama_lokasi) {
                                                    echo '<option value="' .$nama_lokasi . '" selected="selected">' . $nama_lokasi.'</option>';
                                                } else {
                                                    echo '<option value="' . $nama_lokasi . '">' . $nama_lokasi . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Foto</label>
                                    <input type="hidden" name="foto_lama" class="form-control" value="<?= $foto ?>">
                                    <input type="file" name="foto_baru" class="form-control">
                                </div>

                                <input type="hidden" value="<?= $id ?>" name="id">

                                <button type="submit" class="btn btn-primary" name="edit">Update</button>

                        </div>
                    </div>
                </div>


            </div>
        </form>                           
    </div>
</div>

<?php include_once '../layout/footer.php'; ?>
