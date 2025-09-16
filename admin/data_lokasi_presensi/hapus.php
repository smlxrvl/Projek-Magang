<?php

session_start();
ob_start();
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM lokasi_presensi WHERE id='$id'");

$_SESSION['berhasil'] = "Data berhasil dihapus!";
header("location: lokasi_presensi.php");
exit();

include_once '../layout/footer.php';
?>