<?php

require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
$bb = isset($_POST['bb']) ? $_POST['bb'] : '0';
$tb = isset($_POST['tb']) ? $_POST['tb'] : '0';
$imt = round($bb/($tb*$tb/10000),2);
if ($imt <= 17) {
          $h_imt = "Sangat Kurus";
          $saran = "Tetap menerapkan pola hidup sehat";
        } elseif ($imt > 17.01 && $imt <= 18.49) {
             $h_imt = "Kurus";
             $saran = "Tetap menerapkan pola hidup sehat";
        } elseif ($imt > 18.50 && $imt <= 25) {
             $h_imt = "Normal";
             $saran = "Tetap menerapkan pola hidup sehat";
        } elseif ($imt > 25.01 && $imt <= 27) {
             $h_imt = "Gemuk";
             $saran = "Diet seimbang, lakukan olahraga rutin 3-4x/minggu durasi 30 menit, dan minum air putih 2L/ 8 gelas perhari";
        } elseif ($imt > 27.1) {
             $h_imt = "Obesitas";
             $saran = "Lanjutkan pola hidup sehat seperti turunkan berat badan dan lakukan olahraga rutin 3-4x/minggu durasi 30 menit, dan minum air putih 2L/ 8 gelas perhari";
        }
        // } elseif ($imt >= 35.0 && $imt <= 39.9) {
        //      $h_imt = "Obesitas Level 2";
        //       $saran = "Lanjutkan pola hidup sehat seperti turunkan berat badan dan lakukan olahraga rutin 3-4x/minggu durasi 30 menit, dan minum air putih 2L/ 8 gelas perhari";
        // } elseif ($imt >= 40.0) {
        //      $h_imt = "Obesitas Level 3";
        //       $saran = "Lanjutkan pola hidup sehat seperti turunkan berat badan dan lakukan olahraga rutin 3-4x/minggu durasi 30 menit, dan minum air putih 2L/ 8 gelas perhari";
        // }


//input imt
if ($page == 'imt-pegawai' and $action == 'add-imt-pegawai') {
    $autonumber = $_POST['bulan'] . $_POST['tahun'] . $id;
    // echo $autonumber;
    $date = date('Y-m-d', strtotime("1-" . $_POST['bulan'] . "-" . $_POST['tahun']));
    bukainputcek("insert into tt_imt set id_imt = '$autonumber', id_user='$id', tgl_imt = '$date', bb = '$_POST[bb]', tb = '$_POST[tb]',lp = '$_POST[lp]', imt='$imt',h_imt='$h_imt', date=now(),saran='$saran',validasi='T'");
    header('location:../../page-view?' . paramEncrypt('module=imt-pegawai&act=list-data-imt-pegawai'));
} elseif ($page == 'imt-pegawai' and $action == 'update-imt-pegawai') {
    $date = date('Y-m-d', strtotime("1-" . $_POST['bulan'] . "-" . $_POST['tahun']));
    bukainput("update tt_imt set bb = '$_POST[bb]', tb = '$_POST[tb]',lp = '$_POST[lp]', imt='$imt',h_imt='$h_imt', date=now(),saran='$saran' where id_imt = '$id'");
    header('location:../../page-view?' . paramEncrypt('module=imt-pegawai&act=list-data-imt-pegawai'));
} elseif ($page == 'imt-pegawai' and $action == 'delete-imt-pegawai') {
    hapusinput("delete from tt_imt where id_imt = '$id'");
    header('location:../../page-view?' . paramEncrypt('module=imt-pegawai&act=list-data-imt-pegawai'));
}