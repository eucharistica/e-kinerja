<?php
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:

        break;

    case "home":
?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-dashboard"> Dashboard </h3>
            </div>
            <div class="box box-success">
                <div class="box-body">
                    <h3 class="box-title">Selamat Datang Di Aplikasi E-Kinerja</h3>
                </div>
            </div>
        </div>

        <?php
        break;

    case "dashboard":
        $year = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
        //cek kadarluasa
        $str = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_str where id_user='$id_user' ORDER BY id_str DESC");
        $sip = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_sip where id_user='$id_user' ORDER BY id_sip DESC");
        $acls = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_acls where id_user='$id_user' ORDER BY id_acls DESC");
        $atls = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_atls where id_user='$id_user' ORDER BY id_atls DESC");
        $btcls = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_btcls where id_user='$id_user' ORDER BY id_btcls DESC");
        $apn = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_apn where id_user='$id_user' ORDER BY id_apn DESC");
        $phelebethomy = getOne("SELECT CONCAT_WS('-',SUBSTRING(periode, 20, 4),SUBSTRING(periode, 17, 2),SUBSTRING(periode, 14, 2)) as kadarluasa FROM tm_phelebethomy where id_user='$id_user' ORDER BY id_phelebethomy DESC");
        if (
            HitungHari(date('Y-m-d'), $str) <= 180 or HitungHari(date('Y-m-d'), $sip) <= 180 or HitungHari(date('Y-m-d'), $acls) <= 180 or HitungHari(date('Y-m-d'), $atls) <= 180
            or HitungHari(date('Y-m-d'), $btcls) <= 180 or HitungHari(date('Y-m-d'), $apn) <= 180 or HitungHari(date('Y-m-d'), $phelebethomy) <= 180
        ) {
        ?>
            <!-- Pemberitahuan-->
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header bg-aqua">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title fa fa-info-circle "> PEMBERITAHUAN</h4>
                        </div>

                        <div class="modal-body">
                            Hai, <?php echo $nama_pegawai; ?>.. <br><br>
                            <p>Mohon segera perbarui data-data kamu di bawah ini :</p>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>DOKUMEN</th>
                                    <th>SISA MASA AKTIF</th>
                                </thead>
                                <?php
                                if (HitungHari(date('Y-m-d'), $str) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>STR</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $str) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $sip) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>SIP</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $sip) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $acls) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>ACLS</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $acls) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $atls) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>ATLS</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $atls) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $btcls) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>BTCLS</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $btcls) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $apn) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>APN</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $apn) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                if (HitungHari(date('Y-m-d'), $phelebethomy) <= 180) {
                                    echo "<tr>"
                                        . "<td><b>PHELEBETHOMY</b></td>"
                                        . "<td><b>" . HitungHari(date('Y-m-d'), $phelebethomy) . " Hari</b></td>"
                                        . "</tr>";
                                }
                                ?>
                            </table>
                            <br>
                            <p>Jangan lupa upload kembali data yang terbaru di SIMPEG kamu, dan berikan Hard Copy ke Kepegawaian RSUD Matraman, Terima Kasih :)</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>




        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-dashboard"> Dashboard  </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
            </div>
            <!-- Combo Box-->
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThnnow(isset($_POST['tahun']) ? $_POST['tahun'] : null); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
            <?php
            $tahun = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
            $thn_now = $tahun . "-";
            ?>
            <!--<div class="box">-->
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>

                    
                </div>
                <div class="box-body">
                    <!-- Grafik -->
                    <div class="chart">
                        <?php $tampil_grafik = 'yes'; ?>
                        <div id='container'></div>
                    </div>
                    <!-- Tutup Grafik -->
                    <!-- Panel Info -->
                    <div class="x_panel">
                        
                        <!-- Riwayat Penilaian -->
                        <div class="panel-group" id="dropdown">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title fa fa-dashboard">
                                        <a data-toggle="collapse" data-parent="#dropdown" href="#collapse1">
                                            REPORT PENILAIAN KINERJA</a>
                                    </h4>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="box-body table-responsive">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Bulan</th>
                                                        <th>Nilai Kinerja</th>
                                                        <th>Nilai Penyerapan</th>
                                                        <th>Nilai Disiplin dan Kompetensi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $no = 0;
                                                    $sql_pencapaian = bukaquery("SELECT nskp,nprilaku,(penyerapan*0.2) as jumlah, tanggal_penilaian FROM tm_penilaian inner join tm_penyerapan on tm_penilaian.id_penyerapan=tm_penyerapan.id_penyerapan where year(tm_penilaian.tanggal_penilaian)='$thn_now' and id_user='$id_user'");
                                                    while ($get = fetch_array($sql_pencapaian)) {
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $no; ?></td>
                                                            <td><?php echo konversiBulanTahun($get['tanggal_penilaian']); ?></td>
                                                            <td><?php echo $get['nskp'] ?></td>
                                                            <td><?php echo $get['jumlah'] ?></td>
                                                            <td><?php echo $get['nprilaku'] ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Riwayat Kehadiran -->
                        <div class="panel-group" id="dropdown">
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <h4 class="panel-title fa fa-thumbs-o-up">
                                        <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                            REPORT KEHADIRAN</a>
                                    </h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="box-body table-responsive">
                                            <table id="example2" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Bulan</th>
                                                        <th>Sakit < 2 hr</th> <th>Sakit > 2 Hr</th>
                                                        <th>Alpha</th>
                                                        <th>Izin</th>
                                                        <th>Telat</th>
                                                        <th>Pulang Cepat</th>
                                                        <th>Cuti</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $no = 0;
                                                    $tm_kehadiran = bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.id_user='$id_user' and year(tm_waktu_k.date_k)='$thn_now'");
                                                    while ($kehadiran = fetch_array($tm_kehadiran)) {
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $no; ?></td>
                                                            <td><?php echo konversiBulanTahun($kehadiran['date_k']); ?></td>
                                                            <td><?php echo $kehadiran['sakit1']." Hari" ?></td>
                                                            <td><?php echo $kehadiran['sakit2']." Hari" ?></td>
                                                            <td><?php echo $kehadiran['alpha']." Hari" ?></td>
                                                            <td><?php echo $kehadiran['izin']." Hari" ?></td>
                                                            <td><?php echo $kehadiran['telat']." Menit" ?></td>
                                                            <td><?php echo $kehadiran['pulang_cepat']." Menit" ?></td>
                                                            <td><?php echo $kehadiran['ct_alasan_k']+$kehadiran['ct_sakit_k']." Hari" ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Riwayat Gaji -->
                        <!--<div class="panel-group" id="dropdown">-->
                        <!--    <div class="panel panel-success">-->
                        <!--        <div class="panel-heading">-->
                        <!--            <h4 class="panel-title fa fa-money">-->
                        <!--                <a data-toggle="collapse" data-parent="#dropdown" href="#collapse3">-->
                        <!--                    REPORT PENDAPATAN</a>-->
                        <!--            </h4>-->
                        <!--        </div>-->
                        <!--        <div id="collapse3" class="panel-collapse collapse">-->
                        <!--            <div class="panel-body">-->
                        <!--                <div class="box-body table-responsive">-->
                        <!--                    <table id="example3" class="table table-bordered table-striped">-->
                        <!--                        <thead>-->
                        <!--                            <tr>-->
                        <!--                                <th>No</th>-->
                        <!--                                <th>Bulan</th>-->
                        <!--                                <th>Slip Gaji</th>-->
                        <!--                            </tr>-->
                        <!--                        </thead>-->
                        <!--                        <tbody>-->
                                                    <?php
                                                    // $no = 0;
                                                    // $tm_kehadiran = bukaquery("SELECT  tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_penilaian.pendidikan,
                                                    // tm_unit.nama_unit, tm_penilaian.bpjs_ks, (tm_penilaian.gaji_bruto*tm_penilaian.bpjs_ks)+(tm_penilaian.gaji_bruto*tm_penilaian.bpjs_jkk)+(tm_penilaian.gaji_bruto*tm_penilaian.bpjs_ijht)+(tm_penilaian.gaji_bruto*tm_penilaian.bpjs_jp) AS potongan_bpjs,
                                                    // tm_level.nama_level, tm_penilaian.nskp+tm_penilaian.nprilaku+(tm_penyerapan.penyerapan*0.2) AS bhu, tm_penilaian.gaji_pokok, tm_penilaian.gaji_bruto, tm_penilaian.tunjangan, tm_penilaian.tunjangan_val, tm_penilaian.masa_kerja, 
                                                    // ((tm_waktu_s.j_hks*tm_honor_shift.hks)+(tm_waktu_s.j_hkm*tm_honor_shift.hkm)+(tm_waktu_s.j_hlp*tm_honor_shift.hlp)+(tm_waktu_s.j_hls*tm_honor_shift.hls)+(tm_waktu_s.j_hlm*tm_honor_shift.hlm)+(tm_waktu_s.j_hrp*tm_honor_shift.hrp)+(tm_waktu_s.j_hrs*tm_honor_shift.hrs)+(tm_waktu_s.j_hrm*tm_honor_shift.hrm)) AS honor,
                                                    // ((tm_waktu_k.alpha*0.05)*tm_penilaian.tunjangan_val)+((tm_waktu_k.sakit1*0.01)*tm_penilaian.tunjangan_val)+((tm_waktu_k.sakit2*0.02)*tm_penilaian.tunjangan_val)+((tm_waktu_k.izin*0.025)*tm_penilaian.tunjangan_val)+(((tm_waktu_k.telat/450)*0.025)*tm_penilaian.tunjangan_val)+(((tm_waktu_k.pulang_cepat/450)*0.025)*tm_penilaian.tunjangan_val) AS potongan_absensi, tm_penilaian.tanggal_penilaian,
                                                    // tm_penilaian.bpjs_jkk, tm_penilaian.bpjs_ijht, tm_penilaian.bpjs_jp
                                                    // FROM tm_penilaian
                                                    // INNER JOIN tm_pegawai ON tm_penilaian.id_user = tm_pegawai.id_user
                                                    // INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                    // INNER JOIN tm_honor_shift ON tm_unit.id_petugas = tm_honor_shift.id_petugas
                                                    // INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                                    // INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                                    // INNER JOIN tm_waktu_k ON tm_penilaian.id_waktu_k = tm_waktu_k.id_waktu_k
                                                    // INNER JOIN tm_waktu_s ON tm_penilaian.id_waktu_s = tm_waktu_s.id_waktu_s
                                                    // INNER JOIN tm_waktu_t ON tm_penilaian.id_waktu_t = tm_waktu_t.id_waktu_t
                                                    // INNER JOIN tm_penyerapan ON tm_penilaian.id_penyerapan = tm_penyerapan.id_penyerapan

                                                    // where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.id_user='$id_user' and
                                                    // Year(tm_penilaian.tanggal_penilaian)='$thn_now' order by tm_penilaian.tanggal_penilaian DESC");
                                                    // while ($kehadiran = fetch_array($tm_kehadiran)) {
                                                    //     $no++;
                                                    ?>
                                                        <!--<tr>-->
                                                        <!--    <td><?php echo $no; ?></td>-->
                                                        <!--    <td><?php echo konversiBulanTahun($kehadiran['tanggal_penilaian']); ?></td>-->
                                                        <!--    <td>-->
                                                        <!--        <a href="<?php echo 'cetak-slip-gaji-' . FormatTgl('m', $kehadiran['tanggal_penilaian']) . '-' . FormatTgl('Y', $kehadiran['tanggal_penilaian']) . '-' . $kehadiran['id_user']; ?>" target="_blank"><span title="Cetak Slip Gaji" class="btn-xs btn fa fa-file-pdf-o"> </span>-->
                                                        <!--            </button>-->
                                                        <!--        </a>-->
                                                        <!--    </td>-->
                                                        <!--</tr>-->
                                                    <?php
                                                    // }
                                                    ?>
                        <!--                        </tbody>-->
                        <!--                    </table>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!-- Report Potongan -->
                        <div class="panel-group" id="dropdown">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h4 class="panel-title fa fa-credit-card">
                                        <a data-toggle="collapse" data-parent="#dropdown" href="#collapse4">
                                            REPORT PENGEMBALIAN DAN POTONGAN</a>
                                    </h4>
                                </div>
                                <div id="collapse4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="box-body table-responsive">
                                            <table id="example4" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                            <th>Bulan</th>
                                                            <th>Pengembalian Kurang Bayar</th>
                                                            <th>Cicilan Pengembalian Ke</th>
                                                            <th>Ket. Pengembalian</th>
                                                            <th>Potongan Penghasilan</th>
                                                            <th>Cicilan Potongan Ke</th>
                                                            <th>Ket. Potongan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $no = 0;
                                                    $tm_potongan = bukaquery("SELECT tm_potongan_p.potongan_bank, tm_potongan_p.cicilan_bank, tm_potongan_p.keterangan_bank, tm_potongan_p.potongan_rs, tm_potongan_p.cicilan_rs, tm_potongan_p.keterangan_rs,  tm_potongan_p.date_p  FROM  tm_potongan_p where  tm_potongan_p.id_user='$id_user' and year( tm_potongan_p.date_p)='$thn_now'");
                                                    while ($potongan = fetch_array($tm_potongan)) {
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $no; ?></td>
                                                            <td><?php echo konversiBulanTahun($potongan['date_p']); ?></td>
                                                            <td><?php echo $potongan['potongan_bank'] ?></td>
                                                            <td><?php echo $potongan['cicilan_bank'] ?></td>
                                                            <td><?php echo $potongan['keterangan_bank'] ?></td>
                                                            <td><?php echo $potongan['potongan_rs'] ?></td>
                                                            <td><?php echo $potongan['cicilan_rs'] ?></td>
                                                            <td><?php echo $potongan['keterangan_rs'] ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Riwayat Hukdis -->
                        <div class="panel-group" id="dropdown">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <h4 class="panel-title fa fa-thumbs-o-down">
                                        <a data-toggle="collapse" data-parent="#dropdown" href="#collapse5">
                                            REPORT HUKDIS</a>
                                    </h4>
                                </div>
                                <div id="collapse5" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="box-body table-responsive">
                                            <table id="example5" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal Surat Peringatan</th>
                                                        <th>No Surat Peringatan</th>
                                                        <th>Jenis Peringatan</th>
                                                        <th>Aktif</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $no = 0;
                                                    $tm_hukuman = bukaquery("select tt_hukuman.id_hukuman, tt_hukuman.no_hukuman, tt_hukuman.tgl_hukuman, tt_hukuman.aktif_hukuman, tt_hukuman.alasan_hukuman, tm_sanksi.nama_sanksi, tm_sanksi.masa_aktif from tt_hukuman"
                                                        . " inner join tm_sanksi on tt_hukuman.id_sanksi=tm_sanksi.id_sanksi where tt_hukuman.id_user='$id_user'");
                                                    while ($row = fetch_array($tm_hukuman)) {
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $no; ?></td>
                                                            <td><?php echo FormatTgl('d/m/Y', $row['tgl_hukuman']); ?></td>
                                                            <td><?php echo $row['no_hukuman'] ?></td>
                                                            <td><?php echo $row['nama_sanksi'] ?></td>
                                                            <td><?php echo konversiBulanTahun($row['aktif_hukuman']) . " - " . konversiBulan(FormatTgl('m', $row['aktif_hukuman']) + $row['masa_aktif'] - 1) . " " . FormatTgl('Y', $row['aktif_hukuman']); ?></td>
                                                            <td><?php echo $row['alasan_hukuman'] ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tutup Panel Info -->
                </div>
                <!-- /.box-body -->
            <!--</div>-->
        </div>



        <?php

        case "list-data-maping-cuti-pegawai":
        $tampil_cuti = 'yes';
        $cuti = bukaquery("SELECT
    tm_hari_cuti.id_cuti,
    tm_hari_cuti.tanggal,
    tm_pegawai.nama_pegawai
FROM
    tm_hari_cuti
    INNER JOIN tm_cuti ON tm_cuti.id_cuti = tm_hari_cuti.id_cuti
    INNER JOIN tm_pegawai ON tm_pegawai.id_user = tm_cuti.id_user 
    WHERE acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T' AND acc_kasie != 'T' and tm_pegawai.id_unit='$id_unit'  UNION
    SELECT tm_hari_libur.jenis as id_cuti, tm_hari_libur.tanggal, tm_hari_libur.keterangan as nama_pegawai from tm_hari_libur where tm_hari_libur.jenis IN ('Libur Nasional','Libur Hari Raya','Cuti Bersama') UNION
    SELECT tt_agenda.id_agenda,tt_agenda.tanggal, tt_agenda.kegiatan from tt_agenda"); 
    ?> 
    <!-- and tm_cuti.id_user = '$id_user' -->


        <div class="col-lg-16">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title danger">AGENDA</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">

                    <div class="box-body">
                        <div id="calendar"></div> 
                    </div>
                </div>
            </div>
        </div>
   
        
<?php
        break;
}
?>
