<?php 
session_start();
if(!isset($_SESSION['login'])) {
	header("location: ../../auth/login.php?pesan=belum_login");
}elseif($_SESSION['role'] != 'admin') {
	header("location: ../../auth/login.php?pesan=tolak_akses");
}

$title = "Data Divisi";
include_once '../layout/header.php';
require_once('../../config.php');

$result = mysqli_query($connection, "SELECT * FROM divisi ORDER BY id DESC");
?>

<!-- BEGIN PAGE BODY -->
                                <div class="page-body">
                                    <div class="container-xl">
                                        <a href="<?= base_url('admin/data_divisi/tambah.php')?>" class="btn btn-primary">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4.929 4.929a10 10 0 1 1 14.141 14.141a10 10 0 0 1 -14.14 -14.14zm8.071 4.071a1 1 0 1 0 -2 0v2h-2a1 1 0 1 0 0 2h2v2a1 1 0 1 0 2 0v-2h2a1 1 0 1 0 0 -2h-2v-2z" />
                                            </svg>
                                            Tambah Divisi
                                        </a>
                                        
                                        <div class="row row-deck row-cards mt-1">
                                        <table class="table table-bordered">
                                            <tr class="text-center">
                                                <th>No.</th>
                                                <th>Nama Divisi</th>
                                                <th>Aksi</th>
                                            </tr>

                                            <?php if(mysqli_num_rows($result) === 0) : ?>
                                                <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                                                    <div class="text-center">
                                                        <div class="alert alert-warning d-inline-block px-5 py-4">
                                                            <i class="fa-solid fa-circle-exclamation fa-3x text-warning mb-3"></i>
                                                            <h4 class="text-dark">Tidak ada data divisi</h4>
                                                            <p class="text-muted mb-0">Belum ada data divisi yang tersedia untuk ditampilkan.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php else : ?>
                                            <?php $no = 1; 
                                            while($divisi = mysqli_fetch_array($result)) : ?>
                                            
                                            <tr class="text-center">
                                                <td><?= $no++; ?></td>
                                                <td><?= $divisi['divisi']; ?></td>
                                                <td>
                                                <a href="edit.php?id=<?= $divisi['id'] ?>" class="badge bg-primary badge-pill" style="color: #fff;">Edit</a>
                                                <a href="hapus.php?id=<?= $divisi['id'] ?>" class="badge bg-danger badge-pill hapus-btn" style="color: #fff;">Hapus</a>                                                </td>

                                            <?php endwhile; ?>
                                            <?php endif; ?>
                                        </table>

                                            <div class="col-sm-12 col-lg-6">
                                            </div>
                                        </div>
                                    </div>	
                                </div>
                            </tbody>
                        </table>
                    </div>
		<!-- END PAGE BODY -->

<?php include_once '../layout/footer.php'; ?>
