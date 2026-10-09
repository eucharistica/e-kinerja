<?php
$aksi = "module/validasi-pegawai/aksi-validasi-pegawai.php?";
$bln_absensi = isset($url['bulan']) ? $url['bulan'] : null;
$bulan_post = isset($url['bulan']) ? $url['bulan'] : null;
$tahun_post = isset($url['tahun']) ? $url['tahun'] : null;
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
$cek_hak_akses = mysqli_fetch_array(bukaquery("select tm_level.menu_val_pj,tm_level.menu_val_kasatpel,tm_level.menu_val_kasie from tm_level"
    . " inner join tm_user on tm_level.id_level=tm_user.id_level where tm_user.id_user='$id_user'"));

if ($cek_hak_akses['menu_val_pj'] == '1' or $cek_hak_akses['menu_val_kasatpel'] == '1' or $cek_hak_akses['menu_val_kasie'] == '1' or $hak_akses['menu_kepegawaian'] == '1') {

    switch ((isset($url['act']) ? $url['act'] : '')) {
        default:
            header("location:error404");
            break;


        case "list-data-penilaian-unit": #dummy
?>
        <script type="text/javascript">
            function showSisa() {
                <?php
                $thnini = date('Y');
                for ($thn = $thnini; $thn >= 2017; $thn--) {
                    $thn_leng = strlen($thn);
                    if ($thn_leng == 1) {
                        $i = "0" . $thn;
                    } else {
                        $i = $thn;
                    }

                    // membuat IF untuk masing-masing id dpa
                    echo "if (document.pilihan.tahun.value == \"" . $i . "\")";
                    echo "{";

                    // membuat option dokter untuk masing-masing poliklinik yg terjadwal
                    if ($i == $thnini) {
                        // $cek = getOne("SELECT SUM(tm_waktu_s.j_hks + tm_waktu_s.j_hkm) as nilai from tm_waktu_s WHERE tm_waktu_s.id_user='$id_user'");
                        // if ($cek == 0) {
                        //     $hct = 12;
                        // } else {
                        //     $hct = 12;
                        // }
                        $hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user' and tahun='$i'");
                        $jumlah_sisa = getOne("SELECT ($hct-(sum(tm_cuti.jumlah_hari))) as jumlah from tm_cuti where tm_cuti.id_user='$id_user' and tm_cuti.tahun_cuti='$i' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y' OR acc_kasie='-')");
                        if ($jumlah_sisa == '') {
                            $nilai = $hct;
                        } else {
                            if ($jumlah_sisa < 0) {
                                $nilai = 0;
                            } else {
                                $nilai = $jumlah_sisa;
                            }
                        }
                    } else {
                        $jumlah_sisa = getOne("SELECT sum(tm_cuti.jumlah_hari) as jumlah from tm_cuti where tm_cuti.id_user='$id_user' and tm_cuti.tahun_cuti='$i' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y' OR acc_kasie='-') and alasan='PNG'");
                        if ($jumlah_sisa == '') {
                            $nilai = 0;
                        } else {
                            if ($jumlah_sisa < 0) {
                                $nilai = 0;
                            } else {
                                $nilai = $jumlah_sisa;
                            }
                        }
                    }
                    $content = "document.getElementById('sisa').innerHTML = \"<label>Sisa Cuti</label>";

                    $content .= "<input type='text 'class='form-control' value='$nilai' name='sisa_cuti' required readonly>\";";
                    echo $content;
                    echo "}\n";
                }
                ?>
            }
        </script>
        <div class="box">
            
            <!-- Tutup Edit Modal permintaan -->
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PENILAIAN UNIT</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pengajuan-cuti">
                    Buat Penilaian Unit
                </button>

                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-pengajuan-cuti" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Pengajuan Data Cuti pegawai</h4>
                            </div>
                            <form role="form" name="pilihan" action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=add-pengajuan-cuti&id=' . $id_user . ''); ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="control-label">Tahun Cuti </label>
                                        <select class="form-control select2" onchange="showSisa()" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                            <?php loadThn(); ?>
                                        </select>
                                        <div id="sisa"> </div>
                                        <label class="control-label">Tanggal Permohonan </label>
                                        <input type="text" name="tgl_permohonan" class="form-control" placeholder="mm/dd/yyyy" id="datepicker" required readonly>
                                        <label>Jenis Cuti</label>
                                        <?php echo enumDropdown("tm_cuti", "jenis_cuti", "", "-Pilih Jenis Cuti-"); ?>
                                        <label class="control-label">Alasan Cuti</label>
                                        <textarea class="form-control" rows="3" name="alasan_cuti" placeholder="Alasan.." required></textarea>
                                        <label class="control-label">Alamat Saat Cuti</label>
                                        <textarea class="form-control" rows="3" name="alamat_cuti" placeholder="Alamat Selama Cuti" required></textarea>
                                        <label class="control-label">No Tlp Yang Dapat Di Hubungi</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                            </div>
                                            <input type="number" name="no_tlp" placeholder="Your Phone Number" class="form-control pull-right" required>
                                        </div>
                                        <label>Periode Cuti</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="periode_cuti" class="form-control pull-right" id="multidatepicker" required readonly>
                                        </div>
                                        <label>Pengganti Cuti</label>
                                        <select class="form-control select2" name="nip_pengganti" data-placeholder="-Pilih Nama Pengganti-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama Pengganti-</option>
                                            <?php
                                            $tm_pegawai = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF' and tm_pegawai.id_user!='$id_user' order by tm_pegawai.nama_pegawai asc");
                                            while ($row = fetch_array($tm_pegawai)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label>Disposisi PJ</label>
                                        <select class="form-control select2" name="nip_pj" data-placeholder="-Pilih Nama PJ-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama Pj-</option>
                                            <?php
                                            $tm_pj = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai
                                                                                            FROM tm_pegawai
                                                                                            INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                                                            INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                                                            WHERE tm_pegawai.status='AKTIF' and (tm_level.menu_val_pj='1' OR tm_level.menu_val_kasatpel='1') group by tm_pegawai.nama_pegawai");
                                            while ($row = fetch_array($tm_pj)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label>Disposisi Kasie</label>
                                        <select class="form-control select2" name="nip_kasie" data-placeholder="-Pilih Nama Kasie-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Kasie-</option>
                                            <?php
                                            $tm_kasie = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.`status` FROM tm_pegawai
                                                        INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                        INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                        WHERE tm_pegawai.sub_bagian = '$kasie' and tm_level.menu_val_kasie='1'");
                                            while ($row = fetch_array($tm_kasie)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Permohonan</th>
                                <th>Jenis Cuti</th>
                                <th>Periode Cuti</th>
                                <th>Digantikan Oleh</th>
                                <th>Acc Pengganti</th>
                                <th>Acc PJ</th>
                                <th>Acc Kepegawaian</th>
                                <th>Acc Kasie</th>
                                <th>Action/Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.alamat_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti, tm_cuti.acc_pj, tm_cuti.acc_kepegawaian, tm_cuti.acc_kasie, tm_cuti.nip_pengganti, 
                                                        tm_cuti.nip_pj, tm_cuti.nip_kasie , tm_cuti.alasan 
                                                        FROM tm_cuti
                                                        where tm_cuti.id_user='$id_user'  order by tm_cuti.tgl_permohonan desc");
                            while ($row = fetch_array($tm_cuti)) {
                                $tanggal1 = date('Y-m-d', strtotime(substr($row['periode_cuti'], 0, 10)));
                                $tanggal2 = date('Y-m-d', strtotime(substr($row['periode_cuti'], 13, 10)));
                                $lama_cuti = HitungHari(date('d-m-Y', strtotime(substr($row['periode_cuti'], 0, 10))), date('d-m-Y', strtotime(substr($row['periode_cuti'], 13, 10)))) - HitungHariMinggu(date('d-m-Y', strtotime(substr($row['periode_cuti'], 0, 10))), date('d-m-Y', strtotime(substr($row['periode_cuti'], 13, 10))));
                                $no++;
                            ?>
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-ubah-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=update-pengajuan-cuti&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Update Pengajuan Cuti</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label">Tanggal Permohonan (Tgl/Bln/Thn)</label>
                                                            <input type="text" name="tgl_permohonan" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_permohonan']); ?>" placeholder="mm/dd/yyyy" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                                            <label class="control-label">Jenis Cuti</label>
                                                            <?php echo UpdateEnumDropdown("tm_cuti", "jenis_cuti", $row['jenis_cuti'], ""); ?>
                                                            <label class="control-label">Alasan Cuti</label>
                                                            <textarea class="form-control" rows="3" name="alasan_cuti" placeholder="Alasan Cuti" required><?php echo $row['alasan_cuti']; ?></textarea>
                                                            <label class="control-label">Alamat Saat Cuti</label>
                                                            <textarea class="form-control" rows="3" name="alamat_cuti" placeholder="Alamat Selama Cuti" required><?php echo $row['alamat_cuti']; ?></textarea>
                                                            <label class="control-label">No Tlp Yang Dapat Di Hubungi</label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-phone"></i>
                                                                </div>
                                                                <input type="number" name="no_tlp" value="<?php echo $row['no_tlp']; ?>" placeholder="Your Phone Number" class="form-control pull-right" required>
                                                            </div>
                                                            <label>Pengganti Cuti</label>
                                                            <select class="form-control select2" name="nip_pengganti" data-placeholder="-Pilih nama pegawai-" style="width: 100%;" required>
                                                                <option selected="selected" value="">-Pilih Pegawai-</option>
                                                                <?php
                                                                $tm_cuti_pengganti = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF' and tm_pegawai.id_user!='$id_user'");
                                                                while ($ct = fetch_array($tm_cuti_pengganti)) {
                                                                    if ($row['nip_pengganti'] == $ct['nip']) {
                                                                        echo "<option value=" . $ct['nip'] . " selected=" . $row['nip_pengganti'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    } else {
                                                                        echo "<option value=" . $ct['nip'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <label>Disposisi PJ</label>
                                                            <select class="form-control select2" name="nip_pj" data-placeholder="-Pilih Nama PJ-" style="width: 100%;" required>
                                                                <option selected="selected" value="">-Pilih PJ-</option>
                                                                <?php
                                                                $tm_pj = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai
                                                                                            FROM tm_pegawai
                                                                                            INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                                                            INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                                                            WHERE tm_pegawai.status='AKTIF' AND (tm_pegawai.id_unit = '$id_unit' and tm_level.menu_val_pj='1') OR (tm_pegawai.id_kasatpel = '$kasatpel' and tm_level.menu_val_kasatpel='1') ");
                                                                while ($ct = fetch_array($tm_pj)) {
                                                                    if ($row['nip_pj'] == $ct['nip']) {
                                                                        echo "<option value=" . $ct['nip'] . " selected=" . $row['nip_pj'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    } else {
                                                                        echo "<option value=" . $ct['nip'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <label>Disposisi Kasie</label>
                                                            <select class="form-control select2" name="nip_kasie" data-placeholder="-Pilih Nama Kasie-" style="width: 100%;" required>
                                                                <option selected="selected" value="">-Pilih Kasie-</option>
                                                                <?php
                                                                $tm_kasie = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.`status` FROM tm_pegawai
                                                                INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                                INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                                WHERE tm_pegawai.sub_bagian = '$kasie' and tm_level.menu_val_kasie='1'");
                                                                while ($ct = fetch_array($tm_kasie)) {
                                                                    if ($row['nip_kasie'] == $ct['nip']) {
                                                                        echo "<option value=" . $ct['nip'] . " selected=" . $row['nip_kasie'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    } else {
                                                                        echo "<option value=" . $ct['nip'] . ">" . $ct['nama_pegawai'] . "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->
                                <!-- Edit Modal SKP -->
                                <div class="modal fade" id="modal-hapus-<?php echo $row['id_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=delete-pengajuan-cuti&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Delete Data SKP Tahunan</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Yakin Anda ingin Menghapus Data ini,klik Tombol Ya? Untuk Menghapusnya</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tutup Edit Modal permintaan -->

                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                    <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                    <td>
                                        <p><?php
                                            echo "(" . $row['jumlah_hari'] . " Hari) " . konversiTanggalBulan(getOne("select tanggal from tm_hari_cuti where id_cuti='$row[id_cuti]' order by tanggal asc")) . " s/d " . konversiTanggalBulan(getOne("select tanggal from tm_hari_cuti where id_cuti='$row[id_cuti]' order by tanggal desc")) . " " . FormatTgl('Y', getOne("select tanggal from tm_hari_cuti where id_cuti='$row[id_cuti]' order by tanggal desc"))
                                            ?>
                                    </td>
                                    <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.nip='$row[nip_pengganti]'"); ?></td>
                                    <td><?php
                                        if ($row['acc_pengganti'] == 'T') {
                                            echo "<i class='fa fa-close' title='ditolak'></i>";
                                        } elseif ($row['acc_pengganti'] == 'Y') {
                                            echo "<i class='fa fa-check' title='diterima'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php
                                        if ($row['acc_pj'] == 'T') {
                                            echo "<i class='fa fa-close' title='ditolak'></i>";
                                        } elseif ($row['acc_pj'] == 'Y') {
                                            echo "<i class='fa fa-check' title='diterima'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php
                                        if ($row['acc_kepegawaian'] == 'T') {
                                            echo "<i class='fa fa-close' title='ditolak'></i>";
                                        } elseif ($row['acc_kepegawaian'] == 'Y') {
                                            echo "<i class='fa fa-check' title='diterima'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php
                                        if ($row['acc_kasie'] == 'T') {
                                            echo "<i class='fa fa-close' title='ditolak'></i>";
                                        } elseif ($row['acc_kasie'] == 'Y') {
                                            echo "<i class='fa fa-check' title='diterima'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['acc_pengganti'] == 'Y' and $row['acc_pj'] == 'Y' and $row['acc_kepegawaian'] == 'Y' and $row['acc_kasie'] == 'Y') { ?>
                                            <a href="<?php echo 'cetak-surat-cuti-' . $row['id_cuti'] . ''; ?>" target="_blank"><span data-toggle="modal" title="Cetak" class="btn-xs btn-success fa fa-file-pdf-o"> Cetak Surat</span></a>
                                            <!--<span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>-->
                                        <?php
                                        } elseif ($row['acc_pengganti'] == 'Y' and $row['acc_pj'] == 'Y' and $row['acc_kepegawaian'] == 'Y' and $row['acc_kasie'] == 'T') {
                                            if ($row['alasan'] == 'PRB') {
                                                $perihal = 'Perubahan Tanggal ' . ucwords(strtolower($$row['jenis_cuti']));
                                            } else {
                                                $perihal = 'Penangguhan ' . ucwords(strtolower($$row['jenis_cuti']));
                                                $perihal .= "<a href='cetak-surat-penolakan-cuti-$row[id_cuti]' target='_blank'><span title='Cetak' class='btn-xs btn-warning fa fa-file-pdf-o'> Cetak Surat Penolakan</span></a>";
                                            }
                                        ?>
                                            <span data-toggle="modal" title="" class="btn-xs btn-danger"> <?php echo $perihal; ?></span>
                                        <?php } else { ?>
                                            <?php if ($row['acc_pengganti'] == 'Y' or $row['acc_pj'] == 'Y' or $row['acc_kepegawaian'] == 'Y' or $row['acc_kasie'] == 'Y') { ?>
                                                <span data-toggle="modal" title="Menunggu Assesment" class="btn-xs btn-info fa fa-refresh"> Menunggu Assesment</span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            <?php } else { ?>
                                                <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_cuti']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                        <?php
                                            }
                                        }
                                        ?>
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

    case "validasi-waktu-pengurangan":
        
        if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
            $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
        } else {
            $bulan = date('m', strtotime('-1 month'));
        }
        
        if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
            $tahun = $tahun_post;
        } else {
           $tahun = date('Y');
        }
        
        $monthStart = date("Y-m-01", strtotime("$tahun-$bulan-01"));
        $monthEnd   = date("Y-m-t", strtotime($monthStart));
    ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI PENGURANGAN WAKTU PEGAWAI</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="col-md-8">
                            <div class="panel-group" id="dropdown">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-calendar-check-o">
                                            <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                            REPORT ABSENSI <?php echo strtoupper(konversiBulanTahun($monthStart)); ?></a>
                                        </h4>
                                    </div>
                                    <?php
                                    $biodata   = fetch_array(bukaquery("SELECT log_finger, nama_pegawai,nip FROM tm_pegawai WHERE id_user='".addslashes($id)."'"));
                                    $logFinger = $biodata['log_finger'] ?? null;
                                    $namaPeg   = $biodata['nama_pegawai'] ?? '-';
                                    $nipPeg = $biodata['nip'] ?? null;
                                      if (!$logFinger) {
                                          echo '<div class="alert alert-danger">log_finger pegawai tidak ditemukan.</div>';
                                          break;
                                      }
                            
                                      // =========================
                                      // Parameter & agregat
                                      // =========================
                                      $MAX_TELAT_MIN          = 225;           // penalti per missing-IN/OUT
                                      $TODAY_YMD              = date('Y-m-d');
                                      $NOW_TS                 = time();        // local time (server)
                            
                                      $MIN_OUT_GAP_MIN        = 225;            // OUT valid jika >= 225 menit setelah IN
                                      $EARLY_LEAVE_GRACE_MIN  = 5;             // toleransi pulang cepat
                            
                                      $totTelatIn  = 0;
                                      $totTelatOut = 0;
                                      $totTotal    = 0;
                            
                                      $labelBulan  = konversiBulanTahun($monthStart);
                            
                                      // helper: index-friendly MIN/MAX
                                      $minPunch = function($user, $startTs, $endTs) {
                                          return getOne("SELECT MIN(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
                                      };
                                      $maxPunch = function($user, $startTs, $endTs) {
                                          return getOne("SELECT MAX(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
                                      };
                            
                                      // helper: aiCut
                                      $computeAiCutTs = function(string $hariYmd, ?string $jam_masuk, $ai) {
                                          if ($ai !== null && $ai !== '' && preg_match('/^\s*(\d{1,2}):(\d{2})\s*$/', (string)$ai)) {
                                              return "$hariYmd " . trim($ai) . ":00";
                                          }
                                          $aiMin = null;
                                          if (is_numeric($ai)) $aiMin = (int)$ai;
                                          if ($aiMin === null) $aiMin = 90;
                                          if ($jam_masuk && strtotime("$hariYmd $jam_masuk") !== false) {
                                              return date('Y-m-d H:i:s', strtotime("$hariYmd $jam_masuk +$aiMin minutes"));
                                          }
                                          return date('Y-m-d H:i:s', strtotime("$hariYmd 09:00:00 +$aiMin minutes"));
                                      };
                            
                                      // fallback status_pegawai
                                      if (!isset($statusPegawai) || !is_array($statusPegawai) || !array_key_exists('status_pegawai', $statusPegawai)) {
                                          $statusPegawai = ['status_pegawai' => 'NON_SPESIALIS'];
                                      }                                    
                                    
                                    ?>
                                    <div id="collapse2" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="box-body table-responsive">
                                                <table id="example2" class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                      <th>Tanggal</th>
                                                      <th>Nama Pegawai</th>
                                                      <th>Jadwal Shift</th>
                                                      <th>IN</th>
                                                      <th>OUT</th>
                                                      <th>Telat Masuk</th>
                                                      <th>Pulang Cepat</th>
                                                      <th>Total</th>
                                                      <th>Keterangan</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>
                                                    <?php
                                                    $cur  = new DateTimeImmutable($monthStart);
                                                    $last = new DateTimeImmutable($monthEnd);
                                    
                                                    while ($cur <= $last) {
                                                        $hariYmd     = $cur->format('Y-m-d');
                                                        $hariYmdPlus = $cur->modify('+1 day')->format('Y-m-d');
                                                        $isFuture    = ($hariYmd > $TODAY_YMD);
                                    
                                                        // Ambil mapping shift + ai
                                                        $sqlMap = "
                                                          SELECT s.jam_masuk, s.jam_pulang, s.nama_shift, s.kode_shift, s.color, s.ai, j.acc_kepegawaian
                                                          FROM tm_jadwal j
                                                          JOIN tm_shift s ON s.id_absensi = j.id_absensi
                                                          WHERE j.nip_pegawai = '$nipPeg' AND j.tanggal_jadwal = '$hariYmd'
                                                          LIMIT 1
                                                        ";
                                                        $mapping = fetch_array(bukaquery($sqlMap));
                                                        if (!$mapping) {
                                                            $mapping = [
                                                              'jam_masuk' => null,
                                                              'jam_pulang' => null,
                                                              'nama_shift' => '-',
                                                              'kode_shift' => '-',
                                                              'color' => 'white',
                                                              'ai' => null,
                                                              'acc_kepegawaian' => '-',
                                                            ];
                                                        }
                                    
                                                        $kodeShift   = $mapping['kode_shift'];
                                                        $accPegawai  = $mapping['acc_kepegawaian'];
                                                        $jam_masuk   = $mapping['jam_masuk'] ?: '';
                                                        $jam_pulang  = $mapping['jam_pulang'] ?: '';
                                                        $namaShift   = $mapping['nama_shift'] ?: '-';
                                                        $badgeColor  = $mapping['color'] ?: 'white';
                                                        $aiCutTs     = $computeAiCutTs($hariYmd, $jam_masuk ?: null, $mapping['ai']);
                                    
                                                        $absen_in   = '-';
                                                        $absen_out  = '-';
                                                        $status     = '-';
                                    
                                                        $telat_in   = 0;  // menit
                                                        $telat_out  = 0;  // menit
                                                        $pen_in     = 0;  // penalti 225 untuk no IN
                                                        $pen_out    = 0;  // penalti 225 untuk no OUT
                                    
                                                        $startTs = "$hariYmd 00:00:00";
                                                        $endTs   = "$hariYmd 23:59:59";
                                                        $nextStart = "$hariYmdPlus 00:00:00";
                                                        $nextNoon  = "$hariYmdPlus 11:59:59";
                                    
                                                        $isException = in_array($kodeShift, ['OFF','DL','C','Skt','I'], true);
                                    
                                                        $inTs = null; $outTs = null;
                                    
                                                        if ($kodeShift === 'M') {
                                                            // Malam: IN >= 18:00 (H), OUT <= 12:00 (H+1)
                                                            $inTs  = $minPunch($logFinger, "$hariYmd 18:00:00", $endTs);
                                                            $outTs = $maxPunch($logFinger, $nextStart, $nextNoon);
                                                            if ($inTs)  { $absen_in  = date('H:i:s', strtotime($inTs)); $status = 'M'; }
                                                            if ($outTs) { $absen_out = date('H:i:s', strtotime($outTs)); }
                                                        } elseif ($kodeShift === 'SM') {
                                                            // Siang-malam: IN >= 12:00 (H), OUT <= 12:00 (H+1)
                                                            $inTs  = $minPunch($logFinger, "$hariYmd 12:00:00", $endTs);
                                                            $outTs = getOne("SELECT MIN(log.tanggal) FROM log WHERE `user`='".addslashes($logFinger)."' AND log.tanggal BETWEEN '$nextStart' AND '$nextNoon'");
                                                            if ($inTs)  { $absen_in  = date('H:i:s', strtotime($inTs)); $status = 'SM'; }
                                                            if ($outTs) { $absen_out = date('H:i:s', strtotime($outTs)); }
                                                        } else {
                                                            // NORMAL / tidak dikenal:
                                                            // 1) IN = MIN punch di rentang [00:00, aiCut]
                                                            $cutTs = $aiCutTs && strtotime($aiCutTs) ? $aiCutTs : "$hariYmd 09:00:00";
                                                            $inTs  = $minPunch($logFinger, $startTs, $cutTs);
                                    
                                                            if ($inTs) {
                                                                $absen_in = date('H:i:s', strtotime($inTs));
                                                                // 2) OUT valid = MAX punch >= (inTs + MIN_OUT_GAP_MIN)
                                                                $outTs = getOne("
                                                                    SELECT MAX(log.tanggal)
                                                                    FROM log
                                                                    WHERE `user`='".addslashes($logFinger)."'
                                                                      AND log.tanggal BETWEEN '$startTs' AND '$endTs'
                                                                      AND log.tanggal >= DATE_ADD('".$inTs."', INTERVAL ".(int)$MIN_OUT_GAP_MIN." MINUTE)
                                                                ");
                                                            } else {
                                                                // Tidak ada punch hingga AI → IN kosong
                                                                // 3) Jika ada punch > AI, gunakan sebagai OUT kandidat
                                                                $outTs = $maxPunch($logFinger, $cutTs, $endTs);
                                                            }
                                    
                                                            if ($outTs) { $absen_out = date('H:i:s', strtotime($outTs)); }
                                                        }
                                    
                                                        // === FUTURE DATE ===
                                                        if ($isFuture) {
                                                            // Masa depan: jangan hitung telat. OUT tetap boleh tersembunyi.
                                                            $telat_in = $telat_out = $pen_in = $pen_out = 0;
                                                            $status = '-';
                                                            // NB: tampilkan IN/OUT yang sudah terjadi? Biasanya belum ada. Biarkan apa adanya.
                                                        } else {
                                                            if ($isException) {
                                                                // OFF/DL/C/Skt/I
                                                                $telat_in = $telat_out = $pen_in = $pen_out = 0;
                                                                switch ($kodeShift) {
                                                                    case 'OFF': $status='Libur'; break;
                                                                    case 'DL' : $status='Dinas Luar'; break;
                                                                    case 'C'  : $status='Cuti'; break;
                                                                    case 'Skt': $status='Sakit'; break;
                                                                    case 'I'  : $status='Izin'; break;
                                                                    default   : $status='-';
                                                                }
                                                            } else {
                                                                // ====== Hide OUT sebelum jam_pulang (hanya untuk hari ini & punya jam_pulang) ======
                                                                $suppressOutForToday = false;
                                                                if ($hariYmd === $TODAY_YMD && $jam_pulang !== '') {
                                                                    $cutPulangTs = strtotime("$hariYmd $jam_pulang");
                                                                    if ($cutPulangTs !== false && $NOW_TS < $cutPulangTs) {
                                                                        // Sembunyikan OUT & jangan hitung penalti/pulang cepat dulu
                                                                        $suppressOutForToday = true;
                                                                        $outTs = null;
                                                                        $absen_out = '-';
                                                                    }
                                                                }
                                    
                                                                // Penalti tidak ada IN/OUT (berlaku meskipun tidak ada jadwal)
                                                                $noIn  = !$inTs;
                                                                $noOut = !$outTs;
                                    
                                                                if ($noIn && $noOut) {
                                                                    $pen_in  = $MAX_TELAT_MIN;
                                                                    $pen_out = $MAX_TELAT_MIN;
                                                                    $status = ($status === '-' ? '' : $status.'; ').'Tidak ada IN & OUT';
                                                                } else {
                                                                    if ($noIn) {
                                                                        $pen_in = $MAX_TELAT_MIN;
                                                                        $status = ($status === '-' ? '' : $status.'; ').'Tidak ada IN';
                                                                    }
                                                                    if ($noOut && !$suppressOutForToday) {
                                                                        // jika disuppress karena belum lewat jam_pulang, JANGAN penalti
                                                                        $pen_out = $MAX_TELAT_MIN;
                                                                        $status = ($status === '-' ? '' : $status.'; ').'Tidak ada OUT';
                                                                    }
                                                                    if ($noOut && $suppressOutForToday) {
                                                                        $status = ($status === '-' ? '' : $status.'; ').'-';
                                                                    }
                                                                }
                                    
                                                                // Telat masuk (non-spesialis) — hanya jika ada IN & jam_masuk
                                                                if (!$noIn && $statusPegawai['status_pegawai'] !== 'SPESIALIS' && $jam_masuk !== '') {
                                                                    $tIn  = strtotime($absen_in);
                                                                    $tReq = strtotime($jam_masuk);
                                                                    if ($tIn !== false && $tReq !== false && $tIn > $tReq) {
                                                                        $diffMin = intdiv($tIn - $tReq, 60);
                                                                        $telat_in = min($diffMin, $MAX_TELAT_MIN);
                                                                    }
                                                                }
                                    
                                                                // Pulang cepat — hanya jika OUT ada & jam_pulang ada & tidak disuppress
                                                                if (!$noOut && !$suppressOutForToday && $jam_pulang !== '') {
                                                                    $tOut = strtotime($absen_out);
                                                                    $tReq = strtotime($jam_pulang);
                                                                    if ($tOut !== false && $tReq !== false) {
                                                                        $diffMin = (int) floor(($tReq - $tOut) / 60); // positif bila OUT < jam_pulang
                                                                        if ($diffMin > $EARLY_LEAVE_GRACE_MIN) {
                                                                            $telat_out = $diffMin - $EARLY_LEAVE_GRACE_MIN;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                        // === ACC override: hilangkan total telat jika acc_kepegawaian bukan '-' dan bukan 'Alpa'
                                                        if (!$isFuture && !$isException) {
                                                            if (isset($accPegawai) && $accPegawai !== '-' && strcasecmp($accPegawai, 'Alpa') !== 0) {
                                                                // Nolkan semua komponen telat & penalti
                                                                $telat_in = 0;
                                                                $telat_out = 0;
                                                                $pen_in = 0;
                                                                $pen_out = 0;
                                                        
                                                                // Tampilkan status sesuai ACC (timpa status sebelumnya agar jelas)
                                                                $status = $accPegawai;
                                                        
                                                                // Opsional: jika kamu ingin tetap menampilkan IN/OUT apa adanya,
                                                                // biarkan $absen_in / $absen_out tidak diubah.
                                                                // Jika ingin "mengunci" tampilan juga, kamu bisa set $absen_out='-' dsb.
                                                            }
                                                        }
                                    
                                                        $totalHari = (int)$telat_in + (int)$telat_out + (int)$pen_in + (int)$pen_out;
                                    
                                                        // Akumulasi footer
                                                        $totTelatIn  += (int)$telat_in + (int)$pen_in;
                                                        $totTelatOut += (int)$telat_out + (int)$pen_out;
                                                        $totTotal    += (int)$totalHari;
                                    
                                                        $tglLabel = konversitanggal($hariYmd) . " (" . hariindo($hariYmd) . ")";
                                                        ?>
                                                        <tr>
                                                          <td><?php echo htmlspecialchars($tglLabel); ?></td>
                                                          <td><?php echo htmlspecialchars($namaPeg); ?></td>
                                                          <td><?php echo "<button type='button' class='btn btn-xs bg-".htmlspecialchars($badgeColor)."'>".htmlspecialchars($namaShift)."</button>"; ?></td>
                                                          <td><?php echo htmlspecialchars($absen_in); ?></td>
                                                          <td><?php echo htmlspecialchars($absen_out); ?></td>
                                                          <td><?php echo (int)($telat_in + $pen_in); ?></td>
                                                          <td><?php echo (int)($telat_out + $pen_out); ?></td>
                                                          <td><?php echo (int)$totalHari; ?></td>
                                                          <td><?php echo htmlspecialchars($status); ?></td>
                                                        </tr>
                                                        <?php
                                                        $cur = $cur->modify('+1 day');
                                                    } // end while
                                                    ?>
                                                  </tbody>
                                                  <thead>
                                                    <tr>
                                                      <th colspan="5">TOTAL</th>
                                                      <th style="background:#ffe0e0"><?php echo (int)$totTelatIn; ?> Menit</th>
                                                      <th style="background:#e0f0ff"><?php echo (int)$totTelatOut; ?> Menit</th>
                                                      <th style="background:#e48080"><?php echo (int)$totTotal; ?> Menit</th>
                                                      <th></th>
                                                    </tr>
                                                  </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1' or $idlevel == 'LVL-000110') {
                            $read = "";
                        } else {
                            $read = "readonly";
                        }
                        $tmtcek = $monthEnd;
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto,tm_pegawai.id_user from tm_pegawai where tm_pegawai.id_user='$id' "));

                        $hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id' and tahun='$tahun'");
                        $hct_tambah = getOne("Select IFNULL(sum(sisa_cuti_tambah),'0') as sisa_cuti_tambah from tm_validasi_cuti_tambah where id_user='$id' and tahun='$tahun'"); 
                        $cuti_diambil = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id' and tm_cuti.tahun_cuti = '$tahun' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T' and tm_cuti.jenis_cuti = 'CUTI TAHUNAN'");
                        $potong_cuti = getOne("SELECT sum(potong_cuti) FROM `tm_waktu_k` WHERE id_user = '$id' and year(date_k) = '$tahun'");

                        $cek_cuti_melahirkan = getOne("select STR_TO_DATE(LEFT(tm_cuti.periode_cuti,10),'%m/%d/%Y') as mulai, STR_TO_DATE(RIGHT(tm_cuti.periode_cuti,10),'%m/%d/%Y') as akhir from tm_cuti where tm_cuti.id_user='$id' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                        $awal_melahirkan = getOne("select MONTH(STR_TO_DATE(LEFT(tm_cuti.periode_cuti,10),'%m/%d/%Y')) as mulai from tm_cuti where tm_cuti.id_user='$id' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                        $akhir_melahirkan = getOne("select MONTH(STR_TO_DATE(RIGHT(tm_cuti.periode_cuti,10),'%m/%d/%Y')) as akhir from tm_cuti where tm_cuti.id_user='$id' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                        

                        $cutilahiran = getOne("SELECT tm_cuti.jumlah_hari from tm_cuti where tm_cuti.id_user = '$id' and tm_cuti.tahun_cuti = '$tahun' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T' and tm_cuti.jenis_cuti = 'CUTI MELAHIRKAN'");
                        

                        $cuti_lahiran1 = getOne("SELECT
                                    IFNULL( sum( id_absensi IN ( 'ABS-000032' )), 0 ) AS cuti
                                FROM
                                    tm_jadwal
                                    INNER JOIN tm_pegawai on tm_pegawai.nip = tm_jadwal.nip_pegawai
                                    INNER JOIN tm_cuti ON tm_cuti.id_user = tm_pegawai.id_user AND tm_cuti.tahun_cuti = '$tahun' AND tm_cuti.jenis_cuti = 'CUTI MELAHIRKAN' AND tm_cuti.id_user = '$id'
                                WHERE
                                    nip_pegawai = " . $pegawai['nip'] . "
                                    AND MONTH ( tm_jadwal.tanggal_jadwal )= '$bulan'
                                    AND YEAR ( tm_jadwal.tanggal_jadwal )= '$tahun'
                                    AND MONTH ( tm_jadwal.tanggal_jadwal ) BETWEEN '$awal_melahirkan' AND '$akhir_melahirkan'");

                        $cek_cuti_tahunan1 = getOne("SELECT COUNT( tm_hari_cuti.id_cuti ) AS jumlah_hari FROM    tm_hari_cuti  INNER JOIN tm_cuti ON tm_hari_cuti.id_cuti = tm_cuti.id_cuti INNER JOIN tm_pegawai ON tm_cuti.id_user = tm_pegawai.id_user WHERE tm_cuti.id_user = '$id' and MONTH(tm_hari_cuti.tanggal) = '$bulan' and YEAR(tm_hari_cuti.tanggal) = '$tahun' and tm_cuti.jenis_cuti = 'CUTI TAHUNAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'"); 
                        $sakitizin = fetch_array(bukaquery("SELECT id_user,nip_pegawai, IFNULL(sum(id_absensi IN ('ABS-000030')),0) as sakit, IFNULL(sum(id_absensi IN ('ABS-000031')),0) as izin, IFNULL(sum(id_absensi IN ('ABS-000032')),0) as cuti FROM tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));
                        
                        $jumlah_sisa_cuti = ($hct-$cutilahiran-$cuti_diambil-$potong_cuti);
                        if ($jumlah_sisa_cuti == '') {
                            $sisa_cuti = $jumlah_sisa_cuti+$hct_tambah;
                        } else {
                            if ($jumlah_sisa_cuti < 0) {
                                $sisa_cuti = 0+$hct_tambah;
                            } else {
                                $sisa_cuti = $jumlah_sisa_cuti+$hct_tambah;
                            }
                        }
                        if ($cek_cuti_tahunan1 == ''){
                            $cek_cuti_tahunan = '0';
                        }
                        else {
                            $cek_cuti_tahunan = $cek_cuti_tahunan1;
                        }

                        if ($cuti_lahiran1 == ''){
                            $cuti_lahiran = '0';
                        }
                        else {
                            $cuti_lahiran = $cuti_lahiran1;
                        }


                        if (getOne("select id_waktu_k from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan' and year(tm_waktu_k.date_k)='$tahun' and tm_waktu_k.id_user='$id'") == '') {
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-pengurangan&id=$id&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-pengurangan&id=$id&bulan=$bulan&tahun=$tahun");
                            }
                        ?>

                        
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Note : PJ unit mengisi Alpha, Sakit, Izin sesuai absensi.</label>
                                <br>
                                <label>*Potong Cuti dari izin/sakit divalidasi oleh kepegawaian.</label>
                                
                                <!-- <label style="color: red">**Potong Cuti maksimal hanya 2 hari per bulan</label> -->
                                <br>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Alpha</label>
                                        <input type="number" class="form-control" name="alpha" maxlength="2" placeholder="Hari" value="0"  required>
                                        <label>Sakit < 2 Hari</label> <input type="number" class="form-control" name="sakit1" maxlength="2" placeholder="Hari" value="<?php echo $sakitizin['sakit']; ?>" required>
                                                <label>Sakit > 2 Hari</label>
                                                <input type="number" class="form-control" name="sakit2" maxlength="2" placeholder="Hari" value="0" required>
                                                <label>Izin</label>
                                                <input type="number" class="form-control" name="izin" maxlength="2" placeholder="Hari" value="<?php echo $sakitizin['izin']; ?>" required>
                                                <label>Telat</label>
                                                <input type="number" class="form-control" name="telat" value="<?php echo (int)$totTelatIn ?>" maxlength="3" placeholder="0" <?php echo $read; ?> required>
                                                <label>Pulang Cepat</label>
                                                <input type="number" class="form-control" name="pulang_cepat" value="<?php echo (int)$totTelatOut ?>" maxlength="3" placeholder="0" <?php echo $read; ?> required>
                                                <label>Cuti Sakit</label>
                                                <input type="number" class="form-control" name="ct_sakit_k" maxlength="2" placeholder="Hari" value="0" <?php echo $read; ?> required>
                                                <label>Cuti </label>
                                                <input type="number" class="form-control" name="ct_alasan_k" maxlength="2" placeholder="Hari" value="<?php echo $cek_cuti_tahunan; ?>" <?php echo $read; ?> required>
                                                <label>Cuti Persalinan</label>
                                                <input type="number" class="form-control" name="ct_persalinan_k" maxlength="2" placeholder="Hari" value="<?php echo $cuti_lahiran; ?>" <?php echo $read; ?> required>
                                                <label>Izin Setengah Hari</label>
                                                <input type="number" class="form-control" name="izin_setengah_hari" maxlength="2" placeholder="Hari" value="0" <?php echo $read; ?> required>
                                                <!-- <label>Meninggal</label> -->
                                                <input type="number" class="hidden" name="meninggal" maxlength="2" value="0" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Izin</label> -->
                                                <input type="number" class="hidden" name="r_izin" maxlength="2" value="<?php echo $sakitizin['izin']; ?>" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Sakit</label> -->
                                                <input type="number" class="hidden" name="r_sakit" maxlength="2" value="<?php echo $sakitizin['sakit']; ?>" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Sakit 2</label> -->
                                                <input type="number" class="hidden" name="r_sakit2" maxlength="2" value="0" placeholder="0" <?php echo $read; ?> required>
                                                <label>Potong Cuti dari Sakit dan Izin</label>
                                                <input type="number" class="form-control" name="potong_cuti" maxlength="2" value="0" placeholder="0" <?php echo $read; ?> required>
                                                <label>Sisa Cuti</label>
                                                <input type="number" class="form-control" name="sisa_cuti" maxlength="2" value="<?php echo $sisa_cuti; ?>" placeholder="0" <?php echo $read; ?> disabled>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        <?php
                        } else {
                            $waktu_k = fetch_array(bukaquery("select * from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan' and year(tm_waktu_k.date_k)='$tahun' and tm_waktu_k.id_user='$id'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-pengurangan&id=" . $waktu_k['id_waktu_k'] . "&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-pengurangan&id=" . $waktu_k['id_waktu_k'] . "&bulan=$bulan&tahun=$tahun");
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <br>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Alpha</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_k['alpha']; ?>" maxlength="2" name="alpha" placeholder="Hari" <?php echo $read; ?> required>
                                        <label>Sakit < 2 Hari</label> <input type="number" class="form-control" value="<?php echo $waktu_k['sakit1']; ?>" maxlength="2" name="sakit1" placeholder="Hari" required>
                                                <label>Sakit > 2 Hari</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['sakit2']; ?>" maxlength="2" name="sakit2" placeholder="Hari" required>
                                                <label>Izin</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['izin']; ?>" maxlength="2" name="izin" placeholder="Hari" required>
                                                <label>Telat</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['telat']; ?>" maxlength="3" name="telat" placeholder="0" <?php echo $read; ?> required>
                                                <label>Pulang Cepat</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['pulang_cepat']; ?>" maxlength="3" name="pulang_cepat" placeholder="Menit" <?php echo $read; ?> required>
                                                <label>Cuti Sakit</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_sakit_k']; ?>" maxlength="2" name="ct_sakit_k" placeholder="Hari" required>
                                                <label>Cuti Tahunan</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_alasan_k']; ?>" maxlength="2" name="ct_alasan_k" placeholder="Hari" required>
                                                <label>Cuti Persalinan</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_persalinan_k']; ?>" maxlength="2" name="ct_persalinan_k" placeholder="Hari" required>
                                                <label>Izin Setengah hari</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['izin_setengah_hari']; ?>" maxlength="2" name="izin_setengah_hari" placeholder="Hari" required>
                                                <!-- <label>Meninggal</label> -->
                                                <input type="number" class="hidden" value="<?php echo $waktu_k['meninggal']; ?>" maxlength="2" name="meninggal" placeholder="Hari" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Izin</label> -->
                                                <input type="number" class="hidden" name="r_izin" maxlength="2" value="<?php echo $waktu_k['r_izin']; ?>" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Sakit</label> -->
                                                <input type="number" class="hidden" name="r_sakit" maxlength="2" value="<?php echo $waktu_k['r_sakit']; ?>" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <label>Rekap Sakit 2</label> -->
                                                <input type="number" class="hidden" name="r_sakit2" maxlength="2" value="0" placeholder="0" <?php echo $read; ?> required>
                                                <label>Potong Cuti dari Sakit dan Izin</label>
                                                <input type="number" class="form-control" name="potong_cuti" maxlength="2" value="<?php echo $waktu_k['potong_cuti']; ?>" placeholder="0" <?php echo $read; ?> required>
                                                <label>Sisa Cuti</label>
                                                <input type="number" class="form-control" name="sisa_cuti" maxlength="2" value="<?php echo $sisa_cuti; ?>" placeholder="0" <?php echo $read; ?> disabled>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>

                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;


        case "validasi-waktu-pengurangan-pjlp":
?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI PENGURANGAN WAKTU PJLP</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="col-md-8">
                        <div class="panel-group" id="dropdown">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-calendar-check-o">
                                            <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                                REPORT ABSENSI</a>
                                        </h4>
                                    </div>
                                    <?php
                                    $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                                    $date1 = date('00-m-Y');
                                    $date2 = date('t-m-Y');
                                    $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                                    $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                    $tmtcek = FormatTgl('Y-m-d', $tahun_post . "-" . $bulan_post . "-01");
                                    $froma = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                    if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh' or $bulan_post == '' or $tahun_post == '') {
                                        $bulan_absensi = TanggalAkhirBulanKemarin();
                                    } else {
                                        $bulan_absensi = $tmtcek;
                                    }
                                ?>
                                    <div id="collapse2" class="panel-collapse collapse">
                                        <label align="center">Laporan Absensi <?php echo konversiBulanTahun($bulan_absensi) . " "; ?></label>
                                        <div class="panel-body">
                                            <div class="box-body table-responsive">
                                                <table id="example2" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Nama Pegawai</th>
                                                            <th>Jadwal Shift</th>
                                                            <th>IN</th>
                                                            <th>OUT</th>
                                                            <th>Telat</th>
                                                            <!--<th>Pulang Cepat</th>-->
                                                             <th>Keterangan</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                    $biodata = fetch_array(bukaquery("select log_finger,nama_pegawai,id_unit from tm_pegawai where nip='$pegawai[nip]'"));
                                    $from = FormatTgl('00-m-Y', $bulan_absensi);
                                    $to = FormatTgl('t-m-Y', $bulan_absensi);
                                    while (strtotime($from) < strtotime($to)) {
                                        $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                        $from = date("Y-m-d ", $from);
                                        $hari_absen = hariindo ($from);
                                        $tanggalan = konversitanggal($from);
                                        $tanggalmin = date('Y-m-d', strtotime("-1 day", strtotime($from)));
                                        $tanggalplus = date('Y-m-d', strtotime("+1 day", strtotime($from)));
                                        $mapping = fetch_array(bukaquery("SELECT
                                                tm_shift.jam_masuk,
                                                tm_shift.jam_pulang,
                                                tm_shift.nama_shift,
                                                tm_shift.color,
                                                tm_jadwal.acc_kepegawaian,
                                                case 
                                                when tm_shift.color='A' then 'red-gradient'
                                                when tm_shift.color='B' then 'aqua-gradient'
                                                when tm_shift.color='C' then 'blue'
                                                when tm_shift.color='D' then 'red'
                                                when tm_shift.color='E' then 'green' 
                                                when tm_shift.color='F' then 'pink' 
                                                when tm_shift.color='G' then 'purple' 
                                                when tm_shift.color='H' then 'orange'
                                                when tm_shift.color='I' then 'Navy'
                                                when tm_shift.color='J' then 'teal'
                                                when tm_shift.color='K' then 'gray'
                                                when tm_shift.color='L' then 'purple-gradient'
                                                when tm_shift.color='M' then 'blue-gradient'
                                                when tm_shift.color='N' then 'black'
                                                else 'white' end as color 
                                                from tm_jadwal
                                                INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                                                where tm_jadwal.nip_pegawai='$pegawai[nip]' and tanggal_jadwal='$from'"));
                                                
                                            $in = getOne("SELECT log.tanggal FROM log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' ORDER BY log.tanggal asc limit 1 ");
                                                                            
                                            $out = getOne("SELECT log.tanggal FROM log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' ORDER BY log.tanggal desc limit 1 ");

                                            $t_date = '2025-02-01';                                     
                                            $curdate = date('Y-m-d');
                                            $t_in = '0';
                                            $t_out = '0';

                                            #non shift
                                            // if ($mapping['kode_shift'] == 'NS') {
                                            //     $in = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) < 9 ORDER BY log.tanggal asc ");
                                            //     $out = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 12 ORDER BY log.tanggal asc ");
                                
                                            //     if ($in != '') {
                                            //         $absen_in = FormatTgl('H:i:s', $in);
                                            //         $status = 'NS';
                                            //         $jam_masuk = $mapping['jam_masuk'];
                                            //     } else {
                                            //         $absen_in = '-';
                                            //         $status = 'NS';
                                            //         $jam_masuk = '';
                                            //     }
                                
                                            //     if ($out != '') {
                                            //         $absen_out = FormatTgl('H:i:s', $out);
                                            //         $jam_pulang = $mapping['jam_pulang'];
                                            //     } else {
                                            //         $absen_out = '-';
                                            //         $jam_pulang = '';
                                            //     }
                                            // }
                                
                                            // #pagi
                                            // else if ($mapping['kode_shift'] == 'P') {
                                            //     $in = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) < 9 ORDER BY log.tanggal asc ");
                                            //     $out = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 12 ORDER BY log.tanggal asc ");
                                
                                            //     if ($in != '') {
                                            //         $absen_in = FormatTgl('H:i:s', $in);
                                            //         $status = 'P';
                                            //         $jam_masuk = $mapping['jam_masuk'];
                                            //     } else {
                                            //         $absen_in = '-';
                                            //         $status = 'P';
                                            //         $jam_masuk = '';
                                            //     }
                                
                                            //     if ($out != '') {
                                            //         $absen_out = FormatTgl('H:i:s', $out);
                                            //         $jam_pulang = $mapping['jam_pulang'];
                                            //     } else {
                                            //         $absen_out = '-';
                                            //         $jam_pulang = '';
                                            //     }
                                            // }
                                
                                            // else if ($mapping['kode_shift'] == 'PS') {
                                            //     $in = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) < 9 ORDER BY log.tanggal asc ");
                                            //     $out = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 17 ORDER BY log.tanggal asc ");
                                
                                            //     if ($in != '') {
                                            //         $absen_in = FormatTgl('H:i:s', $in);
                                            //         $status = 'PS';
                                            //         $jam_masuk = $mapping['jam_masuk'];
                                            //     } else {
                                            //         $absen_in = '-';
                                            //         $status = 'PS';
                                            //         $jam_masuk = '';
                                            //     }
                                
                                            //     if ($out != '') {
                                            //         $absen_out = FormatTgl('H:i:s', $out);
                                            //         $jam_pulang = $mapping['jam_pulang'];
                                            //     } else {
                                            //         $absen_out = '-';
                                            //         $jam_pulang = '';
                                            //     }
                                            // }
                                
                                            // #shift middle - siang
                                            // else if ($mapping['kode_shift'] == 'S') {
                                            //     $in = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) < 17 ORDER BY log.tanggal asc ");
                                            //     $out = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 17 ORDER BY log.tanggal asc ");
                                
                                            //     if ($in != '') {
                                            //         $absen_in = FormatTgl('H:i:s', $in);
                                            //         $status = 'S';
                                            //         $jam_masuk = $mapping['jam_masuk'];
                                            //     } else {
                                            //         $absen_in = '-';
                                            //         $status = 'S';
                                            //         $jam_masuk = '';
                                            //     }
                                
                                            //     if ($out != '') {
                                            //         $absen_out = FormatTgl('H:i:s', $out);
                                            //         $jam_pulang = $mapping['jam_pulang'];
                                            //     } else {
                                            //         $absen_out = '-';
                                            //         $jam_pulang = '';
                                            //     }
                                            // } 
                                
                                            // else if ($mapping['kode_shift'] == 'MID') {
                                            //     $in = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) < 12 ORDER BY log.tanggal asc ");
                                            //     $out = getOne("SELECT
                                            //             log.tanggal
                                            //             FROM
                                            //             log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                            //             where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 17 ORDER BY log.tanggal asc ");
                                
                                            //     if ($in != '') {
                                            //         $absen_in = FormatTgl('H:i:s', $in);
                                            //         $status = 'MID';
                                            //         $jam_masuk = $mapping['jam_masuk'];
                                            //     } else {
                                            //         $absen_in = '-';
                                            //         $status = 'MID';
                                            //         $jam_masuk = '';
                                            //     }
                                
                                            //     if ($out != '') {
                                            //         $absen_out = FormatTgl('H:i:s', $out);
                                            //         $jam_pulang = $mapping['jam_pulang'];
                                            //     } else {
                                            //         $absen_out = '-';
                                            //         $jam_pulang = '';
                                            //     }
                                            // } 
                                
                                            #shift malam 
                                            if ($mapping['kode_shift'] == 'M') {
                                                $in1 = getOne("SELECT
                                                                        log.tanggal
                                                                        FROM
                                                                        log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                                        where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 18 ORDER BY log.tanggal asc ");
                                                if ($in1 != '') {
                                                    $absen_in = FormatTgl('H:i:s', $in1);
                                                    $status = 'M';
                                                    $jam_masuk = $mapping['jam_masuk'];
                                                } else {
                                                    $absen_in = '-';
                                                    $status = 'M';
                                                    $jam_masuk = '';
                                                }
                                                $out1 = getOne("SELECT
                                                                                    RIGHT(MIN(log.tanggal),8) as tgl
                                                                                FROM
                                                                                    log
                                                                                    INNER JOIN tm_pegawai ON tm_pegawai.log_finger = log.`user` 
                                                                                WHERE
                                                                                    tm_pegawai.log_finger = '$biodata[log_finger]'
                                                                                    AND log.tanggal LIKE '%$tanggalplus%'
                                                                                    and RIGHT(log.tanggal,8) < 12
                                                                                	-- AND log.STATUS = 'O' 
                                                                                GROUP BY
                                                                                    LEFT ( log.tanggal, 10 )");
                                                if ($out1 != '') {
                                                    $absen_out = FormatTgl('H:i:s', $out1);
                                                    $jam_pulang = $mapping['jam_pulang'];
                                                } else {
                                                    $absen_out = '-';
                                                    $jam_pulang = '';
                                                }
                                            } else if ($mapping['kode_shift'] == 'SM') {
                                                $in1 = getOne("SELECT
                                                                        log.tanggal
                                                                        FROM
                                                                        log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                                        where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and RIGHT(log.tanggal,8) > 12 ORDER BY log.tanggal asc ");
                                                if ($in1 != '') {
                                                    $absen_in = FormatTgl('H:i:s', $in1);
                                                    $status = 'SM';
                                                    $jam_masuk = $mapping['jam_masuk'];
                                                } else {
                                                    $absen_in = '-';
                                                    $status = 'SM';
                                                    $jam_masuk = '';
                                                }
                                                $out1 = getOne("SELECT
                                                                                    RIGHT(MIN(log.tanggal),8) as tgl
                                                                                FROM
                                                                                    log
                                                                                    INNER JOIN tm_pegawai ON tm_pegawai.log_finger = log.`user` 
                                                                                WHERE
                                                                                    tm_pegawai.log_finger = '$biodata[log_finger]'
                                                                                    AND log.tanggal LIKE '%$tanggalplus%'
                                                                                    and RIGHT(log.tanggal,8) < 12
                                                                                	-- AND log.STATUS = 'O' 
                                                                                GROUP BY
                                                                                    LEFT ( log.tanggal, 10 )");
                                                if ($out1 != '') {
                                                    $absen_out = FormatTgl('H:i:s', $out1);
                                                    $jam_pulang = $mapping['jam_pulang'];
                                                } else {
                                                    $absen_out = '-';
                                                    $jam_pulang = '';
                                                }
                                            } else if ($mapping['kode_shift'] == 'OFF') {
                                                $absen_in = '-';
                                                $absen_out = '-';
                                                $jam_masuk = '';
                                                $jam_pulang = '';
                                                $status = 'OFF';
                                            } else if ($mapping['kode_shift'] == 'DL') {
                                                $absen_in = '-';
                                                $absen_out = '-';
                                                $jam_masuk = '';
                                                $jam_pulang = '';
                                                $status = 'DL';
                                            } else if ($mapping['kode_shift'] == 'C') {
                                                $absen_in = '-';
                                                $absen_out = '-';
                                                $jam_masuk = '';
                                                $jam_pulang = '';
                                                $status = 'C';
                                            } else if ($mapping['kode_shift'] == 'Skt') {
                                                $absen_in = '-';
                                                $absen_out = '-';
                                                $jam_masuk = '';
                                                $jam_pulang = '';
                                                $status = 'Skt';
                                            } else if ($mapping['kode_shift'] == 'I') {
                                                $absen_in = '-';
                                                $absen_out = '-';
                                                $jam_masuk = '';
                                                $jam_pulang = '';
                                                $status = 'I';
                                            } else {
                                                if ($in != '') {
                                                    $absen_in = FormatTgl('H:i:s', $in);
                                                    $status = '-';
                                                    $jam_masuk = $mapping['jam_masuk'];
                                                } else {
                                                    $absen_in = '-';
                                                    $status = '-';
                                                    $jam_masuk = '';
                                                    if ($mapping['acc_kepegawaian'] == '-' && $from > $t_date && $from <= $curdate) {
                                                        $t_in = '225';
                                                    }
                                                }

                                                if ($out != '') {
                                                    $absen_out = FormatTgl('H:i:s', $out);
                                                    $jam_pulang = $mapping['jam_pulang'];
                                                } else {
                                                    $absen_out = '-';
                                                    $jam_pulang = '';
                                                    $status = '-';
                                                    if ($mapping['acc_kepegawaian'] == '-' && $from > $t_date && $from <= $curdate) {
                                                        $t_out = '225';
                                                    }
                                                }
                                            }

                                            //hitung telat jam masuk
                                            if ($absen_in >= $jam_masuk) {
                                                $jam_finger = (is_string($absen_in) ? strtotime($absen_in) : $absen_in);
                                                $jam_in = (is_string($jam_masuk) ? strtotime($jam_masuk) : $jam_masuk);
                                                $hitung = $jam_finger - $jam_in;
                                                $jam = floor($hitung / (60 * 60));
                                                $telat_in = floor(($hitung) / 60);
                                            } else {
                                                $telat_in = '0';
                                            }

                                            if ($statusPegawai['status_pegawai'] == 'PNS' or $statusPegawai['status_pegawai'] == 'SPESIALIS') {
                                                $telat_in = '0';
                                                $status = '-';
                                            } else {
                                                                if ($mapping['acc_kepegawaian'] == '-' && $from > $t_date && $from <= $curdate) {
                                                                    if ($telat_in > 225) {
                                                                    $telat_in = '225';
                                                                    $status = 'Cek Jadwal atau Log Finger';
                                                                    }
                                                                }
                                                                else{
                                                                    $telat_in ='0';
                                                                }
                                                            }

                                            //hitung pulang cepet
                                            // if ($absen_out <= $jam_pulang) {
                                            //     $jam_finger = (is_string($absen_out) ? strtotime($absen_out) : $absen_out);
                                            //     $jam_out = (is_string($jam_pulang) ? strtotime($jam_pulang) : $jam_pulang);
                                            //     $hitung = $jam_out - $jam_finger;
                                            //     $jam = floor($hitung / (60 * 60));
                                            //     $telat_out = floor(($hitung - $jam * (60 * 60)) / 60);
                                            // } else {
                                            // $telat_out = '-';
                                            // }
                                
                                            $telat = $telat_in + $telat_out + $t_in + $t_out;
                                            // if ($telat == '0' or $telat > '5') {
                                
                                            // } else {
                                            //     $telat = $telat_in;
                                            // }
                                
                                            $totelat[] = $telat;
                                            $totelatin[] = $telat_in;
                                            $totelatout[] = $telat_out;
                                            ?>
                                            <tr>
                                                <td><?php echo $tanggalan . " (" . hariindo($from) . ")"; ?></td>
                                                <td><?php echo $biodata['nama_pegawai']; ?></td>
                                                <td><?php echo "<button type='button' class='btn btn-xs bg-" . $mapping['color'] . "' >"
                                                    . $mapping['nama_shift'] . "</button>"; ?></td>
                                                <td><?php echo $absen_in; ?></td>
                                                <td><?php echo $absen_out; ?></td>
                                                <td><?php echo $telat; ?></td>
                                                <!--<td><?php echo $telat_out; ?></td>-->
                                                <td><?php echo $mapping['acc_kepegawaian']; ?></td>

                                    <?php
                                }
                                    ?>
                            </tbody>
                            <thead>
                                <tr>
                                    <th colspan="5">TOTAL</th>
                                    <th colspan="1" style="background-color: #e48080;">
                                        <?php
                                        echo array_sum($totelat) . " Menit";
                                        ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                            $read = "";
                        } else {
                            $read = "readonly";
                        }
                        $tmtcek = TanggalAkhirBulanKemarin();
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        //                        require_once ('conf/conf_absensi.php');                       
                        //                        $hitung_telat = HitungTelat($pegawai['nip'], $pegawai['id_unit']);
                        $bulan = FormatTgl('m', TanggalAkhirBulanKemarin());
                        $tahun = FormatTgl('Y', TanggalAkhirBulanKemarin());
                        $cek_cuti_melahirkan = getOne("select tm_cuti.id_cuti from tm_cuti where tm_cuti.id_user='$id' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                        $sakitizin = fetch_array(bukaquery("SELECT id_user,nip_pegawai, sum(id_absensi IN ('ABS-000030')) as sakit, sum(id_absensi IN ('ABS-000031')) as izin, sum(id_absensi IN ('ABS-000032')) as cuti FROM tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and month(tm_jadwal.tanggal_jadwal)='$bln_sebelumnya' and year(tm_jadwal.tanggal_jadwal)='$thn'"));


                        if (getOne("select id_waktu_k from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan' and year(tm_waktu_k.date_k)='$tahun' and tm_waktu_k.id_user='$id'") == '') {
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-waktu-pengurangan-pjlp&id=' . $id . '&link=kepegawaian');
                            } else {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-waktu-pengurangan-pjlp&id=' . $id . '');
                            }
                        ?>

                        
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Note : PJ unit mengisi Alpha, Sakit, Izin sesuai absensi.</label>
                                <br>
                                <label>*Potong Cuti dari izin/sakit divalidasi oleh kepegawaian.</label>
                                <!-- <label style="color: red">**Potong Cuti maksimal hanya 2 hari per bulan</label> -->
                                <br>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Alpha</label>
                                        <input type="number" class="form-control" name="alpha" maxlength="2" placeholder="Hari" value="0"  required>
                                        <label>Sakit < 2 Hari</label> <input type="number" class="form-control" name="sakit1" maxlength="2" placeholder="Hari" value="<?php echo $sakitizin['sakit']; ?>" required>
                                                <label>Sakit > 2 Hari</label>
                                                <input type="number" class="form-control" name="sakit2" maxlength="2" placeholder="Hari" value="0" required>
                                                <label>Izin</label>
                                                <input type="number" class="form-control" name="izin" maxlength="2" placeholder="Hari" value="<?php echo $sakitizin['izin']; ?>" required>
                                                <label>Telat</label>
                                                <input type="number" class="form-control" name="telat" value="<?php echo array_sum($totelat) ?>" maxlength="3" placeholder="0" <?php echo $read; ?> required>
                                                <label>Pulang Cepat</label>
                                                <input type="number" class="form-control" name="pulang_cepat" value="0" maxlength="3" placeholder="0" <?php echo $read; ?> required>
                                                <!-- <?php echo array_sum($totelatout) ?> -->
                                                <label>Cuti Sakit</label>
                                                <input type="number" class="form-control" name="ct_sakit_k" maxlength="2" placeholder="Hari" value="0" <?php echo $read; ?> required>
                                                <label>Cuti</label>
                                                <input type="number" class="form-control" name="ct_alasan_k" maxlength="2" placeholder="Hari" value="<?php echo $sakitizin['cuti']; ?>" <?php echo $read; ?> required>
                                                <label>Cuti Persalinan</label>
                                                <input type="number" class="form-control" name="ct_persalinan_k" maxlength="2" placeholder="Hari" value="<?php echo getOne("SELECT count(tanggal) from tm_hari_cuti where month(tanggal) = $bln_sebelumnya and id_cuti='$cek_cuti_melahirkan' order by tanggal asc") ?>" <?php echo $read; ?> required>
                                                <label>Izin Setengah Hari</label>
                                                <input type="number" class="form-control" name="izin_setengah_hari" maxlength="2" placeholder="Hari" value="0" <?php echo $read; ?> required>
                                                <!-- <label>Meninggal</label> -->
                                                <input type="number" class="hidden" name="meninggal" maxlength="2" value="0" placeholder="0" <?php echo $read; ?> required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai-pjlp'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai-pjlp'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        <?php
                        } else {
                            $waktu_k = fetch_array(bukaquery("select * from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan' and year(tm_waktu_k.date_k)='$tahun' and tm_waktu_k.id_user='$id'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-waktu-pengurangan-pjlp&id=' . $waktu_k['id_waktu_k'] . '&link=kepegawaian');
                            } else {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-waktu-pengurangan-pjlp&id=' . $waktu_k['id_waktu_k'] . '');
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Note : PJ unit mengisi Alpha, Sakit, Izin sesuai absensi.</label>
                                <br>
                                <label>*Potong Cuti dari izin/sakit divalidasi oleh kepegawaian.</label><br>
                                <!-- <label style="color: red">**Potong Cuti maksimal hanya 2 hari per bulan</label> -->
                                <br>
                                
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai </label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Alpha</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_k['alpha']; ?>" maxlength="2" name="alpha" placeholder="Hari" <?php echo $read; ?> required>
                                        <label>Sakit < 2 Hari</label> <input type="number" class="form-control" value="<?php echo $waktu_k['sakit1']; ?>" maxlength="2" name="sakit1" placeholder="Hari" required>
                                                <label>Sakit > 2 Hari</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['sakit2']; ?>" maxlength="2" name="sakit2" placeholder="Hari" required>
                                                <label>Izin</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['izin']; ?>" maxlength="2" name="izin" placeholder="Hari" required>
                                                <label>Telat</label>
                                                <!-- <input type="number" class="form-control" value="<?php echo array_sum($totelat); ?>" maxlength="3" name="telat" placeholder="Menit" <?php echo $read; ?> required> -->
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['telat']; ?>" maxlength="3" name="telat" placeholder="0"  required>
                                                <label>Pulang Cepat</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['pulang_cepat']; ?>" maxlength="3" name="pulang_cepat" placeholder="Menit" <?php echo $read; ?> required>
                                                <label>Cuti Sakit</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_sakit_k']; ?>" maxlength="2" name="ct_sakit_k" placeholder="Hari" required>
                                                <label>Cuti</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_alasan_k']; ?>" maxlength="2" name="ct_alasan_k" placeholder="Hari" required>
                                                <label>Cuti Persalinan</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['ct_persalinan_k']; ?>" maxlength="2" name="ct_persalinan_k" placeholder="Hari" required>
                                                <label>Izin Setengah hari</label>
                                                <input type="number" class="form-control" value="<?php echo $waktu_k['izin_setengah_hari']; ?>" maxlength="2" name="izin_setengah_hari" placeholder="Hari" required>
                                                <!-- <label>Meninggal</label> -->
                                                <input type="number" class="hidden" value="<?php echo $waktu_k['meninggal']; ?>" maxlength="2" name="meninggal" placeholder="Hari" <?php echo $read; ?> required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai-pjlp'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai-pjlp'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>

                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <?php
                            if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                                if ($pegawai['jk'] == 'L') {
                                    $foto = 'img/laki.png';
                                } else {
                                    $foto = 'img/perempuan.png';
                                }
                            } else {
                                $foto = "img/" . $pegawai['foto'];
                            }
                            ?>
                            <br><br>
                            <img src="<?php echo $foto; ?>" width="500px" height="500px" class="img-squere">
                        </center>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "validasi-waktu-penambahan":
            
            if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
                $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
            } else {
                $bulan = date('m', strtotime('-1 month'));
            }
            
            if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
                $tahun = $tahun_post;
            } else {
               $tahun = date('Y');
            }
            
            $monthStart = date("Y-m-01", strtotime("$tahun-$bulan-01"));
            $monthEnd   = date("Y-m-t", strtotime($monthStart));
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI PENAMBAHAN WAKTU <?php echo strtoupper(konversiBulanTahun($monthStart)); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-penambahan&id=$id&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-penambahan&id=$id&bulan=$bulan&tahun=$tahun");
                            }
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_waktu_t = getOne("select id_waktu_t from tm_waktu_t where month(tm_waktu_t.date_t)='$bulan' and year(tm_waktu_t.date_t)='$tahun' and tm_waktu_t.id_user='$id'");

                        $diklat = fetch_array(bukaquery("SELECT id_user,nip_pegawai, IFNULL(sum(id_absensi IN ('ABS-000033')),0) as diklat, IFNULL(sum(id_absensi IN ('ABS-000028')),0) as surtug FROM tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));
                        $cuti = fetch_array(bukaquery("SELECT id_user,nip_pegawai, sum(id_absensi IN ('ABS-000030')) as sakit, sum(id_absensi IN ('ABS-000031')) as izin, IFNULL(sum(id_absensi IN ('ABS-000032')),0) as cuti FROM tm_jadwal where nip_pegawai=" . $pegawai['nip'] . " and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));
                        if ($id_waktu_t == '') {
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Note : Penambahan waktu digunakan untuk menambah jumlah hari kerja.</label>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Nama Pegawai</label>
                                            <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                            <label>Cuti Sakit</label>
                                            <input type="number" class="form-control" name="ct_sakit_t" maxlength="2" placeholder="Hari" value="<?php echo getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah_hari FROM tm_cuti inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user INNER JOIN tm_hari_cuti on tm_hari_cuti.id_cuti=tm_cuti.id_cuti where tm_pegawai.id_user='$id' and month(tm_hari_cuti.tanggal)='$bulan' and year(tm_hari_cuti.tanggal)='$tahun' and tm_cuti.jenis_cuti='CUTI SAKIT' AND acc_pengganti = 'Y' AND acc_pj = 'Y' AND acc_kepegawaian = 'Y' AND acc_kasie = 'Y'"); ?>" readonly required>
                                            <label>Cuti Alasan Penting</label>
                                            <input type="number" class="form-control" name="ct_alasan_t" maxlength="2" placeholder="Hari" value="<?php echo getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah_hari FROM tm_cuti inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user INNER JOIN tm_hari_cuti on tm_hari_cuti.id_cuti=tm_cuti.id_cuti where tm_pegawai.id_user='$id' and month(tm_hari_cuti.tanggal)='$bulan' and year(tm_hari_cuti.tanggal)='$tahun' and tm_cuti.jenis_cuti='CUTI KARENA ALASAN PENTING' AND acc_pengganti = 'Y' AND acc_pj = 'Y' AND acc_kepegawaian = 'Y' AND acc_kasie = 'Y'"); ?>"  readonly required>
                                            <label>Cuti Tahunan</label>
                                            <input type="number" class="form-control" name="ct_tahunan_t" value="<?php echo $cuti ['cuti']; ?>" maxlength="2" placeholder="Hari"  required>
                                            <label>Diklat</label>
                                            <input type="number" class="form-control" name="diklat" maxlength="2" placeholder="Hari" value="<?php echo $diklat['diklat']; ?>" required>
                                            <label>Surat Tugas/Perjalanan Dinas</label>
                                            <input type="number" class="form-control" name="spd" maxlength="2" placeholder="Hari" value="<?php echo $diklat['surtug']; ?>" required>
                                            <label>Haji</label>
                                            <input type="number" class="form-control" name="haji" maxlength="2" placeholder="Hari" value="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        <?php
                        } else {
                            $waktu_t = fetch_array(bukaquery("select * from tm_waktu_t where id_user=" . $id . " and id_waktu_t='$id_waktu_t'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-penambahan&id=" . $id_waktu_t . "&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-penambahan&id=" . $id_waktu_t . "&bulan=$bulan&tahun=$tahun");
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Note : Penambahan waktu digunakan untuk menambah jumlah hari kerja.</label>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Nama Pegawai</label>
                                            <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                            <label>Cuti Sakit</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['ct_sakit_t']; ?>" name="ct_sakit_t" maxlength="2" placeholder="Hari" <?php echo $read; ?> required>
                                            <label>Cuti Alasan Penting</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['ct_alasan_t']; ?>" name="ct_alasan_t" maxlength="2" placeholder="Hari" <?php echo $read; ?> required>
                                            <label>Cuti Tahunan</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['ct_tahunan_t']; ?>" name="ct_tahunan_t" maxlength="2" placeholder="Hari" <?php echo $read; ?> required>
                                            <label>Diklat</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['diklat']; ?>" name="diklat" maxlength="2" placeholder="Hari" required>
                                            <label>Surat Tugas/Perjalanan Dinas</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['spd']; ?>" name="spd" maxlength="2" placeholder="Hari" required>
                                            <label>Haji</label>
                                            <input type="number" class="form-control" value="<?php echo $waktu_t['haji']; ?>" name="haji" maxlength="2" placeholder="Hari" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <?php
                            if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                                if ($pegawai['jk'] == 'L') {
                                    $foto = 'img/laki.png';
                                } else {
                                    $foto = 'img/perempuan.png';
                                }
                            } else {
                                $foto = "img/" . $pegawai['foto'];
                            }
                            ?>
                            <br><br>
                            <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                        </center>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "validasi-waktu-shift":
            
            if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
                $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
            } else {
                $bulan = date('m', strtotime('-1 month'));
            }
            
            if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
                $tahun = $tahun_post;
            } else {
               $tahun = date('Y');
            }
            
            $monthStart = date("Y-m-01", strtotime("$tahun-$bulan-01"));
            $monthEnd   = date("Y-m-t", strtotime($monthStart));
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI WAKTU SHIFTING <?php echo strtoupper(konversiBulanTahun($monthStart)); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-shifting&id=$id&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=add-waktu-shifting&id=$id&bulan=$bulan&tahun=$tahun");
                            }
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_waktu_s = getOne("select id_waktu_s from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan' and year(tm_waktu_s.date_s)='$tahun' and tm_waktu_s.id_user='$id'");
                        $nip = $pegawai['nip'];
                        $kerjashift = fetch_array(bukaquery("SELECT id_user, nip_pegawai,
                        IFNULL(sum( kode_shift IN ('P','PS','NS','DL','MID')),0 ) AS kerjapagi,
                        IFNULL(sum( kode_shift IN ('S','PS','SM','MID','SPL1')),0 ) AS kerjasore,
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS kerjamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        WHERE nip_pegawai='$nip' and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));

                        $liburshift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','MID')),0 ) AS liburpagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM','MID','SPL1')),0 ) AS libursore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS liburmalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal                         
                        where tm_hari_libur.jenis IN('Libur Kerja','Libur Nasional','Cuti Bersama')and nip_pegawai='$nip' and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));

                        $rayashift = fetch_array(bukaquery("SELECT id_user,nip_pegawai, 
                        IFNULL(sum( kode_shift IN ('P','PS','MID')),0 ) AS rayapagi, 
                        IFNULL(sum( kode_shift IN ('S','PS','SM','MID','SPL1')),0 ) AS rayasore, 
                        IFNULL(sum( kode_shift IN ( 'SM', 'M')), 0 ) AS rayamalam 
                        FROM tm_jadwal 
                        INNER JOIN tm_shift on tm_shift.id_absensi = tm_jadwal.id_absensi
                        INNER JOIN tm_hari_libur on tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal 
                        where tm_hari_libur.jenis ='Libur Hari Raya' and nip_pegawai='$nip' and month(tm_jadwal.tanggal_jadwal)='$bulan' and year(tm_jadwal.tanggal_jadwal)='$tahun'"));
                        
                        


                        if ($id_waktu_s == '') {
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Hari Kerja : Senin - Sabtu</label><br>
                                <label>Hari Libur : Tanggal merah, cuti bersama, dan hari minggu</label><br>
                                <label>Hari Raya  : Suci Nyepi, Idul Fitri, Idul Adha, Waisak, Natal</label><br>
                                <label>Note : Data diambil dari jadwal pegawai, cek ulang sebelum simpan</label><br>
                                <!-- <label>Isolasi mandiri dimasukkan ke jumlah hari kerja pagi</label> -->
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai <?php echo ''; ?></label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Jumlah Hari Kerja Non Shift/Shift Pagi</label>
                                        <input type="number" class="form-control" name="j_ns" maxlength="2" value="<?php echo $kerjashift['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?>" placeholder="Hari Kerja (Senin-Sabtu)" required>
                                        <label>Jumlah Hari Kerja Sore</label>
                                        <input type="number" class="form-control" name="j_hks" maxlength="2" value="<?php echo $kerjashift['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?>" placeholder="Hari Kerja - (Hari Libur + Hari Raya)" required>
                                        <label>Jumlah Hari Kerja Malam</label>
                                        <input type="number" class="form-control" name="j_hkm" maxlength="2" value="<?php echo $kerjashift['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?>" placeholder="Hari Kerja - (Hari Libur + Hari Raya)" required>
                                        <label>Jumlah Hari Libur Pagi</label>
                                        <input type="number" class="form-control" name="j_hlp" maxlength="2" value="<?php echo $liburshift['liburpagi']; ?>" placeholder="Shift di Hari Libur (Minggu)" required>
                                        <label>Jumlah Hari Libur Sore</label>
                                        <input type="number" class="form-control" name="j_hls" maxlength="2" value="<?php echo $liburshift['libursore']; ?>" placeholder="Shift di Hari Libur (Minggu)" required>
                                        <label>Jumlah Hari Libur Malam</label>
                                        <input type="number" class="form-control" name="j_hlm" maxlength="2" value="<?php echo $liburshift['liburmalam']; ?>" placeholder="Shift di Hari Libur (Minggu)" required>
                                        <label>Jumlah Hari Raya Pagi</label>
                                        <input type="number" class="form-control" name="j_hrp" maxlength="2" value="<?php echo $rayashift['rayapagi']; ?>" placeholder="Shift di Hari Raya/Libur Nasional" required>
                                        <label>Jumlah Hari Raya Sore</label>
                                        <input type="number" class="form-control" name="j_hrs" maxlength="2" value="<?php echo $rayashift['rayasore']; ?>" placeholder="Shift di Hari Raya/Libur Nasional" required>
                                        <label>Jumlah Hari Raya Malam</label>
                                        <input type="number" class="form-control" name="j_hrm" maxlength="2" value="<?php echo $rayashift['rayamalam']; ?>" placeholder="Shift di Hari Raya/Libur Nasional" required>
                                        
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        <?php
                        } else {
                            $waktu_s = fetch_array(bukaquery("select * from tm_waktu_s where id_user=" . $id . " and id_waktu_s='$id_waktu_s'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-shifting&id=" . $id_waktu_t . "&bulan=$bulan&tahun=$tahun&link=kepegawaian");
                            } else {
                                $link = $aksi . paramEncrypt("module=validasi-pegawai&act=update-waktu-shifting&id=" . $id_waktu_t . "&bulan=$bulan&tahun=$tahun");
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <label>Hari Kerja : Senin - Sabtu</label><br>
                                <label>Hari Libur : Tanggal merah dan hari minggu</label><br>
                                <label>Hari Raya  : Suci Nyepi, Idul Fitri, Idul Adha, Waisak, Natal</label><br>
                                <label>Note : Data diambil dari jadwal pegawai, cek ulang sebelum simpan</label><br>
                                <!-- <label>Isolasi mandiri dimasukkan ke jumlah hari kerja pagi</label> -->
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Jumlah Hari Kerja Non Shift/Shift Pagi</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_ns']; ?>" name="j_ns" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Kerja Sore</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hks']; ?>" name="j_hks" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Kerja Malam</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hkm']; ?>" name="j_hkm" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Libur Pagi</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hlp']; ?>" name="j_hlp" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Libur Sore</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hls']; ?>" name="j_hls" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Libur Malam</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hlm']; ?>" name="j_hlm" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Raya Pagi</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hrp']; ?>" name="j_hrp" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Raya Sore</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hrs']; ?>" name="j_hrs" maxlength="2" placeholder="Hari" required>
                                        <label>Jumlah Hari Raya Malam</label>
                                        <input type="number" class="form-control" value="<?php echo $waktu_s['j_hrm']; ?>" name="j_hrm" maxlength="2" placeholder="Hari" required>
                                        
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <?php
                            if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                                if ($pegawai['jk'] == 'L') {
                                    $foto = 'img/laki.png';
                                } else {
                                    $foto = 'img/perempuan.png';
                                }
                            } else {
                                $foto = "img/" . $pegawai['foto'];
                            }
                            ?>
                            <br><br>
                            <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                        </center>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "validasi-potongan-pegawai":
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-potongan-pegawai&id=' . $id . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-potongan-pegawai&id=' . $id . '');
                        }
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_potongan_p = getOne("select id_potongan_p from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$id'");
                        if ($id_potongan_p == '') {
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Pengembalian Kurang Bayar</label>
                                        <input type="number" class="form-control" value="0" name="potongan_bank" maxlength="10" placeholder="Rp." required>
                                        <label class="control-label">Cicilan Kurang Bayar Ke</label>
                                        <?php echo enumDropdown("tm_potongan_p", "cicilan_bank", "", "-Pilih Cicilan Kurang Bayar Ke-"); ?>
                                        <label>Keterangan Pengembalian Kurang Bayar</label>
                                        <input type="text" class="form-control" value="-" name="keterangan_bank" maxlength="1000" placeholder="Keterangan" required>
                                        <label>Potongan Lebih Bayar</label>
                                        <input type="number" class="form-control" value="0" name="potongan_rs" maxlength="10" placeholder="Rp." required>
                                        <label class="control-label">Cicilan Lebih Bayar Ke</label>
                                        <?php echo enumDropdown("tm_potongan_p", "cicilan_rs", "", "-Pilih Cicilan Lebih Bayar Ke-"); ?>
                                        <label>Keterangan Potongan Lebih Bayar</label>
                                        <input type="text" class="form-control" value="-" name="keterangan_rs" maxlength="1000" placeholder="Keterangan" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>

                            <!-- Report Potongan -->
                            <div class="panel-group" id="dropdown">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-credit-card">
                                            <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                                REPORT POTONGAN</a>
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
                                                            <th>Pengembalian Kurang Bayar</th>
                                                            <th>Cicilan Kurang Bayar Ke</th>
                                                            <th>Keterangan Pengembalian Kurang Bayar</th>
                                                            <th>Potongan Lebih Bayar</th>
                                                            <th>Cicilan Lebih Bayar Ke</th>
                                                            <th>Keterangan Potongan Lebih Bayar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $tm_potongan = bukaquery("SELECT tm_potongan_p.potongan_bank, tm_potongan_p.cicilan_bank, tm_potongan_p.keterangan_bank, tm_potongan_p.potongan_rs, tm_potongan_p.cicilan_rs, tm_potongan_p.keterangan_rs,  tm_potongan_p.date_p  FROM  tm_potongan_p where  tm_potongan_p.id_user='$id' and year( tm_potongan_p.date_p)='$thn_now'");
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

                        <?php
                        } else {
                            $potongan_p = fetch_array(bukaquery("select * from tm_potongan_p where id_user=" . $id . " and id_potongan_p='$id_potongan_p'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-potongan-pegawai&id=' . $id_potongan_p . '&link=kepegawaian');
                            } else {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-potongan-pegawai&id=' . $id_potongan_p . '');
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>Pengembalian Kurang Bayar</label>
                                        <input type="number" class="form-control" value="<?php echo $potongan_p['potongan_bank']; ?>" name="potongan_bank" maxlength="10" placeholder="Rp." required>
                                        <label class="control-label">Cicilan Kurang Bayar Ke</label>
                                        <?php echo UpdateEnumDropdown("tm_potongan_p", "cicilan_bank", $potongan_p['cicilan_bank'], ""); ?>
                                        <label>Keterangan Pengembalian Kurang Bayar</label>
                                        <input type="text" class="form-control" value="<?php echo $potongan_p['keterangan_bank']; ?>" name="keterangan_bank" maxlength="1000" placeholder="Keterangan" required>
                                        <label>Potongan Lebih Bayar</label>
                                        <input type="number" class="form-control" value="<?php echo $potongan_p['potongan_rs']; ?>" name="potongan_rs" maxlength="10" placeholder="Rp." required>
                                        <label class="control-label">Cicilan Lebih Bayar Ke</label>
                                        <?php echo UpdateEnumDropdown("tm_potongan_p", "cicilan_rs", $potongan_p['cicilan_rs'], ""); ?>
                                        <label>Keterangan Potongan Lebih Bayar</label>
                                        <input type="text" class="form-control" value="<?php echo $potongan_p['keterangan_rs']; ?>" name="keterangan_rs" maxlength="1000" placeholder="Keterangan" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>

                            <!-- Report Potongan -->
                            <div class="panel-group" id="dropdown">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-credit-card">
                                            <a data-toggle="collapse" data-parent="#dropdown" href="#collapse2">
                                                REPORT POTONGAN</a>
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
                                                            <th>Pengembalian Kurang Bayar</th>
                                                            <th>Cicilan Kurang Bayar Ke</th>
                                                            <th>Keterangan Kurang Bayar</th>
                                                            <th>Potongan Lebih Bayar</th>
                                                            <th>Cicilan Lebih Bayar Ke</th>
                                                            <th>Keterangan Lebih Bayar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $tm_potongan = bukaquery("SELECT tm_potongan_p.potongan_bank, tm_potongan_p.keterangan_bank, tm_potongan_p.potongan_rs, tm_potongan_p.keterangan_rs,  tm_potongan_p.date_p  FROM  tm_potongan_p where  tm_potongan_p.id_user='$id' and year( tm_potongan_p.date_p)='$thn_now'");
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

                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <?php
                            if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                                if ($pegawai['jk'] == 'L') {
                                    $foto = 'img/laki.png';
                                } else {
                                    $foto = 'img/perempuan.png';
                                }
                            } else {
                                $foto = "img/" . $pegawai['foto'];
                            }
                            ?>
                            <br><br>
                            <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                        </center>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "validasi-tkhusus-pegawai":
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-clock-o"> VALIDASI TUNJANGAN KHUSUS PEGAWAI </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-tkhusus-pegawai&id=' . $id . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-tkhusus-pegawai&id=' . $id . '');
                        }
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_potongan_p = getOne("select id_uang_t from tm_uang_t where month(tm_uang_t.date_p)='$bln_sebelumnya' and year(tm_uang_t.date_p)='$thn' and tm_uang_t.id_user='$id'");
                        if ($id_potongan_p == '') {
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>THR</label>
                                        <input type="number" class="form-control" name="thr" maxlength="10" placeholder="Rp." required>
                                        <label>Gaji 13</label>
                                        <input type="number" class="form-control" name="gaji13" maxlength="10" placeholder="Rp." required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>

                        <?php
                        } else {
                            $potongan_p = fetch_array(bukaquery("select * from tm_uang_t where id_user=" . $id . " and id_uang_t='$id_potongan_p'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-tkhusus-pegawai&id=' . $id_potongan_p . '&link=kepegawaian');
                            } else {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-tkhusus-pegawai&id=' . $id_potongan_p . '');
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Pegawai</label>
                                        <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                        <label>THR</label>
                                        <input type="number" class="form-control" value="<?php echo $potongan_p['thr']; ?>" name="thr" maxlength="10" placeholder="Rp." required>
                                        <label>Gaji 13</label>
                                        <input type="number" class="form-control" value="<?php echo $potongan_p['gaji13']; ?>" name="gaji13" maxlength="10" placeholder="Rp." required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($hak_akses['menu_kepegawaian'] == '1') { ?>
                                        <a href="?<?php echo paramEncrypt('module=absensi-pegawai&act=rekap-absensi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </a>
                                    <?php } ?>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>

                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <center>
                            <?php
                            if ($pegawai['foto'] == '-' or $pegawai['foto'] == '') {
                                if ($pegawai['jk'] == 'L') {
                                    $foto = 'img/laki.png';
                                } else {
                                    $foto = 'img/perempuan.png';
                                }
                            } else {
                                $foto = "img/" . $pegawai['foto'];
                            }
                            ?>
                            <br><br>
                            <img src="<?php echo $foto; ?>" width="300px" height="300px" class="img-squere">
                        </center>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;



        case "validasi-kedisiplinan":
            if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
                $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
            } else {
                $bulan = date('m', strtotime('-1 month'));
            }
            
            if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
                $tahun = $tahun_post;
            } else {
               $tahun = date('Y');
            }
            
            $monthStart = date("Y-m-01", strtotime("$tahun-$bulan-01"));
            $monthEnd   = date("Y-m-t", strtotime($monthStart));
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-check-square-o"> VALIDASI KEDISPLINAN UNIT <?php echo strtoupper(konversiBulanTahun($monthStart)); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        if ($hak_akses['menu_kepegawaian'] == '1') {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-kedisiplinan&id=' . $id . '&link=kepegawaian');
                        } else {
                            $link = $aksi . paramEncrypt('module=validasi-pegawai&act=add-kedisiplinan&id=' . $id . '');
                        }
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_disiplin = getOne("select id_disiplin from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bulan' and year(tm_kedisiplinan.date_d)='$tahun' and tm_kedisiplinan.id_user='$id'");
                        if ($id_disiplin == '') {
                        ?>
                            <form id="formKedisiplinanBaru" role="form" action="<?php echo $link; ?>" method="post">
                              <div class="modal-body">
                                <div class="box-body table-responsive">
                            
                                  <!-- Toolbar preset -->
                                  <div class="text-right" style="margin-bottom:10px">
                                    <button type="button" id="btnPresetBagus" class="btn btn-success btn-sm">
                                      <i class="fa fa-thumbs-up"></i> Bagus (9–10)
                                    </button>
                                    <button type="button" id="btnPresetSedang" class="btn btn-warning btn-sm">
                                      <i class="fa fa-adjust"></i> Sedang (7–8)
                                    </button>
                                    <button type="button" id="btnPresetPerlu" class="btn btn-danger btn-sm">
                                      <i class="fa fa-exclamation-circle"></i> Perlu Perbaikan (5–6)
                                    </button>
                                    <button type="button" id="btnClear" class="btn btn-default btn-sm">
                                      Clear
                                    </button>
                                  </div>
                            
                                  <div class="form-group">
                                    <div class="col-xs-12 col-md-4">
                                      <label>Nama Pegawai</label>
                                      <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                            
                                      <label class="m-t-10">Kebersihan Diri</label>
                                      <input type="number" class="form-control disiplin-field" id="d_diri" name="d_diri" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Kerapihan Penampilan</label>
                                      <input type="number" class="form-control disiplin-field" id="d_penampilan" name="d_penampilan" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Kelengkapan Seragam</label>
                                      <input type="number" class="form-control disiplin-field" id="d_seragam" name="d_seragam" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Parameter Penilaian</label>
                                      <p style="margin-bottom:0">
                                        Selalu Sesuai = 10<br>
                                        Sering Sesuai = 8<br>
                                        Terkadang Sesuai = 5
                                      </p>
                                    </div>
                            
                                    <div class="col-xs-12 col-md-8">
                                      <label>Kebersihan Alat/Ruang Kerja</label>
                                      <input type="number" class="form-control disiplin-field" id="d_alat" name="d_alat" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Ruang/Alat Diatur Rapih</label>
                                      <input type="number" class="form-control disiplin-field" id="d_ruangan" name="d_ruangan" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Merawat Sarana Kerja Teratur</label>
                                      <input type="number" class="form-control disiplin-field" id="d_sarana" name="d_sarana" min="1" max="10" inputmode="numeric" placeholder="Nilai 1–10" required>
                            
                                      <label class="m-t-10">Parameter Penilaian</label>
                                      <p style="margin-bottom:0">
                                        Sesuai Standar = 10<br>
                                        Jarang Sesuai Standar = 8<br>
                                        Tidak Sesuai Standar = 5
                                      </p>
                                    </div>
                                  </div>
                            
                                  <div class="form-group"><Label></Label></div>
                                </div>
                              </div>
                            
                              <div class="modal-footer">
                                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </a>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                              </div>
                            
                              <div class="form-group"><br></div>
                            </form>
                            <!-- Report Kehadiran -->
                            <div class="panel-group" id="dropdown">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-calendar-check-o">
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $tm_kehadiran = bukaquery("SELECT tm_waktu_k.sakit1, tm_waktu_k.sakit2, tm_waktu_k.alpha, tm_waktu_k.izin, tm_waktu_k.telat,tm_waktu_k.pulang_cepat, tm_waktu_k.date_k  FROM tm_waktu_k where tm_waktu_k.id_user='$id' and year(tm_waktu_k.date_k)='$tahun'");
                                                        while ($kehadiran = fetch_array($tm_kehadiran)) {
                                                            $no++;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $no; ?></td>
                                                                <td><?php echo konversiBulanTahun($kehadiran['date_k']); ?></td>
                                                                <td><?php echo $kehadiran['sakit1'] ?></td>
                                                                <td><?php echo $kehadiran['sakit2'] ?></td>
                                                                <td><?php echo $kehadiran['alpha'] ?></td>
                                                                <td><?php echo $kehadiran['izin'] ?></td>
                                                                <td><?php echo $kehadiran['telat'] ?></td>
                                                                <td><?php echo $kehadiran['pulang_cepat'] ?></td>
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
                        <?php
                        } else {
                            $kedisplinan = fetch_array(bukaquery("select * from tm_kedisiplinan where id_user=" . $id . " and id_disiplin='$id_disiplin'"));
                            if ($hak_akses['menu_kepegawaian'] == '1') {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-kedisiplinan&id=' . $id_disiplin . '&link=kepegawaian');
                            } else {
                                $link = $aksi . paramEncrypt('module=validasi-pegawai&act=update-kedisiplinan&id=' . $id_disiplin . '');
                            }
                        ?>
                            <form role="form" action="<?php echo $link; ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="box-body table-responsive">
                                            <div class="col-xs-2 col-md-4">
                                                <label>Nama Pegawai</label>
                                                <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                                <label>Kebersihan Diri</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_diri']; ?>" maxlength="2" name="d_diri" placeholder="Nilai 0-10" required>
                                                <label>Kerapihan Penampilan</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_penampilan']; ?>" maxlength="2" name="d_penampilan" placeholder="Nilai 0-10" required>
                                                <label>Kelengkapan Seragam</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_seragam']; ?>" maxlength="2" name="d_seragam" placeholder="Nilai 0-10" required>
                                                <Label>Parameter Penilaian</Label>
                                                <p>
                                                    Selalu Sesuai = 10<br>
                                                    Sering Sesuai = 8<br>
                                                    Terkadang Sesuai = 5
                                                </p>
                                            </div>
                                            <div class="col-xs-4 col-md-8">
                                                <label>Kebersihan Alat/Ruang Kerja</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_alat']; ?>" maxlength="2" name="d_alat" placeholder="Nilai 0-10" required>
                                                <label>Ruang/Alat Di Atur Rapih</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_ruangan']; ?>" maxlength="2" name="d_ruangan" placeholder="Nilai 0-10" required>
                                                <label>Merawat Sarana Kerja Teratur</label>
                                                <input type="text" class="form-control" value="<?php echo $kedisplinan['d_sarana']; ?>" maxlength="2" name="d_sarana" placeholder="Nilai 0-10" required>
                                                <Label>Parameter Penilaian </Label>
                                                <p>
                                                    Sesuai Standart = 10<br>
                                                    Jarang Sesuai Standart = 8<br>
                                                    Tidak Sesuai Standart = 5
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>
                            <!-- Report Kehadiran -->
                            <div class="panel-group" id="dropdown">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title fa fa-calendar-check-o">
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $tm_kehadiran = bukaquery("SELECT tm_waktu_k.sakit1, tm_waktu_k.sakit2, tm_waktu_k.alpha, tm_waktu_k.izin, tm_waktu_k.telat, tm_waktu_k.pulang_cepat, tm_waktu_k.date_k  FROM tm_waktu_k where tm_waktu_k.id_user='$id' and year(tm_waktu_k.date_k)='$tahun'");
                                                        while ($kehadiran = fetch_array($tm_kehadiran)) {
                                                            $no++;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $no; ?></td>
                                                                <td><?php echo konversiBulanTahun($kehadiran['date_k']); ?></td>
                                                                <td><?php echo $kehadiran['sakit1'] ?></td>
                                                                <td><?php echo $kehadiran['sakit2'] ?></td>
                                                                <td><?php echo $kehadiran['alpha'] ?></td>
                                                                <td><?php echo $kehadiran['izin'] ?></td>
                                                                <td><?php echo $kehadiran['telat'] ?></td>
                                                                <td><?php echo $kehadiran['pulang_cepat'] ?></td>
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
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "validasi-kompetensi":
            if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
                $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
            } else {
                $bulan = date('m', strtotime('-1 month'));
            }
            
            if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
                $tahun = $tahun_post;
            } else {
               $tahun = date('Y');
            }
            
            $monthStart = date("Y-m-01", strtotime("$tahun-$bulan-01"));
            $monthEnd   = date("Y-m-t", strtotime($monthStart));
            
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-check-square-o"> VALIDASI KOMPETENSI PEGAWAI <?php echo strtoupper(konversiBulanTahun($monthStart)); ?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-8">
                        <?php
                        $pegawai = fetch_array(bukaquery("select tm_pegawai.id_unit,tm_pegawai.nip,tm_pegawai.nama_pegawai, tm_pegawai.jk, tm_pegawai.foto from tm_pegawai where tm_pegawai.id_user='$id' "));
                        $id_kompetensi = getOne("select id_kompetensi from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bulan' and year(tm_kompetensi.date_kompetensi)='$tahun' and tm_kompetensi.id_user='$id'");
                        if ($id_kompetensi == '') {
                        ?>
                            <form id="formKompetensiBaru" role="form" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=add-kompetensi&id=' . $id . ''); ?>" method="post">
                              <div class="modal-body">
                                <div class="form-group">
                                  <div class="box-body table-responsive">
                            
                                    <!-- Toolbar preset -->
                                    <div class="text-right" style="margin-bottom:10px">
                                      <button type="button" id="btnPresetExceeds" class="btn btn-success btn-sm">
                                        <i class="fa fa-trophy"></i> Exceeds / Melebihi (10)
                                      </button>
                                      <button type="button" id="btnPresetMeets" class="btn btn-warning btn-sm">
                                        <i class="fa fa-check-circle"></i> Meets / Sesuai (7–9)
                                      </button>
                                      <button type="button" id="btnPresetDevelop" class="btn btn-danger btn-sm">
                                        <i class="fa fa-wrench"></i> Develop / Perlu Dikembangkan (4–6)
                                      </button>
                                      <button type="button" id="btnClearKompetensi" class="btn btn-default btn-sm">
                                        Clear
                                      </button>
                                    </div>
                            
                                    <label>Nama Pegawai</label>
                                    <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                            
                                    <table class="table table-bordered table-striped">
                                      <tr><td colspan="3"><label>Petunjuk Pengisian:</label></td></tr>
                                      <tr><td>1.</td><td colspan="2">Beri nilai kepada setiap indikator kompetensi. Pilihan nilai adalah 1 sampai dengan 10 (jangan angka lain).</td></tr>
                                      <tr><td>2.</td><td colspan="2">Nilai dari tiap poin dirata-rata dan dikalikan bobot untuk mendapatkan skor tiap kompetensi.</td></tr>
                                      <tr><td>3.</td><td colspan="2">Seluruh skor kompetensi dijumlahkan untuk mendapatkan skor Competency.</td></tr>
                                      <tr><td colspan="2"><label>Kategori</label></td><td><label>Score</label></td></tr>
                                      <tr><td colspan="2">Develop / Perlu Dikembangkan</td><td>4 – 6</td></tr>
                                      <tr><td colspan="2">Meets / Sesuai</td><td>7 – 9</td></tr>
                                      <tr><td colspan="2">Exceeds / Melebihi</td><td>10</td></tr>
                                      <tr><td colspan="3"></td></tr>
                            
                                      <tr><td colspan="3"><label>KOMPETENSI</label></td></tr>
                            
                                      <!-- KEMAMPUAN MENGANALISA -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>KEMAMPUAN MENGANALISA</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Mampu melakukan pemecahan masalah secara sistematis</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="menganalisa1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Mampu membuat beberapa analisis dan solusi pemecahan permasalahan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="menganalisa2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- KOMUNIKASI -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>KOMUNIKASI</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Mampu memberikan pendapat/masukan ... lisan dan tulisan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="komunikasi1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Mampu memahami keadaan situasi dan kondisi pada saat berkomunikasi</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="komunikasi2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- KERJASAMA -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>KERJASAMA</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Mampu bekerjasama dengan unit-unit terkait</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="kerjasama1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Tetap mendukung keputusan tim ...</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="kerjasama2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- KECERDASAN EMOSIONAL -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>KECERDASAN EMOSIONAL</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Emosional stabil saat tekanan kerja tinggi</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="kecerdasan1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Peka terhadap perasaan orang lain</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="kecerdasan2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>3.</td>
                                        <td>Menjaga hubungan kerja yang baik</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="kecerdasan3" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- FOKUS HASIL -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>FOKUS PADA HASIL KERJA</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Mencapai target sesuai tenggat</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="fokus1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Kelola sumber daya capai hasil memuaskan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="fokus2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>3.</td>
                                        <td>Mengatasi kendala dengan supervisi minimum</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="fokus3" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- TANGGUNG JAWAB -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>TANGGUNG JAWAB</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Komitmen menyelesaikan pekerjaan tanpa dimonitor</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="tanggung1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Diandalkan, mandiri, tepat waktu</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="tanggung2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>3.</td>
                                        <td>Terus melaksanakan tugas dalam tekanan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="tanggung3" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>4.</td>
                                        <td>Tidak mengelak dari tugas & siap konsekuensi</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="tanggung4" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- ORIENTASI KUALITAS -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>ORIENTASI TERHADAP KUALITAS</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Memperhatikan standar, sistem & prosedur</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="orientasi_k1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Continuous improvement</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="orientasi_k2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- INISIATIF -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>INISIATIF</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Ide yang dapat langsung diterapkan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="inisiatif1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Tindakan proaktif mandiri sesuai aturan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="inisiatif2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- DISIPLIN -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>DISIPLIN</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Mematuhi aturan yang ditetapkan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="disiplin1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Tidak absen karena alasan dibuat-buat</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="disiplin2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>3.</td>
                                        <td>Tidak suka terlambat</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="disiplin3" placeholder="Nilai 1–10" required></td>
                                      </tr>
                            
                                      <!-- ORIENTASI PELANGGAN -->
                                      <tr><td><label>No</label></td><td colspan="2"><label>ORIENTASI PELANGGAN</label></td></tr>
                                      <tr>
                                        <td>1.</td>
                                        <td>Memahami kebutuhan pelanggan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="orientasi_p1" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>2.</td>
                                        <td>Responsif terhadap permintaan & keluhan</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="orientasi_p2" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                      <tr>
                                        <td>3.</td>
                                        <td>Membangun & memelihara hubungan efektif</td>
                                        <td><input type="number" min="1" max="10" class="form-control nilai-komp" name="orientasi_p3" placeholder="Nilai 1–10" required></td>
                                      </tr>
                                    </table>
                                  </div>
                                </div>
                                <div class="form-group"><Label></Label></div>
                              </div>
                            
                              <div class="modal-footer">
                                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </a>
                                <input type="submit" class="btn btn-primary" value="Simpan">
                              </div>
                            
                              <div class="form-group"><br></div>
                            </form>
                        <?php
                        } else {
                            $kompetensi = fetch_array(bukaquery("select * from tm_kompetensi where id_user=" . $id . " and id_kompetensi='$id_kompetensi'"));
                        ?>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-kompetensi&id=' . $id_kompetensi . ''); ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="box-body table-responsive">
                                            <label>Nama Pegawai</label>
                                            <input type="text" class="form-control" value="<?php echo $pegawai['nama_pegawai']; ?>" readonly>
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td colspan="3"><label>Petunjuk Pengisian:</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td colspan="2">Beri nilai kepada setiap indikator kompetensi dan Pilihan nilai adalah 1 sampai dengan 10, sesuai kategori di bawah. Jangan mengisi dengan angka lain</td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td colspan="2">Nilai dari tiap point dirata-rata dan dikalikan bobot, untuk mendapatkan score untuk setiap kompetensi</td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td colspan="2">Seluruh Score kompetensi dijumlahkan untuk mendapatkan score Competency</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><label>Kategori</label></td>
                                                    <td><label>score</label></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Develop / Perlu Dikembangkan</td>
                                                    <td>1 - 4</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Meets / Sesuai</td>
                                                    <td>5 - 9</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Exceeds / Melebihi</td>
                                                    <td>10</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><label>KOMPETENSI</label></td>
                                                </tr>
                                                <tr>
                                                    <td><label>No</td>
                                                    <td colspan="2"><label>KEMAMPUAN MENGANALISA</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Mampu melakukan pemecahan masalah secara sistematis</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['menganalisa1']; ?>" name="menganalisa1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Mampu membuat beberapa analisis dan solusi pemecahan permasalahan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['menganalisa2']; ?>" name="menganalisa2" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>KOMUNIKASI</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Mampu memberikan pendapat/masukan kepada atasan, rekan kerja atau bawahan secara sistematis dan akurat secara lisan dan tulisan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['komunikasi1']; ?>" name="komunikasi1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Mampu memahami keadaan situasi dan kondisi pada saat berkomunikasi (menyampaikan pesan, ide dan gagasan)</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['komunikasi2']; ?>" name="komunikasi2" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>KERJASAMA</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Mampu bekerjasama dengan unit-unit terkait</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['kerjasama1']; ?>" name="kerjasama1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Tetap mendukung keputusan team sekalipun keputusan yang diambil tidak sesuai dengan keputusan pribadi</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['kerjasama2']; ?>" name="kerjasama2" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>KECERDASAN EMOSIONAL</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Menunjukkan emosional yang stabil pada saat mendapat tekanan pekerjaan yang tinggi</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['kecerdasan1']; ?>" name="kecerdasan1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Peka terdahap perasaan orang lain</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['kecerdasan2']; ?>" name="kecerdasan2" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Mampu menjaga situasi dan hubungan kerja yang baik dengan atasan, rekan kerja atau bawahan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['kecerdasan3']; ?>" name="kecerdasan3" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>FOKUS PADA HASIL KERJA</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Mampu mencapai target kerja sesuai dengan tenggat waktu</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['fokus1']; ?>" name="fokus1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Mampu merencanakan dan mengelola sumber daya untuk mencapai hasil yang memuaskan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['fokus2']; ?>" name="fokus2" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Dapat mengatasi kendala dengan supervisi minimum</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['fokus3']; ?>" name="fokus3" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>TANGGUNG JAWAB</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Berkomitmen menyelesaikan pekerjaan tanpa perlu dimonitor oleh atasan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['tanggung1']; ?>" name="tanggung1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Dapat diandalkan menyelesaikan pekerjaan dengan mandiri dan tepat waktu</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['tanggung2']; ?>" name="tanggung2" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Dapat terus melaksanakan tugas dalam situasi tekanan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['tanggung3']; ?>" name="tanggung3" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>4.</td>
                                                    <td>Tidak mengelak dari tugas dan wewenang yang diberikan dan bersedia menerima konsekuensi yang timbul dari pekerjaannya</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['tanggung4']; ?>" name="tanggung4" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>ORIENTASI TERHADAP KUALITAS</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Memperhatikan standar kerja yang ditetapkan maupun sistem dan prosedur</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['orientasi_k1']; ?>" name="orientasi_k1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Selalu berusahan meningkatkan kualitas kerja dan mengembangkan sistem kerja yang baik(Continuous improvment)</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['orientasi_k2']; ?>" name="orientasi_k2" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>INISIATIF</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Mampu memberikan ide-ide yang secara langsung dapat diterapkan untuk perbaikan kualitas kerja</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['inisiatif1']; ?>" name="inisiatif1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Mengambil tindakan proaktif secara mandiri bila ada masalah/situasi tertentu sesuai dengan kebutuhan dan aturan yang berlaku</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['inisiatif2']; ?>" name="inisiatif2" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>DISIPLIN</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Melaksanakan dan mematuhi aturan yang sudah ditetapkan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['disiplin1']; ?>" name="disiplin1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Tidak absen untuk alasan yang dibuat-buat</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['disiplin2']; ?>" name="disiplin2" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Tidak suka terlambat</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['disiplin3']; ?>" name="disiplin3" placeholder="Nilai 0-10" required></td>
                                                </tr>

                                                <tr>
                                                    <td><label>No</label></td>
                                                    <td colspan="2"><label>ORIENTASI PELANGGAN</label></td>
                                                </tr>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Dapat memahami kebutuhan pelanggan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['orientasi_p1']; ?>" name="orientasi_p1" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Responsif terhadap permintaan dan keluhan pelanggan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['orientasi_p2']; ?>" name="orientasi_p2" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Membangun dan memelihara hubungan yang efektif dengan pelanggan</td>
                                                    <td><input type="number" class="form-control" maxlength="2" value="<?php echo $kompetensi['orientasi_p3']; ?>" name="orientasi_p3" placeholder="Nilai 0-10" required></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </a>
                                    <input type="submit" class="btn btn-warning" value="Update">
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "hasil-validasi":
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-bullseye"> REFERENSI HASIL VALIDASI KASATPEL</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <?php $biodata = fetch_array(bukaquery("SELECT tm_pegawai.nik, tm_pegawai.nama_pegawai, tm_pegawai.nip, tm_pegawai.no_rek, tm_pegawai.tempat_lahir, tm_pegawai.agama, tm_pegawai.npwp,tm_pegawai.jk, 
                                            tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan, tm_pegawai.alamat, tm_pegawai.no_bpjs,tm_pegawai.tgl_lahir,tm_pegawai.foto,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user=" . $id . "")); ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-4 col-md-4">
                                <div class="col-xs-12">
                                    <label>NIP</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['nip']; ?>" readonly>
                                    <label>NAMA PEGAWAI</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_pegawai']; ?>" readonly>
                                    <label>RUMPUN JABATAN</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['rumpun']; ?>" readonly>
                                    <label>MASA KERJA</label>
                                    <input type="text" class="form-control" value="<?php echo MasaKerjaPenyebut($biodata['tgl_masuk']); ?>" readonly>
                                    <label>PENDIDIKAN</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['pendidikan']; ?>" readonly>
                                    <label>HUKDIS</label>
                                    <input type="text" class="form-control" value="<?php echo NamaHukDis($id); ?>" readonly>
                                    <label>GAPOK</label>
                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiPokok($biodata['pendidikan'], $biodata['tgl_masuk'])); ?>" readonly>
                                    <label>GAJI BRUTO</label>
                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiBruto(GajiPokok($biodata['pendidikan'], $biodata['tgl_masuk']), NilaiStatusKawin($biodata['status_nikah']))); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-xs-8 col-md-4">
                                <div class="col-xs-8">
                                    <label>JENIS KELAMIN</label>
                                    <input type="text" class="form-control" value="<?php echo kelamin($biodata['jk']); ?>" readonly>
                                    <label>TANGGAL MASUK</label>
                                    <input type="text" class="form-control" value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_masuk']); ?>" readonly>
                                    <label>STATUS NIKAH</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" readonly>
                                    <label>BAGIAN/UNIT</label>
                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_unit']; ?>" readonly>
                                </div>
                                <div class="col-xs-4">
                                    <?php
                                    if ($biodata['foto'] == '-' or $biodata['foto'] == '') {
                                        if ($biodata['jk'] == 'L') {
                                            $foto = 'img/laki.png';
                                        } else {
                                            $foto = 'img/perempuan.png';
                                        }
                                    } else {
                                        $foto = "img/" . $biodata['foto'];
                                    }
                                    ?>
                                    <img src="<?php echo $foto; ?>" style="align:center" class="img-rounded" width="250" height="250" alt="">
                                    <br><br>
                                </div>
                                <div class="col-xs-16">
                                    <?php
                                    $refrensi_penyerapan = getOne("select penyerapan from tm_penyerapan where Month(tm_penyerapan.bulan)='$bln_sebelumnya' and Year(tm_penyerapan.bulan)='$thn' ");
                                    ?>
                                </div>
                                <div class="col-xs-16">
                                    <?php
                                    $potongan_bank = getOne("select potongan_bank from tm_potongan_p where Month(date_p)='$bln_sebelumnya' and Year(date_p)='$thn' and tm_potongan_p.id_user='$id'");
                                    ?>
                                </div>
                                <div class="col-xs-16">
                                    <?php
                                    $potongan_rs = getOne("select potongan_rs from tm_potongan_p where Month(date_p)='$bln_sebelumnya' and Year(date_p)='$thn' and tm_potongan_p.id_user='$id'");
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th>UNSUR YANG DI NILAI</th>
                                <th>TOTAL CAPAIAN</th>
                                <th>X BOBOT</th>
                                <th>HASIL</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>a). Nilai Kinerja</td>
                                    <td><?php echo NilKinerja($id, $bln_sebelumnya, $thn); ?></td>
                                    <td> X &ensp;70%</td>
                                    <td><?php echo NilKinerja($id, $bln_sebelumnya, $thn) * 0.7; ?></td>
                                </tr>
                                <tr>
                                    <td>b). Nilai Pendapatan</td>
                                    <td><?php echo $refrensi_penyerapan ?></td>
                                    <td> X &ensp;20%</td>
                                    <td><?php echo $refrensi_penyerapan * 0.2; ?> </td>
                                </tr>
                                <tr>
                                    <td colspan="4">c). Nilai Personil</td>
                                </tr>
                                <tr>
                                    <td>&ensp;&ensp;&ensp; -Disiplin</td>
                                    <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn)); ?> </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&ensp;&ensp;&ensp; -Kompetensi</td>
                                    <td><?php echo number_format(NilKomp($id, $bln_sebelumnya, $thn)); ?> </td>
                                    <td> </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>d). Nilai Pengembalian Kurang Bayar</td>
                                    <td><?php echo $potongan_bank ?></td>
                                    <td> &ensp;Rp.</td>
                                    <td><?php echo $potongan_bank ?> </td>
                                </tr>
                                <tr>
                                    <td>e). Nilai Potongan Lebih Bayar</td>
                                    <td><?php echo $potongan_rs ?></td>
                                    <td> &ensp;Rp.</td>
                                    <td><?php echo $potongan_rs ?> </td>
                                </tr>
                                <tr>
                                    <td>&ensp;&ensp;&ensp; Jumlah Nilai Personil (Disiplin+Kompetensi)</td>
                                    <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)); ?> </td>
                                    <td> X &ensp;10% </td>
                                    <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)) * 0.1; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <b>JUMLAH <p>(Hasil Nilai Kinerja + Hasil Nilai Pendapatan + Hasil Nilai Disiplin + Hasil Nilai Kompetensi)</p></b>
                                    </td>
                                    <td>
                                        <label>
                                            <h2 class=""><u><i><?php echo formatDecDigit((NilKinerja($id, $bln_sebelumnya, $thn) * 0.7) + ($refrensi_penyerapan * 0.2) + ((NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)) * 0.1), 1); ?></i></u></h2>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "hasil-validasi-management":
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-bullseye"> Referensi Hasil Validasi Bulan <label><?php echo konversiBulan($bln_sebelumnya) . " Tahun " . $thn_now; ?></label> </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form role="form" name="autoSumForm" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=add-penilaian&id=' . $id . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="box-body table-responsive">
                                <div class="form-group">
                                    <?php
                                    $biodata = fetch_array(bukaquery("SELECT tm_pegawai.nik, tm_pegawai.nama_pegawai, tm_pegawai.nip, tm_pegawai.no_rek, tm_pegawai.tempat_lahir, tm_pegawai.agama, tm_pegawai.npwp,tm_pegawai.jk, 
                                            tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan, tm_pegawai.alamat, tm_pegawai.no_bpjs,tm_pegawai.tgl_lahir,tm_pegawai.foto,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level, tm_level.grade_kinerja, tm_level.grade_Prilaku 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user=" . $id . ""));
                                    $tmtcek = TanggalAkhirBulanKemarin();
                                    $cekkinerja = getOne("SELECT COUNT(*) FROM tt_kinerja WHERE tt_kinerja.id_user = " . $id . " and MONTH(tanggal_kinerja) = $bln_sebelumnya and YEAR(tanggal_kinerja) = $thn_now");
                                    if ($cekkinerja > 0)  {
                                                        $value = "YA"; 
                                                        }
                                                        else {
                                                        $value = "TIDAK";
                                                        }
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-xs-4 col-md-4">
                                                <div class="col-xs-12">
                                                    <label>NIP</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nip']; ?>" readonly>
                                                    <label>NAMA PEGAWAI</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_pegawai']; ?>" readonly>
                                                    <label>RUMPUN JABATAN</label>
                                                    <input type="text" class="form-control" name="rumpun" value="<?php echo $biodata['rumpun']; ?>" readonly>
                                                    <label>MASA KERJA</label>
                                                    <input type="text" class="form-control" name="masa_kerja" value="<?php echo MasaKerjaPenyebutValidasi($biodata['tgl_masuk']); ?>" readonly>
                                                    <label>PENDIDIKAN</label>
                                                    <input type="text" class="form-control" name="pendidikan" value="<?php echo $biodata['pendidikan']; ?>" readonly>
                                                    
                                                    <label>HUKDIS</label>
                                                    <input type="text" class="form-control" value="<?php echo NamaHukDis($id); ?>" readonly>
                                                    <label>MENGISI KINERJA</label>
                                                    <input type="text" class="form-control" 
                                                    value="<?php echo $value; ?>" readonly>
 
                                                </div>
                                            </div>
                                            <div class="col-xs-8 col-md-4">
                                                <div class="col-xs-8">
                                                    <label>JENIS KELAMIN</label>
                                                    <input type="text" class="form-control" value="<?php echo kelamin($biodata['jk']); ?>" readonly>
                                                    <label>TANGGAL MASUK</label>
                                                    <input type="text" class="form-control" value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_masuk']); ?>" readonly>
                                                    <label>STATUS NIKAH</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" readonly>
                                                    <label>BAGIAN/UNIT</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_unit']; ?>" readonly>
                                                    <label>GAJI BRUTO</label>
                                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiBruto(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek), NilaiStatusKawin($biodata['status_nikah']))); ?>" readonly>
                                                    <label>TUNJANGAN</label>
                                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek) * NilaiStatusRumpun($biodata['rumpun'])); ?>" readonly>
                                                </div>
                                                <div class="col-xs-4">
                                                    <?php
                                                    $tunjangan = GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek) * NilaiStatusRumpun($biodata['rumpun']);
                                                    if ($biodata['foto'] == '-' or $biodata['foto'] == '') {
                                                        if ($biodata['jk'] == 'L') {
                                                            $foto = 'img/laki.png';
                                                        } else {
                                                            $foto = 'img/perempuan.png';
                                                        }
                                                    } else {
                                                        $foto = "img/" . $biodata['foto'];
                                                    }
                                                    ?>
                                                    <img src="<?php echo $foto; ?>" style="align:center" class="img-rounded" width="350" height="350" alt="">
                                                    <br><br>
                                                </div>
                                                <div class="col-xs-16">
                                                    <?php
                                                    $refrensi_penyerapan = fetch_array(bukaquery("select penyerapan,id_penyerapan from tm_penyerapan where Month(tm_penyerapan.bulan)='$bln_sebelumnya' and Year(tm_penyerapan.bulan)='$thn' "));
                                                    ?>
                                                </div>
                                                <div class="col-xs-16">
                                                    <?php
                                                    $potongan_bank = fetch_array(bukaquery("select potongan_bank,id_potongan_p from tm_potongan_p where Month(tm_potongan_p.date_p)='$bln_sebelumnya' and Year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$id'"));
                                                    ?>
                                                </div>
                                                <div class="col-xs-16">
                                                    <?php
                                                    $potongan_rs = fetch_array(bukaquery("select potongan_rs,id_potongan_p from tm_potongan_p where Month(tm_potongan_p.date_p)='$bln_sebelumnya' and Year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$id'"));
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <th>UNSUR YANG DI NILAI </th>
                                            <th colspan="2">TOTAL CAPAIAN</th>
                                            <th>HASIL</th>
                                        </thead>
                                        <tbody>
                                            <td>a). Nilai Kinerja / Capaian Kinerja</td>
                                            <td>
                                                <?php echo formatPembulatan(NilKinerja($id, $bln_sebelumnya, $thn)); ?>
                                            </td>
                                            <td> X &ensp;70%
                                            </td>
                                            <td>
                                                <div class="col-xs-8">
                                                    <?php
                                                    //grading
                                                    $grade1 = $biodata['grade_kinerja'];
                                                    $grade2 = $biodata['grade_Perilaku'];
                                                    if ($biodata['grade_kinerja'] == 0) {
                                                        $grade_kinerja = '70';
                                                    } else {
                                                        $grade_kinerja = $biodata['grade_kinerja'];
                                                    }
                                                    if ($biodata['grade_Perilaku'] == 0) {
                                                        $grade_Perilaku = '10';
                                                    } else {
                                                        $grade_Perilaku = $biodata['grade_Perilaku'];
                                                    }
                                                    //cek cuti melahirkan
                                                    ?>
                                            
                                                <input type="number" step="0.01" class="form-control" id="txt1<?php echo $id; ?>" value="<?php echo formatPembulatan(NilKinerja($id, $bln_sebelumnya, $thn) * 0.7); ?>" name="nskp" min="0" max="<?php echo $grade_kinerja; ?>" placeholder="1-70" onkeyup="sum<?php echo $id; ?>();" required />
                                                </div>
                                            
                                            </td>
                        </tbody>
                        <tbody>
                            <td>b). Nilai Pendapatan / Nilai Capaian Serapan</td>
                            <td><?php echo $refrensi_penyerapan['penyerapan']; ?>
                            </td>
                            <td> X &ensp;20%
                            </td>
                            <td>
                                <div class="col-xs-8">
                                    <!--<input type="number" class="form-control" value="<?php // echo $refrensi_penyerapan['penyerapan'] * 0.2; 
                                                                                            ?>" name="npenyerapan" id="text2" onkeyup="sum();" readonly required>-->
                                    <input type="number" step="0.01" id="txt2<?php echo $id; ?>" class="form-control" value="<?php echo $refrensi_penyerapan['penyerapan'] * 0.2; ?>" name="npenyerapan" onkeyup="sum<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                            <td>c). Nilai Disiplin + Nilai Kompetensi</td>
                            <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn)) . "+" . number_format(NilKomp($id, $bln_sebelumnya, $thn)); ?>
                            </td>
                            <td> X &ensp;10%
                            </td>
                            <td>
                                <div class="col-xs-8">
                                    <!--<input type="number" class="form-control" value="<?php // echo number_format(NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)) * 0.1; 
                                                                                            ?>" name="nPerilaku" id="text3" onkeyup="sum();" required="">-->
                                    <input type="number" step="0.01" id="txt3<?php echo $id; ?>" class="form-control" value="<?php echo number_format(NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)) * 0.1; ?>" name="nprilaku" onkeyup="sum<?php echo $id; ?>();" min="0" max="<?php echo $grade_Perilaku; ?>" required autofocus /></div>
                            </td>
                        </tbody>
                        <tbody>
                            <td>d). Nilai Pengembalian Kurang Bayar </td>
                            <td>
                            </td>
                            <td> &ensp; Rp.
                            </td>
                            <td>
                                <div class="col-xs-8">
                                    <!--<input type="number" class="form-control" value="<?php echo $potongan_bank['potongan_bank'] ?>" name="npenyerapan" id="text2" onkeyup="sum();" readonly required>-->
                                    <input type="number" step="0.01" id="txt5<?php echo $id; ?>" class="form-control" value="<?php if (is_null($potongan_bank['potongan_bank'])) {
                                      echo 0; } else { echo $potongan_bank['potongan_bank']; } ?>"
                                    name="npotonganbank" onkeyup="sum<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                        <tbody>
                            <td>e). Nilai Potongan Lebih Bayar </td>
                            <td>
                            </td>
                            <td> &ensp; Rp.
                            </td>
                            <td>
                                <div class="col-xs-8">
                                    <!--<input type="number" class="form-control" value="<?php echo $potongan_rs['potongan_rs'] ?>" name="npenyerapan" id="text2" onkeyup="sum();" readonly required>-->
                                    <input type="number" step="0.01" id="txt6<?php echo $id; ?>" class="form-control" value="<?php if (is_null($potongan_rs['potongan_rs'])) {
                                      echo 0; } else { echo $potongan_rs['potongan_rs']; } ?>" name="npotonganrs" onkeyup="sum<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                            <td colspan="3">
                                JUMLAH
                            </td>
                            <td>
                                <!-- cek -->
                                <input type="hidden" id="txt_hukdis<?php echo $id; ?>" value="<?php echo NilaiHukDis($id); ?>" onkeyup="sum<?php echo $id; ?>();" />
                                <input type="hidden" class="form-control" value="<?php echo GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek); ?>" name="gaji_pokok" readonly>
                                <input type="hidden" class="form-control" value="<?php echo GajiBruto(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek), NilaiStatusKawin($biodata['status_nikah'])); ?>" name="gaji_bruto" readonly>
                                <input type="hidden" id="txt_tunjangan<?php echo $id; ?>" value="<?php echo $tunjangan; ?>" onkeyup="sum<?php echo $id; ?>();" name="tunjangan" readonly />
                                <input type="hidden" class="form-control" value="<?php echo IdHukDis($id); ?>" name="id_sanksi" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['pajak']; ?>" name="pajak" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $nip; ?>" name="penilai" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" name="status_nikah" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_ks']; ?>" name="bpjs_ks" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_jkk']; ?>" name="bpjs_jkk" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_ijht']; ?>" name="bpjs_ijht" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_jp']; ?>" name="bpjs_jp" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_s from tm_waktu_s where id_user='$id' and Month(tm_waktu_s.date_s)='$bln_sebelumnya' and Year(tm_waktu_s.date_s)='$thn' order by id_waktu_s desc limit 1"); ?>" name="id_waktu_s" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_k from tm_waktu_k where id_user='$id' and Month(tm_waktu_k.date_k)='$bln_sebelumnya' and Year(tm_waktu_k.date_k)='$thn' order by id_waktu_k desc limit 1"); ?>" name="id_waktu_k" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_t from tm_waktu_t where id_user='$id' and Month(tm_waktu_t.date_t)='$bln_sebelumnya' and Year(tm_waktu_t.date_t)='$thn' order by id_waktu_t desc limit 1"); ?>" name="id_waktu_t" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $refrensi_penyerapan['id_penyerapan']; ?>" name="id_penyerapan" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $potongan_bank['id_potongan_p']; ?>" name="id_potongan_p" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $potongan_rs['id_potongan_p']; ?>" name="id_potongan_p" readonly>
                                <input type="hidden" class="form-control" id="tunjangan-val<?php echo $id; ?>" name="tunjangan_val" required />
                                <!-- tutup cek-->
                                <div class="col-xs-3">
                                    <label>Nilai</label>
                                    <!--<input type="number" class="form-control"  name="jumlah" id="text4" readonly required="">-->
                                    <input type="number" class="form-control" name="jumlah" id="txt4<?php echo $id; ?>" readonly required />
                                </div>
                                <div class="col-xs-8">
                                    <label>Di Rupiahkan</label>
                                    <input type="text" class="form-control" id="tunjangan<?php echo $id; ?>" readonly required />
                                </div>
                            </td>
                        </tbody>
                        </table>

                        <script>
                            function convertToRupiah(angka) {
                                var rupiah = '';
                                var angkarev = angka.toString().split('').reverse().join('');
                                for (var i = 0; i < angkarev.length; i++)
                                    if (i % 3 == 0)
                                        rupiah += angkarev.substr(i, 3) + '.';
                                return 'Rp. ' + rupiah.split('', rupiah.length - 1).reverse().join('');
                            }

                            function sum<?php echo $id; ?>() {
                                var txtFirstNumber<?php echo $id; ?> = document.getElementById('txt1<?php echo $id; ?>').value;
                                var txtSecondNumber<?php echo $id; ?> = document.getElementById('txt2<?php echo $id; ?>').value;
                                var txtTigaNumber<?php echo $id; ?> = document.getElementById('txt3<?php echo $id; ?>').value;
                                var txtEmpatNumber<?php echo $id; ?> = document.getElementById('txt_tunjangan<?php echo $id; ?>').value;
                                var txtLimaNumber<?php echo $id; ?> = document.getElementById('txt_hukdis<?php echo $id; ?>').value;
                                var txtEnamNumber<?php echo $id; ?> = document.getElementById('txt5<?php echo $id; ?>').value;
                                var txtTujuhNumber<?php echo $id; ?> = document.getElementById('txt6<?php echo $id; ?>').value;
                                var nilai_val = parseFloat(txtFirstNumber<?php echo $id; ?>) + parseFloat(txtSecondNumber<?php echo $id; ?>) + parseFloat(txtTigaNumber<?php echo $id; ?>);
                                var tunjangan = ((nilai_val * parseFloat(txtEmpatNumber<?php echo $id; ?>) / 100) - ((nilai_val * parseFloat(txtEmpatNumber<?php echo $id; ?>) / 100) * parseFloat(txtLimaNumber<?php echo $id; ?>))) 
                                // - (parseFloat(txtEnamNumber<?php echo $id; ?>) + parseFloat(txtTujuhNumber<?php echo $id; ?>));
                                if (!isNaN(nilai_val)) {
                                    document.getElementById('txt4<?php echo $id; ?>').value = nilai_val;
                                    document.getElementById('tunjangan<?php echo $id; ?>').value = convertToRupiah(tunjangan.toFixed(0));
                                    document.getElementById('tunjangan-val<?php echo $id; ?>').value = tunjangan.toFixed(0);
                                }
                            }
                        </script>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </a>
                <input type="submit" class="btn btn-primary" value="Simpan">
            </div>
            </form>
            </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "hasil-validasi-management-update":
            $refrensi_penyerapan = fetch_array(bukaquery("select penyerapan,id_penyerapan from tm_penyerapan where Month(tm_penyerapan.bulan)='$bln_sebelumnya' and Year(tm_penyerapan.bulan)='$thn' "));
            $potongan_bank = fetch_array(bukaquery("select potongan_bank,id_potongan_p from tm_potongan_p where Month(tm_potongan_p.date_p)='$bln_sebelumnya' and Year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$id'"));
            $potongan_rs = fetch_array(bukaquery("select potongan_rs,id_potongan_p from tm_potongan_p where Month(tm_potongan_p.date_p)='$bln_sebelumnya' and Year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$id'"));
            $penilaian = fetch_array(bukaquery("select id_penilaian,nskp,nprilaku from tm_penilaian where Month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and Year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$id' "));
            $tmtcek = TanggalAkhirBulanKemarin();
        ?>
            <!--Modal Add waktu -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-bullseye"> Update Validasi Bulan <label><?php echo konversiBulan($bln_sebelumnya) . " Tahun " . $thn_now; ?></label> </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form role="form" name="autoSumForm" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-penilaian&id=' . $penilaian['id_penilaian'] . ''); ?>" method="post">
                        <div class="modal-body">
                            <div class="box-body table-responsive">
                                <div class="form-group">
                                    <?php
                                    $biodata = fetch_array(bukaquery("SELECT tm_pegawai.id_user,tm_pegawai.nik, tm_pegawai.nama_pegawai, tm_pegawai.nip, tm_pegawai.no_rek, tm_pegawai.tempat_lahir, tm_pegawai.agama, tm_pegawai.npwp,tm_pegawai.jk, 
                                                    tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan, tm_pegawai.alamat, tm_pegawai.no_bpjs,tm_pegawai.tgl_lahir,tm_pegawai.foto,
                                                    tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                                    tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level 
                                                    FROM
                                                    tm_pegawai
                                                    INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                    INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                                    INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                                    where tm_pegawai.id_user=" . $id . ""));
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-xs-4 col-md-4">
                                                <div class="col-xs-12">
                                                    <label>NIP</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nip']; ?>" readonly>
                                                    <label>NAMA PEGAWAI</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_pegawai']; ?>" readonly>
                                                    <label>RUMPUN JABATAN</label>
                                                    <input type="text" class="form-control" name="rumpun" value="<?php echo $biodata['rumpun']; ?>" readonly>
                                                    <label>MASA KERJA</label>
                                                    <input type="text" class="form-control" name="masa_kerja" value="<?php echo MasaKerjaPenyebutValidasi($biodata['tgl_masuk']); ?>" readonly>
                                                    <label>PENDIDIDIKAN</label>
                                                    <input type="text" class="form-control" name="pendidikan" value="<?php echo $biodata['pendidikan']; ?>" readonly>
                                                    <label>HUKDIS</label>
                                                    <input type="text" class="form-control" value="<?php echo NamaHukDis($id); ?>" readonly>

                                                </div>
                                            </div>
                                            <div class="col-xs-8 col-md-4">
                                                <div class="col-xs-8">
                                                    <label>JENIS KELAMIN</label>
                                                    <input type="text" class="form-control" value="<?php echo kelamin($biodata['jk']); ?>" readonly>
                                                    <label>TANGGAL MASUK</label>
                                                    <input type="text" class="form-control" value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_masuk']); ?>" readonly>
                                                    <label>STATUS NIKAH</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" readonly>
                                                    <label>BAGIAN/UNIT</label>
                                                    <input type="text" class="form-control" value="<?php echo $biodata['nama_unit']; ?>" readonly>
                                                    <label>GAJI BRUTO</label>
                                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiBruto(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek), NilaiStatusKawin($biodata['status_nikah']))); ?>" readonly>
                                                    <label>TUNJANGAN</label>
                                                    <input type="text" class="form-control" value="<?php echo formatDuit(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek) * NilaiStatusRumpun($biodata['rumpun'])); ?>" readonly>
                                                </div>
                                                <div class="col-xs-4">
                                                    <?php
                                                    $tunjangan = GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek) * NilaiStatusRumpun($biodata['rumpun']);
                                                    if ($biodata['foto'] == '-' or $biodata['foto'] == '') {
                                                        if ($biodata['jk'] == 'L') {
                                                            $foto = 'img/laki.png';
                                                        } else {
                                                            $foto = 'img/perempuan.png';
                                                        }
                                                    } else {
                                                        $foto = "img/" . $biodata['foto'];
                                                    }
                                                    ?>
                                                    <img src="<?php echo $foto; ?>" style="align:center" class="img-rounded" width="350" height="350" alt="">
                                                    <br><br>
                                                </div>
                                                <div class="col-xs-16">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <th>UNSUR YANG DI NILAI</th>
                                            <th colspan="2">TOTAL CAPAIAN</th>
                                            <th>HASIL</th>
                                            <th>PENILAIAN</th>
                                        </thead>
                                        <tbody>
                                            <td>a). Nilai Kinerja / Capaian Kinerja</td>
                                            <td><?php echo NilKinerja($id, $bln_sebelumnya, $thn); ?>
                                            </td>
                                            <td> X &ensp;70%
                                            </td>
                                            <td><?php echo NilKinerja($id, $bln_sebelumnya, $thn) * 0.7; ?></td>
                                            <td>
                                                <div class="col-xs-8">
                                                    <!--<input type="number" class="form-control" value="<?php echo NilKinerja($id, $bln_sebelumnya, $thn) * 0.7; ?>" name="nskp" id="text1" onkeyup="sum();"  required="">-->
                                                    <?php
                                                    //cek cuti melahirkan
                                                    $cek_data_cuti_melahirkan = getOne("select tm_cuti.id_cuti from tm_cuti where tm_cuti.id_user='$biodata[id_user]' and tm_cuti.jenis_cuti='CUTI MELAHIRKAN' order by id_cuti DESC");
                                                    if ($cek_data_cuti_melahirkan != '') {
                                                        //                                                                    $aktif_cuti = date('Y-m-d', strtotime(substr($cek_data_cuti_melahirkan, 0, 10)));
                                                        $tmtcek1 = TanggalAwalBulanKemarin();
                                                        $mulai = getOne("select tanggal from tm_hari_cuti where id_cuti='$cek_data_cuti_melahirkan' order by tanggal ASC");
                                                        $selesai = getOne("select tanggal from tm_hari_cuti where id_cuti='$cek_data_cuti_melahirkan' order by tanggal DESC");
                                                        if (strtotime($tmtcek1) >= strtotime($mulai) and strtotime($tmtcek1) <= strtotime($selesai)) {
                                                    ?>
                                                            <label>Sedang Cuti Melahirkan</label>
                                                            <input type="number" class="form-control" id="txt1update<?php echo $id; ?>" value="20" name="nskp" min="0" max="70" onkeyup="sumupdate<?php echo $id; ?>();" required /></div>
                                            <?php } else { ?>
                                                <input type="number" class="form-control" id="txt1update<?php echo $id; ?>" value="<?php echo getOne("select nskp from tm_penilaian where Month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and Year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$id' "); ?>" name="nskp" min="0" max="70" onkeyup="sumupdate<?php echo $id; ?>();" required />
                                </div>
                            <?php
                                                        }
                                                    } else {
                            ?>
                            <input type="number" step="0.01" class="form-control" id="txt1update<?php echo $id; ?>" value="<?php echo $penilaian['nskp']; ?>" name="nskp" min="0" max="70" onkeyup="sumupdate<?php echo $id; ?>();" required />
                            </div>
                        <?php }
                        ?>
                        </td>
                        </tbody>
                        <tbody>
                            <td>b). Nilai Pendapatan / Nilai Capaian Pendapatan</td>
                            <td><?php echo $refrensi_penyerapan['penyerapan']; ?>
                            </td>
                            <td> X &ensp;20%
                            </td>
                            <td></td>
                            <td>
                                <div class="col-xs-8">
                                    <input type="number" step="0.01" id="txt2update<?php echo $id; ?>" class="form-control" value="<?php echo $refrensi_penyerapan['penyerapan'] * 0.2; ?>" name="npenyerapan" onkeyup="sumupdate<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                            <td>c). Nilai Disiplin + Nilai Kompetensi</td>
                            <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn)) . "+" . number_format(NilKomp($id, $bln_sebelumnya, $thn)); ?>
                            </td>
                            <td> X &ensp;10%
                            </td>
                            <td><?php echo number_format(NilDis($id, $bln_sebelumnya, $thn) + NilKomp($id, $bln_sebelumnya, $thn)) * 0.1; ?></td>
                            <td>
                                <div class="col-xs-8">
                                    <input type="number" step="0.01" id="txt3update<?php echo $id; ?>" class="form-control" value="<?php echo $penilaian['nprilaku']; ?>" name="nprilaku" onkeyup="sumupdate<?php echo $id; ?>();" min="0" max="10" required autofocus /></div>
                            </td>
                        </tbody>
                        <tbody>
                            <td>d). Nilai Pengembalian Kurang Bayar</td>
                            <td>
                                <!-- <?php echo $potongan_bank['potongan_bank']; ?> -->
                            </td>
                            <td> &ensp;Rp.
                            </td>
                            <td></td>
                            <td>
                                <!-- <?php echo $potongan_bank['potongan_bank'] ?> -->
                                <div class="col-xs-8">
                                    <input type="number" step="0.01" id="txt5update<?php echo $id; ?>" class="form-control" value="<?php if (is_null($potongan_bank['potongan_bank'])) {
                                      echo 0; } else { echo $potongan_bank['potongan_bank']; } ?>" name="npotonganbank" onkeyup="sumupdate<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                            <td>e). Nilai Potongan Lebih Bayar</td>
                            <td>
                                <!-- <?php echo $potongan_rs['potongan_rs']; ?> -->
                            </td>
                            <td> &ensp;Rp.
                            </td>
                            <td></td>
                            <td>
                                <!-- <?php echo $potongan_rs['potongan_rs'] ?> -->
                                <div class="col-xs-8">
                                    <input type="number" step="0.01" id="txt6update<?php echo $id; ?>" class="form-control" value="<?php if (is_null($potongan_rs['potongan_rs'])) {
                                      echo 0; } else { echo $potongan_rs['potongan_rs']; }?>" name="npotonganrs" onkeyup="sumupdate<?php echo $id; ?>();" readonly required />
                                </div>
                            </td>
                        </tbody>
                        <tbody>
                            <td colspan="4">
                                JUMLAH
                            </td>
                            <td>
                                <!-- cek -->
                                <input type="hidden" id="txt_hukdisupdate<?php echo $id; ?>" value="<?php echo NilaiHukDis($id); ?>" onkeyup="sumupdate<?php echo $id; ?>();" />
                                <input type="hidden" class="form-control" value="<?php echo GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek); ?>" name="gaji_pokok" readonly>
                                <input type="hidden" class="form-control" value="<?php echo GajiBruto(GajiPokokValidasi($biodata['pendidikan'], $biodata['tgl_masuk'], $tmtcek), NilaiStatusKawin($biodata['status_nikah'])); ?>" name="gaji_bruto" readonly>
                                <input type="hidden" id="txt_tunjanganupdate<?php echo $id; ?>" value="<?php echo $tunjangan; ?>" onkeyup="sumupdate<?php echo $id; ?>();" name="tunjangan" readonly />
                                <input type="hidden" class="form-control" value="<?php echo IdHukDis($id); ?>" name="id_sanksi" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['pajak']; ?>" name="pajak" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $nip; ?>" name="penilai" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" name="status_nikah" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_ks']; ?>" name="bpjs_ks" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_jkk']; ?>" name="bpjs_jkk" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_ijht']; ?>" name="bpjs_ijht" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $biodata['bpjs_jp']; ?>" name="bpjs_jp" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_s from tm_waktu_s where id_user='$id' and Month(tm_waktu_s.date_s)='$bln_sebelumnya' and Year(tm_waktu_s.date_s)='$thn' order by id_waktu_s desc limit 1"); ?>" name="id_waktu_s" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_k from tm_waktu_k where id_user='$id' and Month(tm_waktu_k.date_k)='$bln_sebelumnya' and Year(tm_waktu_k.date_k)='$thn' order by id_waktu_k desc limit 1"); ?>" name="id_waktu_k" readonly>
                                <input type="hidden" class="form-control" value="<?php echo getOne("select id_waktu_t from tm_waktu_t where id_user='$id' and Month(tm_waktu_t.date_t)='$bln_sebelumnya' and Year(tm_waktu_t.date_t)='$thn' order by id_waktu_t desc limit 1"); ?>" name="id_waktu_t" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $refrensi_penyerapan['id_penyerapan']; ?>" name="id_penyerapan" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $potongan_bank['id_potongan_p']; ?>" name="id_potongan_p" readonly>
                                <input type="hidden" class="form-control" value="<?php echo $potongan_rs['id_potongan_p']; ?>" name="id_potongan_p" readonly>
                                <input type="hidden" class="form-control" id="tunjangan-valupdate<?php echo $id; ?>" name="tunjangan_val" required />
                                <!-- tutup cek-->
                                <div class="col-xs-3">
                                    <label>Nilai Kinerja</label>
                                    <!--<input type="number" class="form-control"  name="jumlah" id="text4" readonly required="">-->
                                    <input type="number" class="form-control" name="jumlah" id="txt4update<?php echo $id; ?>" readonly required />
                                </div>
                                <div class="col-xs-8">
                                    <label>Di Rupiahkan</label>
                                    <input type="text" class="form-control" id="tunjanganupdate<?php echo $id; ?>" readonly required />
                                </div>
                            </td>
                        </tbody>
                        </table>

                        <script>
                            function convertToRupiah(angka) {
                                var rupiah = '';
                                var angkarev = angka.toString().split('').reverse().join('');
                                for (var i = 0; i < angkarev.length; i++)
                                    if (i % 3 == 0)
                                        rupiah += angkarev.substr(i, 3) + '.';
                                return 'Rp. ' + rupiah.split('', rupiah.length - 1).reverse().join('');
                            }

                            function sumupdate<?php echo $id; ?>() {
                                var txtFirstNumberupdate<?php echo $id; ?> = document.getElementById('txt1update<?php echo $id; ?>').value;
                                var txtSecondNumberupdate<?php echo $id; ?> = document.getElementById('txt2update<?php echo $id; ?>').value;
                                var txtTigaNumberupdate<?php echo $id; ?> = document.getElementById('txt3update<?php echo $id; ?>').value;
                                var txtEmpatNumberupdate<?php echo $id; ?> = document.getElementById('txt_tunjanganupdate<?php echo $id; ?>').value;
                                var txtLimaNumberupdate<?php echo $id; ?> = document.getElementById('txt_hukdisupdate<?php echo $id; ?>').value;
                                var txtEnamNumberupdate<?php echo $id; ?> = document.getElementById('txt5update<?php echo $id; ?>').value;
                                var txtTujuhNumberupdate<?php echo $id; ?> = document.getElementById('txt6update<?php echo $id; ?>').value;
                                var nilai_valupdate = parseFloat(txtFirstNumberupdate<?php echo $id; ?>) + parseFloat(txtSecondNumberupdate<?php echo $id; ?>) + parseFloat(txtTigaNumberupdate<?php echo $id; ?>);
                                var tunjanganupdate = ((nilai_valupdate * parseFloat(txtEmpatNumberupdate<?php echo $id; ?>) / 100) - ((nilai_valupdate * parseFloat(txtEmpatNumberupdate<?php echo $id; ?>) / 100) * parseFloat(txtLimaNumberupdate<?php echo $id; ?>)))
                                 // - (parseFloat(txtEnamNumberupdate<?php echo $id; ?>) + parseFloat(txtTujuhNumberupdate<?php echo $id; ?>));
                                if (!isNaN(nilai_valupdate)) {
                                    document.getElementById('txt4update<?php echo $id; ?>').value = nilai_valupdate;
                                    document.getElementById('tunjanganupdate<?php echo $id; ?>').value = convertToRupiah(tunjanganupdate.toFixed(0));
                                    document.getElementById('tunjangan-valupdate<?php echo $id; ?>').value = tunjanganupdate.toFixed(0);
                                }
                            }
                        </script>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </a>
                <input type="submit" class="btn btn-warning" value="Update">
            </div>
            </form>
            </div>
            </div>
            <!-- Tutup Modal waktu -->
        <?php
            break;

        case "list-validasi-pegawai":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST VALIDASI PEGAWAI </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- /.box-header -->
                    <?php
                    $no = 0;
                    $tmtcek = TanggalAkhirBulanKemarin();
                    $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));
                    if ($cek_hak_akses_validasi['menu_val_kasatpel'] == '1') {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto, IF(tm_level.nama_level LIKE '%PJ%',1,0) as PJ,tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.id_kasatpel='$kasatpel' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.id_unit,tm_pegawai.nama_pegawai asc");
                    }
                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1' && $cek_hak_akses_validasi['menu_val_kasatpel'] == '0' ) {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto, tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.id_unit LIKE '%$id_unit%' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.id_unit,tm_pegawai.nama_pegawai asc");
                    }
                    if ($cek_hak_akses_validasi['menu_val_kasie'] == '1') {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto , tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.sub_bagian='$kasie' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.id_unit,tm_pegawai.nama_pegawai asc");
                    }
                    while ($row = fetch_array($tm_pegawai)) {
                        $no++;
                        $logid = $row['id_user'];
                        $lognama = $row['nama_pegawai'];
                        $pj = $row['PJ'];
                    ?>
                        <div class="col-md-3">
                            <div class="box box-info box-solid">

                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $row['nama_pegawai']; ?></h3>
                                    <div class="box-tools pull-right">
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="center-block">
                                        <center>
                                            <?php
                                            if ($row['foto'] == '-' or $row['foto'] == '') {
                                                if ($row['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $row['foto'];
                                            }
                                            ?>
                                            <img src="<?php echo $foto; ?>" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
                                            <br>
                                            <br>
                                            <p><b><?php echo $row['nama_level']; ?></b></p>
                                        </center>
                                        <?php
                                        $cv_kinerja = getOne("select validasi from tt_kinerja where month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user='$row[id_user]' GROUP BY validasi ORDER BY validasi desc");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bln_sebelumnya' and year(tm_waktu_t.date_t)='$thn' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bln_sebelumnya' and year(tm_waktu_s.date_s)='$thn' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_kedisiplinan = getOne("select count(id_disiplin) from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bln_sebelumnya' and year(tm_kedisiplinan.date_d)='$thn' and tm_kedisiplinan.id_user='$row[id_user]'");
                                        $cv_kompetensi = getOne("select count(id_kompetensi) from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bln_sebelumnya' and year(tm_kompetensi.date_kompetensi)='$thn' and tm_kompetensi.id_user='$row[id_user]'");
                                        $cek_kevalidasian = $cv_waktu_penambahan + $cv_waktu_pengurangan + $cv_waktu_shift + $cv_kedisiplinan + $cv_kompetensi;
                                        $cek_tervalidasi_management = getOne("select id_penilaian from tm_penilaian where month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$row[id_user]'");
                                        ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <th>
                                                    <label>ABSENSI</label>
                                                </th>
                                                <th>
                                                    <label>VALIDASI</label>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-finger-<?php echo $logid;$lognama ?>" title="LOG ABSENSI" class="btn-xs btn-info fa fa-eye"></span>
                                                    <br>
                                                    <a target="_blank" href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="VIEW ABSENSI," class="btn-xs btn-info fa fa-list"></span></a>
                                                </td>
                                                <td>
                                                    <?php if ($cv_kinerja != 'Y') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-danger fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI BELUM"> </i>
                                                    </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-success fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI SELESAI"> </i>
                                                    </a>
                                                        <?php } ?>
                                                    <!-- validasi waktu -->
                                                    <br>
                                                    <?php
                                                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1' && $cek_hak_akses_validasi['menu_val_kasatpel'] == '0') {
                                                        if ($cv_waktu_penambahan != '0') {
                                                    ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT BELUM" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_potongan_pegawai != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI SELESAI" class="btn-xs btn-success fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI BELUM" class="btn-xs btn-danger fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <br>
                                                        <?php
                                                        if ($cv_waktu_penambahan != '0' ) { ?>                                                    
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0' ) { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi disiplin -->
                                                        <?php if ($cv_kedisiplinan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN SELESAI" class="btn-xs btn-success fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN BELUM" class="btn-xs btn-danger fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi kompetensi -->
                                                        <?php if ($cv_kompetensi != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI SELESAI" class="btn-xs btn-success fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI BELUM" class="btn-xs btn-danger fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } ?>

                                                        <!-- Hasil Validasi -->
                                                        <?php if ($cek_kevalidasian >= 5) { ?>
                                                            <?php
                                                            if ($hak_akses['menu_val_kasie'] == '1') {
                                                                if ($cek_tervalidasi_management == '') {
                                                                    $modal = 'hasil-validasi-management';
                                                                    $title = 'Hasil';
                                                                    $warna = 'danger';
                                                                } else {
                                                                    $modal = 'hasil-validasi-management-update';
                                                                    $title = 'Update Hasil';
                                                                    $warna = 'success';
                                                                }
                                                            } else {
                                                                $modal = 'hasil-validasi';
                                                                $title = 'Hasil';
                                                                $warna = 'danger';
                                                            }
                                                            ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=' . $modal . '&id=' . $row['id_user']); ?>">
                                                                <i title="<?php echo $title; ?>" class="btn-xs btn-<?php echo $warna; ?> fa fa-check-square-o"> <?php echo $title; ?></i>
                                                            </a>
                                                    <?php }
                                                    }
                                                    ?>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                       
                        <!--Modal view log -->
                        <div class="modal fade " id="modal-finger-<?php echo $logid;$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye"> LOG ABSENSI - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Absensi</th>
                                                                <th>Status</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $finger = bukaquery("SELECT user, tanggal, (case when log.status ='I' THEN 'IN' ELSE 'OUT' END) as status FROM `log` INNER JOIN tm_pegawai on tm_pegawai.log_finger = log.`user` where id_user = '$logid' and MONTH(tanggal) = '$bln_sebelumnya' and YEAR(tanggal) = '$thn' order by tanggal asc");
                                                            while ($row = fetch_array($finger)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['tanggal']; ?></td>
                                                                    <td><?php echo $row['status']; ?></td>
                                                                    
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal log -->
                    <?php
                    }
                    ?>
                    </tbody>
                </div>
            </div>
        <?php
            break;

        case "list-validasi-pjlp":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST VALIDASI PJLP </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    
                    <!-- /.box-header -->
                    <?php
                    $no = 0;
                    $tmtcek = TanggalAkhirBulanKemarin();
                    $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));
                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1') {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto, tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.status_pegawai='PJLP' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai");
                    }
                    if ($cek_hak_akses_validasi['menu_val_kasatpel'] == '1') {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto, IF(tm_level.nama_level LIKE '%PJ%',1,0) as PJ,tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.id_kasatpel='$kasatpel' and tm_pegawai.status_pegawai='PJLP' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by PJ,tm_unit.nama_unit asc");
                    }
                    if ($cek_hak_akses_validasi['menu_val_kasie'] == '1') {
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto , tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user!='$id_user' and tm_pegawai.sub_bagian='$kasie' and tm_pegawai.status_pegawai='PJLP' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit asc");
                    }
                    while ($row = fetch_array($tm_pegawai)) {
                        $no++;
                        $logid = $row['id_user'];
                        $lognama = $row['nama_pegawai'];
                        $pj = $row['PJ'];
                    ?>
                        <!--thumbinal display>-->
                        <?php
                        if ($row['jk'] == 'L') {
                            $warna = 'bg-aqua-active';
                        } elseif ($row['jk'] == 'P') {
                            $warna = 'bg-fuchsia-active';
                        } else {
                            $warna = 'bg-gray-light';
                        }
                        ?>

                        <!--<div class = "row">-->

                        <div class="col-md-3">
                            <div class="box box-info box-solid">

                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $row['nama_pegawai']; ?></h3>

                                    <div class="box-tools pull-right">
                                        <!--                                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="center-block">
                                        <center>
                                            <?php
                                            if ($row['foto'] == '-' or $row['foto'] == '') {
                                                if ($row['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $row['foto'];
                                            }
                                            ?>
                                            <!--<img src="<?php // echo $foto;                                           
                                                            ?>" style="align:center" class="img-rounded" width="250" height="250" alt="" >-->
                                            <img src="<?php echo $foto; ?>" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
                                            <br>
                                            <br>
                                            <!-- <p><br><label><?php echo $row['nama_pegawai']; ?></label></p> -->
                                            <!-- <?php echo $row['nip']; ?> -->
                                            <p><b><?php echo $row['nama_level']; ?></b></p>
                                        </center>
                                        <?php
                                        $cv_kinerja = getOne("select validasi from tt_kinerja where month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user='$row[id_user]' GROUP BY validasi ORDER BY validasi desc");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bln_sebelumnya' and year(tm_waktu_t.date_t)='$thn' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bln_sebelumnya' and year(tm_waktu_s.date_s)='$thn' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_kedisiplinan = getOne("select count(id_disiplin) from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bln_sebelumnya' and year(tm_kedisiplinan.date_d)='$thn' and tm_kedisiplinan.id_user='$row[id_user]'");
                                        $cv_kompetensi = getOne("select count(id_kompetensi) from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bln_sebelumnya' and year(tm_kompetensi.date_kompetensi)='$thn' and tm_kompetensi.id_user='$row[id_user]'");
                                        $cek_kevalidasian = $cv_waktu_penambahan + $cv_waktu_pengurangan + $cv_waktu_shift + $cv_kedisiplinan + $cv_kompetensi;
                                        $cek_tervalidasi_management = getOne("select id_penilaian from tm_penilaian where month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$row[id_user]'");
                                        ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>

                                                <th>
                                                    <label>ABSENSI</label>
                                                </th>
                                                <th>
                                                    <label>VALIDASI</label>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-finger-<?php echo $logid;$lognama ?>" title="LOG ABSENSI" class="btn-xs btn-info fa fa-eye"></span>
                                                    <br>
                                                    <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai1&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="VIEW ABSENSI" class="btn-xs btn-info fa fa-list"></span></a>
                                                </td>
                                                <td>
                                                    <!-- validasi waktu -->
                                                    <br>
                                                    <?php
                                                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1') {
                                                        if ($cv_waktu_penambahan != '0') {
                                                    ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php } else {
                                                        ?>
                                                        <br>
                                                        <?php
                                                        if ($cv_waktu_penambahan != '0' and $pj == '1') { ?>
                                                    
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                    
                                                        <!-- validasi disiplin -->
                                                        <?php if ($cv_kedisiplinan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN SELESAI" class="btn-xs btn-success fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN BELUM" class="btn-xs btn-danger fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi kompetensi -->
                                                        <?php if ($cv_kompetensi != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI SELESAI" class="btn-xs btn-success fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI BELUM" class="btn-xs btn-danger fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } ?>

                                                        <!-- Hasil Validasi -->
                                                        <?php if ($cek_kevalidasian >= 5) { ?>
                                                            <?php
                                                            if ($hak_akses['menu_val_kasie'] == '1') {
                                                                if ($cek_tervalidasi_management == '') {
                                                                    $modal = 'hasil-validasi-management';
                                                                    $title = 'Hasil';
                                                                    $warna = 'danger';
                                                                } else {
                                                                    $modal = 'hasil-validasi-management-update';
                                                                    $title = 'Update Hasil';
                                                                    $warna = 'success';
                                                                }
                                                            } else {
                                                                $modal = 'hasil-validasi';
                                                                $title = 'Hasil';
                                                                $warna = 'danger';
                                                            }
                                                            ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=' . $modal . '&id=' . $row['id_user']); ?>">
                                                                <i title="<?php echo $title; ?>" class="btn-xs btn-<?php echo $warna; ?> fa fa-check-square-o"> <?php echo $title; ?></i>
                                                            </a>
                                                    <?php }
                                                    }
                                                    ?>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                    

                        <!--Modal view log -->
                        <div class="modal fade " id="modal-finger-<?php echo $logid;$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye"> LOG ABSENSI - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Absensi</th>
                                                                <th>Status</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $finger = bukaquery("SELECT user, tanggal, (case when log.status ='I' THEN 'IN' ELSE 'OUT' END) as status FROM `log` INNER JOIN tm_pegawai on tm_pegawai.log_finger = log.`user` where id_user = '$logid' and MONTH(tanggal) = '$bln_sebelumnya' and YEAR(tanggal) = '$thn' order by tanggal asc");
                                                            while ($row = fetch_array($finger)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['tanggal']; ?></td>
                                                                    <td><?php echo $row['status']; ?></td>
                                                                    
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal log -->
                    <?php
                    }
                    ?>
                    </tbody>
                </div>
            </div>
        <?php
            break;

        case "list-validasi-kinerja-pegawai":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST DATA KINERJA PEGAWAI</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <?php
                     $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));

                     if ($cek_hak_akses_validasi['menu_val_pj'] == '1' or $cek_hak_akses_validasi['menu_val_kasatpel'] == '1') { ?>
                        <a href="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=validasi-all&id=' . $id . ''); ?>">
                                                        <i class="btn btn-success fa fa-check-square-o"> Validasi All</i>
                                                    </a>
                                                        <?php } else { ?>
                        <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-pegawai'); ?>">
                                                        <i class="btn btn-warning fa fa-undo"> Kembali</i>
                                                    </a>
                                                        <?php } ?>
                    <br>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="kinerja" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Nama SKP</th>
                                    <th>Uraian</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Durasi Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Volume</th>
                                    <th>Tgl. Buat</th>
                                    <th>Validasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                $tt_kinerja = bukaquery("SELECT tm_pegawai.nip,tm_pegawai.id_user,tm_pegawai.nama_pegawai, tm_skp.skp, tt_kinerja.id_kinerja, tt_kinerja.uraian, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir, tt_kinerja.date, tm_skp.waktu, tt_kinerja.kd_skp, tt_kinerja.tanggal_kinerja, tt_kinerja.`status`, tt_kinerja.validasi 
                                                    FROM tt_kinerja
                                                    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user
                                                    where Month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and Year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user=" . $id . " order by
                                                        tt_kinerja.validasi
                                                          DESC");
                                while ($row = fetch_array($tt_kinerja)) {
                                    $no++;
                                ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo FormatTgl('d-m-Y', $row['tanggal_kinerja']); ?></td>
                                        <td><?php echo $row['nama_pegawai']; ?></td>
                                        <td><?php echo $row['nip']; ?></td>
                                        <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                        <td><?php echo $row['uraian'] ?></td>
                                        <td><?php echo $row['waktu_mulai']; ?></td>
                                        <td><?php echo $row['waktu_akhir']; ?></td>
                                        <td><?php echo floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60) . " Menit"; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td><?php echo formatDec(floor(((is_string($row['waktu_mulai']) ? strtotime($row['waktu_akhir']) : $row['waktu_akhir']) - (is_string($row['waktu_mulai']) ? strtotime($row['waktu_mulai']) : $row['waktu_mulai'])) / 60) / $row['waktu']); ?></td>
                                        <td><?php echo FormatTgl('d/m/y H:i:s', $row['date']); ?></td>
                                        <td>
                                            <?php if ($row['validasi'] == 'T') { ?>
                                                <a href="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=validasi&id=' . $row['id_user'] . '-' . $row['id_kinerja']); ?>">
                                                    <i class="btn btn-danger"> Validasi</i>
                                                </a>
                                            <?php } else { ?>
                                                <i title="Tervalidasi" class="btn btn-success"> Sudah Tervalidasi</i>
                                                <a href="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=batal-validasi&id=' . $row['id_user'] . '-' . $row['id_kinerja']); ?>">
                                                    <i class="btn btn-warning"> Batalkan Validasi</i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                            <thead>
                                <th></th>
                                <th colspan='7'>Jumlah Menit Tervalidasi</th>
                                <th colspan="5"><?php
                                                if (getOne("select sum(TIMESTAMPDIFF(MINUTE,(waktu_mulai),(waktu_akhir))) as total_durasi from tt_kinerja where Month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and Year(tt_kinerja.tanggal_kinerja)='$thn' and id_user='$id' and validasi='Y' ") != '') {
                                                    echo getOne("select sum(TIMESTAMPDIFF(MINUTE,(waktu_mulai),(waktu_akhir))) as total_durasi from tt_kinerja where Month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and Year(tt_kinerja.tanggal_kinerja)='$thn' and id_user='$id' and validasi='Y' ") . " Menit";
                                                }
                                                ?> </th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <?php
            break;

        case "list-validasi-rekrutmen":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> VALIDASI REKRUTMEN</h3>
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
                                        <label>formasi = &nbsp&nbsp&nbsp&nbsp</label>
                                    </td>
                                    <td>
                                        <select class="form-control select2" name="id_formasi" data-placeholder="-Pilih Formasi-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Formasi-</option>
                                            <?php
                                            $tm_shift = bukaquery("select tm_formasi.id_formasi, tm_formasi.formasi, tm_formasi.pendidikan, tm_formasi.status from tm_formasi where tm_formasi.status='AKTIF' ");
                                            while ($row = fetch_array($tm_shift)) {
                                                echo "<option value=" . $row['id_formasi'] . ">" . $row['formasi'] . "&nbsp" . "(" . $row['pendidikan'] . ")" . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        <label>Provinsi/KL = &nbsp&nbsp&nbsp&nbsp</label>
                                    </td>
                                    <td><?php echo enumDropdown("tm_surat_masuk", "provinsi", "", "Provinsi/KL"); ?></td>
                                </tr> -->
                                <tr>
                                    <td>
                                        <label>Dari Tanggal = </label>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="datepicker1" placeholder="mm/dd/yyyy" name="date" required readonly>
                                        <?php //$cari = $_POST['tanggal_surat'];
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Ke Tanggal = </label>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="datepicker" placeholder="mm/dd/yyyy" name="date1" required readonly>
                                        <?php //$cari_ = $_POST['tanggal_surat'];
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp&nbsp&nbsp&nbsp</td>
                                    <td>
                                        <button type="submit" name="search" name="submit" value="submit" class="btn btn-info">Cari Data <i class="fa fa-search"></i></button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <br><br><br><br>
                    </form>
                    <br><br><br><br>
                    <hr>
                    <?php
                    $formasi_post = isset($_POST['id_formasi']) ? $_POST['id_formasi'] : null;
                    // $provinsi_post = isset($_POST['provinsi']) ? $_POST['provinsi'] : null;
                    $cari = Formattgl("Y-m-d", $_POST['date']);
                    $cari_ = FormatTgl('Y-m-d', $_POST['date1']);
                    if ($formasi_post == '' or $cari == 0 or $cari_ == 0) {
                    ?>
                        <div class="box-header with-border">
                            <h3 class="box-title fa fa-list"> <label align="center">Silakan Cari Data</label></h3>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                    <?php } else {
                    ?>
                        <div class="box-header with-border">
                            <h3 class="box-title fa fa-list"> <label align="center">Rekrutmen = <?php echo konversiTanggal($cari); ?></label></h3>
                            <h3 class="box-title fa "> <label align="center">Sampai <?php echo konversiTanggal($cari_); ?></label></h3>
                            <h3 class="box-title fa "> <label align="center">(Formasi Rekrutmen : <?php echo getOne("select Concat(tm_formasi.formasi,' ', tm_formasi.pendidikan,' ', tm_formasi.status) from tm_formasi where tm_formasi.status='AKTIF' and id_formasi='$formasi_post'"); ?></label>)</h3>
                            <!-- <h3 class="box-title fa "> <label align="center">(Provinsi/KL : <?php echo "$provinsi_post"; ?></label>)</h3> -->
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                    <?php }
                    ?>
                    <div class="box-body table-responsive">
                        <table id="laporan" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>ID REGISTRASI</th>
                                    <th>NIK</th>
                                    <th>NAMA PEGAWAI</th>
                                    <th>TEMPAT LAHIR</th>
                                    <th>TANGGAL LAHIR</th>
                                    <th>JENIS KELAMIN</th>
                                    <th>STATUS NIKAH</th>
                                    <th>AGAMA</th>
                                    <th>ALAMAT</th>
                                    <th>NO. TELEPON</th>
                                    <th>E-MAIL</th>
                                    <th>IPK / NILAI</th>
                                    <th>PENDIDIKAN TERAKHIR</th>
                                    <th>FORMASI</th>
                                    <th>FOTO</th>
                                    <th>SCAN RIWAYAT HIDUP</th>
                                    <th>SCAN KTP</th>
                                    <th>SCAN KK</th>
                                    <th>SCAN IJAZAH</th>
                                    <th>SCAN TRANSKRIP</th>
                                    <th>SCAN SERTIFIKAT</th>
                                    <th>SCAN SKCK</th>
                                    <th>SCAN SURAT</th>
                                    <th>SCAN SERTIFIKAT PROFESI</th>
                                    <th>TANGGAL BERAKHIR STR</th>
                                    <th>SCAN STR</th>
                                    <th>SCAN SURAT LAMARAN</th>
                                    <th>TANGGAL REGISTRASI</th>
                                    <th>ACTION</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                $status = 'eror';
                                if ($formasi_post == '' or $cari == 0 or $cari_ == 0) {
                                    $status = 'eror';
                                } else {
                                    $status = 'AKTIF';
                                }
                                $sql_pegawai = bukaquery("SELECT
                                tm_daftar.id_daftar,
                                tm_daftar.nik,
                                tm_daftar.nama_pegawai,
                                tm_daftar.tempat_lahir,
                                tm_daftar.tgl_lahir,
                                tm_daftar.jk,
                                tm_daftar.status_nikah,
                                tm_daftar.agama,
                                tm_daftar.alamat,
                                tm_daftar.no_hp,
                                tm_daftar.email,
                                tm_daftar.nilai,
                                tm_daftar.pendidikan,
                                tm_daftar.id_formasi,
                                tm_daftar.foto,
                                tm_daftar.riwayat,
                                tm_daftar.ktp,
                                tm_daftar.kk,
                                tm_daftar.ijazah,
                                tm_daftar.transkrip,
                                tm_daftar.sertifikat,
                                tm_daftar.skck,
                                tm_daftar.surat,
                                tm_daftar.sertifikat_profesi,
                                tm_daftar.tgl_str,
                                tm_daftar.str,
                                tm_daftar.lamaran,
                                tm_daftar.`status`,
                                tm_daftar.date,
                                tm_formasi.id_formasi,
                                tm_formasi.formasi,
                                tm_formasi.pendidikan
                                FROM
                                tm_daftar
                                INNER JOIN tm_formasi ON tm_daftar.id_formasi = tm_formasi.id_formasi
                                        where tm_daftar.id_formasi='$formasi_post' and
                                        tm_daftar.date BETWEEN '$cari' and '$cari_'  order by tm_daftar.date ASC");
                                while ($row = fetch_array($sql_pegawai)) {
                                    $no++;
                                ?>
                                    <!-- Edit Modal SKP -->
                                    <div class="modal fade" id="modal-ubah-<?php echo $row['id_daftar']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-master-data-daftar&id=' . $row['id_daftar'] . ''); ?>" method="POST" role="form">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">Update Data Daftar</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label>Id Registrasi</label>
                                                                <input type="text" class="form-control" name="id_daftar" value="<?php echo $row['id_daftar']; ?>" placeholder="Id Registrasi" readonly>
                                                                <label>Nama Pegawai</label>
                                                                <input type="text" class="form-control" name="nama_pegawai" value="<?php echo $row['nama_pegawai']; ?>" placeholder="Nama Pegawai" readonly>
                                                                <label class="control-label">Status<font color="red">*</font></label>
                                                                <?php echo enumDropdown("tm_daftar", "status", "", "-Pilih-"); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tutup Edit Modal permintaan -->
                                    <!-- Tutup Edit Modal permintaan -->
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $row['id_daftar']; ?></td>
                                        <td><?php echo $row['nik']; ?></td>
                                        <td><?php echo $row['nama_pegawai']; ?></td>
                                        <td><?php echo $row['tempat_lahir']; ?></td>
                                        <td><?php echo $row['tgl_lahir']; ?></td>
                                        <td><?php echo $row['jk']; ?></td>
                                        <td><?php echo $row['status_nikah']; ?></td>
                                        <td><?php echo $row['agama']; ?></td>
                                        <td><?php echo $row['alamat'] ?></td>
                                        <td><?php echo $row['no_hp'] ?></td>
                                        <td><?php echo $row['email'] ?></td>
                                        <td><?php echo $row['nilai'] ?></td>
                                        <td><?php echo $row['pendidikan'] ?></td>
                                        <td><?php echo $row['formasi'] ?></td>
                                        <td><a href="files/daftar/<?php echo $row['foto']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['riwayat']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['ktp']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['kk']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['ijazah']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['transkrip']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['sertifikat']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['skck']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['surat']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['sertifikat_profesi']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><?php echo $row['tgl_str']; ?></td>
                                        <td><a href="files/daftar/<?php echo $row['str']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><a href="files/daftar/<?php echo $row['lamaran']; ?>" target="_blank"><span class="btn btn-xs fa fa-file-pdf-o"></span></a></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td>
                                            <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_daftar']; ?>" title="Ubah" class="btn-xs btn-warning"> <?php echo $row['status'] ?></span>
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

        case "list-validasi-cuti":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST MASTER DATA CUTI PEGAWAI</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-cuti">
                        Buat Master Data Cuti Tahunan
                    </button>
                    <button type="button" class="btn btn-danger fa fa-plus" data-toggle="modal" data-target="#modal-add-cuti-tambah">
                        Buat Master Data Cuti Tambahan
                    </button>
                    <form action="" method="post">
                        <div class="form-group">
                            <table>
                                <tr>
                                    <td>
                                        <label style="text-align: center">Tahun</label>
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
                    <!--Modal Add SKP -->
                    <div class="modal fade" id="modal-add-cuti">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title fa fa-plus"> Tambah Data Cuti</h4>
                                </div>
                                <form role="form" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=add-master-data-cuti'); ?>" method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <!-- <div class="col-md-4"> -->
                                            <label>Tahun</label>
                                            <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                                <?php loadThnKondisi(date('Y') + 1); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Pilih Pegawai</label>
                                            <select class="form-control select2" name="id_user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                                <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                <?php
                                                $tm_pegawai1 = bukaquery("select tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF'");
                                                while ($row1 = fetch_array($tm_pegawai1)) {
                                                    echo "<option value=" . $row1['id_user'] . ">" . $row1['nama_pegawai'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <label>Jumlah Cuti</label>
                                        <input type="number" class="form-control" name="sisa_cuti" placeholder="Jumlah Cuti" required>
                                        <!-- </div> -->
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><br><br><br><br><br></label>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Simpan">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- tambah -->
                    <div class="modal fade" id="modal-add-cuti-tambah">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title fa fa-plus"> Tambah Data Cuti</h4>
                                </div>
                                <form role="form" action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=add-master-data-cuti-tambah'); ?>" method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <!-- <div class="col-md-4"> -->
                                            <label>Tahun</label>
                                            <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                                <?php loadThnKondisi(date('Y') + 1); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Pilih Pegawai</label>
                                            <select class="form-control select2" name="id_user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                                <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                                <?php
                                                $tm_pegawai1 = bukaquery("select tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status='AKTIF'");
                                                while ($row1 = fetch_array($tm_pegawai1)) {
                                                    echo "<option value=" . $row1['id_user'] . ">" . $row1['nama_pegawai'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <label>Jumlah Cuti</label>
                                        <input type="number" class="form-control" name="sisa_cuti_tambah" placeholder="Jumlah Cuti" required>
                                        <!-- </div> -->
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><br><br><br><br><br></label>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Simpan">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Tutup Modal Cuti -->
                    <?php
                    $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
                    $thn_now = $tahun . "-";
                    if ($tahun_post == 'Tidak Sesuai' or $tahun_post == '') {
                    ?>
                        <div class="box-header with-border">
                            <h3 class="box-title fa fa-list"> <label>Tahun Tidak Sesuai</label></h3>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                    <?php } else {
                    ?>
                        <div class="box-header with-border">
                            <h3 class="box-title fa fa-list"> <label align="center"><?= $kategori; ?> Cuti Pegawai Tahun <?= $tahun_post; ?></label></h3>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                    <?php }
                    ?>
                    <!-- /.box-header -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab"> MASTER CUTI TAHUNAN</a></li>
                            <li><a href="#tab_2" data-toggle="tab"> MASTER CUTI TAMBAHAN</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="box-body table-responsive">
                                    <table id="example_normal" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Sisa Cuti</th>
                                                <th>Tahun</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $no = 0;
                                        $sql_cuti = bukaquery("select
                                        tm_validasi_cuti.id_validasi_cuti,
                                        tm_pegawai.nip,
                                        tm_pegawai.nama_pegawai,
                                        tm_validasi_cuti.sisa_cuti,
                                        tm_validasi_cuti.tahun
                                        from 
                                        tm_validasi_cuti
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_validasi_cuti.id_user
                                        where tm_validasi_cuti.tahun='$tahun_post'
                                        order by tm_validasi_cuti.tahun asc");
                                        while ($row = fetch_array($sql_cuti)) {
                                            $no++;
                                        ?>
                                            <!-- Edit Modal SKP -->
                                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_validasi_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-master-data-cuti&id=' . $row['id_validasi_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Update Data Cuti Pegawai</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <label>NIP</label>
                                                                        <input type="text" class="form-control" name="nip" placeholder="" value="<?php echo $row['nip']; ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label>Nama Pegawai</label>
                                                                        <input type="text" class="form-control" name="nama" placeholder="" value="<?php echo $row['nama_pegawai']; ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Jumlah Cuti</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['sisa_cuti']; ?>" name="sisa_cuti" placeholder="Masukan sisa cuti" required>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Tahun</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['tahun']; ?>" name="tahun" placeholder="Masukan tahun cuti" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tutup Edit Modal permintaan -->
                                            <!-- Edit Modal SKP -->
                                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_validasi_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=delete-master-data-cuti&id=' . $row['id_validasi_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Hapus Master Data Cuti Pegawai</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <text>Yakin Anda ingin Menghapus Data ini? klik Tombol Ya Untuk Menghapusnya</text>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                                <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tutup Edit Modal permintaan -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo $row['nip']; ?></td>
                                                <td><?php echo $row['nama_pegawai']; ?></td>
                                                <td><?php echo $row['sisa_cuti']; ?></td>
                                                <td><?php echo $row['tahun']; ?></td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_validasi_cuti']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_validasi_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_2">
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Sisa Cuti Tambahan</th>
                                                <th>Tahun</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $no = 0;
                                        $sql_cuti = bukaquery("select
                                        tm_validasi_cuti_tambah.id_validasi_cuti,
                                        tm_pegawai.nip,
                                        tm_pegawai.nama_pegawai,
                                        tm_validasi_cuti_tambah.sisa_cuti_tambah,
                                        tm_validasi_cuti_tambah.tahun
                                        from 
                                        tm_validasi_cuti_tambah
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_validasi_cuti_tambah.id_user
                                        where tm_validasi_cuti_tambah.tahun='$tahun_post'
                                        order by tm_validasi_cuti_tambah.tahun asc");
                                        while ($row = fetch_array($sql_cuti)) {
                                            $no++;
                                        ?>
                                            <!-- Edit Modal SKP -->
                                            <div class="modal fade" id="modal-ubah-tambah-<?php echo $row['id_validasi_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=update-master-data-cuti-tambah&id=' . $row['id_validasi_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Update Data Cuti Pegawai</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <label>NIP</label>
                                                                        <input type="text" class="form-control" name="nip" placeholder="" value="<?php echo $row['nip']; ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label>Nama Pegawai</label>
                                                                        <input type="text" class="form-control" name="nama" placeholder="" value="<?php echo $row['nama_pegawai']; ?>" readonly>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Jumlah Cuti</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['sisa_cuti_tambah']; ?>" name="sisa_cuti_tambah" placeholder="Masukan sisa cuti" required>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p></p>
                                                                        <label>Tahun</label>
                                                                        <input type="number" class="form-control" value="<?php echo $row['tahun']; ?>" name="tahun" placeholder="Masukan tahun cuti" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tutup Edit Modal permintaan -->
                                            <!-- Edit Modal SKP -->
                                            <div class="modal fade" id="modal-hapus-tambah-<?php echo $row['id_validasi_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=validasi-pegawai&act=delete-master-data-cuti-tambah&id=' . $row['id_validasi_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Hapus Master Data Cuti Pegawai</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <text>Yakin Anda ingin Menghapus Data ini? klik Tombol Ya Untuk Menghapusnya</text>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                                <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tutup Edit Modal permintaan -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo $row['nip']; ?></td>
                                                <td><?php echo $row['nama_pegawai']; ?></td>
                                                <td><?php echo $row['sisa_cuti_tambah']; ?></td>
                                                <td><?php echo $row['tahun']; ?></td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-ubah-tambah-<?php echo $row['id_validasi_cuti']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                                    <span data-toggle="modal" data-target="#modal-hapus-tambah-<?php echo $row['id_validasi_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
                    </div>                    
                </div>
            </div>
        <?php
            break;

        case "upload-data-cuti";
            $year = isset($_POST['tahun']) ? $_POST['tahun'] : null;
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-upload"> UPLOAD DATA CUTI</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class=" col-md-6">
                        <form method="post" action="" enctype="multipart/form-data"">                                
                            <div class=" form-group">
                            <label>Import excel :</label>
                            <div class="input-group float-right">
                                <input type="file" name="file" class="form-control">
                                <span class="input-group-btn">
                                    <button type="submit" name="preview" id="search-btn" class="btn btn-info btn-flat"><i class="glyphicon glyphicon-eye-open"> Preview</i>
                                    </button>
                                </span>
                            </div>
                    </div>
                    </form>
                </div>
                <div class="col-md-6 pull-right">
                    <a href="format_upload_cuti.php" target="Blank" class="btn btn-default" style="float:right">
                        <span class="glyphicon glyphicon-download"></span>
                        Download Format
                    </a>
                </div>
                <div class="col-md-12 pull-right">
                    <form action="" method="post">
                        <div class="form-group">
                            <table>
                                <tr>
                                    <td>
                                        <label>Tahun</label>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                            <?php loadThnnow($year); ?>
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
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Maping Data Cuti</h4>
                            </div>
                            <form role="form" name="pilihan" method="post" id="form-add">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Ditujukan Kepegawai</label>
                                        <select class="form-control select2" name="id_user" data-placeholder="-Pilih Nama Pegawai-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama Pegawai-</option>
                                            <?php
                                            $tm_pegawai = bukaquery("select tm_pegawai.id_user, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.status='AKTIF' ");
                                            while ($row = fetch_array($tm_pegawai)) {
                                                echo "<option value=" . $row['id_user'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label class="control-label">Tahun</label>
                                        <input type="text" name="tahun" class="form-control" value="<?= $year ?>" readonly>
                                        <label class="control-label">Jumlah Hari</label>
                                        <input type="number" name="jumlah" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->

                <!-- Edit Modal SKP -->
                <div class="modal fade" id="modal-update" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" role="form" id="form-update">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Update Map Cuti</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control id" name="token" required>
                                            <label class="control-label"></label>
                                            <label class="control-label">nama Pegawai</label>
                                            <input type="text" name="nama" class="form-control nama" required>
                                            <label class="control-label">Tahun</label>
                                            <input type="text" class="form-control tahun" name="tahun" readonly>
                                            <label class="control-label">Jumlah</label>
                                            <input type="text" class="form-control jumlah" name="jumlah" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Edit Modal permintaan -->
            </div>
            <?php
            // Jika user telah mengklik tombol Preview
            if (isset($_POST['preview'])) {
                //$ip = ; // Ambil IP Address dari User
                $nama_file_baru = 'data.xlsx';

                // Cek apakah terdapat file data.xlsx pada folder tmp
                if (is_file('tmp/' . $nama_file_baru)) // Jika file tersebut ada
                    unlink('tmp/' . $nama_file_baru); // Hapus file tersebut

                $tipe_file = $_FILES['file']['type']; // Ambil tipe file yang akan diupload
                $tmp_file = $_FILES['file']['tmp_name'];

                // Cek apakah file yang diupload adalah file Excel 2007 (.xlsx)
                if ($tipe_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                    // Upload file yang dipilih ke folder tmp
                    // dan rename file tersebut menjadi data{ip_address}.xlsx
                    // {ip_address} diganti jadi ip address user yang ada di variabel $ip
                    // Contoh nama file setelah di rename : data127.0.0.1.xlsx
                    move_uploaded_file($tmp_file, 'tmp/' . $nama_file_baru);

                    // Load librari PHPExcel nya
                    require_once 'assets/libs/PHPExcel/PHPExcel.php';

                    $excelreader = new PHPExcel_Reader_Excel2007();
                    $loadexcel = $excelreader->load('tmp/' . $nama_file_baru); // Load file yang tadi diupload ke folder tmp
                    $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

                    // Buat sebuah tag form untuk proses import data ke database
                    $a = $aksi . paramEncrypt("module=master-data&act=upload-data-cuti");
                    echo "<form method='post' action=" . $a . ">";

                    // Buat sebuah div untuk alert validasi kosong
                    echo "<div class='alert alert-danger' id='kosong'>
                                        Semua data belum diisi, Ada <span id='jumlah_kosong'></span> data yang belum diisi.
                                  </div>";

                    echo "<div class='box-body'>
                                            <div class='box-body table-responsive'>
                                        <table class='table table-bordered table-striped'>
                                        <tr>
                                            <th colspan='26' class='text-center'>Preview Data</th>
                                        </tr>
                                        <tr>
                                            <th>Id User</th>
                                                                    <th>Nama Pegawai</th>
                                                                    <th>Tahun</th>
                                                                    <th>Jumlah</th>
                                        </tr>";

                    $numrow = 0;
                    $kosong = 0;
                    foreach ($sheet as $row) { // Lakukan perulangan dari data yang ada di excel
                        $numrow++; // Tambah 1 setiap kali looping
                        // Ambil data pada excel sesuai Kolom
                        $id_user = $row['A']; // Ambil data id
                        $nama = $row['B'];
                        $tahun = $row['C']; // Ambil data nip
                        $jumlah = $row['D'];
                        // Cek jika semua data tidak diisi
                        if (empty($id_user) && empty($tahun) && empty($jumlah))
                            continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)                       
                        // Cek $numrow apakah lebih dari 1
                        // Artinya karena baris pertama adalah nama-nama kolom
                        // Jadi dilewat saja, tidak usah diimport
                        if ($numrow > 1) {
                            // Validasi apakah semua data telah diisi
                            $id_user_td = (!empty($id_user)) ? "" : " style='background: #E07171;'"; // Jika kosong, beri warna merah
                            $tahun_td = (!empty($tahun)) ? "" : " style='background: #E07171;'";
                            $jumlah_td = (!empty($jumlah)) ? "" : " style='background: #E07171;'";

                            // Jika salah satu data ada yang kosong
                            if (empty($id_user) && empty($tahun) && empty($jumlah)) {
                                $kosong++; // Tambah 1 variabel $kosong
                            }
                            echo "<tr>";
                            echo "<td" . $id_user_td . ">" . $id_user . "</td>";
                            echo "<td>" . $nama . "</td>";
                            echo "<td" . $tahun_td . ">" . $tahun . "</td>";
                            echo "<td" . $jumlah_td . ">" . $jumlah . "</td>";
                            echo "</tr>";
                        }
                    }

                    echo "</table> "
                        . "</div>";

                    // Cek apakah variabel kosong lebih dari 1
                    // Jika lebih dari 1, berarti ada data yang masih kosong
                    if ($kosong > 1) {
            ?>
                        <script>
                            $(document).ready(function() {
                                // Ubah isi dari tag span dengan id jumlah_kosong dengan isi dari variabel kosong
                                $("#jumlah_kosong").html('<?php echo $kosong; ?>');
                                $("#kosong").show(); // Munculkan alert validasi kosong
                            });
                        </script>
            <?php
                    } else { // Jika semua data sudah diisi
                        echo "<hr>";
                        // Buat sebuah tombol untuk mengimport data ke database
                        echo "<div class='form-group'><button type='submit' name='import' class='btn btn-primary'><span class='glyphicon glyphicon-upload'></span> Import</button></div>";
                    }

                    echo "</form></div>";
                } else { // Jika file yang diupload bukan File Excel 2007 (.xlsx)
                    // Munculkan pesan validasi
                    echo "<div class='box-body'><div class='alert alert-danger'>
                                        Hanya File Excel 2007 (.xlsx) yang diperbolehkan
                                        </div></div>";
                }
            }
            ?>
            </div>
            <?php if ($year != null) { ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> Data Maping Cuti <?= $year; ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="btn-group ">
                            <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add">
                                Buat Maping Cuti
                            </button>
                        </div>
                        <div class="box-body table-responsive">
                            <table id="table-data-cuti" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Tahun</th>
                                        <th>Jumlah</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    $('#modal-update').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget)
                        var token = button.data('whatever')
                        $('.token').val(token);
                        $.ajax({
                            url: '<?= $aksi . paramEncrypt('act=api-get-map-cuti'); ?>', //url target
                            type: 'POST', //tipe
                            data: {
                                token: token
                            } //mengambil semua data dari form
                        }).done(function(data) {
                            var json = data;
                            var obj = JSON.parse(json);
                            $('.id').val(obj.id);
                            $('.nama').val(obj.nama);
                            $('.jumlah').val(obj.jumlah);
                            $('.tahun').val(obj.tahun);
                        })
                    })

                    //simpan form 
                    $('#form-update').submit(function(e) { //submit form dengan id reg-form
                        e.preventDefault(); //Prevent Default Submission
                        $.ajax({
                            url: '<?= $aksi . paramEncrypt("module=validasi-pegawai&act=update-map-cuti"); ?>', //url target
                            type: 'POST', //tipe
                            data: $(this).serialize(), //mengambil semua data dari form
                        }).done(function(data) {
                            var json = data;
                            var obj = JSON.parse(json);
                            if (obj.state == 'success') {
                                Toast.fire({
                                    type: 'success',
                                    title: obj.massage
                                })
                                setTimeout(function() { // wait for 5 secs(2)
                                    location.reload(); // then reload the page.(3)
                                }, 1500);
                            } else {
                                Toast.fire({
                                    type: 'error',
                                    title: obj.massage
                                })
                            }
                        })
                    });

                    //simpan form
                    $('#form-add').submit(function(e) { //submit form dengan id reg-form
                        e.preventDefault(); //Prevent Default Submission
                        $.ajax({
                            url: '<?= $aksi . paramEncrypt('module=validasi-pegawai&act=add-maping-cuti'); ?>', //url target
                            type: 'POST', //tipe
                            data: $(this).serialize() //mengambil semua data dari form
                        }).done(function(data) {
                            var json = data;
                            var obj = JSON.parse(json);
                            if (obj.state == 'success') {
                                Toast.fire({
                                    type: 'success',
                                    title: obj.massage
                                })
                                setTimeout(function() { // wait for 5 secs(2)
                                    location.reload(); // then reload the page.(3)
                                }, 1500);
                            } else {
                                Toast.fire({
                                    type: 'error',
                                    title: obj.massage
                                })
                            }
                        });
                    });


                });
            </script>
<?php
            break;


        case "list-validasi-yanmed":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST VALIDASI PEGAWAI YANMED </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- /.box-header -->
                    <?php
                    $no = 0;
                    $tmtcek = TanggalAkhirBulanKemarin();
                    $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto , tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.sub_bagian='Pelayanan Medis dan Keperawatan' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit asc");
                    
                    while ($row = fetch_array($tm_pegawai)) {
                        $no++;
                        $logid = $row['id_user'];
                        $lognama = $row['nama_pegawai'];
                        $pj = $row['PJ'];
                    ?>
                        <!--thumbinal display>-->
                        <?php
                        if ($row['jk'] == 'L') {
                            $warna = 'bg-aqua-active';
                        } elseif ($row['jk'] == 'P') {
                            $warna = 'bg-fuchsia-active';
                        } else {
                            $warna = 'bg-gray-light';
                        }
                        ?>

                        <!--<div class = "row">-->

                        <div class="col-md-3">
                            <div class="box box-info box-solid">

                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $row['nama_pegawai']; ?></h3>

                                    <div class="box-tools pull-right">
                                        <!--                                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="center-block">
                                        <center>
                                            <?php
                                            if ($row['foto'] == '-' or $row['foto'] == '') {
                                                if ($row['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $row['foto'];
                                            }
                                            ?>
                                            <!--<img src="<?php // echo $foto;                                           
                                                            ?>" style="align:center" class="img-rounded" width="250" height="250" alt="" >-->
                                            <img src="<?php echo $foto; ?>" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
                                            <br>
                                            <br>
                                            <!-- <p><br><label><?php echo $row['nama_pegawai']; ?></label></p> -->
                                            <!-- <?php echo $row['nip']; ?> -->
                                            <p><b><?php echo $row['nama_level']; ?></b></p>
                                        </center>
                                        <?php
                                        $cv_kinerja = getOne("select validasi from tt_kinerja where month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user='$row[id_user]' GROUP BY validasi ORDER BY validasi desc");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bln_sebelumnya' and year(tm_waktu_t.date_t)='$thn' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bln_sebelumnya' and year(tm_waktu_s.date_s)='$thn' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_kedisiplinan = getOne("select count(id_disiplin) from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bln_sebelumnya' and year(tm_kedisiplinan.date_d)='$thn' and tm_kedisiplinan.id_user='$row[id_user]'");
                                        $cv_kompetensi = getOne("select count(id_kompetensi) from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bln_sebelumnya' and year(tm_kompetensi.date_kompetensi)='$thn' and tm_kompetensi.id_user='$row[id_user]'");
                                        $cek_kevalidasian = $cv_waktu_penambahan + $cv_waktu_pengurangan + $cv_waktu_shift + $cv_kedisiplinan + $cv_kompetensi;
                                        $cek_tervalidasi_management = getOne("select id_penilaian from tm_penilaian where month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$row[id_user]'");
                                        ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <th>
                                                    <label>TARGET SKP</label>
                                                </th>
                                                <th>
                                                    <label>ABSENSI</label>
                                                </th>
                                                <th>
                                                    <label>VALIDASI</label>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" title="SKP TAHUNAN" class="btn-xs btn-info fa fa-bullseye"></span>
                                                </td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-finger-<?php echo $logid;$lognama ?>" title="LOG ABSENSI" class="btn-xs btn-info fa fa-eye"></span>
                                                    <br>
                                                    <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai1&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="VIEW ABSENSI" class="btn-xs btn-info fa fa-list"></span></a>
                                                </td>
                                                <td>
                                                    <?php if ($cv_kinerja != 'Y') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-danger fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI BELUM"> </i>
                                                    </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-success fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI SELESAI"> </i>
                                                    </a>
                                                        <?php } ?>
                                                    <!-- validasi waktu -->
                                                    <br>
                                                    <?php
                                                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1') {
                                                        if ($cv_waktu_penambahan != '0') {
                                                    ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT BELUM" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_potongan_pegawai != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI SELESAI" class="btn-xs btn-success fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI BELUM" class="btn-xs btn-danger fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <br>
                                                        <?php
                                                        if ($cv_waktu_penambahan != '0' and $pj == '1') { ?>
                                                    
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi disiplin -->
                                                        <?php if ($cv_kedisiplinan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN SELESAI" class="btn-xs btn-success fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN BELUM" class="btn-xs btn-danger fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi kompetensi -->
                                                        <?php if ($cv_kompetensi != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI SELESAI" class="btn-xs btn-success fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI BELUM" class="btn-xs btn-danger fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } ?>

                                                        <!-- Hasil Validasi -->
                                                        <?php if ($cek_kevalidasian >= 5) { ?>
                                                            <?php
                                                            if ($hak_akses['menu_val_kasie'] == '1') {
                                                                if ($cek_tervalidasi_management == '') {
                                                                    $modal = 'hasil-validasi-management';
                                                                    $title = 'Hasil';
                                                                    $warna = 'danger';
                                                                } else {
                                                                    $modal = 'hasil-validasi-management-update';
                                                                    $title = 'Update Hasil';
                                                                    $warna = 'success';
                                                                }
                                                            } else {
                                                                $modal = 'hasil-validasi';
                                                                $title = 'Hasil';
                                                                $warna = 'danger';
                                                            }
                                                            ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=' . $modal . '&id=' . $row['id_user']); ?>">
                                                                <i title="<?php echo $title; ?>" class="btn-xs btn-<?php echo $warna; ?> fa fa-check-square-o"> <?php echo $title; ?></i>
                                                            </a>
                                                    <?php }
                                                    }
                                                    ?>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <!--Modal view SKp Tahunan -->
                        <div class="modal fade " id="modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye">SKP TAHUNAN - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>NIP</th>
                                                                <th>Nama SKP</th>
                                                                <th>Nama Bagian</th>
                                                                <th>Kuantitas <br>(Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kuantitas <br>(Pencapaian)</p>
                                                                </th>
                                                                <th>Kualitas <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kualitas <br> (Pencapaian)</p>
                                                                </th>
                                                                <th>Waktu <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Waktu <br> (Pencapaian)</p>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $user = $row['id_user'];
                                                            $no = 0;
                                                            $tt_skptahunan = bukaquery("SELECT tm_pegawai.nip, tm_unit.nama_unit, tm_skp.skp, tt_skptahunan.id_skp, tt_skptahunan.kd_skp, tt_skptahunan.kuantitas, tt_skptahunan.tgl_buat, tt_skptahunan.tgl_update, tm_skp.waktu
                                                        FROM tt_skptahunan
                                                        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
                                                        INNER JOIN tm_pegawai ON tt_skptahunan.id_user = tm_pegawai.id_user
                                                        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                        where tm_pegawai.id_user='$user'");
                                                            while ($row = fetch_array($tt_skptahunan)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['nip']; ?></td>
                                                                    <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                                    <td><?php echo $row['kuantitas']; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'"); ?></td>
                                                                    <td>100%</td>
                                                                    <td><?php echo number_format((getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") / $row['kuantitas'] * 100), 2) . "%"; ?></td>
                                                                    <td><?php echo $row['kuantitas'] * $row['waktu'] . " Menit"; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") * $row['waktu'] . " Menit"; ?></td>
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal skp tahunan -->

                        <!--Modal view log -->
                        <div class="modal fade " id="modal-finger-<?php echo $logid;$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye"> LOG ABSENSI - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Absensi</th>
                                                                <th>Status</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $finger = bukaquery("SELECT user, tanggal, (case when log.status ='I' THEN 'IN' ELSE 'OUT' END) as status FROM `log` INNER JOIN tm_pegawai on tm_pegawai.log_finger = log.`user` where id_user = '$logid' and MONTH(tanggal) = '$bln_sebelumnya' and YEAR(tanggal) = '$thn' order by tanggal asc");
                                                            while ($row = fetch_array($finger)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['tanggal']; ?></td>
                                                                    <td><?php echo $row['status']; ?></td>
                                                                    
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal log -->
                    <?php
                    }
                    ?>
                    </tbody>
                </div>
            </div>
        <?php
            break;

        case "list-validasi-penunjang":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST VALIDASI PEGAWAI PENUNJANG </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- /.box-header -->
                    <?php
                    $no = 0;
                    $tmtcek = TanggalAkhirBulanKemarin();
                    $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto , tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.sub_bagian='Penunjang' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit asc");
                    
                    while ($row = fetch_array($tm_pegawai)) {
                        $no++;
                        $logid = $row['id_user'];
                        $lognama = $row['nama_pegawai'];
                        $pj = $row['PJ'];
                    ?>
                        <!--thumbinal display>-->
                        <?php
                        if ($row['jk'] == 'L') {
                            $warna = 'bg-aqua-active';
                        } elseif ($row['jk'] == 'P') {
                            $warna = 'bg-fuchsia-active';
                        } else {
                            $warna = 'bg-gray-light';
                        }
                        ?>

                        <!--<div class = "row">-->

                        <div class="col-md-3">
                            <div class="box box-info box-solid">

                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $row['nama_pegawai']; ?></h3>

                                    <div class="box-tools pull-right">
                                        <!--                                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="center-block">
                                        <center>
                                            <?php
                                            if ($row['foto'] == '-' or $row['foto'] == '') {
                                                if ($row['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $row['foto'];
                                            }
                                            ?>
                                            <!--<img src="<?php // echo $foto;                                           
                                                            ?>" style="align:center" class="img-rounded" width="250" height="250" alt="" >-->
                                            <img src="<?php echo $foto; ?>" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
                                            <br>
                                            <br>
                                            <!-- <p><br><label><?php echo $row['nama_pegawai']; ?></label></p> -->
                                            <!-- <?php echo $row['nip']; ?> -->
                                            <p><b><?php echo $row['nama_level']; ?></b></p>
                                        </center>
                                        <?php
                                        $cv_kinerja = getOne("select validasi from tt_kinerja where month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user='$row[id_user]' GROUP BY validasi ORDER BY validasi desc");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bln_sebelumnya' and year(tm_waktu_t.date_t)='$thn' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bln_sebelumnya' and year(tm_waktu_s.date_s)='$thn' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_kedisiplinan = getOne("select count(id_disiplin) from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bln_sebelumnya' and year(tm_kedisiplinan.date_d)='$thn' and tm_kedisiplinan.id_user='$row[id_user]'");
                                        $cv_kompetensi = getOne("select count(id_kompetensi) from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bln_sebelumnya' and year(tm_kompetensi.date_kompetensi)='$thn' and tm_kompetensi.id_user='$row[id_user]'");
                                        $cek_kevalidasian = $cv_waktu_penambahan + $cv_waktu_pengurangan + $cv_waktu_shift + $cv_kedisiplinan + $cv_kompetensi;
                                        $cek_tervalidasi_management = getOne("select id_penilaian from tm_penilaian where month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$row[id_user]'");
                                        ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <th>
                                                    <label>TARGET SKP</label>
                                                </th>
                                                <th>
                                                    <label>ABSENSI</label>
                                                </th>
                                                <th>
                                                    <label>VALIDASI</label>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" title="SKP TAHUNAN" class="btn-xs btn-info fa fa-bullseye"></span>
                                                </td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-finger-<?php echo $logid;$lognama ?>" title="LOG ABSENSI" class="btn-xs btn-info fa fa-eye"></span>
                                                    <br>
                                                    <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai1&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="VIEW ABSENSI" class="btn-xs btn-info fa fa-list"></span></a>
                                                </td>
                                                <td>
                                                    <?php if ($cv_kinerja != 'Y') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-danger fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI BELUM"> </i>
                                                    </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-success fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI SELESAI"> </i>
                                                    </a>
                                                        <?php } ?>
                                                    <!-- validasi waktu -->
                                                    <br>
                                                    <?php
                                                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1') {
                                                        if ($cv_waktu_penambahan != '0') {
                                                    ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT BELUM" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_potongan_pegawai != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI SELESAI" class="btn-xs btn-success fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI BELUM" class="btn-xs btn-danger fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <br>
                                                        <?php
                                                        if ($cv_waktu_penambahan != '0' and $pj == '1') { ?>
                                                    
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi disiplin -->
                                                        <?php if ($cv_kedisiplinan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN SELESAI" class="btn-xs btn-success fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN BELUM" class="btn-xs btn-danger fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi kompetensi -->
                                                        <?php if ($cv_kompetensi != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI SELESAI" class="btn-xs btn-success fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI BELUM" class="btn-xs btn-danger fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } ?>

                                                        <!-- Hasil Validasi -->
                                                        <?php if ($cek_kevalidasian >= 5) { ?>
                                                            <?php
                                                            if ($hak_akses['menu_val_kasie'] == '1') {
                                                                if ($cek_tervalidasi_management == '') {
                                                                    $modal = 'hasil-validasi-management';
                                                                    $title = 'Hasil';
                                                                    $warna = 'danger';
                                                                } else {
                                                                    $modal = 'hasil-validasi-management-update';
                                                                    $title = 'Update Hasil';
                                                                    $warna = 'success';
                                                                }
                                                            } else {
                                                                $modal = 'hasil-validasi';
                                                                $title = 'Hasil';
                                                                $warna = 'danger';
                                                            }
                                                            ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=' . $modal . '&id=' . $row['id_user']); ?>">
                                                                <i title="<?php echo $title; ?>" class="btn-xs btn-<?php echo $warna; ?> fa fa-check-square-o"> <?php echo $title; ?></i>
                                                            </a>
                                                    <?php }
                                                    }
                                                    ?>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <!--Modal view SKp Tahunan -->
                        <div class="modal fade " id="modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye">SKP TAHUNAN - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>NIP</th>
                                                                <th>Nama SKP</th>
                                                                <th>Nama Bagian</th>
                                                                <th>Kuantitas <br>(Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kuantitas <br>(Pencapaian)</p>
                                                                </th>
                                                                <th>Kualitas <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kualitas <br> (Pencapaian)</p>
                                                                </th>
                                                                <th>Waktu <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Waktu <br> (Pencapaian)</p>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $user = $row['id_user'];
                                                            $no = 0;
                                                            $tt_skptahunan = bukaquery("SELECT tm_pegawai.nip, tm_unit.nama_unit, tm_skp.skp, tt_skptahunan.id_skp, tt_skptahunan.kd_skp, tt_skptahunan.kuantitas, tt_skptahunan.tgl_buat, tt_skptahunan.tgl_update, tm_skp.waktu
                                                        FROM tt_skptahunan
                                                        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
                                                        INNER JOIN tm_pegawai ON tt_skptahunan.id_user = tm_pegawai.id_user
                                                        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                        where tm_pegawai.id_user='$user'");
                                                            while ($row = fetch_array($tt_skptahunan)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['nip']; ?></td>
                                                                    <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                                    <td><?php echo $row['kuantitas']; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'"); ?></td>
                                                                    <td>100%</td>
                                                                    <td><?php echo number_format((getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") / $row['kuantitas'] * 100), 2) . "%"; ?></td>
                                                                    <td><?php echo $row['kuantitas'] * $row['waktu'] . " Menit"; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") * $row['waktu'] . " Menit"; ?></td>
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal skp tahunan -->

                        <!--Modal view log -->
                        <div class="modal fade " id="modal-finger-<?php echo $logid;$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye"> LOG ABSENSI - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Absensi</th>
                                                                <th>Status</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $finger = bukaquery("SELECT user, tanggal, (case when log.status ='I' THEN 'IN' ELSE 'OUT' END) as status FROM `log` INNER JOIN tm_pegawai on tm_pegawai.log_finger = log.`user` where id_user = '$logid' and MONTH(tanggal) = '$bln_sebelumnya' and YEAR(tanggal) = '$thn' order by tanggal asc");
                                                            while ($row = fetch_array($finger)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['tanggal']; ?></td>
                                                                    <td><?php echo $row['status']; ?></td>
                                                                    
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal log -->
                    <?php
                    }
                    ?>
                    </tbody>
                </div>
            </div>
        <?php
            break;

        case "list-validasi-tu":
        ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list"> LIST VALIDASI PEGAWAI TATA USAHA </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- /.box-header -->
                    <?php
                    $no = 0;
                    $tmtcek = TanggalAkhirBulanKemarin();
                    $cek_hak_akses_validasi = fetch_array(bukaquery("select tm_level.menu_val_pj, tm_level.menu_val_kasatpel, tm_level.menu_val_kasie from tm_level where tm_level.id_level='$idlevel'"));
                        $tm_pegawai = bukaquery("SELECT tm_unit.nama_unit, tm_pegawai.id_user, tm_pegawai.nip, tm_pegawai.id_unit, tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan,tm_pegawai.status_pegawai,tm_pegawai.jk, tm_pegawai.status, tm_pegawai.foto , tm_level.nama_level  
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user on tm_pegawai.id_user = tm_user.id_user
                                            INNER JOIN tm_level on tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.sub_bagian='Tata Usaha' and tm_pegawai.status_pegawai='NON PNS' and tm_pegawai.tgl_masuk <= '$tmtcek' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit asc");
                    
                    while ($row = fetch_array($tm_pegawai)) {
                        $no++;
                        $logid = $row['id_user'];
                        $lognama = $row['nama_pegawai'];
                        $pj = $row['PJ'];
                    ?>
                        <!--thumbinal display>-->
                        <?php
                        if ($row['jk'] == 'L') {
                            $warna = 'bg-aqua-active';
                        } elseif ($row['jk'] == 'P') {
                            $warna = 'bg-fuchsia-active';
                        } else {
                            $warna = 'bg-gray-light';
                        }
                        ?>

                        <!--<div class = "row">-->

                        <div class="col-md-3">
                            <div class="box box-info box-solid">

                                <div class="box-header with-border">
                                    <h3 class="box-title"><?php echo $row['nama_pegawai']; ?></h3>

                                    <div class="box-tools pull-right">
                                        <!--                                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="center-block">
                                        <center>
                                            <?php
                                            if ($row['foto'] == '-' or $row['foto'] == '') {
                                                if ($row['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $row['foto'];
                                            }
                                            ?>
                                            <!--<img src="<?php // echo $foto;                                           
                                                            ?>" style="align:center" class="img-rounded" width="250" height="250" alt="" >-->
                                            <img src="<?php echo $foto; ?>" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
                                            <br>
                                            <br>
                                            <!-- <p><br><label><?php echo $row['nama_pegawai']; ?></label></p> -->
                                            <!-- <?php echo $row['nip']; ?> -->
                                            <p><b><?php echo $row['nama_level']; ?></b></p>
                                        </center>
                                        <?php
                                        $cv_kinerja = getOne("select validasi from tt_kinerja where month(tt_kinerja.tanggal_kinerja)='$bln_sebelumnya' and year(tt_kinerja.tanggal_kinerja)='$thn' and tt_kinerja.id_user='$row[id_user]' GROUP BY validasi ORDER BY validasi desc");
                                        $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                        $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bln_sebelumnya' and year(tm_waktu_t.date_t)='$thn' and tm_waktu_t.id_user='$row[id_user]'");
                                        $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bln_sebelumnya' and year(tm_waktu_s.date_s)='$thn' and tm_waktu_s.id_user='$row[id_user]'");
                                        $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bln_sebelumnya' and year(tm_potongan_p.date_p)='$thn' and tm_potongan_p.id_user='$row[id_user]'");
                                        $cv_kedisiplinan = getOne("select count(id_disiplin) from tm_kedisiplinan where month(tm_kedisiplinan.date_d)='$bln_sebelumnya' and year(tm_kedisiplinan.date_d)='$thn' and tm_kedisiplinan.id_user='$row[id_user]'");
                                        $cv_kompetensi = getOne("select count(id_kompetensi) from tm_kompetensi where month(tm_kompetensi.date_kompetensi)='$bln_sebelumnya' and year(tm_kompetensi.date_kompetensi)='$thn' and tm_kompetensi.id_user='$row[id_user]'");
                                        $cek_kevalidasian = $cv_waktu_penambahan + $cv_waktu_pengurangan + $cv_waktu_shift + $cv_kedisiplinan + $cv_kompetensi;
                                        $cek_tervalidasi_management = getOne("select id_penilaian from tm_penilaian where month(tm_penilaian.tanggal_penilaian)='$bln_sebelumnya' and year(tm_penilaian.tanggal_penilaian)='$thn' and tm_penilaian.id_user='$row[id_user]'");
                                        ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <th>
                                                    <label>TARGET SKP</label>
                                                </th>
                                                <th>
                                                    <label>ABSENSI</label>
                                                </th>
                                                <th>
                                                    <label>VALIDASI</label>
                                                </th>
                                            </thead>
                                            <tbody>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" title="SKP TAHUNAN" class="btn-xs btn-info fa fa-bullseye"></span>
                                                </td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#modal-finger-<?php echo $logid;$lognama ?>" title="LOG ABSENSI" class="btn-xs btn-info fa fa-eye"></span>
                                                    <br>
                                                    <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai1&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="VIEW ABSENSI" class="btn-xs btn-info fa fa-list"></span></a>
                                                </td>
                                                <td>
                                                    <?php if ($cv_kinerja != 'Y') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-danger fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI BELUM"> </i>
                                                    </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=list-validasi-kinerja-pegawai&id=' . $row['id_user']); ?>">
                                                        <i class="btn-xs btn-success fa fa-line-chart" title="VALIDASI KINERJA PEGAWAI SELESAI"> </i>
                                                    </a>
                                                        <?php } ?>
                                                    <!-- validasi waktu -->
                                                    <br>
                                                    <?php
                                                    if ($cek_hak_akses_validasi['menu_val_pj'] == '1') {
                                                        if ($cv_waktu_penambahan != '0') {
                                                    ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT BELUM" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_potongan_pegawai != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI SELESAI" class="btn-xs btn-success fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-potongan-pegawai&id=' . $row['id_user']); ?>" hidden="true">>
                                                                <i title="VALIDASI PENGEMBALIAN DAN POTONGAN PEGAWAI BELUM" class="btn-xs btn-danger fa fa-clock-o" > P</i>
                                                            </a>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <br>
                                                        <?php
                                                        if ($cv_waktu_penambahan != '0' and $pj == '1') { ?>
                                                    
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENGURANGAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_pengurangan != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU SELESAI" class="btn-xs btn-success fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-penambahan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI PENAMBAHAN WAKTU BELUM" class="btn-xs btn-danger fa fa-clock-o"> +</i>
                                                            </a>
                                                        <?php } ?>

                                                        <?php if ($cv_waktu_shift != '0'  and $pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-success fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } elseif ($pj == '1') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-shift&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU SHIFT SELESAI" class="btn-xs btn-danger fa fa-clock-o"> S</i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi disiplin -->
                                                        <?php if ($cv_kedisiplinan != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN SELESAI" class="btn-xs btn-success fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kedisiplinan&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KEDISIPLINAN BELUM" class="btn-xs btn-danger fa fa-thumbs-up"> </i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- validasi kompetensi -->
                                                        <?php if ($cv_kompetensi != '0') { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI SELESAI" class="btn-xs btn-success fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-kompetensi&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI KOMPETENSI BELUM" class="btn-xs btn-danger fa fa-calendar-check-o"> </i>
                                                            </a>
                                                        <?php } ?>

                                                        <!-- Hasil Validasi -->
                                                        <?php if ($cek_kevalidasian >= 5) { ?>
                                                            <?php
                                                            if ($hak_akses['menu_val_kasie'] == '1') {
                                                                if ($cek_tervalidasi_management == '') {
                                                                    $modal = 'hasil-validasi-management';
                                                                    $title = 'Hasil';
                                                                    $warna = 'danger';
                                                                } else {
                                                                    $modal = 'hasil-validasi-management-update';
                                                                    $title = 'Update Hasil';
                                                                    $warna = 'success';
                                                                }
                                                            } else {
                                                                $modal = 'hasil-validasi';
                                                                $title = 'Hasil';
                                                                $warna = 'danger';
                                                            }
                                                            ?>
                                                            <a href="?<?php echo paramEncrypt('module=validasi-pegawai&act=' . $modal . '&id=' . $row['id_user']); ?>">
                                                                <i title="<?php echo $title; ?>" class="btn-xs btn-<?php echo $warna; ?> fa fa-check-square-o"> <?php echo $title; ?></i>
                                                            </a>
                                                    <?php }
                                                    }
                                                    ?>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <!--Modal view SKp Tahunan -->
                        <div class="modal fade " id="modal-skp-tahunan-<?php echo $row['id_user'];$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye">SKP TAHUNAN - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>NIP</th>
                                                                <th>Nama SKP</th>
                                                                <th>Nama Bagian</th>
                                                                <th>Kuantitas <br>(Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kuantitas <br>(Pencapaian)</p>
                                                                </th>
                                                                <th>Kualitas <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Kualitas <br> (Pencapaian)</p>
                                                                </th>
                                                                <th>Waktu <br> (Target)</th>
                                                                <th>
                                                                    <p class="text-red">Waktu <br> (Pencapaian)</p>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $user = $row['id_user'];
                                                            $no = 0;
                                                            $tt_skptahunan = bukaquery("SELECT tm_pegawai.nip, tm_unit.nama_unit, tm_skp.skp, tt_skptahunan.id_skp, tt_skptahunan.kd_skp, tt_skptahunan.kuantitas, tt_skptahunan.tgl_buat, tt_skptahunan.tgl_update, tm_skp.waktu
                                                        FROM tt_skptahunan
                                                        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
                                                        INNER JOIN tm_pegawai ON tt_skptahunan.id_user = tm_pegawai.id_user
                                                        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                        where tm_pegawai.id_user='$user'");
                                                            while ($row = fetch_array($tt_skptahunan)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['nip']; ?></td>
                                                                    <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                                    <td><?php echo $row['kuantitas']; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'"); ?></td>
                                                                    <td>100%</td>
                                                                    <td><?php echo number_format((getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") / $row['kuantitas'] * 100), 2) . "%"; ?></td>
                                                                    <td><?php echo $row['kuantitas'] * $row['waktu'] . " Menit"; ?></td>
                                                                    <td><?php echo getOne("select count(kd_skp) as kuantitas from tt_kinerja where kd_skp='$row[kd_skp]' and id_user='$user'") * $row['waktu'] . " Menit"; ?></td>
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal skp tahunan -->

                        <!--Modal view log -->
                        <div class="modal fade " id="modal-finger-<?php echo $logid;$lognama ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title fa fa-bullseye"> LOG ABSENSI - <?php echo $lognama ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="box-body table-responsive">
                                                    <table id="all" class="table table-bordered table-striped">
                                                        <thead>
                                                            
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal Absensi</th>
                                                                <th>Status</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $finger = bukaquery("SELECT user, tanggal, (case when log.status ='I' THEN 'IN' ELSE 'OUT' END) as status FROM `log` INNER JOIN tm_pegawai on tm_pegawai.log_finger = log.`user` where id_user = '$logid' and MONTH(tanggal) = '$bln_sebelumnya' and YEAR(tanggal) = '$thn' order by tanggal asc");
                                                            while ($row = fetch_array($finger)) {
                                                                $no++;
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['tanggal']; ?></td>
                                                                    <td><?php echo $row['status']; ?></td>
                                                                    
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tutup Modal log -->
                    <?php
                    }
                    ?>
                    </tbody>
                </div>
            </div>
        <?php
            break;
    }
} else {
    header("location:error404");
}
?>
