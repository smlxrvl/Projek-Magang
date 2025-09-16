<?php
    ob_start();
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: ../../auth/login.php?pesan=belum_login');
        exit;
    }elseif($_SESSION['role'] != 'admin') {
        header('Location: ../../auth/login.php?pesan=tolak_akses');
        exit;
    }

    $title = "Rekap Presensi Bulanan";
    require_once('../../config.php');

    require('../../assets/vendor/autoload.php');

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    // Import kelas Style yang dibutuhkan
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Font;
    use PhpOffice\PhpSpreadsheet\Style\Border;


    $filter_bln = $_POST['filter_bulan'] ?? null;
    $filter_thn = $_POST['filter_tahun'] ?? null;

    $nama_bulan_indonesia = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $nama_bulan_string = $nama_bulan_indonesia[str_pad($filter_bln, 2, '0', STR_PAD_LEFT)] ?? 'Bulan Tidak Valid';

    $query = "SELECT presensi.*, peserta.nama, peserta.lokasi_presensi, peserta.kode_magang
              FROM presensi
              JOIN peserta ON presensi.id_peserta = peserta.id
              WHERE MONTH(tanggal_datang) = ? AND YEAR(tanggal_datang) = ?
              ORDER BY tanggal_datang DESC";
    
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $filter_bln, $filter_thn);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $_SESSION['gagal'] = "Error saat menyiapkan query database untuk rekap bulanan.";
        header('Location: ../rekap_presensi/bulanan.php');
        exit;
    }


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set nilai header pada sel Excel
    $sheet->setCellValue('A1', 'Rekap Presensi - ' . $nama_bulan_string . ' ' . $filter_thn);
    // Baris A2 dan A3 yang sebelumnya berisi Bulan dan Tahun dihapus karena sudah digabung ke A1
    // Baris header tabel sekarang dimulai dari A3
    $sheet->setCellValue('A3', 'NO.');
    $sheet->setCellValue('B3', 'NAMA');
    $sheet->setCellValue('C3', 'KODE MAGANG');
    $sheet->setCellValue('D3', 'TANGGAL DATANG');
    $sheet->setCellValue('E3', 'JAM DATANG');
    $sheet->setCellValue('F3', 'TANGGAL PULANG');
    $sheet->setCellValue('G3', 'JAM PULANG');
    $sheet->setCellValue('H3', 'TOTAL JAM KERJA');
    $sheet->setCellValue('I3', 'KETERLAMBATAN');
    
    // Gabungkan sel untuk judul utama
    $sheet->mergeCells('A1:I1'); // Sesuaikan merge cells agar mencakup semua kolom header

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

    // --- Styling untuk Header Tabel (A3:I3) ---
    $sheet->getStyle('A3:I3')->applyFromArray([
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
    foreach (range('A', 'I') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $no = 1;
    $row = 4; // Baris awal untuk data, karena header sekarang di baris 3

    while($data = mysqli_fetch_array($result)) {
        $sudah_pulang = !empty($data['tanggal_pulang']) && $data['tanggal_pulang'] !== '0000-00-00';

        $total_jam_kerja_string = '';
        if ($sudah_pulang) {
            $jam_tanggal_datang = date('Y-m-d H:i:s', strtotime($data['tanggal_datang'] . ' ' . $data['jam_datang']));
            $jam_tanggal_pulang = date('Y-m-d H:i:s', strtotime($data['tanggal_pulang'] . ' ' .  $data['jam_pulang'])) ;

            $timestamp_masuk = strtotime($jam_tanggal_datang);
            $timestamp_pulang = strtotime($jam_tanggal_pulang);

            $selisih = $timestamp_pulang - $timestamp_masuk;
            $ttl_jam_kerja = floor($selisih / 3600);
            $sisa_detik_kerja = $selisih % 3600;
            $selisih_mnt_kerja = floor($sisa_detik_kerja / 60);

            $total_jam_kerja_string = $ttl_jam_kerja . ' jam ' . $selisih_mnt_kerja . ' menit';

        } else {
            $total_jam_kerja_string = '- Belum Pulang -';
        }

        $keterlambatan_string = '';
        $jam_datang_ktr = '00:00:00';

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

        if ($terlambat_detik < 0) {
            $keterlambatan_string = 'Tepat Waktu';
        } else {
            $ttl_jam_terlambat = floor($terlambat_detik / 3600);
            $sisa_detik_terlambat = $terlambat_detik % 3600;
            $selisih_mnt_terlambat = floor($sisa_detik_terlambat / 60);
            $keterlambatan_string = $ttl_jam_terlambat . ' jam ' . $selisih_mnt_terlambat . ' menit';
        }

        // Set nilai data pada sel Excel
        $sheet->setCellValue('A' . $row, $no);
        $sheet->setCellValue('B' . $row, $data['nama']);
        $sheet->setCellValue('C' . $row, $data['kode_magang']);
        $sheet->setCellValue('D' . $row, date('d F Y', strtotime($data['tanggal_datang'])));
        $sheet->setCellValue('E' . $row, $data['jam_datang']);
        $sheet->setCellValue('F' . $row, ($sudah_pulang ? date('d F Y', strtotime($data['tanggal_pulang'])) : '-'));
        $sheet->setCellValue('G' . $row, ($sudah_pulang ? $data['jam_pulang'] : '-'));
        $sheet->setCellValue('H' . $row, $total_jam_kerja_string);
        $sheet->setCellValue('I' . $row, $keterlambatan_string);
        
        // Tambahkan border untuk setiap sel data
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            // --- Tambahkan alignment untuk data sel di sini ---
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            // --- Akhir penambahan alignment ---
        ]);

        $row++;
        $no++;
    }


    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="rekap_bulanan_' . $nama_bulan_string . '_' . $filter_thn . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

?>
