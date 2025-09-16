<?php
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Tambah Lokasi Presensi";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['submit'])) {
    $nama_lokasi = htmlspecialchars($_POST['nama_lokasi']);
    $alamat_lokasi = htmlspecialchars($_POST['alamat_lokasi']);
    $tipe_lokasi = htmlspecialchars($_POST['tipe_lokasi']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $radius = htmlspecialchars($_POST['radius']);
    $jam_masuk = htmlspecialchars($_POST['jam_masuk']);
    $jam_pulang = htmlspecialchars($_POST['jam_pulang']);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($nama_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Nama lokasi harus diisi!";
        }
        if(empty($alamat_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Alamat lokasi harus diisi!";
        }
                if(empty($tipe_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Tipe lokasi harus diisi!";
        }
                if(empty($latitude)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Latitude harus diisi!";
        }
        if(empty($latitude)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Altitude harus diisi!";
        }
            if(empty($radius)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Radius harus diisi!";
        }
        if(empty($jam_masuk)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Jam masuk harus diisi!";
        }
        if(empty($jam_pulang)) {
            $pesan_kesalahan[] = "<i class='fa-regular fa-circle-dot'></i> Jam pulang harus diisi!";
        }

        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $result = mysqli_query($connection, "INSERT INTO lokasi_presensi(nama_lokasi, alamat_lokasi, tipe_lokasi, latitude, longitude, radius, jam_masuk, jam_pulang) 
            VALUES ('$nama_lokasi', '$alamat_lokasi', '$tipe_lokasi', '$latitude', '$longitude', '$radius', '$jam_masuk', '$jam_pulang')");
                
            $_SESSION['berhasil'] = "Data lokasi presensi berhasil ditambahkan.";
            header("location: lokasi_presensi.php");
            exit;
        }
    } 
} 

?>

<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">
                <form action="<?= base_url('admin/data_lokasi_presensi/tambah.php')?>" method="POST">
                    <div class="mb-3">
                        <label for="">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" value="<?php if(isset($_POST['nama_lokasi'])) echo $_POST['nama_lokasi'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Alamat Lokasi</label>
                        <input type="text" name="alamat_lokasi" class="form-control" value="<?php if(isset($_POST['alamat_lokasi'])) echo $_POST['alamat_lokasi'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Tipe Lokasi</label>
                        <select name="tipe_lokasi" class="form-control">
                            <option value="">--Pilih Tipe Lokasi--</option>
                            <option <?php if(isset($_POST['tipe_lokasi']) && $_POST['tipe_lokasi'] == 'Pusat') {
                                echo 'selected';
                                } ?> value="Pusat">Pusat</option>
                            <option <?php if(isset($_POST['tipe_lokasi']) && $_POST['tipe_lokasi'] == 'Cabang') {
                                echo 'selected';
                                } ?> value="Cabang">Cabang</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="<?php if(isset($_POST['latitude'])) echo $_POST['latitude'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="<?php if(isset($_POST['longitude'])) echo $_POST['longitude'] ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="">Radius</label>
                        <input type="text" name="radius" class="form-control" value="<?php if(isset($_POST['radius'])) echo $_POST['radius']?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="<?php if(isset($_POST['jam_masuk'])) echo $_POST['jam_masuk']?>">
                    </div>

                    <div class="mb-3">
                        <label for="">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" value="<?php if(isset($_POST['jam_pulang'])) echo $_POST['jam_pulang']?>">
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include_once '../layout/footer.php'; ?>
