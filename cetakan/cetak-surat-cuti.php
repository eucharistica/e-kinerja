<?php

include '../conf/conf.php';
require('../libs/fpdf181/fpdf.php');
require_once ('../libs/aes-encrypt/function.php');
session_start();
$id = isset($_GET['id']) ? $_GET['id'] : null;
//cek apakah masi ada session login
//if (!isset($_SESSION['nip'])) {
//    header('location:index.php');
//}
//if (!isset($_SESSION['id_level'])) {
//    header('location:index.php');
//}
//if ($_GET['id'] == '' OR $cek == '' ) {
//    header('location:error.php');
//}
//query tampil
$tm_surat = fetch_array(bukaquery("SELECT INSERT(tm_cuti.id_cuti,5,0,'-') as id_cuti,tm_pegawai.nip,tm_pegawai.id_user, tm_pegawai.nama_pegawai,tm_pegawai.sub_bagian, tm_cuti.nip_pengganti,tm_cuti.nip_pj,tm_cuti.nip_kasie, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.periode_cuti, tm_cuti.alamat_cuti,
                            tm_cuti.no_tlp, tm_cuti.alasan_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_pegawai.id_unit, tm_pegawai.sub_bagian, tm_pegawai.id_kasatpel, tm_level.nama_level, tm_pegawai.tgl_masuk, tm_unit.nama_unit, tm_pegawai.sub_bagian
                            FROM tm_cuti
                            inner JOIN tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                            INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                            INNER JOIN tm_level ON tm_user.id_level=tm_level.id_level
                            where tm_cuti.id_cuti='$id'"));
$no_surat = $tm_surat['id_cuti'];
$nip_pengganti = $tm_surat['nip_pengganti'];
$nip_pj = $tm_surat['nip_pj'];
$nip_kasie = $tm_surat['nip_kasie'];
$nm_pengganti = getOne("Select nama_pegawai from tm_pegawai where nip = '$nip_pengganti' ");
$nm_pj = getOne("Select nama_pegawai from tm_pegawai where nip = '$nip_pj' ");
$nm_kasie = getOne("Select nama_pegawai from tm_pegawai where nip = '$nip_kasie' ");
$tanggal1 = date('Y-m-d', strtotime(substr($tm_surat['periode_cuti'], 0, 10)));
$tanggal2 = date('Y-m-d', strtotime(substr($tm_surat['periode_cuti'], 13, 10)));
//maping cuti
if ($tm_surat['jenis_cuti'] == 'CUTI TAHUNAN') {
    $cuti_tahunan = 'YA';
    $cuti_bersama = '-';
    $cuti_sakit = '-';
    $cuti_melahirkan = '-';
    $cuti_alasan_penting = '-';
    $cuti_tanggungan_negara = '-';
} elseif ($tm_surat['jenis_cuti'] == 'CUTI BERSAMA') {
    $cuti_tahunan = '-';
    $cuti_bersama = 'YA';
    $cuti_sakit = '-';
    $cuti_melahirkan = '-';
    $cuti_alasan_penting = '-';
    $cuti_tanggungan_negara = '-';
} elseif ($tm_surat['jenis_cuti'] == 'CUTI SAKIT') {
    $cuti_tahunan = '-';
    $cuti_bersama = '-';
    $cuti_sakit = 'YA';
    $cuti_melahirkan = '-';
    $cuti_alasan_penting = '-';
    $cuti_tanggungan_negara = '-';
} elseif ($tm_surat['jenis_cuti'] == 'CUTI MELAHIRKAN') {
    $cuti_tahunan = '-';
    $cuti_bersama = '-';
    $cuti_sakit = '-';
    $cuti_melahirkan = 'YA';
    $cuti_alasan_penting = '-';
    $cuti_tanggungan_negara = '-';
} elseif ($tm_surat['jenis_cuti'] == 'CUTI KARENA ALASAN PENTING') {
    $cuti_tahunan = '-';
    $cuti_bersama = '-';
    $cuti_sakit = '-';
    $cuti_melahirkan = '-';
    $cuti_alasan_penting = 'YA';
    $cuti_tanggungan_negara = '-';
} elseif ($tm_surat['jenis_cuti'] == 'CUTI DI LUAR TANGGUNGAN NEGARA') {
    $cuti_tahunan = '-';
    $cuti_bersama = '-';
    $cuti_sakit = '-';
    $cuti_melahirkan = '-';
    $cuti_alasan_penting = '-';
    $cuti_tanggungan_negara = 'YA';
}
// $jumlah_sisa = getOne("SELECT (12-(sum(tm_cuti.jumlah_hari))) as jumlah
//                                 from tm_cuti
//                                 where tm_cuti.id_user='$tm_surat[id_user]' and tm_cuti.tahun_cuti='$tm_surat[tahun_cuti]' and jenis_cuti='CUTI TAHUNAN'");

//Cuti manual
$id_user1 = $tm_surat['id_user'];
$year = date("Y");
$hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user= '$id_user1' and tahun ='$year'");
// $cuti_disetujui = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie='Y'");
$cuti_disetujui = getOne("SELECT sum(jumlah_hari) as jumlah from tm_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie='Y'");
// $cuti_diambil = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie!='Y' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
$cuti_diambil = getOne("SELECT sum(jumlah_hari) as jumlah from tm_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie!='Y' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");

$potong_cuti = getOne("SELECT IFNULL(sum(potong_cuti),0) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$year'");

$cutimanual = getOne("SELECT IFNULL(sum(potong_cuti),0) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$year'");
$cutisurat = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and MONTH(tanggal) != MONTH(CURRENT_DATE) and (acc_kasie='T' OR acc_kasie='-') AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
$cutiberjalan = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and MONTH(tanggal) = MONTH(CURRENT_DATE)");                       
$cutilahiran = getOne("SELECT IFNULL(sum(ct_persalinan_k),0) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$year'");

$jumlah_sisa_1 = getOne("SELECT ($hct-$cuti_disetujui-$cuti_diambil-$cutilahiran-$cutimanual)");
                        if ($jumlah_sisa_1 < 0) {
                        $cuti1 = '0' ;
                        } else {
                        $cuti1 = $jumlah_sisa_1;
                        }
$jumlah_sisa = $cuti1;

//kop surat
$kop = fetch_array(bukaquery("SELECT * from setup"));

class PDF extends FPDF {

// Page header
    function Header() {
        $this->Ln(1);

        $this->SetFont('Times', 'BU', 12);
        for ($i = 0; $i < 1; $i++) {
            //$this->Cell(190, 0, '', 1, 1, 'C');
        }
        $this->Ln(5);
    }

// Page footer
    function Footer() {
// Position at 1.5 cm from bottom
        $this->SetY(-15);
// Arial italic 8
        $this->SetFont('Arial', 'I', 8);
// Page number
//        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    var $widths;
    var $aligns;

    function SetWidths($w) {
//Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
//Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data) {
//Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i][0]));
        $h = 5 * $nb;
//Issue a page break first if needed
        $this->CheckPageBreak($h);
//Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($data[$i][1]) ? $data[$i][1] : 'L';
//Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
//Draw the border
            $this->Rect($x, $y, $w, $h);
//Print the text
            $this->MultiCell($w, 5, $data[$i][0], 0, $a);
//Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
//Go to the next line
        $this->Ln($h);
    }

    function Row1($data) {
//Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i][0]));
        $h = 5 * $nb;
//Issue a page break first if needed
        $this->CheckPageBreak($h);
//Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($data[$i][1]) ? $data[$i][1] : 'L';
//Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
//Draw the border
// $this->Rect($x,$y,$w,$h);
//Print the text
            $this->MultiCell($w, 5, $data[$i][0], 0, $a);
//Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
//Go to the next line
        $this->Ln($h);
    }

    function Row2($data) {
//Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i][0]));
        $h = 3 * $nb;
//Issue a page break first if needed
        $this->CheckPageBreak($h);
//Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($data[$i][1]) ? $data[$i][1] : 'L';
//Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
//Draw the border
// $this->Rect($x,$y,$w,$h);
//Print the text
            $this->MultiCell($w, 2, $data[$i][0], 0, $a);
//Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
//Go to the next line
        $this->Ln($h);
    }

    function th($data) {
//Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i][0]));
        $h = 8 * $nb;
//Issue a page break first if needed
        $this->CheckPageBreak($h);
//Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($data[$i][1]) ? $data[$i][1] : 'L';
//Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
//Draw the border
            $this->Rect($x, $y, $w, $h);
//Print the text
            $this->MultiCell($w, 8, $data[$i][0], 0, $a);
//Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
//Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
//If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
//Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l+=$cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

}

// ambil dari url

$pdf = new PDF('P', 'mm', array(210, 330));
// $pdf->AliasNbPages();
// $pdf->AddPage();
// $pdf->Image('../img/jayaraya.png', 7, 17, 20);
// $pdf->Image('../rsuk.png', 182, 19, 20);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->SetFont('Arial', '', 11);
// $pdf->Cell(152, 5, 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
// $pdf->SetFont('Arial', '', 11);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, 'DINAS KESEHATAN', 0, 1, 'C');
// $pdf->SetFont('Arial', 'B', 13);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, strtoupper($kop['nama_instansi']), 0, 1, 'C');
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, $kop['alamat_kop'] , 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, ' Tlp/Fax ' . $kop['tlp'] , 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, 'Website : ' . $kop['website'] , 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, 'Email : ' . $kop['email'] , 0, 1, 'C');
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(152, 5, 'DKI Jakarta', 0, 1, 'C');
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'Kode Pos ' . $kop['kode_pos'], 0, 1, 'R');
// $pdf->Cell(190, 0, '', 1, 1, 'C');
// $pdf->Ln(3);
$pdf->AliasNbPages();
$pdf->AddPage();
// $pdf->Image('../img/jayaraya.png', 7, 15, 30);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->SetFont('Arial', '', 12);
// $pdf->Cell(170, 5, 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
// $pdf->SetFont('Arial', '', 12);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'DINAS KESEHATAN', 0, 1, 'C');
// $pdf->SetFont('Arial', 'B', 12);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'RUMAH SAKIT UMUM DAERAH MATRAMAN', 0, 1, 'C');
// $pdf->SetFont('Arial', '', 9);
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, $kop['alamat_kop'] .', Tlp/Fax: ' . $kop['tlp'] , 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'Website : ' . $kop['website'] . ', Email : ' . $kop['email'], 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'JAKARTA', 0, 1, 'C');
// $pdf->Cell(20, 5, '', 0, 0, 'C');
// $pdf->Cell(170, 5, 'Kode Pos ' . $kop['kode_pos'], 0, 1, 'R');
// $pdf->Cell(190, 0, '', 1, 1, 'C');
$pdf->Ln(3);

$pdf->Cell(125, 5, '', 0, 0, 'C');
$pdf->MultiCell(65, 5, 'Jakarta, ' . konversiTanggal($tm_surat['tgl_permohonan']), 0, 'L');
$pdf->Ln(3);
$pdf->Cell(125, 5, '', 0, 0, 'C');
$pdf->MultiCell(65, 5, 'Kepada ', 0, 'L');
$pdf->Cell(125, 5, '', 0, 0, 'C');
$pdf->Cell(7, 5, 'Yth.', 0, 0, 'L');
$pdf->MultiCell(58, 5, 'Direktur ' . ucwords(strtolower($kop['nama_instansi'])), 0, 'L');
$pdf->Cell(125, 5, '', 0, 0, 'C');
$pdf->MultiCell(58, 5, 'di Tempat', 0, 'L');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, 'FORMULIR PERMINTAAN DAN PEMBERIAN CUTI', 0, 1, 'C');
$pdf->Cell(110, 5, 'NOMOR : '.$no_surat , 0, 0, 'R');
$pdf->Ln(10);


//data pegawai
$pdf->Cell(190, 6, 'I. DATA PEGAWAI', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 5, 'Nama', 1, 0, 'L');
$pdf->Cell(85, 5, $tm_surat['nama_pegawai'], 1, 0, 'L');
$pdf->Cell(25, 5, 'NIP', 1, 0, 'L');
$pdf->Cell(55, 5, $tm_surat['nip'], 1, 1, 'L');

$pdf->Cell(25, 5, 'Jabatan', 1, 0, 'L');
$pdf->Cell(85, 5, ucwords(($tm_surat['nama_level'])), 1, 0, 'L');
$pdf->Cell(25, 5, 'Masa Kerja', 1, 0, 'L');
$pdf->Cell(55, 5, MasaKerjaPenyebut($tm_surat['tgl_masuk']), 1, 1, 'L');

$pdf->Cell(25, 5, 'Unit / Bagian', 1, 0, 'L');
$pdf->Cell(165, 5, ucwords(($tm_surat['sub_bagian'])), 1, 1, 'L');
$pdf->Ln(3);

//jenis cuti
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, 'II. JENIS CUTI YANG DI AMBIL', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 5, '1. Cuti Tahunan', 1, 0, 'L');
$pdf->Cell(50, 5, $cuti_tahunan, 1, 0, 'L');
$pdf->Cell(65, 5, '4. Cuti Sakit', 1, 0, 'L');
$pdf->Cell(30, 5, $cuti_sakit, 1, 1, 'L');

$pdf->Cell(45, 5, '2. Cuti Alasan Penting', 1, 0, 'L');
$pdf->Cell(50, 5, $cuti_alasan_penting, 1, 0, 'L');
$pdf->Cell(65, 5, '5. Cuti Melahirkan', 1, 0, 'L');
$pdf->Cell(30, 5, $cuti_melahirkan, 1, 1, 'L');

$pdf->Cell(45, 5, '3. Cuti Bersama', 1, 0, 'L');
$pdf->Cell(50, 5, $cuti_bersama, 1, 0, 'L');
$pdf->Cell(65, 5, '', 1, 0, 'L');
$pdf->Cell(30, 5, '', 1, 1, 'L');
$pdf->Ln(3);

//alsan cuti
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, 'III. ALASAN CUTI', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(190, 6, $tm_surat['alasan_cuti'], 1, 'J');
$pdf->Ln(3);

//lamanya cuti
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, 'IV. LAMANYA CUTI', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(45, 5, 'Selama', 1, 0, 'L');
$pdf->Cell(50, 5, $tm_surat['jumlah_hari'] . " Hari", 1, 0, 'L');
$pdf->Cell(30, 5, 'Mulai Tanggal', 1, 0, 'L');
$pdf->Cell(65, 5, konversiTanggalBulan(getOne("select tanggal from tm_hari_cuti where id_cuti='$id' order by tanggal asc")) ." ".FormatTgl('Y',getOne("select tanggal from tm_hari_cuti where id_cuti='$id' order by tanggal asc")) ." s/d " . konversiTanggalBulan(getOne("select tanggal from tm_hari_cuti where id_cuti='$id' order by tanggal desc"))." ". FormatTgl('Y',getOne("select tanggal from tm_hari_cuti where id_cuti='$id' order by tanggal desc")), 1, 1, 'L');
$pdf->Ln(3);

//catatan cuti
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 6, 'V. CATATAN CUTI', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, 'Tahun', 1, 0, 'C');
$pdf->Cell(16, 5, 'Jumlah', 1, 0, 'C');
$pdf->Cell(24, 5, 'Cuti diambil', 1, 0, 'C');
$pdf->Cell(35, 5, 'Sisa Cuti', 1, 0, 'C');
$pdf->Cell(35, 5, 'Nama Pengganti', 1, 0, 'L');
$pdf->Cell(60, 5, $nm_pengganti, 1, 1, 'L');

$pdf->Cell(20, 5, $year-2, 1, 0, 'C');
$pdf->Cell(16, 5, '', 1, 0, 'L');
$pdf->Cell(24, 5, '', 1, 0, 'L');
$pdf->Cell(35, 5, '', 1, 0, 'L');
$pdf->Cell(35, 5, 'Nama PJ/Kasatpel', 1, 0, 'L');
$pdf->Cell(60, 5, $nm_pj, 1, 1, 'L');


$pdf->Cell(20, 5, $year-1, 1, 0, 'C');
$pdf->Cell(16, 5, '', 1, 0, 'L');
$pdf->Cell(24, 5, '', 1, 0, 'L');
$pdf->Cell(35, 5, '', 1, 0, 'L');
$pdf->Cell(35, 5, 'Nama Kasie', 1, 0, 'L');
$pdf->Cell(60, 5, $nm_kasie, 1, 1, 'L');

$pdf->Cell(20, 5, $year, 1, 0, 'C');
$pdf->Cell(16, 5, $hct, 1, 0, 'C');
$pdf->Cell(24, 5, $cuti_diambil, 1, 0, 'C');
$pdf->Cell(35, 5, $jumlah_sisa . " Hari", 1, 0, 'C');
$pdf->Cell(35, 5, '', 1, 0, 'L');
$pdf->Cell(60, 5, '', 1, 1, 'L');
$pdf->Ln(3);

//alamat selama cuti
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 5, 'VI. ALAMAT SELAMA MENJALANKAN CUTI', 1, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(190, 6, $tm_surat['alamat_cuti'], 1, 'J');
$pdf->Cell(30, 5, 'No Telepon', 1, 0, 'L');
$pdf->Cell(160, 5, $tm_surat['no_tlp'], 1, 1, 'L');
// //ttd
// $pdf->Cell(105, 5, 'Selama Menjalankan Cuti, Tugas Saya di Serahkan Kepada', 'LR', 0, 'C');
// $pdf->Cell(85, 5, 'Hormat saya,', 'R', 1, 'C');
// $pdf->Cell(105, 15, '', 'LR', 0, 'C');
// $pdf->Cell(85, 15, '', 'R', 1, 'C');
// $pdf->Cell(105, 5, getOne("select nama_pegawai from tm_pegawai where nip='$tm_surat[nip_pengganti]'"), 'LR', 0, 'C');
// $pdf->Cell(85, 5, $tm_surat['nama_pegawai'], 'R', 1, 'C');
// $pdf->Cell(105, 5, "NIP. " . $tm_surat['nip_pengganti'], 'LBR', 0, 'C');
// $pdf->Cell(85, 5, "NIP. " . $tm_surat['nip'], 'LBR', 1, 'C');
$pdf->Ln(3);

//pertimbangan alasan langsung
//$pdf->Cell(190, 5, 'VII. PERTIMBANGAN ATASAN LANGSUNG', 1, 1, 'L');
//$pdf->Cell(45, 5, 'DISETUJUI', 1, 0, 'L');
//$pdf->Cell(50, 5, "PERUBAHAN", 1, 0, 'L');
//$pdf->Cell(45, 5, 'DITANGGUHKAN', 1, 0, 'L');
//$pdf->Cell(50, 5, "TIDAK DISETUJUI" , 1, 1, 'L');
//$pdf->Cell(45, 5, "", 1, 0, 'L');
//$pdf->Cell(50, 5, "", 1, 0, 'L');
//$pdf->Cell(45, 5, "", 1, 0, 'L');
//$pdf->Cell(50, 5, "" , 1, 1, 'L');
////ttd
//$pdf->Cell(190, 5, 'Atasan Langsung', 'LR', 1, 'C');
//$pdf->Cell(190, 8, '', 'LR', 1, 'C');
//$pdf->Cell(190, 5, getOne("select nama_pegawai from tm_pegawai where nip='$tm_surat[nip_pj]'"), 'LR', 1, 'C');
//$pdf->Cell(190, 5, "NIP. ".$tm_surat['nip_pj'], 'LBR', 1, 'C');
//$pdf->Ln(3);
//$pdf->AddPage();
//pertimbangan jabatan berwenang
// $pdf->Cell(190, 5, 'VII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI', 1, 1, 'L');
// $pdf->Cell(45, 5, 'DISETUJUI', 1, 0, 'L');
// $pdf->Cell(50, 5, "PERUBAHAN", 1, 0, 'L');
// $pdf->Cell(45, 5, 'DITANGGUHKAN', 1, 0, 'L');
// $pdf->Cell(50, 5, "TIDAK DISETUJUI", 1, 1, 'L');
// $pdf->Cell(45, 5, "", 1, 0, 'L');
// $pdf->Cell(50, 5, "", 1, 0, 'L');
// $pdf->Cell(45, 5, "", 1, 0, 'L');
// $pdf->Cell(50, 5, "", 1, 1, 'L');
// $pdf->Cell(190, 0, '', 1, 1, 'L');

// ttd pengganti
$pdf->Ln(15);
$pdf->Cell(135, 5, '', 0, 0, 'C');
$pdf->MultiCell(65, 5, 'Mengetahui,', 0, 'L');
$pdf->Cell(95, 15, 'Pengganti Cuti', 0, 0, 'C');
$pdf->Cell(95, 15, 'Direktur RSUD MATRAMAN', 0, 1, 'C');
$pdf->Cell(95, 15, '', 0, 0, 'C');
$pdf->Cell(95, 15, '', 0, 1, 'C');

$pdf->Cell(95, 5, getOne("select nama_pegawai from tm_pegawai where nip='$tm_surat[nip_pengganti]'"), 0, 0, 'C');
$pdf->Cell(95, 5, $kop['direktur'], 0, 1, 'C');
$pdf->Cell(95, 5, "NIP. " . $tm_surat['nip_pengganti'], 0, 0, 'C');
$pdf->Cell(95, 5, "NIP. " . $kop['nip_direktur'], 0, 1, 'C');

//ttd direktur
// $pdf->Ln(15);
// $pdf->Cell(135, 5, '', 0, 0, 'C');
// $pdf->MultiCell(65, 5, 'Mengetahui,', 0, 'L');
// $pdf->Ln(3);
// $pdf->Cell(135, 5, '', 0, 0, 'C');
// $pdf->MultiCell(65, 5, 'Direktur', 0, 'L');
// $pdf->Cell(115, 5, '', 0, 0, 'C');
// $pdf->MultiCell(65, 5, 'Rumah Sakit Umum Daerah Matraman', 0, 'L');
// $pdf->Ln(19);
// $pdf->Cell(127, 5, '', 0, 0, 'C');
// $pdf->MultiCell(65, 5, $kop['direktur'], 0, 'L');
// $pdf->Cell(125, 5, '', 0, 0, 'C');
// $pdf->MultiCell(65, 5, "NIP. " . $kop['nip_direktur'], 0, 'L');
// $pdf->Ln();

$pdf->Output();
?>
