<?php

include '../conf/conf.php';
require('../libs/fpdf181/fpdf.php');
require_once ('../libs/aes-encrypt/function.php');
session_start();
$id = isset($_GET['id']) ? $_GET['id'] : null;
$cek = getOne("select tm_add_surtug.id_surat from tm_add_surtug where tm_add_surtug.id_surat='$id'");
//cek apakah masi ada session login
if (!isset($_SESSION['nip'])) {
    header('location:index.php');
}
if (!isset($_SESSION['id_level'])) {
    header('location:index.php');
}
if ($_GET['id'] == '' OR $cek == '') {
    header('location:error.php');
}
//baca surat tugas
read("update tm_add_surtug set tm_add_surtug.`read`='1' where id_surat='$id' and nip='$_SESSION[nip]'");
//query tampil
$tm_surat = fetch_array(bukaquery("SELECT * from tm_surat_tugas where tm_surat_tugas.id_surat='$id'"));
//kop surat
$kop = fetch_array(bukaquery("SELECT * from setup"));
//ttd
if ($tm_surat['an'] == '-') {
    $jabatan = ' Direktur ' . 'RSUD MATRAMAN';
    $nama = $kop['direktur'];
    $nip = $kop['nip_direktur'];
} else {
    $jabatan = 'a.n. Direktur ' . 'RSUD MATRAMAN';
    $nama = getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.nip='$tm_surat[an]'");
    $nip = $tm_surat['an'];
}

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

$pdf = new PDF('P', 'mm', 'Legal');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Image('../img/jayaraya.png', 90, 15, 30);
$pdf->Cell(180, 5, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln(35);
$pdf->Cell(190, 5, 'RUMAH SAKIT UMUM DAERAH MATRAMAN', 0, 1, 'C');
$pdf->Cell(190, 5, 'DINAS KESEHATAN', 0, 1, 'C');
$pdf->Cell(190, 5, 'PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(5);
$pdf->Cell(190, 5, 'SURAT TUGAS', 0, 1, 'C');
$pdf->Ln(1);
$pdf->Cell(165, 5, 'NOMOR :' .'									/			', 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(190, 5, 'TENTANG', 0, 1, 'C');
$pdf->Ln(1);
$pdf->MultiCell(190, 5, strtoupper($tm_surat['kegiatan']), 0, 'C');
$pdf->Ln(3);
$pdf->Cell(190, 5, 'MENUGASKAN', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(15, 5, '', 0, 0, 'C');
$pdf->SetWidths(array(8, 70, 50, 43));
$pdf->th(array(
    array("No", 'C'),
    array("Nama", 'C'),
    array("Instansi", 'C'),
    array("Jabatan", 'C')
));
$no = 0;
$sql_penugasan = bukaquery("select tm_pegawai.nama_pegawai,tm_level.nama_level from tm_add_surtug "
        . "inner join tm_pegawai on tm_add_surtug.nip=tm_pegawai.nip "
        . "inner join tm_user on tm_pegawai.id_user=tm_user.id_user "
        . "inner join tm_level on tm_user.id_level=tm_level.id_level "
        . "where tm_add_surtug.id_surat='$id'");
while ($row = fetch_array($sql_penugasan)) {
    $no++;
    $pdf->Cell(15, 5, '', 0, 0, 'C');
    $pdf->th(array(
        array($no, 'C'),
        array($row['nama_pegawai'], 'L'),
        array($kop['nama_instansi'], 'L'),
        array($row['nama_level'], 'L')
    ));
}
$pdf->Ln(5);
$pdf->Cell(15, 5, '', 0, 0, 'C');
$pdf->Cell(90, 5, 'Yang dilaksanakan pada :', 0, 1, 'L');
$pdf->Ln(3);
$pdf->SetWidths(array(30, 5, 60));
$pdf->Cell(25, 5, '', 0, 0, 'C');
$pdf->row1(array(
    array("Hari", 'L'),
    array(":", 'C'),
    array(hariindo($tm_surat['tgl_kegiatan']), 'L')
));
$pdf->Cell(25, 5, '', 0, 0, 'C');
$pdf->row1(array(
    array("Tanggal", 'L'),
    array(":", 'C'),
    array(konversiTanggal($tm_surat['tgl_kegiatan']), 'L')
));
$pdf->Cell(25, 5, '', 0, 0, 'C');
$pdf->row1(array(
    array("Waktu", 'L'),
    array(":", 'C'),
    array($tm_surat['waktu'], 'L')
));
$pdf->Cell(25, 5, '', 0, 0, 'C');
$pdf->row1(array(
    array("Tempat", 'L'),
    array(":", 'C'),
    array($tm_surat['lokasi'], 'L')
));
$pdf->Ln(3);
$pdf->Cell(25, 5, '', 0, 0, 'C');
$pdf->Cell(65, 5, 'Demikan surat tugas ini dibuat agar dilaksanakan dengan sebaik-baiknya dengan ', 0, 1, 'L');
$pdf->Cell(15, 5, '', 0, 0, 'C');
$pdf->Cell(65, 5, 'penuh rasa tanggung jawab.', 0, 1, 'L');
$pdf->Ln(3);
$pdf->Cell(100, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Dikeluarkan di Jakarta', 0, 1, 'L');
$pdf->Cell(100, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Pada Tanggal' . " " . konversiTanggal($tm_surat['tgl_surat']), 0, 1, 'L');
$pdf->Ln(5);
$pdf->Cell(120, 5, '', 0, 0, 'C');
$pdf->MultiCell(75, 5, $jabatan, 0, 'L');
$pdf->Ln(30);
$pdf->Cell(120, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, $nama, 0, 1, 'L');
$pdf->Cell(120, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, "NIP." . $nip, 0, 1, 'L');
$pdf->Cell(10, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'BUKTI MELAKSANAKAN TUGAS', 0, 1, 'L');
$pdf->Cell(10, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Tandatangan / cap yang berwenang,', 0, 1, 'L');
$pdf->Cell(10, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Kepala.........................', 0, 1, 'L');
$pdf->Ln(30);
$pdf->Cell(10, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, '_____________________________', 0, 1, 'L');


$pdf->AddPage();
$pdf->Ln(3);
$pdf->Cell(190, 5, 'NOTULENSI', 'B', 1, 'C');

$pdf->Output();
?>
