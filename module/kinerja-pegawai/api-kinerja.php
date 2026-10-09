<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../conf/conf.php');
if (session_status() === PHP_SESSION_NONE) session_start();

function abort_json($code, $msg) {
  http_response_code($code);
  echo json_encode(['error' => $msg]);
  exit;
}

// 1) Pakai id_user dari session jika sudah ada
$id_user = $_SESSION['id_user'] ?? null;

// 2) Jika belum ada, turunkan dari NIP di session lalu cache ke session
if (!$id_user) {
  $nip = $_SESSION['nip'] ?? null;
  if ($nip) {
    // Jika NIP memang numerik, amankan input (opsional tapi bagus)
    $nip_safe = preg_replace('/\D+/', '', $nip);
    $id_user = getOne("SELECT id_user FROM tm_pegawai WHERE nip='$nip_safe' LIMIT 1");
    if ($id_user) {
      $_SESSION['id_user'] = $id_user; // cache agar request berikutnya tidak query DB lagi
    }
  }
}

// 3) Gagal dapat id_user → 401
if (!$id_user) {
  abort_json(401, 'unauthorized');
}

function toInt($v){ return (int)$v; }

$act = isset($_GET['act']) ? $_GET['act'] : null;

if ($act === 'monthly-summary') {
    $y = toInt($_GET['y'] ?? date('Y'));
    $m = toInt($_GET['m'] ?? date('m'));
    $first = sprintf('%04d-%02d-01', $y, $m);

    $sql = bukaquery("
        SELECT DATE(tt_kinerja.tanggal_kinerja) AS tgl,
               COUNT(*) AS aktivitas,
               SUM(TIMESTAMPDIFF(MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir)) AS menit,
               SUM(ROUND(TIMESTAMPDIFF(MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir)/tm_skp.waktu)) AS volume,
               SUM(CASE WHEN IFNULL(tt_kinerja.validasi,'T')='T' THEN 1 ELSE 0 END) AS belum_validasi,
               SUM(CASE WHEN IFNULL(tt_kinerja.validasi,'T')<>'T' THEN 1 ELSE 0 END) AS disetujui
        FROM tt_kinerja
        INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
        WHERE tt_kinerja.id_user = '$id_user'
          AND tt_kinerja.tanggal_kinerja BETWEEN DATE('$first') AND LAST_DAY(DATE('$first'))
        GROUP BY DATE(tt_kinerja.tanggal_kinerja)
    ");

    $days = [];
    $sumA=0; $sumM=0; $sumB=0; $sumS=0;
    while ($r = fetch_array($sql)) {
        $days[$r['tgl']] = [
            'aktivitas'=>(int)$r['aktivitas'],
            'menit'    =>(int)$r['menit'],
            'volume'   =>(int)$r['volume'],
            'belum'    =>(int)$r['belum_validasi'],
            'setuju'   =>(int)$r['disetujui'],
        ];
        $sumA += (int)$r['aktivitas'];
        $sumM += (int)$r['menit'];
        $sumB += (int)$r['belum_validasi'];
        $sumS += (int)$r['disetujui'];
    }

    echo json_encode([
        'days' => $days,
        'summary' => [
            'total_aktivitas'      => $sumA,
            'total_menit'          => $sumM,
            'total_belum_validasi' => $sumB,
            'total_disetujui'      => $sumS,
        ],
    ]); exit;
}

if ($act === 'day-list') {
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

    $sql = bukaquery("
        SELECT  tt_kinerja.id_kinerja, tt_kinerja.status, tt_kinerja.kd_skp,   -- << penting
        tt_kinerja.uraian, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir,
        tt_kinerja.tanggal_kinerja,
        tm_skp.skp, tm_skp.waktu,
        ROUND(TIMESTAMPDIFF(MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir)) AS menit,
        ROUND(TIMESTAMPDIFF(MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir)/tm_skp.waktu) AS volume
        FROM tt_kinerja
        INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
        WHERE tt_kinerja.id_user='$id_user' AND tt_kinerja.tanggal_kinerja = '$date'
        ORDER BY tt_kinerja.waktu_mulai
    ");

    $items = [];
    while ($r = fetch_array($sql)) {
        $items[] = [
          'id_kinerja'  => $r['id_kinerja'],
          'status'      => $r['status'],
          'kd_skp'      => $r['kd_skp'],           // << kirim ke front-end
          'uraian'      => $r['uraian'],
          'waktu_mulai' => $r['waktu_mulai'],
          'waktu_akhir' => $r['waktu_akhir'],
          'tanggal'     => $r['tanggal_kinerja'],
          'skp'         => $r['skp'],              // nama SKP (buat fallback tulisan)
          'waktu'       => (int)$r['waktu'],
          'menit'       => (int)$r['menit'],
          'volume'      => (int)$r['volume'],
        ];
    }
    echo json_encode(['items'=>$items]); exit;
}

if ($act === 'skp-by-year') {
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d'); // default hari ini
    $year = date('Y', strtotime($tanggal));

    $q = bukaquery("
        SELECT tm_skp.kd_skp, tm_skp.skp, tm_skp.waktu
        FROM tt_skptahunan
        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
        WHERE tt_skptahunan.id_user = '$id_user'
          AND YEAR(tt_skptahunan.tgl_buat) = '$year'
        ORDER BY tm_skp.skp
    ");

    $options = '';
    $found = false;
    while ($r = fetch_array($q)) {
        $found = true;
        $options .= '<option value="'.$r['kd_skp'].'">'.$r['skp'].' ['.$r['waktu'].' Menit]</option>';
    }

    // Kalau tidak ada SKP, tampilkan placeholder khusus
    if (!$found) {
        $options = '<option value="">(SKP '. $year .' belum diinput)</option>';
    } else {
        // Tambahkan opsi default jika ada hasil
        $options = '<option value="">-Pilih Aktifitas-</option>' . $options;
    }

    echo json_encode(['options'=>$options]);
    exit;
}

if ($act === 'monthly-detail') {
    $y = intval($_GET['y'] ?? date('Y'));
    $m = intval($_GET['m'] ?? date('m'));

    $start = "$y-$m-01";
    $end   = date("Y-m-t", strtotime($start));

    $sql = bukaquery("
        SELECT t.*, s.skp, s.waktu
        FROM tt_kinerja t
        LEFT JOIN tm_skp s ON t.kd_skp = s.kd_skp
        WHERE t.id_user='$id_user' 
          AND t.tanggal_kinerja BETWEEN '$start' AND '$end'
        ORDER BY t.tanggal_kinerja, t.waktu_mulai
    ");

    $items = [];
    while($r = fetch_array($sql)){
        $items[] = [
            'tanggal' => $r['tanggal_kinerja'],
            'waktu_mulai' => $r['waktu_mulai'],
            'waktu_akhir'=> $r['waktu_akhir'],
            'status' => $r['status'],
            'skp' => $r['skp'],
            'uraian' => $r['uraian'],
            'menit' => $r['waktu']
        ];
    }

    echo json_encode(['items'=>$items]);
    exit;
}



echo json_encode(['error'=>'unknown action']);
