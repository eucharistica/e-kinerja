<?php

require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//setting validasi penginputan
$tutup_kinerja = getOne("select setup.tutup_kinerja from setup");

//input olahraga
if ($page == 'olahraga-pegawai' and $action == 'add-olahraga-pegawai') {
    $autonumber = nokiamat('id_kinerja', 'tt_olahraga');
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal_kinerja']);
    if (FormatTgl('d', date('d-m-Y')) <= $tutup_kinerja) {
        if (FormatTgl('m-Y', $tanggal) == BulanKemarin()) {
            bukainput("insert into tt_olahraga set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Olahraga', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'));
        } else {
            echo "<script>alert('Maaf hanya diperbolehkan menginput bulan " . BulanKemarin() . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        if (FormatTgl('m-Y', $tanggal) > FormatTgl('m-Y', date('d-m-Y'))) {
            echo "<script>alert('Maaf tidak memperbolehkan menginput bulan " . FormatTgl('m-Y', $tanggal) . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        } else {
            bukainput("insert into tt_olahraga set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Olahraga', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'));
        }
    }
} elseif ($page == 'olahraga-pegawai' and $action == 'update-olahraga-pegawai') {
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal_kinerja']);
    bukainput("update tt_olahraga set tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]',uraian='$_POST[uraian]',waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now() where id_kinerja='$id'");
    header('location:../../page-view?' . paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'));
} elseif ($page == 'olahraga-pegawai' and $action == 'delete-olahraga') {
    hapusinput("delete from tt_olahraga where id_kinerja='$id'");
    header('location:../../page-view?' . paramEncrypt('module=olahraga-pegawai&act=list-data-olahraga-pegawai'));
}
