<?php
require_once ('../../conf/conf.php');
require_once ('../../libs/aes-encrypt/function.php');

$koneksi = bukakoneksi();

$URI   = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url   = decode($URI);
$page  = isset($url['module']) ? $url['module'] : null;
$action= isset($url['act']) ? $url['act'] : null;
$id    = isset($url['id']) ? $url['id'] : null;

/**
 * Generate ID MCU berdasarkan tahun + counter
 * contoh: 20250001, 20250002, dst
 */
function generateIdMcu($koneksi) {
    $tahun = date("Y");
    $sql = "SELECT MAX(id_mcu) as maxID FROM tm_mcu WHERE LEFT(id_mcu,4) = '$tahun'";
    $result = mysqli_query($koneksi, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row && $row['maxID']) {
        $lastNo = intval(substr($row['maxID'], 4));
        $nextNo = str_pad($lastNo + 1, 4, "0", STR_PAD_LEFT);
    } else {
        $nextNo = "0001";
    }

    return [
        'id_mcu' => $tahun . $nextNo, // contoh: 20250001
        'no_urut' => $nextNo          // contoh: 0001
    ];
}

if ($page == 'mcu' and $action == 'add-up-mcu-pegawai') {
    $ekstensi_diperbolehkan = ['png', 'jpg', 'pdf'];
    $nama      = $_FILES['file']['name'];
    $ekstensi  = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
    $ukuran    = $_FILES['file']['size'];
    $file_tmp  = $_FILES['file']['tmp_name'];
    $folder    = "../../files/mcu/";

    $auto      = generateIdMcu($koneksi);
    $autonumber= $auto['id_mcu'];   // ✅ id_mcu final
    $nextNo    = $auto['no_urut'];  // ✅ nomor urut

    $id_user   = $_POST['user'];
    $tgl_mcu   = $_POST['tgl_mcu'];
    $hasil     = $_POST['hasil'];
    
    $tgl_mcu_db = date("Y-m-d", strtotime(str_replace("/", "-", $tgl_mcu)));

    // Format nama file => idUser_tglMCU_noUrut.pdf
    $tgl_format = str_replace("/", "-", $tgl_mcu);
    $nama_baru  = $id_user . "_" . $tgl_format . "_" . $nextNo . "." . $ekstensi;

    if ($nama != '') {
    if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
        if ($ukuran < 5242880) {
            if (move_uploaded_file($file_tmp, $folder . $nama_baru)) {
                
                // $autonumber = generateIdMcu($koneksi);

                $query = "INSERT INTO tm_mcu (id_mcu,id_user,tgl_mcu,hasil,file_mcu)
                          VALUES ('$autonumber','$id_user','$tgl_mcu_db','$hasil','$nama_baru')";

                if (mysqli_query($koneksi, $query)) {
                    header('location:../../page-view?' . paramEncrypt('module=mcu&act=list-data-up-mcu-pegawai'));
                    exit();
                } else {
                    echo "<b>QUERY ERROR:</b><br>$query<br><br>";
                    echo "Error DB: " . mysqli_error($koneksi);
                    exit();
                }
            } else {
                echo "<script>alert('Upload gagal! Tidak bisa memindahkan file ke folder tujuan.'); window.location = 'javascript:history.go(-1)'</script>";
            }
        } else {
            echo "<script>alert('GAGAL !! Upload file, terlalu besar (maks 5MB).'); window.location = 'javascript:history.go(-1)'</script>";
        }
    } else {
        echo "<script>alert('File hanya boleh PDF, JPG, atau PNG!'); window.location = 'javascript:history.go(-1)'</script>";
    }
} else {
    echo "<script>alert('Anda belum memilih file untuk diupload!'); window.location = 'javascript:history.go(-1)'</script>";
}

} elseif ($page == 'mcu' and $action == 'update-up-mcu-pegawai') {
    $nama      = $_FILES['file']['name'];
    $ekstensi  = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
    $ukuran    = $_FILES['file']['size'];
    $file_tmp  = $_FILES['file']['tmp_name'];
    $folder    = "../../files/mcu/";

    $tgl_mcu   = $_POST['tgl_mcu'];
    $hasil     = $_POST['hasil'];

    // tgl ke format DB
    $tgl_mcu_db = date("Y-m-d", strtotime(str_replace("/", "-", $tgl_mcu)));

    // ambil data lama
    $id_user   = getOne("SELECT id_user FROM tm_mcu WHERE id_mcu='$id'");
    $file_lama = getOne("SELECT file_mcu FROM tm_mcu WHERE id_mcu='$id'");

    $tgl_format= str_replace("/", "-", $tgl_mcu);
    $nama_baru = $id_user . "_" . $tgl_format . ".pdf"; // selalu simpan pakai PDF

    if (!empty($nama)) {
        if ($ukuran < 5242880 && in_array($ekstensi, ['pdf','jpg','png'])) {
            // hapus file lama
            if ($file_lama && file_exists($folder . $file_lama)) {
                unlink($folder . $file_lama);
            }
            // simpan file baru dengan nama sama
            move_uploaded_file($file_tmp, $folder . $nama_baru);

            $query = "UPDATE tm_mcu SET 
                        tgl_mcu='$tgl_mcu_db',
                        hasil='$hasil',
                        file_mcu='$nama_baru'
                      WHERE id_mcu='$id'";
        } else {
            echo "<script>alert('File tidak valid atau terlalu besar!'); window.history.back();</script>";
            exit;
        }
    } else {
        // update tanpa ganti file
        $query = "UPDATE tm_mcu SET 
                    tgl_mcu='$tgl_mcu_db',
                    hasil='$hasil'
                  WHERE id_mcu='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        header('location:../../page-view?' . paramEncrypt('module=mcu&act=list-data-up-mcu-pegawai'));
        exit();
    } else {
        echo "<script>alert('Error DB: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
    
} elseif ($page == 'mcu' and $action == 'delete-up-mcu-pegawai') {
    $file_mcu = getOne("SELECT file_mcu FROM tm_mcu WHERE id_mcu='$id'");
    $folder = "../../files/mcu/";
    if ($file_mcu && file_exists($folder . $file_mcu)) {
        unlink($folder . $file_mcu);
    }
    $query = "DELETE FROM tm_mcu WHERE id_mcu='$id'";
    if (mysqli_query($koneksi, $query)) {
        header('location:../../page-view?' . paramEncrypt('module=mcu&act=list-data-up-mcu-pegawai'));
        exit();
    } else {
        echo "<script>alert('Error DB: " . mysqli_error($koneksi) . "'); window.location = 'javascript:history.go(-1)'</script>";
    }
}

?>
