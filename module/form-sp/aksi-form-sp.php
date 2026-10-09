<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//untuk peringatan pegawai
if ($page == 'form-hukuman' and $action == 'add-surat-peringatan') {
    $autonumber = notahun('id_hukuman', 'tt_hukuman');
    $tanggal = FormatTgl('Y-m-d', $_POST['tgl_hukuman']);
    $aktif_hukuman = date('Y-m-d', strtotime("1-" . $_POST['bulan'] . "-" . $_POST['tahun']));
    bukainput("insert into tt_hukuman set id_hukuman='$autonumber', id_user='$_POST[id_user]', id_sanksi='$_POST[id_sanksi]', no_hukuman='$_POST[no_hukuman]', tgl_hukuman='$tanggal', aktif_hukuman='$aktif_hukuman', alasan_hukuman='$_POST[alasan_hukuman]'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-peringatan&act=list-data-form-sp'));
} elseif ($page == 'form-hukuman' and $action == 'update-surat-peringatan') {
    $tanggal = FormatTgl('Y-m-d', $_POST['tgl_hukuman']);
    $aktif_hukuman = date('Y-m-d', strtotime("1-" . $_POST['bulan'] . "-" . $_POST['tahun']));
    bukainput("update tt_hukuman set id_user='$_POST[id_user]', id_sanksi='$_POST[id_sanksi]', no_hukuman='$_POST[no_hukuman]', tgl_hukuman='$tanggal', aktif_hukuman='$aktif_hukuman', alasan_hukuman='$_POST[alasan_hukuman]' where id_hukuman='$id'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-peringatan&act=list-data-form-sp'));
} elseif ($page == 'form-hukuman' and $action == 'delete-surat-peringatan') {
    hapusinput("delete from tt_hukuman where id_hukuman='$id'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-peringatan&act=list-data-form-sp'));
}


    