<?php

require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
$nip = isset($url['nip']) ? $url['nip'] : null;
$unit = isset($url['unit']) ? $url['unit'] : null;
//untuk validasi waktu pengurangan
if ($page == 'validasi-pegawai' and $action == 'add-waktu-pengurangan') {
    $autonumber = nokiamat('id_waktu_k', 'tm_waktu_k');
    $date = date('Y-m-t', strtotime("-1 month", strtotime(date("Y-m-t"))));
    bukainput("insert into tm_waktu_k set id_waktu_k='$autonumber', id_user='$id', sakit1='$_POST[sakit1]', sakit2='$_POST[sakit2]', "
        . "alpha='$_POST[alpha]', izin='$_POST[izin]', telat='$_POST[telat]', ct_sakit_k='$_POST[ct_sakit_k]', ct_alasan_k='$_POST[ct_alasan_k]', "
        . "ct_persalinan_k='$_POST[ct_persalinan_k]', izin_setengah_hari='$_POST[izin_setengah_hari]', meninggal='$_POST[meninggal]', date_k='$date'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
} elseif ($page == 'validasi-pegawai' and $action == 'update-waktu-pengurangan') {
    bukainput("update tm_waktu_k set sakit1='$_POST[sakit1]', sakit2='$_POST[sakit2]', "
        . "alpha='$_POST[alpha]', izin='$_POST[izin]', telat='$_POST[telat]', ct_sakit_k='$_POST[ct_sakit_k]', ct_alasan_k='$_POST[ct_alasan_k]', "
        . "ct_persalinan_k='$_POST[ct_persalinan_k]', izin_setengah_hari='$_POST[izin_setengah_hari]', meninggal='$_POST[meninggal]' where id_waktu_k='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
}

//untuk validasi waktu penambahan
elseif ($page == 'validasi-pegawai' and $action == 'add-waktu-penambahan') {
    $autonumber = nokiamat('id_waktu_t', 'tm_waktu_t');
    $date = date('Y-m-t', strtotime("-1 month", strtotime(date("Y-m-t"))));
    bukainput("insert into tm_waktu_t set id_waktu_t='$autonumber', id_user='$id', "
        . "ct_sakit_t='$_POST[ct_sakit_t]', ct_alasan_t='$_POST[ct_alasan_t]', ct_tahunan_t='$_POST[ct_tahunan_t]', diklat='$_POST[diklat]', "
        . "spd='$_POST[spd]', haji='$_POST[haji]', date_t='$date'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
} elseif ($page == 'validasi-pegawai' and $action == 'update-waktu-penambahan') {
    bukainput("update tm_waktu_t set ct_sakit_t='$_POST[ct_sakit_t]', ct_alasan_t='$_POST[ct_alasan_t]', ct_tahunan_t='$_POST[ct_tahunan_t]', diklat='$_POST[siklat]', "
        . "spd='$_POST[spd]', haji='$_POST[haji]' where id_waktu_t='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
}

//untuk validasi waktu shift
elseif ($page == 'validasi-pegawai' and $action == 'add-waktu-shifting') {
    $autonumber = nokiamat('id_waktu_s', 'tm_waktu_s');
    $date = date('Y-m-t', strtotime("-1 month", strtotime(date("Y-m-t"))));
    bukainput("insert into tm_waktu_s set id_waktu_s='$autonumber', id_user='$id', "
        . "j_hks='$_POST[j_hks]', j_hkm='$_POST[j_hkm]', j_hlp='$_POST[j_hlp]', j_hls='$_POST[j_hls]', "
        . "j_hlm='$_POST[j_hlm]', j_hrp='$_POST[j_hrp]', j_hrs='$_POST[j_hrs]', j_hrm='$_POST[j_hrm]', j_ns='$_POST[j_ns]', date_s='$date'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
} elseif ($page == 'validasi-pegawai' and $action == 'update-waktu-shifting') {
    bukainput("update tm_waktu_s set j_hks='$_POST[j_hks]', j_hkm='$_POST[j_hkm]', j_hlp='$_POST[j_hlp]', j_hls='$_POST[j_hls]', "
        . "j_hlm='$_POST[j_hlm]', j_hrp='$_POST[j_hrp]', j_hrs='$_POST[j_hrs]', j_hrm='$_POST[j_hrm]', j_ns='$_POST[j_ns]' where id_waktu_s='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
}

//untuk validasi potongan pegawai
elseif ($page == 'validasi-pegawai' and $action == 'add-potongan-pegawai') {
    $autonumber = nokiamat('id_potongan_p', 'tm_potongan_p');
    $date = date('Y-m-t', strtotime("-1 month", strtotime(date("Y-m-t"))));
    bukainput("insert into tm_potongan_p set id_potongan_p='$autonumber', id_user='$id', "
        . "potongan_bank='$_POST[potongan_bank]', cicilan_bank='$_POST[cicilan_bank]', keterangan_bank='$_POST[keterangan_bank]', potongan_rs='$_POST[potongan_rs]', cicilan_rs='$_POST[cicilan_rs]', keterangan_rs='$_POST[keterangan_rs]', date_p='$date'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
} elseif ($page == 'validasi-pegawai' and $action == 'update-potongan-pegawai') {
    bukainput("update tm_potongan_p set potongan_bank='$_POST[potongan_bank]', cicilan_bank='$_POST[cicilan_bank]', keterangan_bank='$_POST[keterangan_bank]', potongan_rs='$_POST[potongan_rs]', cicilan_rs='$_POST[cicilan_rs]', keterangan_rs='$_POST[keterangan_rs]' where id_potongan_p='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
}

//untuk tunjangan khusus pegawai
elseif ($page == 'validasi-pegawai' and $action == 'add-tkhusus-pegawai') {
    $autonumber = nokiamat('id_uang_t', 'tm_uang_t');
    $date = date('Y-m-t', strtotime("-1 month", strtotime(date("Y-m-t"))));
    bukainput("insert into tm_uang_t set id_uang_t='$autonumber', id_user='$id', "
    . "thr='$_POST[thr]', gaji13='$_POST[gaji13]', date_p='$date'");
    // echo "insert into tm_uang_t set id_uang_t='$autonumber', id_user='$id', "
    //     . "thr='$_POST[thr]', gaji13='$_POST[gaji13]', date_p='$date'";
        header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
    } elseif ($page == 'validasi-pegawai' and $action == 'update-tkhusus-pegawai') {
        bukainput("update tm_uang_t set thr='$_POST[thr]', gaji13='$_POST[gaji13]' where id_uang_t='$id'");
        header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=rekap-absensi-kepegawaian'));
}

//untuk pengaturan shift/absensi
elseif ($page == 'pengaturan' and $action == 'add-pengaturan') {
    $autonumber = notahun('id_shift', 'set_shift');
    bukainput("insert into set_shift set id_shift='$autonumber', id_unit='$_POST[id_unit]', id_absensi='$_POST[id_absensi]'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=pengaturan-absensi'));
} elseif ($page == 'pengaturan' and $action == 'update-pengaturan') {
    bukainput("update set_shift set id_unit='$_POST[id_unit]', id_absensi='$_POST[id_absensi]' where id_shift='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=pengaturan-absensi'));
} elseif ($page == 'pengaturan' and $action == 'delete-pengaturan') {
    hapusinput("delete from set_shift where id_shift='$id'");
    header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=pengaturan-absensi'));
}

//update keterangan absensi
elseif ($page == 'absensi-pegawai' and $action == 'update-validasi-absensi') {
    // echo "update tm_jadwal set id_absensi='$_POST[id_absensi]', acc_kepegawaian='$_POST[acc_kepegawaian]', date=now() where id_jadwal='$id'";
    bukainput("update tm_jadwal set id_absensi='$_POST[id_absensi]', acc_kepegawaian='$_POST[acc_kepegawaian]', date=now() where id_jadwal='$id'");
        header('location:../../page-view?' . paramEncrypt('module=absensi-pegawai&act=cek-absensi-pegawai&id='.$nip.'&dpa='.$unit.''));
}
