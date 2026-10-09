<?php
// ====== START: cegah output nyasar & sembunyikan warning di JSON ======
ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
// =====================================================================

require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');

function is_ajax(){
  return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest')
         || (isset($_POST['ajax']) && $_POST['ajax']=='1');
}
function respond_json($payload){
  if (ob_get_length() !== false) { ob_clean(); } // bersihkan HTML nyasar
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload);
  exit;
}
function rq($key, $def=null){ return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $def; }

// ----- ambil route dari URL terenkripsi ATAU dari query biasa/POST -----
$URI  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$url  = decode($URI);
$page = isset($url['module']) ? $url['module'] : rq('module');
$action = isset($url['act']) ? $url['act'] : rq('act');
$id   = isset($url['id']) ? $url['id'] : rq('id');

// setting validasi penginputan (biarkan spt semula)
$tutup_kinerja = getOne("SELECT setup.validasi_jadwal FROM setup");

// (opsional) jika AJAX tapi rute tidak dikenali, beri pesan jelas
if (is_ajax() && !in_array($action, ['add-jadwal-pegawai','update-jadwal'])) {
  respond_json(['ok'=>false, 'message'=>'Route tidak dikenali', 'debug'=>['module'=>$page,'act'=>$action]]);
}

// ======================================================================
// ADD  (UP-SERT: kalau sudah ada → UPDATE; kalau belum → INSERT)
// ======================================================================
if ($page=='jadwal-pegawai' && $action=='add-jadwal-pegawai') {

    // --- Validasi minimal field wajib dari AJAX ---
    $required = ['id_user','id_unit','nip','tanggal_jadwal','id_absensi'];
    foreach ($required as $k) {
        if (!isset($_POST[$k]) || $_POST[$k]==='') {
            if (is_ajax()) respond_json(['ok'=>false,'message'=>"Field '$k' wajib diisi."]);
            header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'));
            exit;
        }
    }

    // Normalisasi tanggal
    $tanggalRaw = $_POST['tanggal_jadwal'];
    $tanggal    = FormatTgl('Y-m-d', $tanggalRaw);
    if (!$tanggal) { $tanggal = $tanggalRaw; }

    // Cek apakah sudah ada jadwal untuk (nip, tanggal)
    $existingId = getOne("SELECT id_jadwal FROM tm_jadwal 
                          WHERE nip_pegawai='{$_POST['nip']}' 
                            AND tanggal_jadwal='$tanggal' 
                          LIMIT 1");

    if ($existingId) {
        // ---- UPDATE SAJA ----
        $ok = bukainput("UPDATE tm_jadwal 
                         SET id_absensi='{$_POST['id_absensi']}', 
                             id_unit='{$_POST['id_unit']}',
                             date=NOW()
                         WHERE id_jadwal='$existingId'");
        if (!$ok) {
            if (is_ajax()) respond_json(['ok'=>false,'message'=>'Gagal menyimpan jadwal (UPDATE existing).']);
            header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'));
            exit;
        }

        // Ambil properti shift untuk repaint sel
        $s = fetch_array(bukaquery("SELECT kode_shift,
            CASE 
              WHEN color='A' THEN 'red-gradient'
              WHEN color='B' THEN 'aqua-gradient'
              WHEN color='C' THEN 'blue'
              WHEN color='D' THEN 'red'
              WHEN color='E' THEN 'green'
              WHEN color='F' THEN 'pink'
              WHEN color='G' THEN 'purple'
              WHEN color='H' THEN 'orange'
              WHEN color='I' THEN 'Navy'
              WHEN color='J' THEN 'teal'
              WHEN color='K' THEN 'gray'
              WHEN color='L' THEN 'purple-gradient'
              WHEN color='M' THEN 'blue-gradient'
              WHEN color='N' THEN 'black'
              ELSE 'white' END AS color_css
            FROM tm_shift WHERE id_absensi='{$_POST['id_absensi']}'"));

        if (is_ajax()){
            respond_json(['ok'=>true,'data'=>[
                'id_jadwal'  => $existingId,                 // ← pakai ID lama
                'id_absensi' => $_POST['id_absensi'],
                'kode_shift' => $s['kode_shift'] ?? '',
                'color_css'  => $s['color_css'] ?? 'white'
            ]]);
        }

        header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'));
        exit;

    } else {
        // ---- INSERT BARU ----
        $autonumber = nokiamat('id_jadwal', 'tm_jadwal');

        $ok = bukainput("INSERT INTO tm_jadwal SET 
            id_jadwal      ='$autonumber',
            id_user        ='{$_POST['id_user']}',
            tanggal_jadwal ='$tanggal',
            id_unit        ='{$_POST['id_unit']}',
            id_absensi     ='{$_POST['id_absensi']}',
            nip_pegawai    ='{$_POST['nip']}',
            acc_pj='-', acc_kepegawaian='-', acc_kasie='-',
            date=NOW()");
        if (!$ok) {
            if (is_ajax()) respond_json(['ok'=>false,'message'=>'Gagal menyimpan jadwal (INSERT).']);
            header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'));
            exit;
        }

        $s = fetch_array(bukaquery("SELECT kode_shift,
            CASE 
              WHEN color='A' THEN 'red-gradient'
              WHEN color='B' THEN 'aqua-gradient'
              WHEN color='C' THEN 'blue'
              WHEN color='D' THEN 'red'
              WHEN color='E' THEN 'green'
              WHEN color='F' THEN 'pink'
              WHEN color='G' THEN 'purple'
              WHEN color='H' THEN 'orange'
              WHEN color='I' THEN 'Navy'
              WHEN color='J' THEN 'teal'
              WHEN color='K' THEN 'gray'
              WHEN color='L' THEN 'purple-gradient'
              WHEN color='M' THEN 'blue-gradient'
              WHEN color='N' THEN 'black'
              ELSE 'white' END AS color_css
            FROM tm_shift WHERE id_absensi='{$_POST['id_absensi']}'"));

        if (is_ajax()){
            respond_json(['ok'=>true,'data'=>[
                'id_jadwal'  => $autonumber,
                'id_absensi' => $_POST['id_absensi'],
                'kode_shift' => $s['kode_shift'] ?? '',
                'color_css'  => $s['color_css'] ?? 'white'
            ]]);
        }

        header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'));
        exit;
    }
}


// ======================================================================
// UPDATE
// ======================================================================
elseif ($page === 'jadwal-pegawai' && $action === 'update-jadwal') {

    $required = ['id_jadwal','id_absensi'];
    foreach ($required as $k) {
        if (!isset($_POST[$k]) || $_POST[$k]==='') {
            if (is_ajax()) respond_json(['ok'=>false,'message'=>"Field '$k' wajib diisi."]);
            header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai')); exit;
        }
    }

    $ok = bukainput("UPDATE tm_jadwal 
                     SET id_absensi='{$_POST['id_absensi']}', date=NOW()
                     WHERE id_jadwal='{$_POST['id_jadwal']}'");

    if (!$ok) {
        if (is_ajax()) respond_json(['ok'=>false,'message'=>'Gagal menyimpan jadwal (UPDATE).']);
        header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai')); exit;
    }

    $s = fetch_array(bukaquery("SELECT kode_shift,
        CASE 
          WHEN color='A' THEN 'red-gradient'
          WHEN color='B' THEN 'aqua-gradient'
          WHEN color='C' THEN 'blue'
          WHEN color='D' THEN 'red'
          WHEN color='E' THEN 'green'
          WHEN color='F' THEN 'pink'
          WHEN color='G' THEN 'purple'
          WHEN color='H' THEN 'orange'
          WHEN color='I' THEN 'Navy'
          WHEN color='J' THEN 'teal'
          WHEN color='K' THEN 'gray'
          WHEN color='L' THEN 'purple-gradient'
          WHEN color='M' THEN 'blue-gradient'
          WHEN color='N' THEN 'black'
          ELSE 'white' END AS color_css
        FROM tm_shift WHERE id_absensi='{$_POST['id_absensi']}'"));

    if (is_ajax()) {
        respond_json(['ok'=>true,'data'=>[
            'id_jadwal'  => $_POST['id_jadwal'],
            'id_absensi' => $_POST['id_absensi'],
            'kode_shift' => $s['kode_shift'] ?? '',
            'color_css'  => $s['color_css'] ?? 'white'
        ]]);
    }
    header('location:../../page-view?'.paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai')); exit;
}

// (opsional) aksi lain...
