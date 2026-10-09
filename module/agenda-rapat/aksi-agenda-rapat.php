<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//untuk peringatan pegawai
if ($page == 'agenda-rapat' and $action == 'add-agenda') {
    $autonumber = notahun('id_agenda', 'tt_agenda');
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal']);    
    bukainput("insert into tt_agenda set id_agenda='$autonumber', ruang='$_POST[ruang]',waktu='$_POST[waktu]', kegiatan='$_POST[kegiatan]', note='$_POST[note]', tanggal='$tanggal'");
    header('location:../../page-view?' . paramEncrypt('module=agenda-rapat&act=list-agenda-rapat'));
} elseif ($page == 'agenda-rapat' and $action == 'update-agenda') {
    $tanggal = FormatTgl('Y-m-d', $_POST['tgl_hukuman']);
    bukainput("update tt_agenda set ruang='$_POST[ruang]',waktu='$_POST[waktu]', kegiatan='$_POST[kegiatan]', note='$_POST[note]' where id_agenda='$id'");
    header('location:../../page-view?' . paramEncrypt('module=agenda-rapat&act=list-agenda-rapat'));
} elseif ($page == 'agenda-rapat' and $action == 'delete-aganda') {
    hapusinput("delete from tt_agenda where id_agenda='$id'");
    header('location:../../page-view?' . paramEncrypt('module=agenda-rapat&act=list-agenda-rapat'));
}


    