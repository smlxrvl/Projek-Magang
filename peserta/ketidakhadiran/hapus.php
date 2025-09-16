<?php

session_start();
ob_start();
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM ketidakhadiran WHERE id='$id'");

$_SESSION['berhasil'] = "Data berhasil dihapus!";
header("location: ketidakhadiran.php");
exit();

include_once '../layout/footer.php';
?>