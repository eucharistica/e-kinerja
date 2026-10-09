<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;

//hak akses
if ($page == 'configurasi' and $action == 'simpan-hak-akses') {
    $autonumber = autonomer('tm_level', 'id_level', 'LVL-', '10');
    bukainput("insert into tm_level set id_level='$autonumber', nama_level='$_POST[nama_level]', menu_kepegawaian='$_POST[menu_kepegawaian]', menu_surtug='$_POST[menu_surtug]', menu_diklat='$_POST[menu_diklat]', "
            . "menu_keuangan='$_POST[menu_keuangan]', menu_val_pj='$_POST[menu_val_pj]', menu_val_kasatpel='$_POST[menu_val_kasatpel]', menu_val_kasie='$_POST[menu_val_kasie]', menu_laporan='$_POST[menu_laporan]',"
            . "upload_perpustakaan='$_POST[upload_perpustakaan]',menu_helpdesk='$_POST[menu_helpdesk]', configurasi='$_POST[configurasi]'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=list-hak-akses'));
} elseif ($page == 'configurasi' and $action == 'update-hak-akses') {
    bukainput("update tm_level set nama_level='$_POST[nama_level]', menu_kepegawaian='$_POST[menu_kepegawaian]', menu_surtug='$_POST[menu_surtug]', menu_diklat='$_POST[menu_diklat]', "
            . "menu_keuangan='$_POST[menu_keuangan]', menu_val_pj='$_POST[menu_val_pj]', menu_val_kasatpel='$_POST[menu_val_kasatpel]', menu_val_kasie='$_POST[menu_val_kasie]', menu_laporan='$_POST[menu_laporan]',"
            . "upload_perpustakaan='$_POST[upload_perpustakaan]',menu_helpdesk='$_POST[menu_helpdesk]',configurasi='$_POST[configurasi]' where id_level='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=list-hak-akses'));
} elseif ($page == 'configurasi' and $action == 'hapus-hak-akses') {
    hapusinput("delete from tm_level where id_level='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=list-hak-akses'));
}

//update setup
elseif ($page == 'configurasi' and $action == 'update-setting-waktu-kinerja') {
    hapusinput("update setup set tutup_kinerja='$_POST[tutup_kinerja]', validasi_pj='$_POST[validasi_pj]', validasi_kasie='$_POST[validasi_kasie]', validasi_jadwal='$_POST[validasi_jadwal]' where id='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
} elseif ($page == 'configurasi' and $action == 'update-setting-keterlambatan-absensi') {
    hapusinput("update setup set dispensasi_absensi='$_POST[dispensasi_absensi]' where id='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
}elseif ($page == 'configurasi' and $action == 'update-pengumuman') {
    hapusinput("update setup set pengumuman='$_POST[pengumuman]' where id='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
}elseif ($page == 'configurasi' and $action == 'update-runningtext') {
    hapusinput("update setup set running_text='$_POST[runningtext]' where id='$id'");
    header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
} elseif ($page == 'configurasi' and $action == 'update-data-dasar') {
    $ekstensi_diperbolehkan = array('png', 'jpg');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../img/";
    $kode = paramEncrypt(isset($_POST['kode_skpd']) ? $_POST['kode_skpd'] : null);
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran <= '5242880') {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $foto = getOne("select logo from setup where id='$id'");
                unlink("../../img/" . $foto);
                bukainputcek("update setup set "
                        . "kode_skpd='$kode',"
                        . "nama_instansi='$_POST[nama_instansi]',"
                        . "alamat_kop='$_POST[alamat]',"
                        . "direktur='$_POST[direktur]',"
                        . "nip_direktur='$_POST[nip_direktur]',"
                        . "tlp='$_POST[tlp]',"
                        . "fax='$_POST[fax]',"
                        . "email='$_POST[email]',"
                        . "website='$_POST[website]',"
                        . "kode_pos='$_POST[kode_pos]',"
                        . "logo='$nama_baru'"
                        . "where id='$id'");
                header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
            } else {
                echo "<script>alert('GAGAL !! Upload Logo, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainput("update setup set "
                . "kode_skpd='$kode',"
                . "nama_instansi='$_POST[nama_instansi]',"
                . "alamat_kop='$_POST[alamat]',"
                . "direktur='$_POST[direktur]',"
                . "nip_direktur='$_POST[nip_direktur]',"
                . "tlp='$_POST[tlp]',"
                . "fax='$_POST[fax]',"
                . "email='$_POST[email]',"
                . "website='$_POST[website]',"
                . "kode_pos='$_POST[kode_pos]',"
                . "logo='$nama_baru'"
                . "where id='$id'");
        header('location:../../page-view?' . paramEncrypt('module=configurasi&act=setup'));
    }
}
?>