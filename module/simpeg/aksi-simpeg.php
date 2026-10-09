<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;

//update profil pegawai
if ($page == 'simpeg' and $action == 'update-profil-pegawai') {
    $tgl_lahir = FormatTgl('Y-m-d', $_POST['tgl_lahir']);
    $ekstensi_diperbolehkan = array('png', 'jpg');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../img/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran <= '5242880') {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $foto = getOne("select tm_pegawai.foto from tm_pegawai where id_user='$id'");
                unlink("../../img/" . $foto);
                bukainputcek("update tm_pegawai set "
                        . "nik='$_POST[nik]',"
                        . "nama_pegawai='$_POST[nama_pegawai]',"
                        . "alamat='$_POST[alamat]',"
                        . "tempat_lahir='$_POST[tempat_lahir]',"
                        . "tgl_lahir='$tgl_lahir',"
                        . "no_rek='$_POST[no_rek]',"
                        . "npwp='$_POST[npwp]',"
                        . "no_bpjs='$_POST[no_bpjs]',"
                        . "no_hp='$_POST[no_hp]',"
                        . "jk='$_POST[jk]',"
                        . "agama='$_POST[agama]',"
                        . "foto='$nama_baru'"
                        . "where id_user='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload foto, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainput("update tm_pegawai set "
                . "nik='$_POST[nik]',"
                . "nama_pegawai='$_POST[nama_pegawai]',"
                . "alamat='$_POST[alamat]',"
                . "tempat_lahir='$_POST[tempat_lahir]',"
                . "tgl_lahir='$tgl_lahir',"
                . "no_rek='$_POST[no_rek]',"
                . "npwp='$_POST[npwp]',"
                . "no_bpjs='$_POST[no_bpjs]',"
                . "no_hp='$_POST[no_hp]',"
                . "jk='$_POST[jk]',"
                . "agama='$_POST[agama]'"
                . "where id_user='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
}

//ganti password 
elseif ($page == 'simpeg' and $action == 'ganti-password') {
    if (getOne("select tm_user.password from tm_user where tm_user.id_user='$id'") == paramEncrypt(isset($_POST['password_lama']) ? $_POST['password_lama'] : null)) {
        $pass_new = paramEncrypt(isset($_POST['password_baru']) ? $_POST['password_baru'] : null);
        bukainput("update tm_user set password='$pass_new' where id_user='$id'");
        header('location:../../logout.php');
    } else {
        echo "<script>alert('Maaf Password Lama Anda Tidak Sesuai!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'spv' and $action == 'ganti-password-spv') {
    $enkrip = paramEncrypt($id);
    if (getOne("select setup.password from setup where kode_skpd='$enkrip'") == paramEncrypt(real_escape(isset($_POST['pass_lama']) ? $_POST['pass_lama'] : null))) {
        $pass_new = paramEncrypt(real_escape(isset($_POST['pass_baru']) ? $_POST['pass_baru'] : null));
        bukainput("update setup set password='$pass_new' where kode_skpd='$enkrip'");
        header('location:../../logout');
    } else {
        echo "<script>alert('Maaf Password Lama Anda Tidak Sesuai!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
}


//simpeg riwayat_pendidikan   
elseif ($page == 'simpeg' and $action == 'add-riwayat-pendidikan') {
    $autonumber = notahun('id_riwayat_pend', 'tm_riwayat_pend');
    $tgl_ijazah = FormatTgl('Y-m-d', $_POST['tgl_ijazah']);
    bukainput("insert into tm_riwayat_pend set id_riwayat_pend='$autonumber', id_user='$id', pendidikan='$_POST[pendidikan]',periode='$_POST[periode]', "
            . "nama_sekolah='$_POST[nama_sekolah]', tgl_ijazah='$tgl_ijazah',kota='$_POST[kota]',jurusan='$_POST[jurusan]', no_ijazah='$_POST[no_ijazah]'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'update-riwayat-pendidikan') {
    $tgl_ijazah = FormatTgl('Y-m-d', $_POST['tgl_ijazah']);
    bukainput("update tm_riwayat_pend set pendidikan='$_POST[pendidikan]',periode='$_POST[periode]', "
            . "nama_sekolah='$_POST[nama_sekolah]', tgl_ijazah='$tgl_ijazah',kota='$_POST[kota]',jurusan='$_POST[jurusan]', no_ijazah='$_POST[no_ijazah]' where id_riwayat_pend='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-pendidikan') {
    hapusinput("delete from tm_riwayat_pend where id_riwayat_pend='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg keluarga  
elseif ($page == 'simpeg' and $action == 'add-keluarga') {
    $autonumber = notahun('id_fams', 'tm_keluarga');
    $tgl = FormatTgl('Y-m-d', $_POST['tanggal_lahir']);
    bukainput("insert into tm_keluarga set id_fams='$autonumber', id_user='$id', nik='$_POST[nik]', nama_keluarga='$_POST[nama_keluarga]',hubungan='$_POST[hubungan]', "
            . "tgl_lahir='$tgl',jk='$_POST[jk]',pendidikan='$_POST[pendidikan]'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'update-keluarga') {
    $tgl_lahir = FormatTgl('Y-m-d', $_POST['tgl_lahir']);
    bukainput("update tm_keluarga set nik='$_POST[nik]', nama_keluarga='$_POST[nama_keluarga]',hubungan='$_POST[hubungan]', "
            . "tgl_lahir='$tgl_lahir',jk='$_POST[jk]',pendidikan='$_POST[pendidikan]' where id_fams='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'delete-keluarga') {
    hapusinput("delete from tm_keluarga where id_fams='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg riwayat penempatan   
elseif ($page == 'simpeg' and $action == 'add-riwayat-penempatan') {
    $autonumber = notahun('id_riwayat_penempatan', 'tm_riwayat_penempatan');
    bukainput("insert into tm_riwayat_penempatan set id_riwayat_penempatan='$autonumber', id_user='$id', no_sk='$_POST[no_sk]', id_unit='$_POST[id_unit]', periode='$_POST[periode]'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'update-riwayat-penempatan') {
    bukainput("update tm_riwayat_penempatan set no_sk='$_POST[no_sk]', id_unit='$_POST[id_unit]', periode='$_POST[periode]' where id_riwayat_penempatan='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-penempatan') {
    hapusinput("delete from tm_riwayat_penempatan where id_riwayat_penempatan='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg riwayat diklat  

elseif ($page == 'simpeg' and $action == 'add-riwayat-diklat') {
    $autonumber = notahun('id_riwayat_diklat', 'tm_riwayat_diklat');
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/diklat/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran <= '5242880') {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_riwayat_diklat set id_riwayat_diklat='$autonumber', id_user='$id', nama_pelatihan='$_POST[nama_pelatihan]', instansi_pelatihan='$_POST[instansi_pelatihan]', lokasi='$_POST[lokasi]',"
            . "alamat_pelatihan='$_POST[alamat_pelatihan]', periode='$_POST[periode]', total_jam='$_POST[total_jam]', jenis_diklat='$_POST[jenis_diklat]', no_sertifikat='$_POST[no_sertifikat]', status_akreditasi='$_POST[status_akreditasi]', status_peserta='$_POST[status_peserta]', jumlah_skp='$_POST[nilai_skp]', config='1', validasi='T', file_diklat='$nama_baru', keterangan=''");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload foto, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainput("insert into tm_riwayat_diklat set id_riwayat_diklat='$autonumber', id_user='$id', nama_pelatihan='$_POST[nama_pelatihan]', instansi_pelatihan='$_POST[instansi_pelatihan]', lokasi='$_POST[lokasi]',"
            . "alamat_pelatihan='$_POST[alamat_pelatihan]', periode='$_POST[periode]', total_jam='$_POST[total_jam]', jenis_diklat='$_POST[jenis_diklat]', no_sertifikat='$_POST[no_sertifikat]', status_akreditasi='$_POST[status_akreditasi]', status_peserta='$_POST[status_peserta]', jumlah_skp='$_POST[nilai_skp]', config='1', validasi='T', keterangan=''");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
}

//update diklat
elseif ($page == 'simpeg' and $action == 'update-riwayat-diklat') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/diklat/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran <= '5242880') {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_diklat = getOne("select file_diklat from tm_riwayat_diklat where id_user='$id'");
                unlink("../../simpeg/" . $file_diklat);
                bukainputcek("update tm_riwayat_diklat set nama_pelatihan='$_POST[nama_pelatihan]', instansi_pelatihan='$_POST[instansi_pelatihan]', lokasi='$_POST[lokasi]',"
            . "alamat_pelatihan='$_POST[alamat_pelatihan]', periode='$_POST[periode]', total_jam='$_POST[total_jam]', jenis_diklat='$_POST[jenis_diklat]', no_sertifikat='$_POST[no_sertifikat]', status_akreditasi='$_POST[status_akreditasi]', status_peserta='$_POST[status_peserta]', jumlah_skp='$_POST[nilai_skp]', validasi='T', file_diklat='$nama_baru' where id_riwayat_diklat = '$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload foto, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainput("update tm_riwayat_diklat set nama_pelatihan='$_POST[nama_pelatihan]', instansi_pelatihan='$_POST[instansi_pelatihan]', lokasi='$_POST[lokasi]',"
            . "alamat_pelatihan='$_POST[alamat_pelatihan]', periode='$_POST[periode]', total_jam='$_POST[total_jam]', jenis_diklat='$_POST[jenis_diklat]', no_sertifikat='$_POST[no_sertifikat]', status_akreditasi='$_POST[status_akreditasi]', status_peserta='$_POST[status_peserta]', jumlah_skp='$_POST[nilai_skp]', validasi='T' "
            . "where id_riwayat_diklat='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
}


elseif ($page == 'simpeg' and $action == 'delete-riwayat-diklat') {
    $file_diklat = getOne("select file_diklat from tm_riwayat_diklat where id_user='$id'");
    unlink("../../simpeg/" . $file_diklat);
    hapusinput("delete from tm_riwayat_diklat where id_riwayat_diklat='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//upload ktp
elseif ($page == 'simpeg' and $action == 'add-ktp') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_ktp set "
                        . "no_nik='$_POST[no_nik]',"
                        . "id_user='$id',"
                        . "file_ktp='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file ktp!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-ktp') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_ktp = getOne("select tm_ktp.file_ktp from tm_ktp where no_nik='$id'");
                unlink("../../simpeg/" . $file_ktp);
                bukainputcek("update tm_ktp set "
                        . "no_nik='$_POST[no_nik]',"
                        . "file_ktp='$nama_baru' where no_nik='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_ktp set "
                . "no_nik='$_POST[no_nik]'"
                . "where no_nik='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-ktp') {
    $file_ktp = getOne("select tm_ktp.file_ktp from tm_ktp where no_nik='$id'");
    unlink("../../simpeg/" . $file_ktp);
    hapusinput("delete from tm_ktp where no_nik='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//upload kk
elseif ($page == 'simpeg' and $action == 'add-kk') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_kk set "
                        . "no_kk='$_POST[no_kk]',"
                        . "id_user='$id',"
                        . "file_kk='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file kk!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-kk') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_kk = getOne("select tm_kk.file_kk from tm_kk where no_kk='$id'");
                unlink("../../simpeg/" . $file_kk);
                bukainputcek("update tm_kk set "
                        . "no_kk='$_POST[no_kk]'"
                        . "file_kk='$nama_baru' where no_kk='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file KK!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'delete-kk') {
    $file_kk = getOne("select tm_kk.file_kk from tm_kk where no_kk='$id'");
    unlink("../../simpeg/" . $file_kk);
    hapusinput("delete from tm_kk where no_kk='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//upload ijazah
elseif ($page == 'simpeg' and $action == 'add-ijazah') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_ijazah set "
                        . "id_riwayat_pend='$_POST[id_riwayat_pend]',"
                        . "id_user='$id',"
                        . "file_ijazah='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file Ijazah!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-ijazah') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_ijazah = getOne("select tm_ijazah.file_ijazah from tm_ijazah where id_riwayat_pend='$id'");
                unlink("../../simpeg/" . $file_ijazah);
                bukainputcek("update tm_ijazah set "
                        . "id_riwayat_pend='$_POST[id_riwayat_pend]',"
                        . "file_ijazah='$nama_baru' where id_riwayat_pend='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_ijazah set "
                . "id_riwayat_pend='$_POST[id_riwayat_pend]',"
                . "id_user='$id' where id_riwayat_pend='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-ijazah') {
    $file_ijazah = getOne("select tm_ijazah.file_ijazah from tm_ijazah where id_riwayat_pend='$id'");
    unlink("../../simpeg/" . $file_ijazah);
    hapusinput("delete from tm_ijazah where id_riwayat_pend='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//upload npwp
elseif ($page == 'simpeg' and $action == 'add-npwp') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_npwp set "
                        . "no_npwp='$_POST[no_npwp]',"
                        . "id_user='$id',"
                        . "file_npwp='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file NPWP!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-npwp') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_npwp = getOne("select tm_npwp.file_npwp from tm_npwp where no_npwp='$id'");
                unlink("../../simpeg/" . $file_npwp);
                bukainputcek("update tm_npwp set "
                        . "no_npwp='$_POST[no_npwp]',"
                        . "file_npwp='$nama_baru' where no_npwp='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_npwp set "
                . "no_npwp='$_POST[no_npwp]'"
                . "where no_npwp='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-npwp') {
    $file_npwp = getOne("select tm_npwp.file_npwp from tm_npwp where no_npwp='$id'");
    unlink("../../simpeg/" . $file_npwp);
    hapusinput("delete from tm_npwp where no_npwp='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg str
elseif ($page == 'simpeg' and $action == 'add-riwayat-str') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_str', 'tm_str');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_str set "
                        . "id_str='$autonumber',"
                        . "id_user='$id',"
                        . "no_str='$_POST[no_str]',"
                        . "periode='$_POST[periode]',"
                        . "file_str='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file STR !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-str') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_str = getOne("select tm_str.file_str from tm_str where id_str='$id'");
                unlink("../../simpeg/" . $file_str);
                bukainputcek("update tm_str set "
                        . "no_str='$_POST[no_str]',"
                        . "periode='$_POST[periode]',"
                        . "file_str='$nama_baru' where id_str='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_str set "
                . "no_str='$_POST[no_str]',"
                . "periode='$_POST[periode]' where id_str='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-str') {
    $file_str = getOne("select tm_str.file_str from tm_str where id_str='$id'");
    unlink("../../simpeg/" . $file_str);
    hapusinput("delete from tm_str where id_str='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg sip
elseif ($page == 'simpeg' and $action == 'add-riwayat-sip') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_sip', 'tm_sip');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_sip set "
                        . "id_sip='$autonumber',"
                        . "id_user='$id',"
                        . "no_sip='$_POST[no_sip]',"
                        . "periode='$_POST[periode]',"
                        . "file_sip='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
      echo "<script>alert('Maaf anda belum upload file SIP !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-sip') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_sip = getOne("select tm_sip.file_sip from tm_sip where id_sip='$id'");
                unlink("../../simpeg/" . $file_sip);
                bukainputcek("update tm_sip set "
                        . "no_sip='$_POST[no_sip]',"
                        . "periode='$_POST[periode]',"
                        . "file_sip='$nama_baru' where id_sip='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_sip set "
                . "no_sip='$_POST[no_sip]',"
                . "periode='$_POST[periode]' where id_sip='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-sip') {
    $file_sip = getOne("select tm_sip.file_sip from tm_sip where id_sip='$id'");
    unlink("../../simpeg/" . $file_sip);
    hapusinput("delete from tm_sip where id_sip='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg acls
elseif ($page == 'simpeg' and $action == 'add-riwayat-acls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_acls', 'tm_acls');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_acls set "
                        . "id_acls='$autonumber',"
                        . "id_user='$id',"
                        . "no_acls='$_POST[no_acls]',"
                        . "periode='$_POST[periode]',"
                        . "file_acls='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
      echo "<script>alert('Maaf anda belum upload file ACLS !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-acls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_acls = getOne("select tm_acls.file_acls from tm_acls where id_acls='$id'");
                unlink("../../simpeg/" . $file_acls);
                bukainputcek("update tm_acls set "
                        . "no_acls='$_POST[no_acls]',"
                        . "periode='$_POST[periode]',"
                        . "file_acls='$nama_baru' where id_acls='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_acls set "
                . "no_acls='$_POST[no_acls]',"
                . "periode='$_POST[periode]' where id_acls='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-acls') {
    $file_acls = getOne("select tm_acls.file_acls from tm_acls where id_acls='$id'");
    unlink("../../simpeg/" . $file_acls);
    hapusinput("delete from tm_acls where id_acls='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg atls
elseif ($page == 'simpeg' and $action == 'add-riwayat-atls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_atls', 'tm_atls');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_atls set "
                        . "id_atls='$autonumber',"
                        . "id_user='$id',"
                        . "no_atls='$_POST[no_atls]',"
                        . "periode='$_POST[periode]',"
                        . "file_atls='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file ATLS !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-atls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_atls = getOne("select tm_atls.file_atls from tm_atls where id_atls='$id'");
                unlink("../../simpeg/" . $file_atls);
                bukainputcek("update tm_atls set "
                        . "no_atls='$_POST[no_atls]',"
                        . "periode='$_POST[periode]',"
                        . "file_atls='$nama_baru' where id_atls='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_atls set "
                . "no_atls='$_POST[no_atls]',"
                . "periode='$_POST[periode]' where id_atls='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-atls') {
    $file_atls = getOne("select tm_atls.file_atls from tm_atls where id_atls='$id'");
    unlink("../../simpeg/" . $file_atls);
    hapusinput("delete from tm_atls where id_atls='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg bctls
elseif ($page == 'simpeg' and $action == 'add-riwayat-btcls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_btcls', 'tm_btcls');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_btcls set "
                        . "id_btcls='$autonumber',"
                        . "id_user='$id',"
                        . "no_btcls='$_POST[no_btcls]',"
                        . "periode='$_POST[periode]',"
                        . "file_btcls='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file BTCLS !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-btcls') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_btcls = getOne("select tm_btcls.file_btcls from tm_btcls where id_btcls='$id'");
                unlink("../../simpeg/" . $file_btcls);
                bukainputcek("update tm_btcls set "
                        . "no_btcls='$_POST[no_btcls]',"
                        . "periode='$_POST[periode]',"
                        . "file_btcls='$nama_baru' where id_btcls='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_btcls set "
                . "no_btcls='$_POST[no_btcls]',"
                . "periode='$_POST[periode]' where id_btcls='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-btcls') {
    $file_btcls = getOne("select tm_btcls.file_btcls from tm_btcls where id_btcls='$id'");
    unlink("../../simpeg/" . $file_btcls);
    hapusinput("delete from tm_btcls where id_btcls='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg apn
elseif ($page == 'simpeg' and $action == 'add-riwayat-apn') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_apn', 'tm_apn');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_apn set "
                        . "id_apn='$autonumber',"
                        . "id_user='$id',"
                        . "no_apn='$_POST[no_apn]',"
                        . "periode='$_POST[periode]',"
                        . "file_apn='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
       echo "<script>alert('Maaf anda belum upload file APN !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-apn') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_apn = getOne("select tm_apn.file_apn from tm_apn where id_apn='$id'");
                unlink("../../simpeg/" . $file_apn);
                bukainputcek("update tm_apn set "
                        . "no_apn='$_POST[no_apn]',"
                        . "periode='$_POST[periode]',"
                        . "file_apn='$nama_baru' where id_apn='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_apn set "
                . "no_apn='$_POST[no_apn]',"
                . "periode='$_POST[periode]' where id_apn='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-apn') {
    $file_apn = getOne("select tm_apn.file_apn from tm_apn where id_apn='$id'");
    unlink("../../simpeg/" . $file_apn);
    hapusinput("delete from tm_apn where id_apn='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

//simpeg phelebethomy
elseif ($page == 'simpeg' and $action == 'add-riwayat-phelebethomy') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    $autonumber = notahun('id_phelebethomy', 'tm_phelebethomy');
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_phelebethomy set "
                        . "id_phelebethomy='$autonumber',"
                        . "id_user='$id',"
                        . "no_phelebethomy='$_POST[no_phelebethomy]',"
                        . "periode='$_POST[periode]',"
                        . "file_phelebethomy='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
       echo "<script>alert('Maaf anda belum upload file PHELEBETHOMY !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-riwayat-phelebethomy') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_phelebethomy = getOne("select tm_phelebethomy.file_phelebethomy from tm_phelebethomy where id_phelebethomy='$id'");
                unlink("../../simpeg/" . $file_phelebethomy);
                bukainputcek("update tm_phelebethomy set "
                        . "no_phelebethomy='$_POST[no_phelebethomy]',"
                        . "periode='$_POST[periode]',"
                        . "file_phelebethomy='$nama_baru' where id_phelebethomy='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_phelebethomy set "
                . "no_phelebethomy='$_POST[no_phelebethomy]',"
                . "periode='$_POST[periode]' where id_phelebethomy='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-riwayat-phelebethomy') {
    $file_phelebethomy = getOne("select tm_phelebethomy.file_phelebethomy from tm_phelebethomy where id_phelebethomy='$id'");
    unlink("../../simpeg/" . $file_phelebethomy);
    hapusinput("delete from tm_phelebethomy where id_phelebethomy='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
}

// upload sk jabatan
elseif ($page == 'simpeg' and $action == 'add-sk-jabatan') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_sk_jabatan set "
                        . "id_riwayat_penempatan='$_POST[id_riwayat_penempatan]',"
                        . "id_user='$id',"
                        . "file_skjabatan='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_27'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file SK Jabatan!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-sk-jabatan') {
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_skjabatan = getOne("select tm_sk_jabatan.file_skjabatan from tm_sk_jabatan where id_riwayat_penempatan='$id'");
                unlink("../../simpeg/" . $file_skjabatan);
                bukainputcek("update tm_sk_jabatan set "
                        . "id_riwayat_penempatan='$_POST[id_riwayat_penempatan]',"
                        . "file_skjabatan='$nama_baru' where id_riwayat_penempatan='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_sk_jabatan set "
                . "id_riwayat_penempatan='$_POST[id_riwayat_penempatan]',"
                . "id_user='$id' where id_riwayat_penempatan='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_27'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-sk-jabatan') {
    $file_skjabatan = getOne("select tm_sk_jabatan.file_skjabatan from tm_sk_jabatan where id_riwayat_penempatan='$id'");
    unlink("../../simpeg/" . $file_skjabatan);
    hapusinput("delete from tm_sk_jabatan where id_riwayat_penempatan='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_27'));
}

// upload spk / rkk
elseif ($page == 'simpeg' and $action == 'add-spkrkk') {
    $autonumber = notahun('id_spkrkk', 'tm_spk_rkk');
    $tgl = FormatTgl('Y-m-d', $_POST['tanggal_dikeluarkan']);
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";

    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                bukainputcek("insert into tm_spk_rkk set "
                        . "id_spkrkk='$autonumber',"
                        . "id_user='$id',"
                        . "no_spkrkk='$_POST[no_spkrkk]',"
                        . "tgl_dikeluarkan='$tgl',"
                        . "tipe='$_POST[tipe]',"
                        . "file_spkrkk='$nama_baru'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_28'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file SPK / RKK!!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'simpeg' and $action == 'update-spkrkk') {
    $tgl = FormatTgl('Y-m-d', $_POST['tgl_dikeluarkan']);
    $ekstensi_diperbolehkan = array('png', 'jpg', 'pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $id . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../simpeg/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                $file_spkrkk = getOne("select tm_spk_rkk.file_spkrkk from tm_spk_rkk where id_spkrkk='$id'");
                unlink("../../simpeg/" . $file_spkrkk);
                bukainputcek("update tm_spk_rkk set "
                        . "no_spkrkk='$_POST[no_spkrkk]',"
                        . "tgl_dikeluarkan='$tgl',"
                        . "tipe='$_POST[tipe]',"
                        . "file_spkrkk='$nama_baru' where id_spkrkk='$id'");
                header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_28'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF, JPEG dan PNG!!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        bukainputcek("update tm_spk_rkk set "
                . "no_spkrkk='$_POST[no_spkrkk]',"
                . "tgl_dikeluarkan='$tgl',"
                . "tipe='$_POST[tipe]' where id_spkrkk='$id'");
        header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_28'));
    }
} elseif ($page == 'simpeg' and $action == 'delete-spkrkk') {
    $file_spkrkk = getOne("select tm_spk_rkk.file_spkrkk from tm_spk_rkk where id_spkrkk='$id'");
    unlink("../../simpeg/" . $file_spkrkk);
    hapusinput("delete from tm_spk_rkk where id_spkrkk='$id'");
    header('location:../../page-view?' . paramEncrypt('module=simpeg&act=profile-update&main_tab=tab_3&sub_tab=tab_28'));
}

?>
