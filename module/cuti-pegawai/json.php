<?php

require_once('../../conf/conf.php');
require_once('../../assets/libs/aes-encrypt/function.php');

//api pegawai
if (isset($_GET['m']) ? $_GET['m'] : '' == 'event') {
    $x = isset($_GET['x']) ? $_GET['x'] : '';
    $d = isset($_GET['d']) ? $_GET['d'] : '';
    $data = array();
    $q_cuti = "SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, concat(tm_pegawai.nama_pegawai, ' (',tm_unit.nama_unit,')') as nama_pegawai
            FROM tm_hari_cuti
            INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
            INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
            INNER JOIN tm_unit on tm_unit.id_unit=tm_pegawai.id_unit ";
    if ($x != '') {
        $q_cuti .= "where tm_cuti.acc_kasie!='T' AND tm_pegawai.sub_bagian='$x'";
    }
    if ($d != '') {
        $q_cuti .= "where tm_cuti.acc_kasie!='T' AND tm_pegawai.id_unit='$d'";
    }
    $cuti = bukaquery($q_cuti);
    $post = isset($_POST['search']) ? $_POST['search'] : null;
    $sql = bukaquery($q_cuti);
    while ($row = fetch_array($sql)) {
        array_push($data, array("title" => $row['nama_pegawai'], "start" => $row['tanggal'], "end" => $row['tanggal'], "backgroundColor" => "#f56954", "borderColor" => "#f56954"));
    }
    echo json_encode($data);
} else {
}
