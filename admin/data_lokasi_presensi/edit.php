<?php 
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Edit Data Lokasi Presensi";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['update'])) {
    $id = $_POST['id'];
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
            $result = mysqli_query($connection, "UPDATE lokasi_presensi SET 
            nama_lokasi='$nama_lokasi',
            alamat_lokasi='$alamat_lokasi',
            tipe_lokasi='$tipe_lokasi',
            latitude='$latitude',
            longitude='$longitude',            
            radius='$radius',
            jam_masuk='$jam_masuk',
            jam_pulang='$jam_pulang'
            WHERE id='$id'");
            $_SESSION['berhasil'] = "Edit data berhasil!";
            header("location: lokasi_presensi.php");
            exit();
        }
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($connection, "SELECT * FROM lokasi_presensi WHERE id=$id");

while($lokasi = mysqli_fetch_array($result)) {
    $nama_lokasi = $lokasi['nama_lokasi'];
    $alamat_lokasi = $lokasi['alamat_lokasi'];
    $tipe_lokasi = $lokasi['tipe_lokasi'];
    $latitude = $lokasi['latitude'];
    $longitude = $lokasi['longitude'];
    $radius = $lokasi['radius'];
    $jam_masuk = $lokasi['jam_masuk'];
    $jam_pulang = $lokasi['jam_pulang'];

}
?>

<!-- BEGIN PAGE BODY -->
                                <div class="page-body">
                                    <div class="container-xl">
                                        
<div class="card col-md-6">
    <div class="card-body">

        <form action="<?= base_url('admin/data_lokasi_presensi/edit.php')?>" method="POST">

            <div class="mb-3">
                <label for="">Nama Lokasi</label>
                <input type="text" class="form-control" name="nama_lokasi" value="<?= $nama_lokasi?>">
            </div>

            <div class="mb-3">
                <label for="">Alamat Lokasi</label>
                <input type="text" class="form-control" name="alamat_lokasi" value="<?= $alamat_lokasi?>">
            </div>

            <div class="mb-3">
                <label for="">Tipe Lokasi</label>
                <select name="tipe_lokasi" class="form-control">
                    <option value="">--Pilih Tipe Lokasi--</option>
                    <option value="Pusat" <?= ($tipe_lokasi == 'Pusat') ? 'selected' : '' ?>>Pusat</option>
                    <option value="Cabang" <?= ($tipe_lokasi == 'Cabang') ? 'selected' : '' ?>>Cabang</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="">Latitude</label>
                <input type="text" class="form-control" name="latitude" value="<?= $latitude?>">
            </div>

            <div class="mb-3">
                <label for="">Longitude</label>
                <input type="text" class="form-control" name="longitude" value="<?= $longitude?>">
            </div>

            <div class="mb-3">
                <label for="">Radius</label>
                <input type="text" class="form-control" name="radius" value="<?= $radius?>">
            </div>

            <div class="mb-3">
                <label for="">Jam Masuk</label>
                <input type="time" class="form-control" name="jam_masuk" value="<?= $jam_masuk?>">
            </div>

            <div class="mb-3">
                <label for="">Jam Pulang</label>
                <input type="time" class="form-control" name="jam_pulang" value="<?= $jam_pulang?>">
            </div>


            <input type="hidden" value=<?= $id?> name="id">
            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </form>

    </div>
</div>                                    </div>	
                                </div>
                            </tbody>
                        </table>
                    </div>
		<!-- END PAGE BODY -->

<?php include_once '../layout/footer.php'; ?>
