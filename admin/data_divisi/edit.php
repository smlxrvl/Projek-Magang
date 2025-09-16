<?php 
session_start();
ob_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Edit Data Divisi";
include_once '../layout/header.php';
require_once('../../config.php');

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $divisi = htmlspecialchars($_POST['divisi']);

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(empty($divisi)) {
            $pesan_kesalahan = "Field tidak boleh kosong!";
        }
        if(!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = $pesan_kesalahan;
        } else {
            $result = mysqli_query($connection, "UPDATE divisi SET divisi='$divisi' WHERE id='$id'");
            $_SESSION['berhasil'] = "Edit data berhasil!";
            header("location: divisi.php");
            exit();
        }
    }
}

//$id = $_GET['id'];
$id = isset($_GET['id']) ? ($_GET['id']) : $_POST['id'];
$result = mysqli_query($connection, "SELECT * FROM divisi WHERE id='$id'");

while($divisi = mysqli_fetch_array($result)) {
    $nama_divisi = $divisi['divisi'];
}
?>

<!-- BEGIN PAGE BODY -->
                                <div class="page-body">
                                    <div class="container-xl">
                                        
<div class="card col-md-6">
    <div class="card-body">

        <form action="<?= base_url('admin/data_divisi/edit.php')?>" method="POST">

            <div class="mb-3">
                <label for="">Nama Divisi/Departemen/Unit</label>
                <input type="text" class="form-control" name="divisi" value="<?= $nama_divisi?>">
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
