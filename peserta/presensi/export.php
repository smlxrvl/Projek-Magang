<?php
    ob_start();
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: ../../auth/login.php?pesan=belum_login');
        exit; // Tambahkan exit
    }elseif($_SESSION['role'] != 'peserta') { // Pastikan role adalah 'peserta'
        header('Location: ../../auth/login.php?pesan=tolak_akses');
        exit; // Tambahkan exit
    }

    $title = "Rekap Presensi Harian"; // Judul ini mungkin perlu disesuaikan jika ini file export umum
    require_once('../../config.php'); // Pastikan koneksi database tersedia

    require('../../assets/vendor/autoload.php');

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    // Import kelas Style yang dibutuhkan
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Font;
    use PhpOffice\PhpSpreadsheet\Style\Border;

    // Array untuk konversi angka bulan ke nama bulan Indonesia
    $nama_bulan_indonesia = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $nama = $_SESSION['nama'];

    // Fungsi helper untuk format tanggal ke Bahasa Indonesia
    function formatTanggalIndonesia($dateString, $nama_bulan_array) {
        if (empty($dateString) || $dateString === '0000-00-00') {
            return '-'; // Atau nilai default lain jika tanggal kosong
        }
        $timestamp = strtotime($dateString);
        $tanggal = date('d', $timestamp);
        $bulan_angka = date('m', $timestamp);
        $tahun = date('Y', $timestamp);
        
        $nama_bulan = $nama_bulan_array[$bulan_angka] ?? '';
        
        return $tanggal . ' ' . $nama_bulan . ' ' . $tahun;
    }


    // Inisialisasi variabel POST dengan null coalescing operator
    // Pastikan ini mengambil dari $_POST karena form export menggunakan POST
    $tanggal_dari = $_POST['tanggal_dari'] ?? null;
    $tanggal_sampai = $_POST['tanggal_sampai'] ?? null;
    $id_peserta = $_POST['id_peserta'] ?? $_SESSION['id']; // Ambil dari POST jika ada, fallback ke SESSION


    // Gunakan Prepared Statement untuk query database utama
    // Semua nilai dinamis harus menjadi placeholder (?)
    $query = "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang
              FROM presensi
              JOIN peserta ON presensi.id_peserta = peserta.id
              WHERE presensi.id_peserta = ? AND tanggal_datang BETWEEN ? AND ?
              ORDER BY tanggal_datang DESC";

    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        // 'iss' = i (integer) untuk id_peserta, ss (string) untuk tanggal_dari dan tanggal_sampai
        // Pastikan $id_peserta adalah integer saat binding
        mysqli_stmt_bind_param($stmt, "iss", $id_peserta, $tanggal_dari, $tanggal_sampai);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        // Tangani error jika prepared statement gagal
        $_SESSION['gagal_presensi'] = "Error saat menyiapkan query database untuk rekap presensi Anda.";
        header('Location: ../rekap_presensi/harian.php'); // Redirect kembali ke halaman laporan harian
        exit;
    }

    // Ambil jam masuk kantor untuk perhitungan keterlambatan
    $jam_datang_ktr_global = '00:00:00'; // Default
    $lokasi_presensi_session = $_SESSION['lokasi_presensi'] ?? ''; // Ambil dari sesi peserta
    if (!empty($lokasi_presensi_session)) {
        $lokasi_query_global = "SELECT jam_masuk FROM lokasi_presensi WHERE nama_lokasi = ?";
        $stmt_lokasi_global = mysqli_prepare($connection, $lokasi_query_global);
        if ($stmt_lokasi_global) {
            mysqli_stmt_bind_param($stmt_lokasi_global, "s", $lokasi_presensi_session);
            mysqli_stmt_execute($stmt_lokasi_global);
            $result_lokasi_global = mysqli_stmt_get_result($stmt_lokasi_global);
            $lokasi_data_global = mysqli_fetch_assoc($result_lokasi_global);
            mysqli_stmt_close($stmt_lokasi_global);
            if ($lokasi_data_global && !empty($lokasi_data_global['jam_masuk'])) {
                $jam_datang_ktr_global = date('H:i:s' , strtotime($lokasi_data_global['jam_masuk']));
            }
        }
    }


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set nilai header pada sel Excel
    // Menggunakan format yang diminta: "Rekap Presensi [Tanggal Dari] sampai [Tanggal Sampai]"
    $sheet->setCellValue('A1', 'Rekap Presensi ' . formatTanggalIndonesia($tanggal_dari, $nama_bulan_indonesia) . ' sampai ' . formatTanggalIndonesia($tanggal_sampai, $nama_bulan_indonesia));
    
    $sheet->setCellValue('A3', 'Nama: ' . $nama);
    // Baris header tabel sekarang dimulai dari A3
    $sheet->setCellValue('A5', 'NO.');
    $sheet->setCellValue('B5', 'TANGGAL DATANG');
    $sheet->setCellValue('C5', 'JAM DATANG');
    $sheet->setCellValue('D5', 'TANGGAL PULANG');
    $sheet->setCellValue('E5', 'JAM PULANG');
    $sheet->setCellValue('F5', 'TOTAL JAM KERJA');
    $sheet->setCellValue('G5', 'KETERLAMBATAN');
    
    // Gabungkan sel untuk judul utama (A1)
    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A3:B3'); // Sesuaikan merge cells karena sekarang hanya sampai kolom G
    // Baris A2 dan A3 yang sebelumnya digabung sekarang dihapus dari mergeCells
    // karena tidak ada konten di sana atau konten sudah digabung ke A1

    // --- Styling untuk Judul Utama (A1) ---
    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 14, // Ukuran font bisa disesuaikan
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFFFF00'], // Kuning (FF adalah alpha, FFFF00 adalah kuning)
        ],
    ]);

    $sheet->getStyle('A3')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12, // Ukuran font bisa disesuaikan
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // --- Styling untuk Header Tabel (A3:G3) ---
    // Dipindahkan dari A5:I5 ke A3:G3, dan disesuaikan range kolomnya
    $sheet->getStyle('A5:G5')->applyFromArray([ // Sesuaikan range kolom
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FF000000'], // Hitam
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFD9E1F2'], // Warna abu-abu terang sebagai contoh
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // Garis hitam tipis
            ],
        ],
    ]);


    // Atur lebar kolom otomatis
    // Sesuaikan range kolom
    foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $no = 1;
    $row = 6; // Baris awal untuk data, karena header tabel sekarang di baris 3

    while($data = mysqli_fetch_array($result)) {
        // Cek apakah sudah absen pulang
        $sudah_pulang = !empty($data['tanggal_pulang']) && $data['tanggal_pulang'] !== '0000-00-00';

        $total_jam_kerja_string = '';
        if ($sudah_pulang) {
            // Perhitungan total jam kerja
            $jam_tanggal_datang = date('Y-m-d H:i:s', strtotime($data['tanggal_datang'] . ' ' . $data['jam_datang']));
            $jam_tanggal_pulang = date('Y-m-d H:i:s', strtotime($data['tanggal_pulang'] . ' ' .  $data['jam_pulang'])) ;

            $timestamp_masuk = strtotime($jam_tanggal_datang);
            $timestamp_pulang = strtotime($jam_tanggal_pulang);

            $selisih = $timestamp_pulang - $timestamp_masuk;
            $ttl_jam_kerja = floor($selisih / 3600);
            $sisa_detik_kerja = $selisih % 3600; // Sisa detik setelah jam
            $selisih_mnt_kerja = floor($sisa_detik_kerja / 60);

            $total_jam_kerja_string = $ttl_jam_kerja . ' jam ' . $selisih_mnt_kerja . ' menit';

        } else {
            // Jika belum absen pulang
            $total_jam_kerja_string = '- Belum Pulang -';
        }

        // Perhitungan keterlambatan
        $keterlambatan_string = '';
        $jam_datang_ktr = '00:00:00'; // Default jika jam masuk kantor tidak ditemukan

        // Gunakan Prepared Statement untuk mengambil jam masuk kantor
        $lokasi_query = "SELECT jam_masuk FROM lokasi_presensi WHERE nama_lokasi = ?";
        $stmt_lokasi = mysqli_prepare($connection, $lokasi_query);
        if ($stmt_lokasi) {
            mysqli_stmt_bind_param($stmt_lokasi, "s", $data['lokasi_presensi']);
            mysqli_stmt_execute($stmt_lokasi);
            $result_lokasi = mysqli_stmt_get_result($stmt_lokasi);
            $lokasi_presensi_data = mysqli_fetch_assoc($result_lokasi);
            mysqli_stmt_close($stmt_lokasi);

            if ($lokasi_presensi_data && !empty($lokasi_presensi_data['jam_masuk'])) {
                $jam_datang_ktr = date('H:i:s' , strtotime($lokasi_presensi_data['jam_masuk']));
            }
        }

        $jam_datang = date('H:i:s', strtotime($data['jam_datang']));
        $timestamp_masuk_real = strtotime($jam_datang);
        $timestamp_jam_datang_ktr = strtotime($jam_datang_ktr);

        $terlambat_detik = $timestamp_masuk_real - $timestamp_jam_datang_ktr;

        if ($terlambat_detik < 0) { // Jika masuk lebih awal atau tepat waktu
            $keterlambatan_string = 'Tepat Waktu';
        } else { // Jika terlambat
            $ttl_jam_terlambat = floor($terlambat_detik / 3600);
            $sisa_detik_terlambat = $terlambat_detik % 3600;
            $selisih_mnt_terlambat = floor($sisa_detik_terlambat / 60);
            $keterlambatan_string = $ttl_jam_terlambat . ' jam ' . $selisih_mnt_terlambat . ' menit';
        }

        // Set nilai data pada sel Excel
        $sheet->setCellValue('A' . $row, $no);
        // Kolom B dan C (Nama dan Kode Magang) dihapus dari tabel peserta
        $sheet->setCellValue('B' . $row, formatTanggalIndonesia($data['tanggal_datang'], $nama_bulan_indonesia)); // Tanggal Datang
        $sheet->setCellValue('C' . $row, $data['jam_datang']); // Jam Datang
        $sheet->setCellValue('D' . $row, ($sudah_pulang ? formatTanggalIndonesia($data['tanggal_pulang'], $nama_bulan_indonesia) : '-')); // Tanggal Pulang
        $sheet->setCellValue('E' . $row, ($sudah_pulang ? $data['jam_pulang'] : '-')); // Jam Pulang
        $sheet->setCellValue('F' . $row, $total_jam_kerja_string); // Total Jam Kerja
        $sheet->setCellValue('G' . $row, $keterlambatan_string); // Keterlambatan
        
        // Tambahkan border dan alignment untuk setiap sel data
        // Sesuaikan range kolom
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $row++;
        $no++;
    }


    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // Ubah nama file agar tanggal diformat juga
    header('Content-Disposition: attachment;filename="rekap_presensi_' . formatTanggalIndonesia($tanggal_dari, $nama_bulan_indonesia) . '_sampai_' . formatTanggalIndonesia($tanggal_sampai, $nama_bulan_indonesia) . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
?>
