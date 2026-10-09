<?php

//configurasi
require_once('../../conf/conf.php');
require_once('../../assets/libs/aes-encrypt/function.php');
require('../../conf/ssp.class.php');
//Set URI
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$url = decode($URI);
$id = isset($url['pg']) ? $url['pg'] : '';
$ta = isset($url['ta']) ? $url['ta'] : '';


//cek askes
//CekAkses();
// SQL server connection information
$sql_details = array(
  'user' => $db_username,
  'pass' => $db_password,
  'db' => $db_name,
  'host' => $db_hostname
);
switch ((isset($url['tab']) ? $url['tab'] : '')) {

  case "pegawai-all":
    // DB table to use
    $table = "
        (SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,
        tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
        tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level 
        FROM
        tm_pegawai
        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
        INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
        INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level) temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nip', 'dt' => 1),
      array('db' => 'nama_pegawai', 'dt' => 2),
      array('db' => 'tgl_masuk', 'dt' => 3),
      array('db' => 'nama_unit', 'dt' => 4),
      array('db' => 'pendidikan', 'dt' => 5),
      array('db' => 'status_nikah', 'dt' => 6),
      array('db' => 'rumpun', 'dt' => 7),
      array('db' => 'nama_level', 'dt' => 8),
      array('db' => 'status_pegawai', 'dt' => 9),
      array('db' => 'status', 'dt' => 10),
      array('db' => 'id_user', 'dt' => 0)
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "cuti-all":
    // DB table to use
    $table = "
          (SELECT tm_pegawai.id_user, tm_pegawai.nama_pegawai, tm_upload_cuti.jumlah, ($ta) as tahun
          FROM
          tm_pegawai
          LEFT JOIN tm_upload_cuti ON tm_upload_cuti.id_user = tm_pegawai.id_user 
          WHERE tm_pegawai.status_pegawai='NON PNS' AND tm_upload_cuti.tahun='$ta'  AND tm_pegawai.status='AKTIF' ) temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nama_pegawai', 'dt' => 1),
      array('db' => 'tahun', 'dt' => 2),
      array('db' => 'jumlah', 'dt' => 3),
      array(
        'db' => 'id_user',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          if ($row['jumlah'] != null) {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-update' data-whatever='" . paramEncrypt($row['id_user'] . "|" . $row['tahun']) . "'>Edit</a></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Reset Password ke settingan default ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=hapus-map-cuti&id=' . $row['id_user'] . '|' . $row['tahun']) . "'>Hapus</a></li>                                 
                                </div>
                            </div>";
          }
        },
        'dt' => 4
      ),
      array('db' => 'id_user', 'dt' => 0)
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "non-pns":
    // DB table to use
    $table = "
        (SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
        tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
        tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
        FROM
        tm_pegawai
        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
        INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
        INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
        where tm_pegawai.status_pegawai='NON PNS' AND tm_pegawai.status='$id') temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nip', 'dt' => 1),
      array('db' => 'nama_pegawai', 'dt' => 2),
      array('db' => 'tgl_masuk', 'dt' => 3),
      array('db' => 'nama_unit', 'dt' => 4),
      array('db' => 'pendidikan', 'dt' => 5),
      array('db' => 'status_nikah', 'dt' => 6),
      array('db' => 'rumpun', 'dt' => 7),
      array('db' => 'nama_level', 'dt' => 8),
      array(
        'db' => 'status',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          if ($row['status'] == 'AKTIF') {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                  <li><a href='cetak-drh-" . paramEncrypt($row['id_user']) . "' target='_Blank'>Cetak DRH</a></li>
                                  <li class='divider'></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Reset Password ke settingan default ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=reset-master-pegawai&id=' . $row['id_user']) . "'>Reset Pass</a></li>                                 
                                </div>
                            </div>";
          } else {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                </div>
                            </div>";
          }
        },
        'dt' => 9
      ),
      array('db' => 'id_user', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "pns":
    // DB table to use
    $table = "
        (SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
        tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
        tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
        FROM
        tm_pegawai
        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
        INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
        INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
        where tm_pegawai.status_pegawai='PNS' AND tm_pegawai.status='$id') temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nip', 'dt' => 1),
      array('db' => 'nama_pegawai', 'dt' => 2),
      array('db' => 'tgl_masuk', 'dt' => 3),
      array('db' => 'nama_unit', 'dt' => 4),
      array('db' => 'pendidikan', 'dt' => 5),
      array('db' => 'status_nikah', 'dt' => 6),
      array('db' => 'rumpun', 'dt' => 7),
      array('db' => 'nama_level', 'dt' => 8),
      array(
        'db' => 'status',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          if ($row['status'] == 'AKTIF') {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                  <li><a href='cetak-drh-" . paramEncrypt($row['id_user']) . "' target='_Blank'>Cetak DRH</a></li>
                                  <li><a href='?" . paramEncrypt('module=kinerja-pegawai&act=skp-tahunan-pegawai&id=' . $row['id_user']) . "'>SKP</a></li>
                                  <li class='divider'></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Reset Password ke settingan default ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=reset-master-pegawai&id=' . $row['id_user']) . "'>Reset Pass</a></li>                                 
                                </div>
                            </div>";
          } else {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                </div>
                            </div>";
          }
        },
        'dt' => 9
      ),
      array('db' => 'id_user', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "epjlp":
    // DB table to use
    $table = "
        (SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
        tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
        tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
        FROM
        tm_pegawai
        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
        INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
        INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
        where tm_pegawai.status_pegawai='EPJLP' AND tm_pegawai.status='$id') temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nip', 'dt' => 1),
      array('db' => 'nama_pegawai', 'dt' => 2),
      array('db' => 'tgl_masuk', 'dt' => 3),
      array('db' => 'nama_unit', 'dt' => 4),
      array('db' => 'pendidikan', 'dt' => 5),
      array('db' => 'status_nikah', 'dt' => 6),
      array('db' => 'rumpun', 'dt' => 7),
      array('db' => 'nama_level', 'dt' => 8),
      array(
        'db' => 'status',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          if ($row['status'] == 'AKTIF') {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                  <li><a href='cetak-drh-" . paramEncrypt($row['id_user']) . "' target='_Blank'>Cetak DRH</a></li>
                                  <li class='divider'></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Reset Password ke settingan default ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=reset-master-pegawai&id=' . $row['id_user']) . "'>Reset Pass</a></li>                                 
                                </div>
                            </div>";
          } else {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                </div>
                            </div>";
          }
        },
        'dt' => 9
      ),
      array('db' => 'id_user', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "part-time":
    // DB table to use
    $table = "
        (SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
        tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
        tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_pegawai.id_kasatpel
        FROM
        tm_pegawai
        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
        INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
        INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
        where tm_pegawai.status_pegawai='PART-TIME' AND tm_pegawai.status='$id') temp";

    // Table's primary key
    $primaryKey = 'id_user';
    $columns = array(
      array('db' => 'nip', 'dt' => 1),
      array('db' => 'nama_pegawai', 'dt' => 2),
      array('db' => 'tgl_masuk', 'dt' => 3),
      array('db' => 'nama_unit', 'dt' => 4),
      array('db' => 'pendidikan', 'dt' => 5),
      array('db' => 'status_nikah', 'dt' => 6),
      array('db' => 'rumpun', 'dt' => 7),
      array('db' => 'nama_level', 'dt' => 8),
      array(
        'db' => 'status',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          if ($row['status'] == 'AKTIF') {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                  <li><a href='cetak-drh-" . paramEncrypt($row['id_user']) . "' target='_Blank'>Cetak DRH</a></li>
                                  <li class='divider'></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Reset Password ke settingan default ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=reset-master-pegawai&id=' . $row['id_user']) . "'>Reset Pass</a></li>                                 
                                </div>
                            </div>";
          } else {
            return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a href='?" . paramEncrypt('module=validasi-pegawai&act=update-data-pegawai&id=' . $row['id_user']) . "'>Edit</a></li>
                                  <li><a href='?" . paramEncrypt('module=simpeg&act=profile-pegawai&id=' . $row['id_user']) . "'>Profile</a></li>
                                </div>
                            </div>";
          }
        },
        'dt' => 9
      ),
      array('db' => 'id_user', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "table-hari-kerja":
    // DB table to use
    $table = "
        (SELECT
        tm_hari_kerja.id_hari_kerja, DATE_FORMAT(tm_hari_kerja.bulan,'%M %Y') as bulan, tm_hari_kerja.hari
        FROM tm_hari_kerja where YEAR(tm_hari_kerja.bulan)='$id' Order by tm_hari_kerja.bulan ASC ) temp";

    // Table's primary key
    $primaryKey = 'id_hari_kerja';
    $columns = array(
      array('db' => 'bulan', 'dt' => 1),
      array('db' => 'hari', 'dt' => 2),
      array(
        'db' => 'id_hari_kerja',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-ubah' data-whatever='" . paramEncrypt($row['id_hari_kerja']) . "'>Edit</a></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Hapus Data Ini ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=delete-validasi-pegawai-hari-kerja&id=' . $row['id_hari_kerja']) . "'>Hapus</a></li>
                                </div>
                            </div>";
        },
        'dt' => 3
      ),
      array('db' => 'id_hari_kerja', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "table-grade":
    // DB table to use
    $table = "
        (select id_level,grade_kinerja,grade_prilaku,nama_level from tm_level) temp";

    // Table's primary key
    $primaryKey = 'id_level';
    $columns = array(
      array('db' => 'nama_level', 'dt' => 1),
      array('db' => 'grade_kinerja', 'dt' => 2),
      array('db' => 'grade_prilaku', 'dt' => 3),
      array(
        'db' => 'id_level',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-ubah' data-whatever='" . paramEncrypt($row['id_level']) . "'>Edit</a></li>
                                 </div>
                            </div>";
        },
        'dt' => 4
      ),
      array('db' => 'id_level', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "table-skp":
    // DB table to use
    $table = "
        (SELECT kd_skp,skp,waktu FROM tm_skp) temp";

    // Table's primary key
    $primaryKey = 'kd_skp';
    $columns = array(
      array('db' => 'skp', 'dt' => 1),
      array('db' => 'waktu', 'dt' => 2),
      array(
        'db' => 'kd_skp',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-ubah' data-whatever='" . paramEncrypt($row['kd_skp']) . "'>Edit</a></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Hapus Data Ini ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=delete-validasi-pegawai-skp&id=' . $row['kd_skp']) . "'>Hapus</a></li>
                                 </div>
                            </div>";
        },
        'dt' => 3
      ),
      array('db' => 'kd_skp', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "table-bagian":
    // DB table to use
    $table = "
        (SELECT tm_unit.id_unit,tm_unit.nama_unit, tm_unit.id_petugas, tm_honor_shift.petugas FROM tm_unit 
        INNER JOIN tm_honor_shift ON tm_unit.id_petugas = tm_honor_shift.id_petugas where nama_unit!='-') temp";

    // Table's primary key
    $primaryKey = 'id_unit';
    $columns = array(
      array('db' => 'nama_unit', 'dt' => 1),
      array('db' => 'petugas', 'dt' => 2),
      array(
        'db' => 'id_unit',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-orange' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-ubah' data-whatever='" . paramEncrypt($row['id_unit']) . "'>Edit</a></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Hapus Data Ini ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=delete-validasi-pegawai-bagian&id=' . $row['id_unit']) . "'>Hapus</a></li>
                                 </div>
                            </div>";
        },
        'dt' => 3
      ),
      array('db' => 'id_unit', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;

  case "table-penyerapan":
    // DB table to use
    $table = "
        (select id_penyerapan,DATE_FORMAT(bulan,'%M %Y') as bulan,concat(penyerapan,' %') as penyerapan from tm_penyerapan where year(bulan)='$id' order by bulan desc) temp";

    // Table's primary key
    $primaryKey = 'id_penyerapan';
    $columns = array(
      array('db' => 'bulan', 'dt' => 1),
      array('db' => 'penyerapan', 'dt' => 2),
      array(
        'db' => 'id_penyerapan',
        'dt' => 'dt',
        'formatter' => function ($d, $row) {
          return "<div class='btn-group'>
                                <button type='button' class='btn btn-xs dropdown-toggle bg-purple' data-toggle='dropdown'>
                                  Action <span class='caret'> </span>
                                </button>
                                <div class='dropdown-menu'>
                                  <li><a data-toggle='modal' data-target='#modal-ubah' data-whatever='" . paramEncrypt($row['id_penyerapan']) . "'>Edit</a></li>
                                  <li><a onClick=\"javascript: return confirm('Yakin Ingin Hapus Data Ini ?');\" href='module/validasi-pegawai/aksi-validasi-pegawai?" . paramEncrypt('module=validasi-pegawai&act=delete-validasi-pegawai-penyerapan&id=' . $row['id_penyerapan']) . "'>Hapus</a></li>
                                </div>
                            </div>";
        },
        'dt' => 3
      ),
      array('db' => 'id_penyerapan', 'dt' => 0),
    );
    echo json_encode(
      SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns)
    );
    break;
}
