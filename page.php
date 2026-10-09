<?php
$page = isset($url['module']) ? $url['module'] : null;
if ($page == "dashboard") {
    include "module/dashboard/dashboard.php";
} elseif ($page == "absensi-pegawai") {
    include "module/absensi/absensi-pegawai.php";
} elseif ($page == "kinerja-pegawai") {
    include "module/kinerja-pegawai/kinerja-pegawai.php";
} elseif ($page == "simpeg") {
    include "module/simpeg/simpeg.php";
} elseif ($page == "master-data") {
    include "module/master-data/master-data.php";
} elseif ($page == "validasi-pegawai") {
    include "module/validasi-pegawai/validasi-pegawai.php";
} elseif ($page == "cuti-pegawai") {
    include "module/cuti-pegawai/cuti-pegawai.php";
} elseif ($page == "form-surat-peringatan") {
    include "module/form-sp/form-sp.php";
} elseif ($page == "form-surat-tugas") {
    include "module/form-tugas/form-tugas.php";
} elseif ($page == "diklat") {
    include "module/diklat/diklat.php";
} elseif ($page == "laporan") {
    include "module/laporan/laporan.php";
} elseif ($page == "perpustakaan") {
    include "module/perpustakaan/perpustakaan.php";
} elseif ($page == "configurasi") {
    include "module/hak-akses/hak-akses.php";
} elseif ($page == "helpdesk") {
    include "module/helpdesk/helpdesk.php";
}elseif ($page == "agenda-rapat") {
    include "module/agenda-rapat/agenda-rapat.php";
}elseif ($page == "jadwal-pegawai") {
    include "module/jadwal-pegawai/jadwal-pegawai.php";
}elseif ($page == "daftar-hadir") {
    include "module/daftar-hadir/daftar-pegawai.php";
}elseif ($page == "k3") {
    include "module/k3/input-k3-pegawai.php";
}elseif ($page == "view-k3") {
    include "module/k3/view-k3-pegawai.php";
}elseif ($page == "kesling") {
    include "module/kesling/input-kesling.php";
}elseif ($page == "view") {
    include "module/kesling/view-kesling.php";
}elseif ($page == "view-kesling-unit") {
    include "module/kesling/view-kesling-unit.php";
}elseif ($page == "mcu-pegawai") {
    include "module/mcu-pegawai/mcu-pegawai.php";
}elseif ($page == "mcu") {
    include "module/mcu-pegawai/up-mcu-pegawai.php";
}elseif ($page == "olahraga-pegawai") {
    include "module/olahraga-pegawai/olahraga-pegawai.php";
}elseif ($page == "imt-pegawai") {
    include "module/imt-pegawai/imt-pegawai.php";
}elseif ($page == "kebersihan-unit") {
    include "module/kesling/kebersihan.php";
}elseif ($page == "kebersihan") {
    include "module/kesling/kebersihan-unit.php";
}else {
    include"eror.php";
}
?>