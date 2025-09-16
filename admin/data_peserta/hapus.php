<?php
session_start();
ob_start();
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($connection, "DELETE FROM peserta WHERE id='$id'");

$_SESSION['berhasil'] = "Data berhasil dihapus!";
header("location: peserta.php");
exit();

include_once '../layout/footer.php';
?>