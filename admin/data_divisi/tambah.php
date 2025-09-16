<?php 
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Tambah Data Divisi";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['submit'])) {

    $divisi = htmlspecialchars($_POST['divisi']);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($divisi)) {
            $pesan_kesalahan = "Field tidak boleh kosong!";
        }
        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = $pesan_kesalahan;
        } else {
            $result = mysqli_query($connection, "INSERT INTO divisi(divisi) VALUES ('$divisi')");
            $_SESSION['berhasil'] = "Data tersimpan!";
            header("location: divisi.php");
            exit();
        }
    }
}

?>

<!-- BEGIN PAGE BODY -->
                                <div class="page-body">
                                    <div class="container-xl">
                                        
<div class="card col-md-6">
    <div class="card-body">

        <form action="<?= base_url('admin/data_divisi/tambah.php')?>" method="POST">

            <div class="mb-3">
                <label for="">Nama Divisi/Departemen/Unit</label>
                <input type="text" class="form-control" name="divisi">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        </form>

    </div>
</div>                                    </div>	
                                </div>
                            </tbody>
                        </table>
                    </div>
		<!-- END PAGE BODY -->

<?php include_once '../layout/footer.php'; ?>
