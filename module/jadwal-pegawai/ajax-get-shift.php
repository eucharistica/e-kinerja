<?php
// MATIKAN output error/notice untuk endpoint JSON ini
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

header('Content-Type: application/json; charset=utf-8');

require_once('../../conf/conf.php');

// JAGA-JAGA: bersihkan output buffer kalau ada echo/notice yang terlanjur keluar
if (ob_get_length()) { ob_clean(); }

$id_unit = $_GET['id_unit'] ?? '';
if ($id_unit === '') { echo json_encode(['ok'=>false,'html'=>'']); exit; }

$opt = '';
$q = bukaquery("SELECT ts.id_absensi, ts.nama_shift, ts.jam_masuk, ts.jam_pulang
                FROM tm_shift ts 
                INNER JOIN set_shift ss ON ss.id_absensi = ts.id_absensi
                WHERE ts.status='1'
                GROUP BY ts.id_absensi
                ORDER BY ts.nama_shift DESC");
while($r = fetch_array($q)){
  $opt .= "<option value='{$r['id_absensi']}'>".$r['nama_shift']." (".$r['jam_masuk']." - ".$r['jam_pulang'].")</option>";
}

echo json_encode(['ok'=>true,'html'=>$opt], JSON_UNESCAPED_UNICODE);
exit;
