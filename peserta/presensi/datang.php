<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" 
    integrity="sha512-dQIiHSl2hr3NWKKLycPndtpbh5iaHLo6MwrXm7F0FM5e+kL2U16oE9uIwPHUl6fQBeCthiEuV/rzP3MiAB8Vfw==" 
    crossorigin="anonymous" 
    referrerpolicy="no-referrer">
</script>
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<style>
    #map { height: 343px; }
</style>

<?php 
    ob_start();
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: ../../auth/login.php?pesan=belum_login');
    }elseif($_SESSION['role'] != 'peserta') {
        header('Location: ../../auth/login.php?pesan=tolak_akses');
    }

    $title = "Absen Datang";
    include_once ('../layout/header.php');
    include_once ('../../config.php');

    if (isset($_POST['tombol_datang'])) {
        $latitude_peserta = $_POST['latitude_peserta'];
        $longitude_peserta = $_POST['longitude_peserta'];
        $latitude_kantor = $_POST['latitude_kantor'];
        $longitude_kantor = $_POST['longitude_kantor'];
        $radius = $_POST['radius'];
        $tanggal_datang = $_POST['tanggal_datang'];
        $jam_datang = $_POST['jam_datang'];
        $id_peserta = $_POST['id_peserta'];
    }

    if (empty($latitude_peserta) || empty($longitude_peserta)) {
        $_SESSION['gagal_presensi'] = "Lokasi anda tidak terdeteksi";
        header("Location: ../home/home.php");
        exit;
    }

    $diff_coord = $longitude_peserta - $longitude_kantor;
    $jarak = sin(deg2rad($latitude_peserta)) * sin(deg2rad($latitude_kantor)) + cos(deg2rad($latitude_peserta)) * cos(deg2rad($latitude_kantor)) * cos(deg2rad($diff_coord));
    $jarak = acos($jarak);
    $jarak = rad2deg($jarak);
    $mil = $jarak * 60 * 1.1515;
    $jarak_km = $mil * 1.609344;
    $jarak_meter = $jarak_km * 1000;
?>

<?php   if($jarak_meter < $radius) { ?>
            <?php
            $_SESSION['gagal_presensi'] = "Anda berada di luar area kantor";
            header("Location: ../home/home.php");
            exit;
            ?>

    <?php } else { ?>

        <div class="page-body">
            <div class="container-xl">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card text-center">
                            <div class="card-body" style="margin: auto;">
                                <input type="hidden" id="id" value="<?= $_SESSION['id']?>">
                                <input type="hidden" id="tanggal_datang" value="<?= $tanggal_datang?>">
                                <input type="hidden" id="jam_datang" value="<?= $jam_datang?>">
                                <div id="my_camera"></div>
                                <div id="my_result"></div>
                                <div class='text-black-50'><?= date('d/m/Y', strtotime($tanggal_datang)) . " - " . $jam_datang?></div>
                                <button id="ambil-foto"class="btn btn-info bg-success mt-2">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script language="JavaScript">
            Webcam.set({
                width: 360,
                height: 280,
                dest_width: 360,
                dest_height: 280,
                image_format: 'jpeg',
                jpeg_quality: 100,
                force_flash: false
            });
            Webcam.attach( '#my_camera' );
            document.getElementById('ambil-foto').addEventListener('click', function() {

                let id = document.getElementById('id').value;
                let tanggal_datang = document.getElementById('tanggal_datang').value;
                let jam_datang = document.getElementById('jam_datang').value;
                Webcam.snap( function(data_uri) {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        document.getElementById('my_result').innerHTML = '<img src="'+data_uri+'"/>';
                        if (xhttp.readyState == 4 && xhttp.status == 200) {
                            window.location.href = "../home/home.php";
                        }
                    };
                    xhttp.open("POST", "datang_action.php", true);
                    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhttp.send(
                        'photo=' + encodeURIComponent(data_uri) +
                        '&id=' + id +
                        '&tanggal_datang=' + tanggal_datang +
                        '&jam_datang=' + jam_datang
                    );
                } );
            } );

        </script>
        <script>
            // map leaFlet js
            let lat_ktr = <?= $latitude_kantor ?>;
            let long_ktr = <?= $longitude_kantor ?>;
            let lat_pst = <?= $latitude_peserta ?>;
            let long_pst = <?= $longitude_peserta ?>;

            var mymap = L.map('map').setView([lat_ktr, long_ktr], 18);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
                             '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                             'Imagery © <a href="http://mapbox.com">Mapbox</a>',
                id: 'mapbox.streets'
            }).addTo(mymap);

            var marker = L.marker([lat_pst, long_pst]).addTo(mymap).bindPopup("Posisi anda.").openPopup();
            var circle = L.circle([lat_ktr, long_ktr], {
                color: 'green',
                fillColor: 'green',
                fillOpacity: 0.5,
                radius: 100
            }).addTo(mymap);
        </script>
        
       <!-- <a href="javascript:void(take_snapshot())">Take Snapshot</a> -->

    <?php } ?> 

<?php include ('../layout/footer.php'); ?>