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
    $id_presensi = $_POST['id'];
    $tanggal_pulang = $_POST['tanggal_pulang'];
    $jam_pulang = $_POST['jam_pulang'];
    
    // Debug - hapus setelah testing
    error_log("DEBUG Pulang: id=$id_presensi, tanggal=$tanggal_pulang, jam=$jam_pulang");
    file_put_contents('debug.log', "DEBUG Pulang: id=$id_presensi, tanggal=$tanggal_pulang, jam=$jam_pulang\n", FILE_APPEND);
    
    date_default_timezone_set('Asia/Makassar');

    $foto = $file_foto;
    $foto = str_replace('data:image/jpeg;base64,', '', $foto);
    $foto = str_replace(' ', '+', $foto);
    $data = base64_decode($foto);
    $nama_file = 'foto/' . 'pulang-' . date('Ymd-His') . '.png';
    $file = 'pulang-' . date('Ymd-His') . '.png';
    file_put_contents($nama_file, $data);

    $query = "UPDATE presensi SET tanggal_pulang = '$tanggal_pulang', jam_pulang = '$jam_pulang', foto_pulang = '$file' WHERE id = '$id_presensi'";
    file_put_contents('debug.log', "Query: $query\n", FILE_APPEND);
    
    $result = mysqli_query($connection, $query);

    if($result) {
        file_put_contents('debug.log', "Update berhasil\n", FILE_APPEND);
        $_SESSION['berhasil'] = "Presensi pulang, berhasil";
    } else {
        $error = mysqli_error($connection);
        file_put_contents('debug.log', "Update gagal: $error\n", FILE_APPEND);
        $_SESSION['gagal'] = "Presensi pulang, gagal: $error";
    }

?>
