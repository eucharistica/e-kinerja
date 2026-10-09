<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
//untuk cuti pegawai
if ($page == 'cuti-pegawai' and $action == 'add-pengajuan-cuti') {
    $autonumber = notahun('id_cuti', 'tm_cuti');
    $id_unit = getOne("select id_unit from tm_pegawai where id_user='$id'");
    $tgl_cuti = date('d-m-Y', strtotime(substr($_POST['periode_cuti'], 0, 10)));
    $tanggal_permohonan = FormatTgl('Y-m-d', $_POST['tgl_permohonan']);
    $tanggal_permohonan1 = FormatTgl('d-m-Y', $_POST['tgl_permohonan']);
    $PecahStr1 = explode(" - ", $_POST['periode_cuti']);        
        
    if (strtotime($tgl_cuti) > strtotime($tanggal_permohonan1)) {
        $PecahStr = explode(",", $_POST['periode_cuti']);        
        $a = 0;
        for ($i = 0; $i < count($PecahStr); $i++) {
            $tanggal = FormatTgl('Y-m-d', $PecahStr[$i]);
            //query cek tanggal yang sama di unit yang sama selain kasi nolak permohonan
            $detail_tanggal = getOne("SELECT IF(count(tm_hari_cuti.id_cuti) <= 30,0,1) as nilainya 
                            FROM tm_hari_cuti
                            inner JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
                            inner JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
                            inner JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                            where tm_hari_cuti.tanggal='$tanggal' and tm_cuti.acc_kasie!='T' and tm_cuti.acc_kepegawaian!='T' and tm_cuti.acc_pj!='T' and tm_cuti.jenis_cuti!='CUTI MELAHIRKAN' and tm_cuti.jenis_cuti!='CUTI KARENA ALASAN PENTING' and tm_cuti.jenis_cuti!='CUTI SAKIT'");

            $hitung[] = $detail_tanggal;
            $a++;
        }
        //jumlah hari pengajuan
        $jumlah_hari = count($PecahStr);       
        $cek_tanggal_sama = array_sum($hitung);

        // $limit_cuti = ($cek_tanggal_sama - 12);
        if ($_POST['jenis_cuti'] == 'CUTI TAHUNAN') {
            if ($cek_tanggal_sama < 1) {
                $nilai = $_POST['sisa_cuti'] - count($PecahStr);
                if ($jumlah_hari <= $_POST['sisa_cuti']) {
                    //                    if ($_POST['jenis_cuti'] == 'CUTI TAHUNAN') {
                    if ($nilai >= 0) {
                        bukainput("insert into tm_cuti set id_cuti='$autonumber', tgl_permohonan='$tanggal_permohonan', id_user='$id',jenis_cuti='$_POST[jenis_cuti]',periode_cuti='$_POST[periode_cuti]',"
                            . "alasan_cuti='$_POST[alasan_cuti]',tahun_cuti='$_POST[tahun]', jumlah_hari='$jumlah_hari', alamat_cuti='$_POST[alamat_cuti]',no_tlp='$_POST[no_tlp]',nip_pengganti='$_POST[nip_pengganti]',nip_pj='$_POST[nip_pj]',nip_kasie='$_POST[nip_kasie]',acc_pengganti='-',acc_pj='-',acc_kepegawaian='-',acc_kasie='-',alasan=''");
                        $a = 0;
                        for ($i = 0; $i < count($PecahStr); $i++) {
                            $date = FormatTgl('Y-m-d', $PecahStr[$i]);
                            bukainput("insert into tm_hari_cuti set id_cuti='$autonumber', tanggal='$date'");
                            $a++;
                        }
                        header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'));
                        
                        // echo 'berhasil';
                    } else {
                        echo "<script>window.alert('Maaf Anda Tidak Bisa Mengambil Cuti Selama $jumlah_hari Hari, Karena Sisa Cuti Anda Tinggal $_POST[sisa_cuti] Hari lagi !');window.location.href='../../page-view?" . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai') . "';</script>";
                    }

                } else {
                    // echo 'kelebihan';
                    echo "<script>window.alert('Maaf sisa cuti anda di tahun " . $_POST['tahun'] . " hanya sisa $_POST[sisa_cuti] hari !');window.location.href='../../page-view?" . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai') . "';</script>";
                }
            } else {
                // echo '<pre>'; print_r($hitung); echo '</pre>';
                echo "<script>window.alert('Maaf tanggal yang anda pilih sudah melebihi kapasitas cuti harian');window.location.href='../../page-view?" . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai') . "';</script>";
            }
        } 
        elseif ($_POST['jenis_cuti'] == 'CUTI MELAHIRKAN') {
            bukainput("insert into tm_cuti set id_cuti='$autonumber', tgl_permohonan='$tanggal_permohonan', id_user='$id',jenis_cuti='$_POST[jenis_cuti]',periode_cuti='$_POST[periode_cuti]',"
                . "alasan_cuti='$_POST[alasan_cuti]',tahun_cuti='$_POST[tahun]', jumlah_hari='90', alamat_cuti='$_POST[alamat_cuti]',no_tlp='$_POST[no_tlp]',nip_pengganti='$_POST[nip_pengganti]',nip_pj='$_POST[nip_pj]',nip_kasie='$_POST[nip_kasie]',acc_pengganti='-',acc_pj='-',acc_kepegawaian='-',acc_kasie='-'");
            for ($i = 0; $i < count($PecahStr1); $i++) {
                $date = FormatTgl('Y-m-d', $PecahStr1[$i]);
                bukainput("insert into tm_hari_cuti set id_cuti='$autonumber', tanggal='$date'");
                $a++;
            }
            header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'));
        }
        else {
            bukainput("insert into tm_cuti set id_cuti='$autonumber', tgl_permohonan='$tanggal_permohonan', id_user='$id',jenis_cuti='$_POST[jenis_cuti]',periode_cuti='$_POST[periode_cuti]',"
                . "alasan_cuti='$_POST[alasan_cuti]',tahun_cuti='$_POST[tahun]', jumlah_hari='$jumlah_hari', alamat_cuti='$_POST[alamat_cuti]',no_tlp='$_POST[no_tlp]',nip_pengganti='$_POST[nip_pengganti]',nip_pj='$_POST[nip_pj]',nip_kasie='$_POST[nip_kasie]',acc_pengganti='-',acc_pj='-',acc_kepegawaian='-',acc_kasie='-'");
            for ($i = 0; $i < count($PecahStr); $i++) {
                $date = FormatTgl('Y-m-d', $PecahStr[$i]);
                bukainput("insert into tm_hari_cuti set id_cuti='$autonumber', tanggal='$date'");
                $a++;
            }
            header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'));
        }
    } else {
        echo "<script>window.alert('Maaf tidak di izinkan membuat tanggal permohonan cuti setelah cuti sudah dilakukan !');window.location.href='../../page-view?" . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai') . "';</script>";
    }
} elseif ($page == 'cuti-pegawai' and $action == 'update-pengajuan-cuti') {
    $tanggal = FormatTgl('Y-m-d', $_POST['tgl_permohonan']);
    bukainput("update tm_cuti set tgl_permohonan='$tanggal',jenis_cuti='$_POST[jenis_cuti]',alasan_cuti='$_POST[alasan_cuti]',alamat_cuti='$_POST[alamat_cuti]',no_tlp='$_POST[no_tlp]',"
        . "nip_pengganti='$_POST[nip_pengganti]',nip_pj='$_POST[nip_pj]',nip_kasie='$_POST[nip_kasie]',acc_pengganti='-',acc_pj='-',acc_kepegawaian='-',acc_kasie='-',acc_kasie='-',alasan='' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'delete-pengajuan-cuti') {
    hapusinput("delete from tm_hari_cuti where id_cuti='$id'");
    hapusinput("delete from tm_cuti where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-pengajuan-cuti-pegawai'));
}

//assesment cuti
elseif ($page == 'cuti-pegawai' and $action == 'assesment-pengajuan-cuti') {
    bukainput("update tm_cuti set acc_pengganti='Y' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pengganti-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'reject-pengajuan-cuti') {
    bukainput("update tm_cuti set acc_pengganti='T' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pengganti-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'assesment-pengajuan-cuti-pj') {
    bukainput("update tm_cuti set acc_pj='Y' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pj-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'reject-pengajuan-cuti-pj') {
    bukainput("update tm_cuti set acc_pj='T' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-pj-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'assesment-pengajuan-cuti-kepegawaian') {
    bukainput("update tm_cuti set acc_kepegawaian='Y' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kepegawaian-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'reject-pengajuan-cuti-kepegawaian') {
    bukainput("update tm_cuti set acc_kepegawaian='T' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kepegawaian-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'assesment-pengajuan-cuti-kasie') {
    bukainput("update tm_cuti set acc_kasie='Y' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kasie-cuti-pegawai'));
} elseif ($page == 'cuti-pegawai' and $action == 'reject-pengajuan-cuti-kasie') {
    bukainput("update tm_cuti set acc_kasie='T', alasan='$_POST[alasan]' where id_cuti='$id'");
    header('location:../../page-view?' . paramEncrypt('module=cuti-pegawai&act=list-data-assesment-kasie-cuti-pegawai'));
    echo $_POST['alasan'];
}