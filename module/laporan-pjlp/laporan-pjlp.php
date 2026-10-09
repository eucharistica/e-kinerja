<?php
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header("location:error404");
        break;

    case "laporan-gaji-bruto-pegawai":
?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN GAJI BRUTO PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $tmtcek = $tahun_post . "-" . $bulan_post . "-02";
                if ($bulan_post == '' or $tahun_post == '') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Laporan <?php echo konversiBulanTahun($tmtcek); ?></label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <br>
                    <form action="cetak-spj-gaji" method="post" target="_Blank">
                        <table>
                            <tr>
                                <td><input type="hidden" class="form-control" name="bulan" width="10px" value="<?php echo $bulan_post; ?>" readonly required></td>
                                <td><input type="hidden" class="form-control" name="tahun" value="<?php echo $tahun_post; ?>" readonly required></td>
                                <td>
                                    <button type="submit" name="submit" title="Print SPJ" class="btn btn-info"><i class="fa fa-print"> Print SPJ</i></button>
                                </td>
                            </tr>
                        </table>
                    </form>
                <?php }
                ?>

                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>TMT</th>
                                <th>Masa Kerja</th>
                                <th>Pendidikan</th>
                                <th>Status Pernikahan</th>
                                <th>Besaran Penghasilan Pokok/Bulan</th>
                                <th>Jumlah BRUTO Sebelum Potongan&Pajak</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_level.nama_level, tm_level.id_level 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='$status' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_pegawai.nama_pegawai asc");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $masuktgl = $row ['tgl_masuk'];
                                $masakerja = JumlahBulan($masuktgl);
                                $bruto_penilaian = getOne("SELECT tm_penilaian.gaji_bruto FROM tm_penilaian where id_user='$row[id_user]' and Month(tm_penilaian.tanggal_penilaian)='$bulan_post' and Year(tm_penilaian.tanggal_penilaian)='$tahun_post'");
                                $baru = $bruto_penilaian*0.75;
                            ?>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo "'" . $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo konversiTanggal(FormatTgl('Y-m-d', $row['tgl_masuk'])); ?></td>
                                    <td><?php echo PenyebutNilaiMasaKerjaCari($row['tgl_masuk'], $tmtcek); ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo $row['status_nikah']; ?></td>
                                    <td><?php echo formatDuit2(GajiPokokLaporan($row['pendidikan'], $row['tgl_masuk'], $tmtcek)); ?></td>

                                    <td><?php if ($bruto_penilaian == '') {
        echo formatDuit2(GajiBruto(GajiPokokLaporan($row['pendidikan'], $row['tgl_masuk'], $tmtcek), NilaiStatusKawin($row['status_nikah'])));
    } elseif ($masakerja <= 3) {
        echo formatDuit2($baru);
    } else {
        echo formatDuit2($bruto_penilaian);
    }
                                         ?></td>
                                    <td>
                                        <a href="<?php echo 'cetak-slip-gaji-' . $bulan_post . '-' . $tahun_post . '-' . $row['id_user']; ?>" target="_blank"><span title="Cetak Slip Gaji" class="btn-xs btn fa fa-file-pdf-o"> </span>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "laporan-tunjangan-khusus":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN TUNJANGAN KHUSUS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table>
                            <tr>
                                <td>
                                    <label style="text-align: center">Status</label>
                                </td>
                                <td>
                                    <label style="text-align: center">Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="status" data-placeholder="-Pilih Status-" required>
                                        <option value="-" selected>-Pilih Status-</option>
                                        <option value="Gaji 13">Gaji 13</option>
                                        <option value="THR">THR</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $kategori = isset($_POST['status']) ? $_POST['status'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                if ($tahun_post == 'Tidak Sesuai' or $tahun_post == '') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Laporan <?= $kategori; ?> Pegawai Tahun <?= $tahun_post; ?></label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="laporan-khusus" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Slip Gaji</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Norek</th>
                                <th>NPWP</th>
                                <th>Status Nikah</th>
                                <th>Pendidikan</th>
                                <th>Rumpun</th>
                                <th>Masa Kerja</th>
                                <th><?= $kategori; ?></th>
                                <th>PPH 21</th>
                                <th>Dibayarkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($tahun_post == 0) {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            $sql = "SELECT
                            tm_pegawai.nip,tm_pegawai.nama_pegawai,tm_pegawai.no_rek,tm_pegawai.npwp,tm_pegawai.status_nikah,tm_pegawai.pendidikan,tm_pegawai.rumpun,tm_pegawai.tgl_masuk,tm_pegawai.pajak ";
                            if ($kategori == "Gaji 13") {
                                $sql .= ",tm_uang_t.gaji13 as nilai ";
                            }
                            if ($kategori == "THR") {
                                $sql .= ",tm_uang_t.thr as nilai ";
                            }
                            $sql .= "FROM tm_pegawai
                            INNER JOIN tm_uang_t ON tm_uang_t.id_user=tm_pegawai.id_user
                            where tm_pegawai.status='$status' AND  YEAR(tm_uang_t.date_p)='$tahun_post'";
                            $sql_pegawai = bukaquery($sql);
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                            ?>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td>#</td>
                                    <td><?= $row['nip'] ?></td>
                                    <td><?= $row['nama_pegawai'] ?></td>
                                    <td><?= $row['no_rek'] ?></td>
                                    <td><?= $row['npwp'] ?></td>
                                    <td><?= $row['status_nikah'] ?></td>
                                    <td><?= $row['pendidikan'] ?></td>
                                    <td><?= $row['rumpun'] ?></td>
                                    <td><?= PenyebutNilaiMasaKerjaCari($row['tgl_masuk'], $tmtcek); ?></td>
                                    <td><?= formatDuit2($row['nilai']) ?></td>
                                    <td><?= formatDuit2(PPH21Khusus($row['nilai'], NilaiStatusPajak($row['pajak']))) ?></td>
                                    <td><?= formatDuit2($row['nilai'] - PPH21Khusus($row['nilai'], NilaiStatusPajak($row['pajak']))) ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "laporan-potongan-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN POTONGAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $tmtcek = $tahun_post . "-" . $bulan_post . "-02";
                if ($bulan_post == '' or $tahun_post == '') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Laporan <?php echo konversiBulanTahun($tmtcek); ?></label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <br>
                    <form action="cetak-spj-gaji" method="post" target="_Blank">
                        <table>
                            <tr>
                                <td><input type="hidden" class="form-control" name="bulan" width="10px" value="<?php echo $bulan_post; ?>" readonly required></td>
                                <td><input type="hidden" class="form-control" name="tahun" value="<?php echo $tahun_post; ?>" readonly required></td>
                                <td>
                                    <!-- <button type="submit" name="submit" title="Print Potongan Pegawai" class="btn btn-info"><i class="fa fa-print"> Print Potongan Pegawai</i></button>-->
                                </td>
                            </tr>
                        </table>
                    </form>
                <?php }
                ?>

                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bulan & Tahun</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Pengembalian Kurang Bayar</th>
                                <th>Cicilan Kurang Bayar Ke</th>
                                <th>Keterangan Kurang Bayar</th>
                                <th>Potongan Lebih Bayar</th>
                                <th>Cicilan Lebih Bayar Ke</th>
                                <th>Keterangan Lebih Bayar</th>
                                <!--<th>Detail</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, 
                            tm_potongan_p.id_potongan_p,
                            tm_potongan_p.id_user,
                            tm_potongan_p.potongan_bank,
                            tm_potongan_p.cicilan_bank,
                            tm_potongan_p.keterangan_bank,
                            tm_potongan_p.potongan_rs,
                            tm_potongan_p.cicilan_rs,
                            tm_potongan_p.keterangan_rs,
                            tm_potongan_p.date_p
                            FROM
                            tm_pegawai
                            INNER JOIN tm_potongan_p ON tm_potongan_p.id_user = tm_pegawai.id_user
                            where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='$status' and tm_pegawai.tgl_masuk <= '$tmtcek'");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $potongan_pegawai = getOne("SELECT tm_potongan_p.potongan_bank, tm_potongan_p.cicilan_bank, tm_potongan_p.keterangan_bank, tm_potongan_p.potongan_rs, tm_potongan_p.cicilan_rs, tm_potongan_p.keterangan_bank FROM tm_potongan_p where id_user='$row[id_user]' and Month(tm_potongan_p.date_p)='$bulan_post' and Year(tm_potongan_p.date_p)='$tahun_post'");
                            ?>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo konversiBulanTahun($row['date_p']); ?></td>
                                    <td><?php echo "'" . $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['potongan_bank']; ?></td>
                                    <td><?php echo $row['cicilan_bank']; ?></td>
                                    <td><?php echo $row['keterangan_bank']; ?></td>
                                    <td><?php echo $row['potongan_rs']; ?></td>
                                    <td><?php echo $row['cicilan_rs']; ?></td>
                                    <td><?php echo $row['keterangan_rs']; ?></td>
                                    <td>
                                        <!--
                                        <?php
                                        //cek nilai bruto jika ada nilai dari penilaian maka akan di tampilkan nilai yang dipenilaian
                                        if ($bruto_penilaian == '') {
                                            echo formatDuit2(GajiBruto(GajiPokokLaporan($row['pendidikan'], $row['tgl_masuk'], $tmtcek), NilaiStatusKawin($row['status_nikah'])));
                                        } else {
                                            echo formatDuit2($bruto_penilaian);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo 'cetak-slip-gaji-' . $bulan_post . '-' . $tahun_post . '-' . $row['id_user']; ?>" target="_blank"><span title="Cetak Slip Gaji" class="btn-xs btn fa fa-file-pdf-o">  </span>                         
                                            </button>
                                        </a>
                                    </td>
                                </tr>                                  
                                <?php
                            }
                                ?> 
                            -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "laporan-tunjangan-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN TUNJANGAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $tmtcek = $tahun_post . "-" . $bulan_post . "-02";
                if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Sesuai') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Tunjangan Pegawai Bulan <?php echo konversiBulanTahun($tmtcek); ?></label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Slip Gaji</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Norek</th>
                                <th>NPWP</th>
                                <th>Status Nikah</th>
                                <th>Pendidikan</th>
                                <th>Rumpun</th>
                                <th>Masa Kerja</th>
                                <th>Gaji Pokok</th>
                                <th>Gaji Bruto</th>
                                <th>Tunjangan Murni</th>
                                <th>Nilai Kinerja</th>
                                <th>HukDis</th>
                                <th>Presentase HukDis</th>
                                <th style="background-color: indianred;">Kurang Bayar</th>
                                <th style="background-color: indianred;">Lebih Bayar</th>
                                <th>Alpha/Hari</th>
                                <th>Sakit 1-2/Hari</th>
                                <th>Sakit >2/Hari</th>
                                <th>Izin/Hari</th>
                                <th>Telat/Menit</th>
                                <th>Alpha/Rupiah</th>
                                <th>Sakit 1-2/Rupiah</th>
                                <th>Sakit >2/Rupiah</th>
                                <th>Izin/Rupiah</th>
                                <th>Telat/Rupiah</th>
                                <th style="background-color: indianred;">Tot. Pot. Kehadiran</th>
                                <th style="background-color: lightblue;">Tunj. Val Bruto(13*14)</th>
                                <th>Tot. Honor Shift</th>
                                <th>Tunj. NETTO Setelah Ditambah/Dikurang Bayar</th>
                                <th>BPJS Kes</th>
                                <th>BPJS TK</th>
                                <th>Total Pot. BPJS</th>
                                <th>Pendapatan Bulan Ini</th>
                                <th>Pend. Bruto/Tahun</th>
                                <th>Biaya Jab/Tahun</th>
                                <th>Netto/Tahun</th>
                                <th>PTKP/Tahun</th>
                                <th>PKP/Tahun</th>
                                <th>PPH 21/Bulan</th>
                                <!-- <th>Tot. Pend. Pot. PPH 21</th> -->
                                <th style="background-color: lightgreen;">Jumlah Gaji NETTO (32-35)</th>
                                <th style="background-color: lightgreen;">Jumlah Tunj NETTO (30+31-42)</th>
                                <!--EXT Putra-->
                                <!--                                <th>Jumlah BRUTO Sebelum Pajak</th>
                                    <th>Detail</th>-->
                            </tr>
                            <!--EXT Putra-->
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                                <td>9</td>
                                <td>10</td>
                                <td>11</td>
                                <td>12</td>
                                <td>13</td>
                                <td>14</td>
                                <td>15</td>
                                <td>16</td>
                                <td style="background-color: indianred;">17</td>
                                <td style="background-color: indianred;">18</td>
                                <td>19</td>
                                <td>20</td>
                                <td>21</td>
                                <td>22</td>
                                <td>23</td>
                                <td>24</td>
                                <td>25</td>
                                <td>26</td>
                                <td>27</td>
                                <td>28</td>
                                <td style="background-color: indianred;">29</td>
                                <<td style="background-color: lightblue;">30</td>
                                <td>31</td>
                                <td>32</td>
                                <td>33</td>
                                <td>34</td>
                                <td>35</td>
                                <td>36</td>
                                <td>37</td>
                                <td>38</td>
                                <td>39</td>
                                <td>40</td>
                                <td>41</td>
                                <td>42</td>
                                <!-- <td>43</td> -->
                                <td style="background-color: lightgreen;">43</td>
                                <td style="background-color: lightgreen;">44</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($bulan_post == 0 or $tahun_post == 0) {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            $bpjs_ks = getOne("select jumlah_bpjs_ks from tm_ump_bpjs where tahun='$tahun_post'");
                            $bpjs_tk = getOne("select jumlah_bpjs_tk from tm_ump_bpjs where tahun='$tahun_post'");
                            $sql_pegawai = bukaquery("SELECT  tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_penilaian.pendidikan, tm_pegawai.no_rek, tm_pegawai.npwp, tm_pegawai.status_nikah,
                                        tm_unit.nama_unit, IF
    ( tm_penilaian.gaji_bruto < '$bpjs_ks', '$bpjs_ks' * tm_penilaian.bpjs_ks, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_ks ) AS bpjs_kes,
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_jkk, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_jkk ) AS bpjs_jkk,
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_ijht, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_ijht ) AS bpjs_ijht,
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_jp, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_jp ) AS bpjs_jp,
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_jkk, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_jkk ) +
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_ijht, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_ijht ) +
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_tk', '$bpjs_tk' * tm_penilaian.bpjs_jp, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_jp ) +
IF
    ( tm_penilaian.gaji_bruto < '$bpjs_ks', '$bpjs_ks' * tm_penilaian.bpjs_ks, tm_penilaian.gaji_bruto * tm_penilaian.bpjs_ks ) AS potongan_bpjs,
                                            tm_level.nama_level,
                                            tm_penilaian.nskp+tm_penilaian.nprilaku+(tm_penyerapan.penyerapan*0.2) as bhu,
                                            tm_penilaian.id_sanksi, tm_penilaian.gaji_pokok, tm_penilaian.gaji_bruto, tm_penilaian.tunjangan, tm_penilaian.tunjangan_val, tm_penilaian.masa_kerja, tm_penilaian.rumpun, tm_penilaian.pajak, tm_waktu_k.sakit1,
                                            tm_waktu_k.sakit2, tm_waktu_k.alpha, tm_waktu_k.izin, tm_waktu_k.izin_setengah_hari, tm_waktu_k.telat,
                                            ((tm_waktu_s.j_hks*tm_honor_shift.hks)+(tm_waktu_s.j_hkm*tm_honor_shift.hkm)+(tm_waktu_s.j_hlp*tm_honor_shift.hlp)+(tm_waktu_s.j_hls*tm_honor_shift.hls)+(tm_waktu_s.j_hlm*tm_honor_shift.hlm)+(tm_waktu_s.j_hrp*tm_honor_shift.hrp)
                                            +(tm_waktu_s.j_hrs*tm_honor_shift.hrs)+(tm_waktu_s.j_hrm*tm_honor_shift.hrm)) As honor,
                                            ((tm_waktu_k.alpha*0.05)*tm_penilaian.tunjangan_val)+((tm_waktu_k.sakit1*0.01)*tm_penilaian.tunjangan_val)+((tm_waktu_k.sakit2*0.02)*tm_penilaian.tunjangan_val)+((tm_waktu_k.izin*0.025)*tm_penilaian.tunjangan_val)+(((tm_waktu_k.telat/450)*0.025)*tm_penilaian.tunjangan_val) as potongan_absensi,
                                            tm_penyerapan.penyerapan
                                            FROM tm_penilaian
                                            INNER JOIN tm_pegawai ON tm_penilaian.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_honor_shift ON tm_unit.id_petugas = tm_honor_shift.id_petugas
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            INNER JOIN tm_waktu_k ON tm_penilaian.id_waktu_k = tm_waktu_k.id_waktu_k
                                            INNER JOIN tm_waktu_s ON tm_penilaian.id_waktu_s = tm_waktu_s.id_waktu_s
                                            INNER JOIN tm_waktu_t ON tm_penilaian.id_waktu_t = tm_waktu_t.id_waktu_t
                                            INNER JOIN tm_penyerapan ON tm_penilaian.id_penyerapan = tm_penyerapan.id_penyerapan
                                            where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='$status' and
                                            Month(tm_penilaian.tanggal_penilaian)='$bulan_post' and Year(tm_penilaian.tanggal_penilaian)='$tahun_post' GROUP BY tm_penilaian.id_user order by tm_pegawai.nama_pegawai ASC");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                                $potongan_p =  fetch_array(bukaquery("select tm_potongan_p.id_potongan_p, tm_potongan_p.potongan_bank,tm_potongan_p.potongan_rs from tm_potongan_p where Month(tm_potongan_p.date_p)='$bulan_post' and Year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]'"));

                                $gajibersih = $row['gaji_bruto'] - $row['potongan_bpjs'];
                                $pendnetto = $gajibersih+$row['tunjangan_val'];
                                $potongan_telat = ((($row['telat']/450)*0.025)*$pendnetto);
                                $tunjbersih = $row['tunjangan_val'] - $potongan_telat;
                                $potongan_absensi = (($row['alpha']*0.05)*$row['tunjangan_val'])+(($row['sakit1']*0.01)*$row['tunjangan_val'])+(($row['sakit1']*0.02)*$row['tunjangan_val'])+(($row['izin']*0.05)*$row['tunjangan_val'])+$potongan_telat;
                                $totalpend = $gajibersih + $tunjbersih + $row['honor'];
                                $totalpendbersih = ($totalpend + $potongan_p['potongan_bank']) - $potongan_p['potongan_rs'];
                                $totaltunjbersih = ($tunjbersih + $potongan_p['potongan_bank']) - $potongan_p['potongan_rs'];
                                $bpjs_tk = $row['bpjs_jkk'] + $row['bpjs_ijht']+ $row['bpjs_jp'];
                                $biayajabsetahun = BiayaJabatanSetahun($totalpendbersih);
                                $biayajab = BiayaJabatanBulan($totalpendbersih);
                                $netsetahun = ($totalpendbersih *12) - $biayajabsetahun;
                                $pkpsetahun = $netsetahun - NilaiStatusPajak($row['pajak']);
                                $ptkpsetahun = NilaiStatusPajak($row['pajak']);
                                $masuktgl = $row ['tgl_masuk'];
                                $masakerja = JumlahBulan($masuktgl);

                                //                                $bruto_penilaian = getOne("SELECT tm_penilaian.gaji_bruto FROM tm_penilaian where id_user='$row[id_user]' and Month(tm_penilaian.tanggal_penilaian)='$bulan_post'");
                                $cek_data_cuti_melahirkan = getOne("select tm_cuti.id_cuti from tm_cuti where tm_cuti.id_user='$row[id_user]' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                                if ($cek_data_cuti_melahirkan != '') {
                                    $mulai = getOne("select tanggal from tm_hari_cuti where id_cuti='$cek_data_cuti_melahirkan' order by tanggal ASC");
                                    $selesai = getOne("select tanggal from tm_hari_cuti where id_cuti='$cek_data_cuti_melahirkan' order by tanggal DESC");
                                    if (strtotime($tmtcek) >= strtotime($mulai) and strtotime($tmtcek) <= strtotime($selesai)) {
                                        $tunjangan_val = $row['tunjangan_val'] * 0.5;
                                        $pph21 = 0;
                                        $tunjangan = 0;
                                    } else {
                                        $tunjangan_val = $row['tunjangan_val'];
                                        $hitung_pph21 = PPH21mtr($pkpsetahun);
                                        if ($hitung_pph21 < 0) {
                                            $pph21 = 0;
                                        } else {
                                            $pph21 = $hitung_pph21;
                                        }
                                        $tunjangan = (($row['gaji_bruto'] + $tunjangan_val - $row['potongan_absensi'] + $row['honor']) - $row['potongan_bpjs']) - $pph21 - $row['gaji_bruto'];
                                    }
                                } else {
                                    $tunjangan_val = $row['tunjangan_val'];
                                    $hitung_pph21 = PPH21mtr($pkpsetahun);
                                    if ($hitung_pph21 < 0) {
                                        $pph21 = 0;
                                    } else {
                                        $pph21 = $hitung_pph21;
                                    }
                                    $tunjangan = (($row['gaji_bruto'] + $tunjangan_val - $row['potongan_absensi'] + $row['honor']) - $row['potongan_bpjs']) - $pph21 - $row['gaji_bruto'];
                                }
                            ?>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td>
                                        <a href="<?php echo 'cetak-slip-gaji-' . $bulan_post . '-' . $tahun_post . '-' . $row['id_user']; ?>" target="_blank"><span title="Cetak Slip Gaji" class="btn-xs btn fa fa-file-pdf-o"> </span>
                                            </button>
                                        </a>
                                    </td>
                                    <td><?php echo "'" . $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['no_rek']; ?></td>
                                    <td><?php echo $row['npwp']; ?></td>
                                    <td><?php echo $row['status_nikah']; ?></td>
                                    <td><?php echo $row['pendidikan']; ?></td>
                                    <td><?php echo $row['rumpun']; ?></td>
                                    <td><?php echo $row['masa_kerja']; ?></td>
                                    <td><?php echo formatDuit2($row['gaji_pokok']); ?></td>
                                    <td><?php if ($masakerja <= 3) { 
                                        $gajibaru = $row['gaji_bruto'] *  0.75;
                                        echo formatDuit2($gajibaru);
                                        } else {
                                        echo formatDuit2($row['gaji_bruto']);
                                        }
                                         ?></td>
                                    <td><?php echo formatDuit2($row['tunjangan']); ?></td>
                                    <td><?php echo $row['bhu'] . "%"; ?></td>
                                    <td>
                                        <?php
                                        $sanksi =  fetch_array(bukaquery("select tm_sanksi.nama_sanksi,tm_sanksi.nilai_sanksi from tm_sanksi where tm_sanksi.id_sanksi='$row[id_sanksi]'"));
                                        if ($sanksi['nama_sanksi'] == '') {
                                            echo '-';
                                        } else {
                                            echo $sanksi['nama_sanksi'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($sanksi['nilai_sanksi'] == '') {
                                            echo '-';
                                        } else {
                                            echo $sanksi['nilai_sanksi'];
                                        }
                                        ?>
                                    </td>
                                    <td style="background-color: indianred;">
                                        <?php
                                        if ($potongan_p['potongan_bank'] == '') {
                                            echo '-';
                                        } else {
                                            echo formatDuit2($potongan_p['potongan_bank']);
                                        }
                                        ?>
                                    </td>
                                    <td style="background-color: indianred;">
                                        <?php
                                        if ($potongan_p['potongan_rs'] == '') {
                                            echo '-';
                                        } else {
                                            echo formatDuit2($potongan_p['potongan_rs']);
                                        }
                                        ?>
                                    </td>
                                    
                                    <td><?php echo $row['alpha'] . " Hari"; ?></td>
                                    <td><?php echo $row['sakit1'] . " Hari"; ?></td>
                                    <td><?php echo $row['sakit2'] . " Hari"; ?></td>
                                    <td><?php echo $row['izin'] . " Hari"; ?></td>
                                    <td><?php echo $row['telat'] . " Menit"; ?></td>
                                    <td><?php echo formatDuit2(($row['alpha'] * 0.05) * $tunjangan_val); ?></td>
                                    <td><?php echo formatDuit2(($row['sakit1'] * 0.01) * $tunjangan_val); ?></td>
                                    <td><?php echo formatDuit2(($row['sakit2'] * 0.02) * $tunjangan_val); ?></td>
                                    <td><?php echo formatDuit2(($row['izin'] * 0.025) * $tunjangan_val); ?></td>
                                    <td><?php echo formatDuit2($potongan_telat); ?></td>
                                    <td style="background-color: indianred;"><?php echo formatDuit2($potongan_absensi); ?></td>
                                    <td style="background-color: lightblue;"><?php echo formatDuit2($tunjangan_val); ?></td>
                                    <td><?php echo formatDuit2($row['honor']); ?></td>
                                    <td><?php echo formatDuit2($totaltunjbersih); ?></td>
                                    <td><?php echo formatDuit2($row['bpjs_kes']); ?></td>
                                    <td><?php echo formatDuit2($bpjs_tk); ?></td>
                                    <td><?php echo formatDuit2($row['potongan_bpjs']); ?></td>
                                    <td><?php echo formatDuit2($totalpendbersih); ?></td>
                                    <td><?php echo formatDuit2($totalpendbersih*12); ?></td>
                                    <td><?php echo formatDuit2($biayajabsetahun); ?></td>
                                    <td><?php echo formatDuit2($netsetahun); ?></td>
                                    <td><?php echo formatDuit2($ptkpsetahun); ?></td>
                                    <td><?php echo formatDuit2($pkpsetahun); ?></td>
                                    <td><?php
                                        if ($pph21 < 0) {
                                            echo '0';
                                        } else {
                                            echo formatDuit2($pph21);
                                        }
                                        ?></td>
                                    <!-- <td><?php echo formatDuit2((($row['gaji_bruto'] + $tunjangan_val - $row['potongan_absensi'] + $row['honor']) - $row['potongan_bpjs']) - $pph21); ?></td> -->
                                    <td style="background-color: lightgreen;"><?php echo formatDuit2($gajibersih) ; ?></td>
                                    <!--Ext Putra-->
                                    <td style="background-color: lightgreen;"><?php
                                                                                if ($tunjangan <= 0) {
                                                                                    echo '0';
                                                                                } else {
                                                                                    echo formatDuit2($totaltunjbersih - $pph21);
                                                                                }
                                                                                ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "laporan-penilaian-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN PENILAIAN PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="Left">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                                <td>
                                    <label>Tahun</label>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                        <?php loadThn('-Pilih Tahun-'); ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
                <br><br><br><br>
                <hr>
                <?php
                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                $tmtcek = $tahun_post . "-" . $bulan_post . "-01";
                if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Sesuai') {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php } else {
                ?>
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Penilaian Pegawai Bulan <?php echo konversiBulanTahun($tmtcek); ?></label></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                <?php }
                ?>
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>Nilai Kinerja</th>
                                <th>Nilai Perilaku</th>
                                <th>Nilai Pendapatan</th>
                                <th>BHU</th>
                                <th>HukDis</th>
                                <th>Presentase HukDis</th>
                                <th>Alpha/Hari</th>
                                <th>Sakit 1-2/Hari</th>
                                <th>Sakit >2/Hari</th>
                                <th>Izin/Hari</th>
                                <th>Telat/Menit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $status = 'eror';
                            if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                $status = 'eror';
                            } else {
                                $status = 'AKTIF';
                            }
                            //                            and tm_pegawai.sub_bagian='$kasie'
                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.nama_pegawai,tm_penilaian.nskp,tm_penilaian.nprilaku, tm_pegawai.nip, tm_penilaian.nskp+tm_penilaian.nprilaku+(tm_penyerapan.penyerapan*0.2) as bhu, (tm_penyerapan.penyerapan*0.2) as penyerapan,
                                        tm_penilaian.id_sanksi, tm_waktu_k.sakit1, tm_waktu_k.sakit2, tm_waktu_k.alpha, tm_waktu_k.izin, tm_waktu_k.izin_setengah_hari, tm_waktu_k.telat 
                                        FROM tm_penilaian
                                        INNER JOIN tm_pegawai ON tm_penilaian.id_user = tm_pegawai.id_user
                                        INNER JOIN tm_waktu_k ON tm_penilaian.id_waktu_k = tm_waktu_k.id_waktu_k
                                        INNER JOIN tm_waktu_t ON tm_penilaian.id_waktu_t = tm_waktu_t.id_waktu_t
                                        INNER JOIN tm_penyerapan ON tm_penilaian.id_penyerapan = tm_penyerapan.id_penyerapan
                                        where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='$status' and
                                        Month(tm_penilaian.tanggal_penilaian)='$bulan_post' and Year(tm_penilaian.tanggal_penilaian)='$tahun_post'  order by tm_pegawai.tgl_masuk ASC");
                            while ($row = fetch_array($sql_pegawai)) {
                                $no++;
                            ?>
                                <!-- Tutup Edit Modal permintaan -->
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo "'" . $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['nskp']; ?></td>
                                    <td><?php echo $row['nprilaku']; ?></td>
                                    <td><?php echo $row['penyerapan']; ?></td>
                                    <td><?php echo $row['bhu'] . "%"; ?></td>
                                    <td>
                                        <?php
                                        if (getOne("select tm_sanksi.nama_sanksi from tm_sanksi where tm_sanksi.id_sanksi='$row[id_sanksi]'") == '') {
                                            echo '-';
                                        } else {
                                            echo getOne("select tm_sanksi.nama_sanksi from tm_sanksi where tm_sanksi.id_sanksi='$row[id_sanksi]'");
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (getOne("select tm_sanksi.nilai_sanksi from tm_sanksi where tm_sanksi.id_sanksi='$row[id_sanksi]'") == '') {
                                            echo '-';
                                        } else {
                                            echo getOne("select tm_sanksi.nilai_sanksi from tm_sanksi where tm_sanksi.id_sanksi='$row[id_sanksi]'");
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $row['alpha'] . " Hari"; ?></td>
                                    <td><?php echo $row['sakit1'] . " Hari"; ?></td>
                                    <td><?php echo $row['sakit2'] . " Hari"; ?></td>
                                    <td><?php echo $row['izin'] . " Hari"; ?></td>
                                    <td><?php echo $row['telat'] . " Menit"; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        break;

    case "lap-data-upload":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> CEK DATA UPLOAD PEGAWAI NON PNS</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <p></p>
                <!-- /.box-header -->
                <div class="box-body table-responsive">

                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama Pegawai</th>
                                <th>NO HP</th>
                                <th>KTP</th>
                                <th>KK</th>
                                <th>Ijazah</th>
                                <th>NPWP</th>
                                <th>STR</th>
                                <th>Periode STR</th>
                                <th>SIP</th>
                                <th>Periode SIP</th>
                                <th>ACLS</th>
                                <th>Periode ACLS</th>
                                <th>ATLS</th>
                                <th>Periode ATLS</th>
                                <th>BTCLS</th>
                                <th>Periode BTCLS</th>
                                <th>APN</th>
                                <th>Periode APN</th>
                                <th>PHELEBHETOMY</th>
                                <th>Periode PHELEBHETOMY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $list_pegawai = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.id_user, tm_pegawai.nama_pegawai, tm_pegawai.no_hp
                                            FROM
                                            tm_pegawai                                           
                                            where tm_pegawai.status_pegawai='NON PNS'");
                            while ($row = fetch_array($list_pegawai)) {
                                $no++;
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nip']; ?></td>
                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                    <td><?php echo $row['no_hp']; ?></td>
                                    <td><?php echo getOne("select count(*) from tm_ktp where tm_ktp.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_kk where tm_kk.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_ijazah where tm_ijazah.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_npwp where tm_npwp.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_str where tm_str.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_str.periode, 20, 4),SUBSTRING(tm_str.periode, 17, 2),SUBSTRING(tm_str.periode, 14, 2))ORDER BY tm_str.id_str DESC),',',1) AS str
                                                            from tm_str where tm_str.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_sip where tm_sip.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_sip.periode, 20, 4),SUBSTRING(tm_sip.periode, 17, 2),SUBSTRING(tm_sip.periode, 14, 2))ORDER BY tm_sip.id_sip DESC),',',1) AS sip
                                                            from tm_sip where tm_sip.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_acls where tm_acls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_acls.periode, 20, 4),SUBSTRING(tm_acls.periode, 17, 2),SUBSTRING(tm_acls.periode, 14, 2))ORDER BY tm_acls.id_acls DESC),',',1) AS acls
                                                            from tm_acls where tm_acls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_atls where tm_atls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_atls.periode, 20, 4),SUBSTRING(tm_atls.periode, 17, 2),SUBSTRING(tm_atls.periode, 14, 2))ORDER BY tm_atls.id_atls DESC),',',1) AS atls
                                                            from tm_atls where tm_atls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_btcls where tm_btcls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_btcls.periode, 20, 4),SUBSTRING(tm_btcls.periode, 17, 2),SUBSTRING(tm_btcls.periode, 14, 2))ORDER BY tm_btcls.id_btcls DESC),',',1) AS btcls
                                                            from tm_btcls where tm_btcls.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_apn where tm_apn.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_apn.periode, 20, 4),SUBSTRING(tm_apn.periode, 17, 2),SUBSTRING(tm_apn.periode, 14, 2))ORDER BY tm_apn.id_apn DESC),',',1) AS apn
                                                            from tm_apn where tm_apn.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select count(*) from tm_phelebethomy where tm_phelebethomy.id_user='$row[id_user]'"); ?></td>
                                    <td><?php echo getOne("select SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('-',SUBSTRING(tm_phelebethomy.periode, 20, 4),SUBSTRING(tm_phelebethomy.periode, 17, 2),SUBSTRING(tm_phelebethomy.periode, 14, 2))ORDER BY tm_phelebethomy.id_phelebethomy DESC),',',1) AS phelebethomy
                                                            from tm_phelebethomy where tm_phelebethomy.id_user='$row[id_user]'"); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php
        break;
}
?>