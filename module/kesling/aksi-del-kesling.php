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
//untuk skp tahunan
if ($page == 'kinerja-pegawai' and $action == 'add-skp-tahunan') {
    $autonumber = notahun('id_skp', 'tt_skptahunan');
    bukainput("insert into tt_skptahunan set id_skp='$autonumber', id_user='$id', kd_skp='$_POST[kd_skp]',kuantitas='$_POST[kuantitas]',tgl_buat=now(),tgl_update=now()");
    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'));
} elseif ($page == 'view' and $action == 'update-kesling') {
    bukainput("update tt_kesling set r_ppi='$_POST[r_ppi]' where id_kesling='$id'");
    header('location:../../page-view?' . paramEncrypt('module=view&act=list-data-kesling1'));
} elseif ($page == 'view-kesling-unit' and $action == 'unit-kesling') {
    bukainput("update tt_kesling set t_unit='$_POST[t_unit]' where id_kesling='$id'");
    header('location:../../page-view?' . paramEncrypt('module=view-kesling-unit&act=list-data-kesling-unit'));
} elseif ($page == 'kinerja-pegawai' and $action == 'delete-skp-tahunan') {
    hapusinput("delete from tt_skptahunan where id_skp='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'));
}

//untuk kinerja pegawai
elseif ($page == 'kinerja-pegawai' and $action == 'add-kinerja-utama') {
    $autonumber = nokiamat('id_kinerja', 'tt_kinerja');
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal_kinerja']);
    if (FormatTgl('d', date('d-m-Y')) <= $tutup_kinerja) {
        if (FormatTgl('m-Y', $tanggal) == BulanKemarin()) {
            bukainput("insert into tt_kinerja set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Utama', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=k3&act=list-data-k3'));
        } else {
            echo "<script>alert('Maaf hanya diperbolehkan menginput bulan " . BulanKemarin() . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        if (FormatTgl('m-Y', $tanggal) > FormatTgl('m-Y', date('d-m-Y')) or FormatTgl('m-Y', $tanggal) < FormatTgl('m-Y', date('d-m-Y'))) {
            echo "<script>alert('Maaf tidak memperbolehkan menginput bulan " . FormatTgl('m-Y', $tanggal) . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        } else {
            bukainput("insert into tt_kinerja set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Utama', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
        }
    }
} elseif ($page == 'kinerja-pegawai' and $action == 'add-kinerja-tambahan') {
    $autonumber = nokiamat('id_kinerja', 'tt_kinerja');
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal_kinerja']);
    if (FormatTgl('d', date('d-m-Y')) <= $tutup_kinerja) {
        if (FormatTgl('m-Y', $tanggal) == BulanKemarin()) {
            bukainput("insert into tt_kinerja set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Tambahan', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
        } else {
            echo "<script>alert('Maaf hanya diperbolehkan menginput bulan " . BulanKemarin() . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        if (FormatTgl('m-Y', $tanggal) > FormatTgl('m-Y', date('d-m-Y'))) {
            echo "<script>alert('Maaf tidak memperbolehkan menginput bulan " . FormatTgl('m-Y', $tanggal) . " !!'); window.location = 'javascript:history.go(-1)'</script>";
        } else {
            bukainput("insert into tt_kinerja set id_kinerja='$autonumber', id_user='$id', tanggal_kinerja='$tanggal', kd_skp='$_POST[kd_skp]', uraian='$_POST[uraian]',"
                . "waktu_mulai='$_POST[waktu_mulai]',waktu_akhir='$_POST[waktu_akhir]',date=now(),status='Tambahan', validasi='T'");
            header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
        }
    }
} elseif ($page == 'kesling' and $action == 'update-kesling-utama') {
    bukainput("update tt_kesling set detail='$_POST[detail]',tgl_hasil='$_POST[tgl_hasil]',id_kegiatan='$_POST[id_kegiatan]',jenis='$_POST[jenis]',hasil='$_POST[hasil]',standar='$_POST[standar]',kesimpulan='$_POST[kesimpulan]',r_kesling='$_POST[r_kesling]' where id_kesling='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kesling&act=list-data-kesling'));
} elseif ($page == 'kesling' and $action == 'delete-kesling') {
    hapusinput("delete from tt_kesling where id_kesling='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kesling&act=list-data-kesling'));
} elseif ($page == 'kebersihan' and $action == 'delete-kebersihan') {
    hapusinput("delete from kebersihan where id_kebersihan='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kebersihan-unit&act=list-data-kebersihan'));
} elseif ($page == 'kebersihan' and $action == 'update-kebersihan') {
    bukainput("update kebersihan set valid='$_POST[valid]' where id_kebersihan='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kebersihan&act=list-data-kebersihan1'));
}
