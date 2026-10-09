<?php

include '../conf/conf.php';
require('../libs/fpdf181/fpdf.php');
require_once ('../libs/aes-encrypt/function.php');
session_start();
$id = isset($_GET['id']) ? $_GET['id'] : null;
// memisahkan string menjadi array
$data = explode("-", $id);
$tmtcek = $data[1] . "-" . $data[0] . "-02";
$id_user = $data[2];
//cek apakah masi ada session login
//if (!isset($_SESSION['userid'])) {
//    header('location:index.php');
//}
//if (!isset($_SESSION['kd_skpd'])) {
//    header('location:index.php');
//}
//if (!isset($_SESSION['leveluser'])) {
//    header('location:index.php');
//}
//if ($_GET['id'] == '' OR $cek == '') {
//    header('location:error.php');
//}
$pegawai = mysql_fetch_array(bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, 
                                            tm_level.nama_level
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.id_user='$id_user' and tm_pegawai.tgl_masuk <= '$tmtcek'"));
$set_bendahara = fetch_array(bukaquery("SELECT tm_pegawai.nama_pegawai, tm_pegawai.nip FROM tm_pegawai where id_user in (select set_spj.bendahara_pengeluaran from set_spj)"));
$set_pejabat = fetch_array(bukaquery("SELECT tm_pegawai.nama_pegawai, tm_pegawai.nip FROM tm_pegawai where id_user in (select set_spj.ppk_keuangan from set_spj)"));
$kop = mysql_fetch_array(bukaquery("SELECT setup.nama_instansi, setup.alamat_kop, setup.tlp, setup.fax, setup.email, setup.website, setup.kode_pos, setup.logo FROM setup"));
$gaji_pokok = GajiPokokLaporan($pegawai['pendidikan'], $pegawai['tgl_masuk'], $tmtcek);
$gaji_bruto = GajiBruto(GajiPokokLaporan($pegawai['pendidikan'], $pegawai['tgl_masuk'], $tmtcek), NilaiStatusKawin($pegawai['status_nikah']));

class PDF extends FPDF {

// Page header
    function Header() {
// Logo
        //$this->Image('../../img/logo.png', 7, 10, 30);
// Query kop surat
        //$kop = mysql_fetch_array(bukaquery("select m_skpd.organisasi, m_skpd.alamat, m_skpd.tlp, m_skpd.fax, m_skpd.website, m_skpd.email FROM m_skpd where m_skpd.kd_skpd='$_SESSION[kd_skpd]'"));
// Title
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
            $this->Cell(190, 0, '', 0, 1, 'C');
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
            $l += $cw[$c];
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

$pdf = new PDF('L', 'mm', array(210, 180));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Image('../img/jayaraya.png', 7, 15, 30);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(170, 5, 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'DINAS KESEHATAN', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, strtoupper($kop['nama_instansi']), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, $kop['alamat_kop'] . ' Tlp ' . $kop['tlp'] . ' Faksimile ' . $kop['fax'] . '', 0, 1, 'C');
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'Website : ' . $kop['website'] . ' Email ' . $kop['email'], 0, 1, 'C');
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'Jakarta', 0, 1, 'C');
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'Kode Pos ' . $kop['kode_pos'], 0, 1, 'R');
$pdf->Cell(190, 0, '', 1, 1, 'C');
$pdf->Ln(1);

// set font
$pdf->SetFont('Arial', 'BU', 10);

// set penomoran
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'SLIP GAJI PEGAWAI', 0, 1, 'C');
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'BULAN ' . strtoupper(konversiBulanTahun($tmtcek)), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(28, 5, 'NIP', 0, 0, 'L');
$pdf->Cell(3, 5, ': ', 0, 0, 'L');
$pdf->Cell(20, 5, $pegawai['nip'], 0, 1, 'L');
$pdf->Cell(28, 5, 'NAMA', 0, 0, 'L');
$pdf->Cell(3, 5, ': ', 0, 0, 'L');
$pdf->Cell(20, 5, $pegawai['nama_pegawai'], 0, 1, 'L');
$pdf->Cell(28, 5, 'JABATAN', 0, 0, 'L');
$pdf->Cell(3, 5, ': ', 0, 0, 'L');
$pdf->Cell(20, 5, ucwords(strtolower($pegawai['nama_level'])), 0, 1, 'L');
$pdf->Ln(3);
$pdf->SetFont('Arial', 'BU', 8);
$pdf->Cell(30, 5, 'PENGHASILAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Ln(2);
$pdf->Cell(28, 5, 'Gaji Pokok', 0, 0, 'L');
$pdf->Cell(3, 5, ': ', 0, 0, 'L');
$pdf->Cell(20, 5, 'Rp. ' . formatDuit2($gaji_pokok), 0, 1, 'L');
$pdf->Cell(28, 5, 'Gaji Bruto', 0, 0, 'L');
$pdf->Cell(3, 5, ': ', 0, 0, 'L');
$pdf->Cell(20, 5, 'Rp. ' . formatDuit2($gaji_bruto), 0, 1, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->MultiCell(185, 10, 'Terbilang :' . penyebut(formatDuit2Terbilang($gaji_bruto)).' Rupiah', 1, 'J');
//$pdf->Cell(185, 10, 'Terbilang :' . penyebut(formatDuit2Terbilang($gaji_bruto)) . ' Rupiah', 1, 1, 'L');
$pdf->Ln(2);
//ttd
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, '', 0, 0, 'C');
$pdf->Cell(55, 5, '', 0, 0, 'C');
$pdf->Cell(45, 5, 'Jakarta, ' . konversiTanggal($tmtcek), 0, 1, 'C');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Mengetahui', 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, 'Bendahara Pengeluaran Pembantu', 0, 1, 'C');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, ucwords(strtolower($kop['nama_instansi'])), 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, ucwords(strtolower($kop['nama_instansi'])), 0, 1, 'C');

$pdf->Ln(10);

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, $set_pejabat['nama_pegawai'], 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, $set_bendahara['nama_pegawai'], 0, 1, 'C');

$pdf->Cell(50, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, "NIP. " . $set_pejabat['nip'], 0, 0, 'C');
$pdf->Cell(70, 5, '', 0, 0, 'C');
$pdf->Cell(10, 5, "NIP. " . $set_bendahara['nip'], 0, 1, 'C');

$pdf->Ln(1);
$pdf->Cell(35, 10, 'Note : Data Masi Uji Coba', 0, 0, 'L');
$pdf->Output();
?>
