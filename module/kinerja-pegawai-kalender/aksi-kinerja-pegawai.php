<?php

require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null;
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;
/* fallback: terima id dari POST agar aman untuk form/AJAX */
if (!$id && isset($_POST['id'])) $id = $_POST['id'];

//setting validasi penginputan
$tutup_kinerja = getOne("select setup.tutup_kinerja from setup");
//untuk skp tahunan
if ($page == 'kinerja-pegawai' and $action == 'add-skp-tahunan') {
    $autonumber = notahun('id_skp', 'tt_skptahunan');
    bukainput("insert into tt_skptahunan set id_skp='$autonumber', id_user='$id', kd_skp='$_POST[kd_skp]',kuantitas='$_POST[kuantitas]',tgl_buat=now(),tgl_update=now()");
    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'));
} elseif ($page == 'kinerja-pegawai' and $action == 'update-skp-tahunan') {
    bukainput("update tt_skptahunan set kd_skp='$_POST[kd_skp]',kuantitas='$_POST[kuantitas]',tgl_update=now() where id_skp='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'));
} elseif ($page == 'kinerja-pegawai' and $action == 'delete-skp-tahunan') {
    hapusinput("delete from tt_skptahunan where id_skp='$id'");
    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-skp-tahunan-pegawai'));
}

// ===== helper kecil: validasi jendela input =====
function boleh_input_tanggal_kinerja($tanggal, $tutup_kinerja) {
    // $tanggal = 'Y-m-d'
    if (FormatTgl('d', date('d-m-Y')) <= (int)$tutup_kinerja) {
        // sampai tgl tutup → hanya boleh bulan kemarin
        return (FormatTgl('m-Y', $tanggal) == BulanKemarin());
    } else {
        // lewat tgl tutup → hanya boleh bulan ini
        return (FormatTgl('m-Y', $tanggal) == FormatTgl('m-Y', date('d-m-Y')));
    }
}

// ====== KINERJA PEGAWAI: ADD (Utama/Tambahan) ======
if ($page === 'kinerja-pegawai' && in_array($action, ['add-kinerja','add-kinerja-utama','add-kinerja-tambahan'])) {
    $autonumber = nokiamat('id_kinerja', 'tt_kinerja');
    $tanggal    = FormatTgl('Y-m-d', $_POST['tanggal_kinerja'] ?? '');
    $status     = ($action === 'add-kinerja-tambahan') ? 'Tambahan'
                : (($action === 'add-kinerja-utama') ? 'Utama'
                : (($_POST['jenisRadio'] ?? 'Utama') === 'Tambahan' ? 'Tambahan' : 'Utama'));

    // ========== GUARD minimal ==========
    if (!$id) {
        echo "<script>alert('ID user tidak diketahui.');history.back();</script>"; exit;
    }
    if (!$tanggal) {
        echo "<script>alert('Tanggal kinerja kosong.');history.back();</script>"; exit;
    }
    // kd_skp HARUS ada. (ingat: hidden input name='kd_skp' di form!)
    $kd_skp = $_POST['kd_skp'] ?? '';
    if ($kd_skp === '') {
        echo "<script>alert('Aktivitas (SKP) belum dipilih.');history.back();</script>"; exit;
    }

    if (!boleh_input_tanggal_kinerja($tanggal, $tutup_kinerja)) {
        $pesan = (FormatTgl('d', date('d-m-Y')) <= $tutup_kinerja) ? BulanKemarin() : FormatTgl('m-Y', date('d-m-Y'));
        echo "<script>alert('Maaf, input hanya diperbolehkan untuk bulan {$pesan}.');history.back();</script>";
        exit;
    }

    $uraian      = $_POST['uraian']       ?? '';
    $waktu_mulai = $_POST['waktu_mulai']  ?? '';
    $waktu_akhir = $_POST['waktu_akhir']  ?? '';

    bukainput("INSERT INTO tt_kinerja SET
        id_kinerja      = '$autonumber',
        id_user         = '$id',
        tanggal_kinerja = '$tanggal',
        kd_skp          = '$kd_skp',
        uraian          = '$uraian',
        waktu_mulai     = '$waktu_mulai',
        waktu_akhir     = '$waktu_akhir',
        `date`          = NOW(),
        `status`        = '$status',
        validasi        = 'T'");

    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
    exit;
}

// ===== UPDATE (Detil → Simpan) =====
elseif ($page === 'kinerja-pegawai' && in_array($action, ['update-kinerja','update-kinerja-utama'])) {
    if (!$id) { echo "<script>alert('ID kinerja tidak ditemukan.');history.back();</script>"; exit; }

    $kd_skp      = $_POST['kd_skp']      ?? '';
    $uraian      = $_POST['uraian']      ?? '';
    $waktu_mulai = $_POST['waktu_mulai'] ?? '';
    $waktu_akhir = $_POST['waktu_akhir'] ?? '';

    if ($kd_skp === '') {
        echo "<script>alert('Aktivitas (SKP) belum dipilih.');history.back();</script>"; exit;
    }

    bukainput("UPDATE tt_kinerja SET
                  kd_skp      = '$kd_skp',
                  uraian      = '$uraian',
                  waktu_mulai = '$waktu_mulai',
                  waktu_akhir = '$waktu_akhir',
                  `date`      = NOW()
               WHERE id_kinerja = '$id'");

    header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
    exit;
}

// ===== DELETE =====
elseif ($page === 'kinerja-pegawai' && $action === 'delete-kinerja') {
    if (!$id) { echo "<script>alert('ID kinerja tidak ditemukan.');history.back();</script>"; exit; }

    $ok = hapusinput("DELETE FROM tt_kinerja WHERE id_kinerja='$id'");

    // Jika dipanggil dari AJAX, cukup keluar (client yang reload)
    if (!empty($_POST['ajax'])) {
        // Optional: echo 'OK'/'ERR' di sini kalau mau.
        exit;
    }

    if ($ok) {
        header('location:../../page-view?' . paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai'));
    } else {
        echo "<script>alert('Gagal menghapus. Data mungkin masih dipakai.');history.back();</script>";
    }
    exit;
}