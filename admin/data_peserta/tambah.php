<?php
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Tambah Peserta";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['submit'])) {

    $get_code = mysqli_query($connection, "SELECT kode_magang FROM peserta ORDER BY kode_magang DESC LIMIT 1");
    
    if(mysqli_num_rows($get_code) > 0) {
        $data = mysqli_fetch_assoc($get_code);
        $kode_magang_db = $data['kode_magang'];
        $no_baru = (int)$kode_magang_db + 1;
        $kode_magang_baru = str_pad($no_baru, 3, '0', STR_PAD_LEFT);
    } else {
        $kode_magang_baru = '001';
    }

    $kode_magang = $kode_magang_baru;
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
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $no_rek = isset($_POST['no_rek']) ? $_POST['no_rek'] : '';

    if(isset($_FILES['foto'])) {
        $file = $_FILES['foto'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_diretori = '../../assets/img/foto_peserta/' . $nama_file;

        $get_extension = pathinfo($nama_file, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_size = 10 * 1024 * 1024;
        
        move_uploaded_file($file_tmp, $file_diretori);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($nama)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Nama harus diisi!";
        }
        if(empty($jenis_kelamin)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Jenis kelamin harus diisi!";
        }
        if(empty($alamat)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Alamat harus diisi!";
        }
        if(empty($no_handphone)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> No. Handphone harus diisi!";
        }
        if(empty($universitas)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Universitas harus diisi!";
        }
        if(empty($divisi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Unit Kerja harus diisi!";
        }
        if(empty($status)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Status harus diisi!";
        }
        if(empty($no_rek)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> No. Rekening harus diisi!";
        }
        if(empty($nim)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> NIM harus diisi!";
        }
        if(empty($username)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Username harus diisi!";
        }
        

        if(empty($password_raw)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Password harus diisi!";
        }
        if($password_raw !== $confirm_password) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Password tidak cocok!";
        }
        if(empty($role)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Role harus diisi!";
        }
        if(empty($lokasi_presensi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Lokasi presensi harus diisi!";
        }
        if(!in_array(strtolower($get_extension), $allowed_extensions)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Hanya jpg, jpeg, png yang diperbolehkan.";
        }
        if($file_size > $max_size) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Maksimal ukuran file 10 MB.";
        }

        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            $peserta = mysqli_query($connection, "INSERT INTO peserta(kode_magang, nim, nama, jenis_kelamin, alamat, no_handphone, universitas, lokasi_presensi, foto, no_rek) 
            VALUES ('$kode_magang', '$nim', '$nama', '$jenis_kelamin', '$alamat', '$no_handphone', '$universitas', '$lokasi_presensi', '$nama_file', '$no_rek')");
             
            $id_magang = mysqli_insert_id($connection);
            $user = mysqli_query($connection, "INSERT INTO users(id_magang, username, password, status, role, divisi) 
            VALUES ('$id_magang', '$username', '$password', '$status', '$role', '$divisi')");
                
            $_SESSION['berhasil'] = "Data peserta berhasil ditambahkan.";
            header("location: peserta.php");
            exit;
        }
    } 
} 

?>

<div class="page-body">
    <div class="container-xl">
        <form action="<?= base_url('admin/data_peserta/tambah.php')?>" method="POST" enctype="multipart/form-data">
            <div class="row justifty-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                                <!-- <div class="mb-3>
                                    <label for="">Kode Magang</label>
                                    <input type="text" name="kode_magang" class="form-control" value="<?= $kode_magang_baru?>" readonly>
                                    
                                </div> -->

                                <div class="mb-3">
                                    <label for="">Nama</label>
                                    <input type="text" name="nama" class="form-control" value="<?php if(isset($_POST['nama'])) echo $_POST['nama'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option <?php if(isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') {
                                            echo 'selected';
                                            } ?> value="Laki-laki">Laki-laki</option>
                                        <option <?php if(isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') {
                                            echo 'selected';
                                            } ?> value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="<?php if(isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Nomor HP</label>
                                    <input type="text" name="no_handphone" class="form-control" value="<?php if(isset($_POST['no_handphone'])) echo $_POST['no_handphone'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Universitas</label>
                                    <input type="text" name="universitas" class="form-control" value="<?php if(isset($_POST['universitas'])) echo $_POST['universitas'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Unit Kerja</label>
                                    <select name="divisi" class="form-control">
                                        <option value="">--Pilih Unit Kerja--</option>
                                        <?php
                                            $get_divisi = mysqli_query($connection, "SELECT * FROM divisi ORDER BY divisi ASC");
                                            while ($divisi = mysqli_fetch_assoc($get_divisi)) {
                                                $nama_divisi = $divisi['divisi'];

                                                if(isset($_POST['divisi']) && $_POST['divisi'] == $nama_divisi) {
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
                                        <option <?php if(isset($_POST['status']) && $_POST['status'] == 'aktif') {
                                            echo 'selected';
                                            } ?> value="Aktif">Aktif</option>
                                        <option <?php if(isset($_POST['status']) && $_POST['status'] == 'nonaktif') {
                                            echo 'selected';
                                            } ?> value="Nonaktif">Nonaktif</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Nomor Rekening</label>
                                    <input type="text" name="no_rek" class="form-control" value="<?php if(isset($_POST['no_rek'])) echo $_POST['no_rek'] ?>">
                                </div>
                                
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                                <div class="mb-3">
                                    <label for="">NIM</label>
                                    <input type="text" name="nim" class="form-control" value="<?php if(isset($_POST['nim'])) echo $_POST['nim'] ?>">
                                </div>
                        
                                <div class="mb-3">
                                    <label for="">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php if(isset($_POST['username'])) echo $_POST['username'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Password</label>
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
                                        <option <?php if(isset($_POST['role']) && $_POST['role'] == 'admin') {
                                            echo 'selected';
                                            } ?> value="admin">Admin</option>
                                        <option <?php if(isset($_POST['role']) && $_POST['role'] == 'peserta') {
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
                                                if(isset($_POST['lokasi_presensi']) && $_POST['lokasi_presensi'] == $nama_lokasi) {
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
                                    <input type="file" name="foto" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-primary" name="submit">Simpan</button>

                        </div>
                    </div>
                </div>


            </div>
        </form>                           
    </div>
</div>

<?php include_once '../layout/footer.php'; ?>
