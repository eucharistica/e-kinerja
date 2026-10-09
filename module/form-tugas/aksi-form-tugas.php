<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//untuk peringatan pegawai
if ($page == 'form-tugas' and $action == 'add-surat-tugas') {
    $autonumber = notahun('id_surat', 'tm_surat_tugas');
    $tanggal_surat = FormatTgl('Y-m-d', $_POST['tgl_surat']);
    $tanggal_kegiatan = FormatTgl('Y-m-d', $_POST['tgl_kegiatan']);
    $an=isset($_POST['an']) ? $_POST['an'] : null;
    if ($an==''){
        $ttd='-';
    }else {
        $ttd=$_POST['an'];
    }
    bukainput("insert into tm_surat_tugas set id_surat='$autonumber', tgl_surat='$tanggal_surat', no_surat='$_POST[no_surat]', kegiatan='$_POST[kegiatan]', tgl_kegiatan='$tanggal_kegiatan', waktu='$_POST[waktu]', lokasi='$_POST[lokasi]', an='$ttd'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'));
} elseif ($page == 'form-tugas' and $action == 'update-surat-tugas') {
    $tanggal_surat = FormatTgl('Y-m-d', $_POST['tgl_surat']);
    $tanggal_kegiatan = FormatTgl('Y-m-d', $_POST['tgl_kegiatan']);
    bukainput("update tm_surat_tugas set tgl_surat='$tanggal_surat', no_surat='$_POST[no_surat]', kegiatan='$_POST[kegiatan]', tgl_kegiatan='$tanggal_kegiatan', waktu='$_POST[waktu]', lokasi='$_POST[lokasi]', an='$_POST[an]' where id_surat='$id'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'));
} elseif ($page == 'form-tugas' and $action == 'delete-surat-tugas') {
    hapusinput("delete from tm_surat_tugas where id_surat='$id'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'));
}
// penugasan
elseif ($page == 'form-tugas' and $action == 'add-penugasan') {
    $autonumber = nokiamat('id_add', 'tm_add_surtug');
    bukainput("insert into tm_add_surtug set id_add='$autonumber', id_surat='$id', nip='$_POST[nip]'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'));
} elseif ($page == 'form-tugas' and $action == 'delete-penugasan') {
    bukainput("delete from tm_add_surtug where id_add='$id'");
    header('location:../../page-view?' . paramEncrypt('module=form-surat-tugas&act=list-data-form-tugas'));
}
?>


