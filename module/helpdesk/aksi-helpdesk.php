<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//pegawai pns
if ($page == 'helpdesk' and $action == 'add-helpdesk') {
    $autonumber = notahun('no_tiket', 'tt_helpdesk');
    bukainput("insert into tt_helpdesk set no_tiket = '$autonumber', jenis = '$_POST[jenis]', narasi = '$_POST[narasi]', respon = '', status='Terkirim', nip='$id', selesai='', date=NOW()");
    header('location:../../page-view?' . paramEncrypt('module=helpdesk&act=list-helpdesk'));
} elseif ($page == 'helpdesk' and $action == 'update-helpdesk') {
    bukainput("update tt_helpdesk set jenis = '$_POST[jenis]', narasi = '$_POST[narasi]',date=NOW() where no_tiket = '$id'");
    header('location:../../page-view?' . paramEncrypt('module=helpdesk&act=list-helpdesk'));
} elseif ($page == 'helpdesk' and $action == 'delete-helpdesk') {
    hapusinput("delete from tt_helpdesk where no_tiket = '$id'");
    header('location:../../page-view?' . paramEncrypt('module=helpdesk&act=list-helpdesk'));
} elseif ($page == 'helpdesk' and $action == 'update-status') {
    if ($_POST['status'] == "Selesai") {
        bukainput("update tt_helpdesk set respon = '$_POST[respon]',status = '$_POST[status]', selesai = NOW(), date=NOW() where no_tiket = '$id'");
    } else {
        bukainput("update tt_helpdesk set respon = '$_POST[respon]',status = '$_POST[status]', date=NOW() where no_tiket = '$id'");
    }
    header('location:../../page-view?' . paramEncrypt('module=helpdesk&act=tiket-helpdesk'));
}
?>