<?php

require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;

if ($page == 'perpustakaan' and $action == 'add-ebook') {
    $autonumber = nokiamat('id_ebook', 'tm_ebook');
    $ekstensi_diperbolehkan = array('pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $autonumber  . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../files/ebook/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 10242880) {
                bukainputcek("insert into tm_ebook set id_ebook='$autonumber', no_ebook='$_POST[no_ebook]',kategori='$_POST[kategori]',judul='$_POST[judul]',penulis='$_POST[penulis]',penerbit='$_POST[penerbit]',tahun='$_POST[tahun]',kota='$_POST[kota]',tentang='$_POST[tentang]',deskripsi='$_POST[deskripsi]', status='1', file='$nama_baru'");
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=ebook'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 10MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file !!'); window.location = 'javascript:history.go(-1)'</script>";
    }

} elseif ($page == 'perpustakaan' and $action == 'add-spo') {
    $autonumber = nokiamat('id_spo', 'tm_spo');
    $ekstensi_diperbolehkan = array('pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $autonumber  . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../files/spo/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                bukainputcek("insert into tm_spo set id_spo='$autonumber', no_spo='$_POST[no_spo]',tentang='$_POST[tentang]',deskripsi='$_POST[deskripsi]', status='1', file='$nama_baru'");
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=spo'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'perpustakaan' and $action == 'add-sk') {
    $tanggal = FormatTgl('Y-m-d', $_POST['tanggal_sk']);
    $autonumber = nokiamat('id_sk', 'tm_sk');
    $ekstensi_diperbolehkan = array('pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $autonumber  . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../files/sk/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 5242880) {
                bukainputcek("insert into tm_sk set id_sk='$autonumber', no_sk='$_POST[no_sk]',tanggal_sk='$tanggal', tentang='$_POST[tentang]',deskripsi='$_POST[deskripsi]', status='1', file='$nama_baru'");
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=kebijakan'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 5MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} elseif ($page == 'perpustakaan' and $action == 'add-peraturan') {
    $autonumber = nokiamat('id_peraturan', 'tm_peraturan');
    $ekstensi_diperbolehkan = array('pdf');
    $nama = $_FILES['file']['name'];
    $x = explode('.', $nama);
    $nama_baru = $autonumber  . "_" . round(microtime(true)) . '.' . end($x);
    $ekstensi = strtolower(end($x));
    $ukuran = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $folder = "../../files/peraturan/";
    if ($nama != '') {
        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 32428800) {                
                bukainputcek("insert into tm_peraturan set id_peraturan='$autonumber', no_peraturan='$_POST[no_peraturan]',tentang='$_POST[tentang]', jenis='$_POST[jenis]', status='1', file='$nama_baru'");
                move_uploaded_file($file_tmp, $folder . $nama_baru);
                header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=peraturan'));
            } else {
                echo "<script>alert('GAGAL !! Upload file, mungkin terlalu besar, tidak diperbolehkan lebih dari 9MB !!'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('File hanya diperbolehkan berformat PDF !!'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('Maaf anda belum upload file !!'); window.location = 'javascript:history.go(-1)'</script>";
    }
}

//delete
 elseif ($page == 'perpustakaan' and $action == 'delete-ebook') {
    $file = getOne("select tm_ebook.file from tm_ebook where id_ebook='$id'");    
    hapusinput("delete from tm_ebook where id_ebook='$id'");
    unlink("../../files/ebook/" . $file);
    header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=ebook'));
} elseif ($page == 'perpustakaan' and $action == 'delete-spo') {
    $file = getOne("select tm_spo.file from tm_spo where id_spo='$id'");    
    hapusinput("delete from tm_spo where id_spo='$id'");
    unlink("../../files/spo/" . $file);
    header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=spo'));
} elseif ($page == 'perpustakaan' and $action == 'delete-sk') {
    $file = getOne("select tm_sk.file from tm_sk where id_sk='$id'");    
    hapusinput("delete from tm_sk where id_sk='$id'");
    unlink("../../files/sk/" . $file);
    header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=kebijakan'));
} elseif ($page == 'perpustakaan' and $action == 'delete-peraturan') {
    $file = getOne("select tm_peraturan.file from tm_peraturan where id_peraturan='$id'");    
    hapusinput("delete from tm_peraturan where id_peraturan='$id'");
    unlink("../../files/peraturan/" . $file);
    header('location:../../page-view?' . paramEncrypt('module=perpustakaan&act=peraturan'));
}
?>