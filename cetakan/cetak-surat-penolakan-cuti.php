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
$tm_surat = fetch_array(bukaquery("SELECT tm_pegawai.nip,tm_pegawai.id_user, tm_pegawai.nama_pegawai, tm_cuti.nip_pengganti,tm_cuti.nip_pj,tm_cuti.nip_kasie, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.periode_cuti, tm_cuti.alamat_cuti,tm_cuti.alasan,tm_cuti.tahun_cuti, 
                            tm_cuti.no_tlp, tm_cuti.alasan_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_pegawai.id_unit, if(tm_pegawai.sub_bagian='Tata Usaha',CONCAT('Kepala Sub Bagian ',tm_pegawai.sub_bagian),tm_pegawai.sub_bagian) as sub_bagian, tm_pegawai.id_kasatpel, tm_level.nama_level, tm_pegawai.tgl_masuk, tm_unit.nama_unit 
                            FROM tm_cuti
                            inner JOIN tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                            INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                            INNER JOIN tm_level ON tm_user.id_level=tm_level.id_level
                            where tm_cuti.id_cuti='$id'"));
$tanggal1 = date('Y-m-d', strtotime(substr($tm_surat['periode_cuti'], 0, 10)));
$tanggal2 = date('Y-m-d', strtotime(substr($tm_surat['periode_cuti'], 13, 10)));
//maping penolakan
if ($tm_surat['alasan'] == 'PRB') {
    $perihal = 'Perubahan Tanggal ' . ucwords(strtolower($tm_surat['jenis_cuti']));
    $isi = "Dengan ini menolak permohonan cuti " . $tm_surat['nama_pegawai'] . " sebanyak " . $tm_surat['jumlah_hari'] . " hari di tahun " . $tm_surat['tahun_cuti'] . ", dan diharapkan pegawai yang bersangkutan melakukan perubahan tanggal cuti tersebut ke tanggal lain.";
} else {
    $perihal = 'Penangguhan ' . ucwords(strtolower($tm_surat['jenis_cuti']));
    $thn = $tm_surat['tahun_cuti'] + 1;
    $isi = "Dengan ini menangguhkan permohonan cuti " . $tm_surat['nama_pegawai'] . " sebanyak " . $tm_surat['jumlah_hari'] . " hari di tahun " . $tm_surat['tahun_cuti'] . ", dan cuti tersebut akan berlaku di tahun " . $thn . " dengan batas penggunaan hak cuti penangguhan sesuai dengan surat edaran yang diterbitkan.";
}
$kop = fetch_array(bukaquery("SELECT * from setup"));

class PDF extends FPDF
{

    // Page header
    function Header()
    {
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
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        //        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
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

    function Row1($data)
    {
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

    function Row2($data)
    {
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

    function th($data)
    {
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

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
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

$pdf = new PDF('P', 'mm', array(210, 350));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Image('../img/jayaraya.png', 7, 15, 30);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(170, 5, 'PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'DINAS KESEHATAN', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, strtoupper($kop['nama_instansi']), 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, $kop['alamat_kop'] . ' Tlp ' . $kop['tlp'] . '', 0, 1, 'C');
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, ' Email : ' . $kop['email'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'DKI JAKARTA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(170, 5, 'Kode Pos ' . $kop['kode_pos'], 0, 1, 'R');
$pdf->Cell(190, 0, '', 1, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(125, 5, '', 0, 0, 'C');
$pdf->MultiCell(65, 5, 'Jakarta ' . konversiTanggal(FormatTgl('Y-m-d', date('Y-m-d'))), 0, 'L');
$pdf->Ln(1);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(5, 5, 'Kepada', 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(7, 5, 'Yth. ' . $tm_surat['nama_pegawai'], 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(40, 5, 'di Tempat', 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Nomor', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 1, 'C');
$pdf->Ln(1);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Perihal', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'C');
$pdf->Cell(30, 5, $perihal, 0, 1, 'L');


$pdf->Ln(10);
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(7, 5, 'Saya yang bertanda tangan dibawah ini :', 0, 1, 'L');
$pdf->Ln(2);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Nama', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'C');
$pdf->Cell(30, 5, getOne("select nama_pegawai from tm_pegawai where nip='$tm_surat[nip_kasie]'"), 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'NIP', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'C');
$pdf->Cell(30, 5, $tm_surat['nip_kasie'], 0, 1, 'L');
$pdf->Ln(1);
$pdf->Cell(20, 5, '', 0, 0, 'C');
$pdf->Cell(15, 5, 'Jabatan', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'C');
$pdf->Cell(30, 5, $tm_surat['sub_bagian'], 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(5, 5, '', 0, 0, 'C');

//body
$pdf->MultiCell(180, 6, $isi, 0, 'J');
$pdf->Ln(3);
$pdf->Cell(5, 10, '', 0, 0, 'C');
$pdf->MultiCell(180, 6, "Demikian surat ini kami sampaikan, Atas kerjasamanya di ucapkan terima kasih.", 0, 'J');


$pdf->Ln(10);
//ttd
$pdf->Cell(95, 5, '', 0, 0, 'C');
$pdf->Cell(95, 5, 'Hormat Saya,', 0, 1, 'C');
$pdf->Cell(95, 5, '', 0, 0, 'C');
$pdf->MultiCell(95, 5, $tm_surat['sub_bagian'], 0, 'C');
// $pdf->Cell(95, 5, 'Kepala Sub Bagian ' . $tm_surat['sub_bagian'], 0, 1, 'C');
$pdf->Ln(25);
$pdf->Cell(95, 5, '', 0, 0, 'C');
$pdf->Cell(95, 5, getOne("select nama_pegawai from tm_pegawai where nip='$tm_surat[nip_kasie]'"), 0, 1, 'C');
$pdf->Cell(95, 5, '', 0, 0, 'C');
$pdf->Cell(95, 5, 'NIP. ' . $tm_surat['nip_kasie'], 0, 1, 'C');
$pdf->Ln(3);
$pdf->Output();
