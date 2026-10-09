<?php

include '../conf/conf.php';
require('../libs/fpdf181/fpdf.php');
require_once ('../libs/aes-encrypt/function.php');
session_start();
$id = isset($_GET['id']) ? $_GET['id'] : null;
$bulan = isset($_POST['bulan']) ? $_POST['bulan'] : null;
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : null;
$tmtcek = $tahun . "-" . $bulan . "-02";
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
$tm_gaji = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_level.nama_level, tm_level.id_level 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_pegawai.nama_pegawai ASC");

//set spj
$set_ppk = fetch_array(bukaquery("SELECT tm_pegawai.nama_pegawai, tm_pegawai.nip FROM tm_pegawai where id_user in (select set_spj.ppk_keuangan from set_spj)"));
$set_bendahara = fetch_array(bukaquery("SELECT tm_pegawai.nama_pegawai, tm_pegawai.nip FROM tm_pegawai where id_user in (select set_spj.bendahara_pengeluaran from set_spj)"));

//kop
$kop = fetch_array(bukaquery("SELECT * from setup"));

class PDF extends FPDF {

// Page header
    function Header() {
// Logo
//        $this->Image('../../img/logo.png', 7, 10, 30);
//
//// Query kop surat
//        $kop = mysql_fetch_array(bukaquery("select m_skpd.organisasi, m_skpd.alamat, m_skpd.tlp, m_skpd.fax, m_skpd.website, m_skpd.email FROM m_skpd where m_skpd.kd_skpd='$_SESSION[kd_skpd]'"));
//
//// Title
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->SetFont('Arial', '', 14);
//        $this->Cell(170, 5, 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
//        $this->SetFont('Arial', '', 13);
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, 'DINAS KESEHATAN', 0, 1, 'C');
//        $this->SetFont('Arial', 'B', 12);
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, strtoupper($kop['organisasi']), 0, 1, 'C');
//        $this->SetFont('Arial', '', 11);
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, $kop['alamat'] . ' Tlp ' . $kop['tlp'] . ' Faksimile ' . $kop['fax'] . '', 0, 1, 'C');
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, 'Website : ' . $kop['website'] . ' Email ' . $kop['email'], 0, 1, 'C');
//        $this->SetFont('Arial', '', 11);
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, 'Jakarta', 0, 1, 'C');
//        $this->SetFont('Arial', '', 11);
//        $this->Cell(20, 5, '', 0, 0, 'C');
//        $this->Cell(170, 5, 'Kode Pos 10650 ', 0, 1, 'R');
// Line break
        $this->Ln(1);

        $this->SetFont('Times', 'BU', 12);
        for ($i = 0; $i < 1; $i++) {
            //$this->Cell(190, 0, '', 1, 1, 'C');
        }
        $this->Ln(5);
    }

    function konversiBulanTahun($tanggal) {
        list($thn, $bln, $tgl) = explode('-', $tanggal);
        $tmp = konversiBulan($bln) . " " . $thn;
        return $tmp;
    }

    function konversiBulan($bln) {
        switch ($bln) {
            case "01": $bulan = "Januari";
                break;
            case "02": $bulan = "Februari";
                break;
            case "03": $bulan = "Maret";
                break;
            case "04": $bulan = "April";
                break;
            case "05": $bulan = "Mei";
                break;
            case "06": $bulan = "Juni";
                break;
            case "07": $bulan = "Juli";
                break;
            case "08": $bulan = "Agustus";
                break;
            case "09": $bulan = "September";
                break;
            case "10": $bulan = "Oktober";
                break;
            case "11": $bulan = "November";
                break;
            case "12": $bulan = "Desember";
                break;
            default : $bulan = "Tidak Sesuai";
        }
        return $bulan;
    }

// Page footer
    function Footer() {
// Position at 1.5 cm from bottom
        $this->SetY(-15);
// Arial italic 8
        $this->SetFont('Arial', 'I', 8);
// Page number
        $bulan = isset($_POST['bulan']) ? $_POST['bulan'] : null;
        $tahun = isset($_POST['tahun']) ? $_POST['tahun'] : null;
        $tmtcek = $tahun . "-" . $bulan . "-02";
        $this->Cell(0, 3, 'Spj Gaji Non PNS ' . konversiBulanTahun($tmtcek) . ', Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
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
        $h = 12 * $nb;
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
            $this->MultiCell($w, 12, $data[$i][0], 0, $a);
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

$pdf = new PDF('L', 'mm', 'array(216, 330)');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->MultiCell(180, 10, 'Surat Pertanggung Jawaban Honorarium Pegawai Honorer / Tidak Tetap NON PNS ' . ucwords(strtolower($kop['nama_instansi'])) . ' (Gaji Pokok)', 0, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Ln(3);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'SIPKD/UKPD', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, paramDecrypt($kop['kode_skpd']) . " / " . ucwords(strtolower($kop['nama_instansi'])), 0, 1, 'L');

$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'Program', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, "1.02.10 / Program Pengembangan Dan Pemberdayaan Sumber Daya Manusia", 0, 1, 'L');

$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'Kegiatan', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, "1.02.10.001 / Pemberdayaan SDM Urusan Kesehatan", 0, 1, 'L');

$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'Kode Rekening', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, "5.2.1.02.02 / Honorarium Pegawai Honorer/Tidak Tetap", 0, 1, 'L');

$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'Bulan', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(100, 5, konversiBulanTahun($tmtcek), 0, 1, 'L');

$pdf->Ln(3);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetWidths(array(8, 40, 60, 18, 30, 30));
$pdf->Row(array(
    array("NO", 'C'),
    array("NIP", 'C'),
    array("NAMA", 'C'),
    array("VOLUME (Bulan)", 'C'),
    array("JUMLAH YANG DITERIMA ATAS BESARAN PENGHASILAN GAJI BRUTO / BULAN (Rp.)", 'C'),
    array("TANDA TANGAN", 'C')
));
$no = 0;
$pdf->SetFont('Arial', '', 8);
while ($row = fetch_array($tm_gaji)) {
    $no++;
    if (($no % 2) == 0) {
        $posisi = "L";
    } else {
        $posisi = "R";
    }
    $bruto_penilaian = getOne("SELECT tm_penilaian.gaji_bruto FROM tm_penilaian where id_user='$row[id_user]' and Month(tm_penilaian.tanggal_penilaian)='$bulan'");
    if ($bruto_penilaian == '') {
        $bruto= formatDuit2(GajiBruto(GajiPokokLaporan($row['pendidikan'], $row['tgl_masuk'], $tmtcek), NilaiStatusKawin($row['status_nikah'])));
    } else {
        $bruto= formatDuit2($bruto_penilaian);
    }
    $total[] = GajiBruto(GajiPokokLaporan($row['pendidikan'], $row['tgl_masuk'], $tmtcek), NilaiStatusKawin($row['status_nikah']));
    $pdf->SetWidths(array(8, 40, 60, 18, 30, 30));
    $pdf->Cell(5, 5, '', 0, 0, 'C');
    $pdf->th(array(
        array($no, 'C'),
        array($row['nip'], 'L'),
        array($row['nama_pegawai'], 'L'),
        array("1", 'C'),
        array($bruto, 'R'),
        array($no . ".................", $posisi)
    ));
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(126, 5, "TOTAL", 1, 0, 'C');
$pdf->Cell(30, 5, formatDuit2(array_sum($total)), 1, 1, 'R');
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->MultiCell(156, 5, "Terbilang : " . penyebut(formatDuit2Terbilang(array_sum($total))) . " Rupiah", 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Telah Diperiksa Oleh', 0, 0, 'C');
$pdf->Cell(55, 5, '', 0, 0, 'C');
$pdf->Cell(20, 5, 'Jakarta, ', 0, 1, 'L');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Pejabat Penatausahaan Keuangan', 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Bendahara Pengeluaran Pembantu', 0, 1, 'C');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, ucwords(strtolower($kop['nama_instansi'])), 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, ucwords(strtolower($kop['nama_instansi'])), 0, 1, 'C');

$pdf->Ln(25);

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, $set_ppk['nama_pegawai'], 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, $set_bendahara['nama_pegawai'], 0, 1, 'C');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, "NIP. " . $set_ppk['nip'], 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, "NIP. " . $set_bendahara['nip'], 0, 1, 'C');
$pdf->Ln(15);
$pdf->Cell(85, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Mengetahui', 0, 1, 'C');
$pdf->Cell(85, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Direktur', 0, 1, 'C');
$pdf->Cell(85, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, ucwords(strtolower($kop['nama_instansi'])), 0, 1, 'C');
$pdf->Ln(25);
$pdf->Cell(85, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, $kop['direktur'], 0, 1, 'C');
$pdf->Cell(85, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, "NIP. " . $kop['nip_direktur'], 0, 1, 'C');

$pdf->Output();
?>
