<?php

    ob_start();
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: ../../auth/login.php?pesan=belum_login');
    }elseif($_SESSION['role'] != 'peserta') {
        header('Location: ../../auth/login.php?pesan=tolak_akses');
    }

    include_once ('../../config.php');

    $file_foto = $_POST['photo'];
    $id_peserta = $_POST['id'];
    $tanggal_datang = $_POST['tanggal_datang'];
    $jam_datang = $_POST['jam_datang'];
    date_default_timezone_set('Asia/Makassar');

    $foto = $file_foto;
    $foto = str_replace('data:image/jpeg;base64,', '', $foto);
    $foto = str_replace(' ', '+', $foto);
    $data = base64_decode($foto);
    $nama_file = 'foto/' . 'datang-' . date('Ymd-His') . '.png';
    $file = 'datang-' . date('Ymd-His') . '.png';
    file_put_contents($nama_file, $data);

    $result = mysqli_query($connection, "INSERT INTO presensi(id_peserta, tanggal_datang, jam_datang, foto_datang) 
    VALUES ('$id_peserta', '$tanggal_datang', '$jam_datang', '$file')");

    if($result) {
        $_SESSION['berhasil'] = "Presensi datang, berhasil";
    } else {
        $_SESSION['gagal'] = "Presensi datang, gagal";
    }

?>