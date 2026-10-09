<?php
$aksi = "module/cuti-pegawai/aksi-cuti-pegawai.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header("location:error404");
        break;

    case "list-data-pengajuan-cuti-pegawai":
        $tampil_cuti_full = 'yes';
        $cuti = bukaquery("SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, CONCAT(COUNT(tm_pegawai.nama_pegawai) ,' Pegawai')as nama_pegawai
        FROM tm_hari_cuti
        INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
        WHERE acc_pengganti != 'T' and acc_pj != 'T' and acc_kepegawaian != 'T' and acc_kasie != 'T' and jenis_cuti != 'CUTI MELAHIRKAN' GROUP BY tanggal");
        $yanghamil = bukaquery("SELECT nama_pegawai,
                                substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ', 1 ) AS tgl_mulai,
                                substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ',- 1 ) AS tgl_selesai 
                                FROM
                                    tm_cuti
                                    INNER JOIN tm_pegawai ON tm_pegawai.id_user = tm_cuti.id_user 
                                WHERE
                                    acc_pengganti != 'T' 
                                    AND acc_pj != 'T' 
                                    AND acc_kepegawaian != 'T' 
                                    AND acc_kasie != 'T' 
                                    AND jenis_cuti = 'CUTI MELAHIRKAN' 
                                ORDER BY
                                    nama_pegawai,tgl_mulai");
?>
        
        <script type="text/javascript">
            function showSisa() {
                <?php
                $thnini = date('Y');
                $thnkmrn = date('Y') -1;
                $thnlusa = date('Y') -2;
                for ($thn = $thnini; $thn >= 2023; $thn--) {
                    $thn_leng = strlen($thn);
                    if ($thn_leng == 1) {
                        $i = "0" . $thn;
                    } else {
                        $i = $thn;
                    }

                    echo "if (document.pilihan.tahun.value == \"" . $i . "\")";
                    echo "{";


                    if ($i == $thnini) {
                        $hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user' and tahun='$i'");
                        $hct_tambah = getOne("Select IFNULL(sum(sisa_cuti_tambah),'0') as sisa_cuti_tambah from tm_validasi_cuti_tambah where id_user='$id_user' and tahun='$i'");                        
                        $cuti_disetujui = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user' and tm_cuti.tahun_cuti = '$i' and acc_kasie='Y'");
                        $cuti_diambil = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user' and tm_cuti.tahun_cuti = '$i' and acc_kasie!='Y' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $potong_cuti = getOne("SELECT sum(potong_cuti) FROM `tm_waktu_k` WHERE id_user = '$id_user' and year(date_k) = '$i'");

                        $cutimanual = getOne("SELECT sum(potong_cuti) FROM `tm_waktu_k` WHERE id_user = '$id_user' and year(date_k) = '$i'");
                        $cutisurat = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user' and tm_cuti.tahun_cuti = '$i' and MONTH(tanggal) != MONTH(CURRENT_DATE) and (acc_kasie='T' OR acc_kasie='-') AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $cutilahiran = getOne("SELECT sum(ct_persalinan_k) FROM `tm_waktu_k` WHERE id_user = '$id_user' and year(date_k) = '$i'");
                        $cutiberjalan = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user' and tm_cuti.tahun_cuti = '$i' and MONTH(tanggal) = MONTH(CURRENT_DATE)");
                        
                        $jumlah_sisa = getOne("SELECT ($hct-$cutimanual-$cutilahiran-IFNULL(sum(tm_cuti.jumlah_hari),0)) as jumlah from tm_cuti where tm_cuti.id_user='$id_user' and tm_cuti.tahun_cuti='$i' and jenis_cuti NOT IN ('CUTI MELAHIRKAN','CUTI BERSAMA') and (acc_kasie='T' OR acc_kasie='-' OR acc_kasie='Y') AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        // $jumlah_sisa_cuti = ($hct-$cutimanual-$cutilahiran-$cutiberjalan-$cutisurat);
                        
                        $jumlah_sisa_cuti = ($hct-$cuti_disetujui-$cutilahiran-$cuti_diambil-$cutimanual);
                        if ($jumlah_sisa_cuti == '') {
                            $nilai = $jumlah_sisa_cuti+$hct_tambah;
                        } else {
                            if ($jumlah_sisa_cuti < 0) {
                                $nilai = 0+$hct_tambah;
                            } else {
                                $nilai = $jumlah_sisa_cuti+$hct_tambah;
                            }
                        }
                    } else if ($i == $thnkmrn) {

                        $hctkmrn = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user' and tahun='$i'");
                        $jumlah_sisa = getOne("SELECT ($hctkmrn-IFNULL(sum(tm_cuti.jumlah_hari),0)) as jumlah from tm_cuti where tm_cuti.id_user='$id_user' and tm_cuti.tahun_cuti='$i' and jenis_cuti NOT IN ('CUTI MELAHIRKAN','CUTI BERSAMA') and (acc_kasie='Y' OR acc_kasie='-')");
                        if ($jumlah_sisa == '') {
                            $nilai = '0';
                        } else {
                            if ($i = $thnkmrn and $jumlah_sisa >6) {
                                // $nilai = '0';
                                $nilai = '6';
                            } else {
                                $nilai = '0';
                            }
                        }
                    } else{

                        $hctlusa = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user' and tahun='$i'");
                        $jumlah_sisa = getOne("SELECT ($hctlusa-IFNULL(sum(tm_cuti.jumlah_hari),0)) as jumlah from tm_cuti where tm_cuti.id_user='$id_user' and tm_cuti.tahun_cuti='$i' and jenis_cuti NOT IN ('CUTI MELAHIRKAN','CUTI BERSAMA') and (acc_kasie='Y' OR acc_kasie='-')");
                        if ($jumlah_sisa == '') {
                            $nilai = '0';
                        } else {
                            if ($i = $thnlusa and $jumlah_sisa >3) {
                                // $nilai = '3';
                                $nilai = $jumlah_sisa;
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
            <div class="modal fade" id="modal-maping-cuti" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="box-title fa fa-list"> JUMLAH CUTI YANG SUDAH DI AMBIL
                            </h3>
                            <p>Merah = Cuti Melahirkan</p>
                            <p>Biru = Cuti Tahunan</p>

                        </div>
                        <div class="modal-body ">
                            <div class="box-body no-padding">
                                <div id="cuti"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tutup Edit Modal permintaan -->
            <!-- $jumlah_sisa_cuti = ($hct-$cuti_disetujui-$cutilahiran-$cuti_diambil-$cutimanual); -->
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PENGAJUAN CUTI PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-pengajuan-cuti" title="" >
                    Buat Pengajuan Cuti
                </button>
                <button type="button" class="btn btn-primary fa fa-plus" data-toggle="modal" data-target="#modal-add-pengajuan-cutilahiran">
                    Buat Cuti Melahirkan
                </button>
                <button type="button" class="btn btn-warning fa fa-eye" data-toggle="modal" data-target="#modal-maping-cuti">
                    Lihat Daftar Cuti
                </button>
                <br>
                <!--Modal Add Cuti -->
                <div class="modal fade" id="modal-add-pengajuan-cuti" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Pengajuan Cuti pegawai</h4>
                            </div>
                            <form role="form" name="pilihan" action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=add-pengajuan-cuti&id=' . $id_user . ''); ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="control-label">Tanggal Surat </label>
                                        <input type="text" name="tgl_permohonan" class="form-control" id="datepicker11" value="<?php echo date("m/d/Y"); ?>" required readonly>
                                        <label class="control-label">Tahun Cuti </label>
                                        <select class="form-control select2" onchange="showSisa()" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                            <?php loadThnCuti(); ?>
                                        </select>
                                        <div id="sisa"> </div>
                                        
                                        <label>Jenis Cuti</label>
                                        <?php echo enumDropdown("tm_cuti", "jenis_cuti", "", "-Pilih Jenis Cuti-"); ?>
                                        <label class="control-label">Alasan Cuti</label>
                                        <textarea class="form-control" rows="3" name="alasan_cuti" placeholder="Alasan.." required></textarea>
                                        <label class="control-label">Alamat Saat Cuti</label>
                                        <textarea class="form-control" rows="3" name="alamat_cuti" placeholder="Alamat Selama Cuti" required></textarea>
                                        <label class="control-label">No Telepon</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                            </div>
                                            <input type="number" name="no_tlp" placeholder="Your Phone Number" class="form-control pull-right" required>
                                        </div>
                                        <label>Tanggal Cuti</label>
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
                                            $tm_pegawai = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai !='PJLP' and tm_pegawai.status='AKTIF' order by tm_pegawai.nama_pegawai asc");
                                            while ($row = fetch_array($tm_pegawai)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label>Disposisi KaIns/Kasatpel</label>
                                        <select class="form-control select2" name="nip_pj" data-placeholder="-Pilih Nama PJ-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama KaIns/Kasatpel-</option>
                                            <?php
                                            $tm_pj = bukaquery("SELECT
                                                            	tm_kasatpel.nip,
                                                            	tm_kasatpel.nama_kasatpel as nama_pegawai
                                                            FROM
                                                            	tm_kasatpel
                                                            	INNER JOIN tm_pegawai ON tm_pegawai.nip = tm_kasatpel.nip
                                                            WHERE
                                                            	tm_pegawai.STATUS = 'AKTIF' 
                                                            	AND status_pegawai != 'PJLP' 
                                                            GROUP BY
                                                            	tm_pegawai.nama_pegawai
                                                            ORDER BY nama_kasatpel");
                                            while ($row = fetch_array($tm_pj)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label>Disposisi Kasubbag/Kasie/Direktur</label>
                                        <select class="form-control select2" name="nip_kasie" data-placeholder="-Pilih Nama Kasie-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih-</option>
                                            <?php
                                            $tm_kasie = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.`status` FROM tm_pegawai
                                                        INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                        INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                        WHERE tm_pegawai.sub_bagian = '$kasie' and tm_pegawai.status = 'AKTIF' and tm_level.id_level != 'LVL-000052' and tm_level.id_level != 'LVL-000064' and tm_level.menu_val_kasie='1' ");
                                            while ($row = fetch_array($tm_kasie)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Simpan" >
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tutup Modal Skp -->
                
                <!--Modal Add Cuti Lahiran-->
                <div class="modal fade" id="modal-add-pengajuan-cutilahiran" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Pengajuan Cuti Lahiran</h4>
                            </div>
                            <form role="form" name="pilihan1" action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=add-pengajuan-cuti&id=' . $id_user . ''); ?>" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="control-label">Tanggal Permohonan </label>
                                        <input type="text" name="tgl_permohonan" class="form-control" id="datepicker11" value="<?php echo date("m/d/Y"); ?>" required readonly>
                                        <label class="control-label">Tahun Cuti </label>
                                        <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" style="width: 100%;" required>
                                            <?php loadThnCuti(); ?>
                                        </select>
                                        
                                        <label>Jenis Cuti</label>
                                        <input type="text" name="jenis_cuti" class="form-control" value="CUTI MELAHIRKAN" required readonly>                                       
                                        <label class="control-label">Alasan Cuti</label>
                                        <input type="text" name="alasan_cuti" class="form-control" value="CUTI MELAHIRKAN" required readonly>
                                        <label class="control-label">Alamat Saat Cuti</label>
                                        <textarea class="form-control" rows="3" name="alamat_cuti" placeholder="Alamat Selama Cuti" required></textarea>
                                        <label class="control-label">No Telepon</label>
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
                                            <input type="text" name="periode_cuti" class="form-control pull-right" id="daterange-btn" required readonly>
                                        </div>
                                        <label>Pengganti Cuti</label>
                                        <select class="form-control select2" name="nip_pengganti" data-placeholder="-Pilih Nama Pengganti-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Nama Pengganti-</option>
                                            <?php
                                            $tm_pegawai = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.status_pegawai !='PJLP' and tm_pegawai.status='AKTIF' and tm_pegawai.id_user!='$id_user' order by tm_pegawai.nama_pegawai asc");
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
                                                                                            WHERE tm_pegawai.status='AKTIF' and tm_level.menu_val_kasatpel='1' group by tm_pegawai.nama_pegawai");
                                            while ($row = fetch_array($tm_pj)) {
                                                echo "<option value=" . $row['nip'] . ">" . $row['nama_pegawai'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <label>Disposisi Kasubbag/Kasie/Direktur</label>
                                        <select class="form-control select2" name="nip_kasie" data-placeholder="-Pilih Nama Kasie-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih-</option>
                                            <?php
                                            $tm_kasie = bukaquery("SELECT tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_pegawai.`status` FROM tm_pegawai
                                                        INNER JOIN tm_user ON tm_pegawai.id_user=tm_user.id_user
                                                        INNER JOIN tm_level on tm_user.id_level=tm_level.id_level
                                                        WHERE tm_pegawai.sub_bagian = '$kasie' and tm_pegawai.status = 'AKTIF' and tm_level.id_level != 'LVL-000052' and tm_level.id_level != 'LVL-000064' and tm_level.menu_val_kasie='1'or menu_val_dir = '1'");
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
                                // $tanggal1 = date('Y-m-d', strtotime(substr($row['periode_cuti'], 0, 10)));
                                // $tanggal2 = date('Y-m-d', strtotime(substr($row['periode_cuti'], 13, 10)));
                                // $lama_cuti = HitungHari(date('d-m-Y', strtotime(substr($row['periode_cuti'], 0, 10))), date('d-m-Y', strtotime(substr($row['periode_cuti'], 13, 10)))) - HitungHariMinggu(date('d-m-Y', strtotime(substr($row['periode_cuti'], 0, 10))), date('d-m-Y', strtotime(substr($row['periode_cuti'], 13, 10))));
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
                                                                $tm_cuti_pengganti = bukaquery("select tm_pegawai.nip, tm_pegawai.nama_pegawai from tm_pegawai where  tm_pegawai.status='AKTIF' and tm_pegawai.id_user!='$id_user'");
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
                                                            <label>Klik tombol YA jika anda ingin membatalkan dan menghapus data cuti anda</label>
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
                                        <p>
                                            <?php
                                                echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                            ?>
                                        </p>
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
                                        } 
                                        elseif ($row['acc_kasie'] == 'Y') {
                                            echo "<i class='fa fa-check' title='diterima'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['acc_pengganti'] == 'Y' and $row['acc_pj'] == 'Y' and $row['acc_kepegawaian'] == 'Y' and $row['acc_kasie'] == 'Y') { ?>
                                            <a href="<?php echo 'cetak-surat-cuti-' . $row['id_cuti'] . ''; ?>" target="_blank"><span data-toggle="modal" title="Cetak" class="btn-xs btn-success fa fa-file-pdf-o"> Cetak Surat</span></a>
                                            <!-- <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span> -->
                                        <?php
                                        } elseif ($row['acc_pengganti'] == 'Y' and $row['acc_pj'] == 'Y' and $row['acc_kepegawaian'] == 'Y' and $row['acc_kasie'] == 'T') {
                                            ?>
                                            <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_cuti']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                            <?php if ($row['alasan'] == 'Perubahan Tanggal' or $row['alasan'] == '') {
                                                $perihal = 'Ubah Tanggal ' . ucwords(strtolower($$row['jenis_cuti']));

                                            } else {
                                                $perihal = 'Penangguhan ' . ucwords(strtolower($$row['jenis_cuti']));
                                                $perihal .= "<a href='cetak-surat-penolakan-cuti-$row[id_cuti]' target='_blank'><span title='Cetak' class='btn-xs btn-warning fa fa-file-pdf-o'> Cetak Surat Penolakan</span></a>";
                                            }
                                        ?>
                                            <span data-toggle="modal" title="" class="btn-xs btn-danger"> <?php echo $perihal; ?></span>


                                        <?php } else { ?>
                                            <?php if ($row['acc_pengganti'] == 'Y' or $row['acc_pj'] == 'Y' or $row['acc_kepegawaian'] == 'Y' or $row['acc_kasie'] == 'Y') { ?>
                                                <span data-toggle="modal" title="Menunggu Assesment" class="btn-xs btn-info fa fa-refresh"> Menunggu Persetujuan</span>
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

    case "list-data-maping-cuti-pegawai-laporan":
        $year = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> DATA LAPORAN CUTI PEGAWAI PERTAHUN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-2">
                    <form action="" method="post" id="search">
                        <div class="input-group ">
                            <select class="form-control select2" name="tahun" id="tahun" data-placeholder="-Pilih Tahun-" required>
                                <?php loadThnnow($year); ?>
                            </select>
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-info btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    <!--</div>-->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped" width='100%'>
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Nama Pegawai</td>
                                        <td>Unit</td>
                                        <td>Tahun Cuti</td>
                                        <td>Saldo Cuti Tahunan</td>
                                        <td>Saldo Cuti Tambahan</td>
                                        <td>Jan</td>
                                        <td>Feb</td>
                                        <td>Mar</td>
                                        <td>Apr</td>
                                        <td>Mei</td>
                                        <td>Jun</td>
                                        <td>Jul</td>
                                        <td>Aug</td>
                                        <td>Sep</td>
                                        <td>Okt</td>
                                        <td>Nov</td>
                                        <td>Des</td>
                                        <td>Total Cuti</td>
                                        <td>Potong Izin/Sakit</td>
                                        <td>Sisa Cuti</td>
                                    </tr>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $thnini = date('Y');
                                    $thnkmrn = date('Y') -1;
                                    $sql = bukaquery("SELECT
                                        tm_pegawai.nip,
                                        tm_pegawai.nama_pegawai,
                                        tm_pegawai.id_user,
                                        tm_unit.nama_unit,
                                        ($year) as tahun,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 1 AND 1  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jan,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 2 AND 2  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as feb,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 3 AND 3  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as mar,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 4 AND 4  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as apr,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 5 AND 5  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as mei,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 6 AND 6  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jun,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 7 AND 7  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jul,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 8 AND 8  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as aug,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 9 AND 9  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as sep,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 10 AND 10  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as okt,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 11 AND 11  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as nov,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 12 AND 12  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as des,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 1 AND 12  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as total
                                        FROM
                                        tm_pegawai
                                        INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit
                                        INNER JOIN tm_waktu_k on tm_pegawai.id_user=tm_waktu_k.id_user
                                        WHERE tm_pegawai.`status`='AKTIF' and tm_pegawai.status_pegawai !='PJLP'
                                        and tm_pegawai.status_pegawai !='SPESIALIS' and tm_pegawai.status_pegawai !='PNS'
                                        GROUP BY tm_pegawai.nip ORDER BY tm_unit.nama_unit ASC ");
                                    while ($row = fetch_array($sql)) {
                        $id_user1 = $row['id_user'];
                        $hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user= '$id_user1' and tahun ='$year'");
                        $hct_tambah = getOne("Select IFNULL(sum(sisa_cuti_tambah),'0') as sisa_cuti_tambah from tm_validasi_cuti_tambah where id_user='$id_user1' and tahun='$year'");
                        // $cuti_disetujui = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie='Y'");
                        $cuti_diambil = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'  AND jenis_cuti != 'CUTI MELAHIRKAN'");
                        $potong_cuti = getOne("SELECT sum(potong_cuti) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$year'");
                        // $cutimanual = getOne("SELECT sum(ct_sakit_k + ct_alasan_k) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$thnini'");
                        $cutilahiran = getOne("SELECT sum(ct_persalinan_k) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$thnini'");
                        $jumlah_sisa_1 = getOne("SELECT ($hct-$cutilahiran-$cuti_diambil)");
                        
                        if ($jumlah_sisa_1 < 0) {
                        $cuti1 = '0' ;
                        } else {
                        $cuti1 = $jumlah_sisa_1;
                        }
                        // $cutisistem = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y')");

                        $hctkmrn = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user1' and tahun='$thnkmrn'");
                        $jumlah_sisa_2 = getOne("SELECT ($hctkmrn-IFNULL(sum(tm_cuti.jumlah_hari),0)) as jumlah from tm_cuti where tm_cuti.id_user='$id_user1' and tm_cuti.tahun_cuti='$thnkmrn' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y')");
                        if ($jumlah_sisa_2 == '') {
                            $cuti2 = '0';
                        } else {
                            if ($jumlah_sisa_2 >6) {
                                $cuti2 = '6';
                            } else {
                                $cuti2 = $jumlah_sisa_2;
                            }
                        }

                        $jan = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '1' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $feb = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '2' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $mar = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '3' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $apr = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '4' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $may = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '5' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $jun = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '6' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $jul = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '7' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $aug = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '8' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $sep = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '9' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $okt = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '10' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $nov = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '11' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $des = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '12' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $total = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");

                        $total_cuti = $cuti_diambil;
                        $sisacuti = $cuti1;
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_pegawai']; ?></td>
                                            <td><?= $row['nama_unit']; ?></td>
                                            <td><?= $row['tahun']; ?></td>
                                            <td><?= $hct; ?></td>
                                            <td><?= $hct_tambah; ?></td>
                                            <td><?= $jan+$row['jan']; ?></td>
                                            <td><?= $feb+$row['feb']; ?></td>
                                            <td><?= $mar+$row['mar']; ?></td>
                                            <td><?= $apr+$row['apr']; ?></td>
                                            <td><?= $may+$row['mei']; ?></td>
                                            <td><?= $jun+$row['jun']; ?></td>
                                            <td><?= $jul+$row['jul']; ?></td>
                                            <td><?= $aug+$row['aug']; ?></td>
                                            <td><?= $sep+$row['sep']; ?></td>
                                            <td><?= $okt+$row['okt']; ?></td>
                                            <td><?= $nov+$row['nov']; ?></td>
                                            <td><?= $des+$row['des']; ?></td>
                                            <td><?= $total_cuti; ?></td>
                                            <td><?= $potong_cuti; ?></td>
                                            <td><?= $hct-($potong_cuti+$total_cuti)+$hct_tambah; ?></td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        break;

    case "list-data-maping-cuti-unit":
        $year = isset($_POST['tahun']) ? $_POST['tahun'] : $thn_now;
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LAPORAN CUTI PEGAWAI PERTAHUN (PJ)</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <!--<div class="row" >-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped" width='100%'>
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Nama Pegawai</td>
                                        <td>Unit</td>
                                        <td>Tahun Cuti</td>
                                        <td>Jan</td>
                                        <td>Feb</td>
                                        <td>Mar</td>
                                        <td>Apr</td>
                                        <td>Mei</td>
                                        <td>Jun</td>
                                        <td>Jul</td>
                                        <td>Aug</td>
                                        <td>Sep</td>
                                        <td>Okt</td>
                                        <td>Nov</td>
                                        <td>Des</td>
                                        <td>Total Cuti</td>
                                        <td>Potong Izin/Sakit</td>
                                        <td>Sisa Cuti</td>
                                    </tr>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $thnini = date('Y');
                                    $thnkmrn = date('Y') -1;
                                    $sql = bukaquery("SELECT
                                        tm_pegawai.nip,
                                        tm_pegawai.nama_pegawai,
                                        tm_pegawai.id_user,
                                        tm_unit.nama_unit,
                                        ($year) as tahun,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 1 AND 1  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jan,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 2 AND 2  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as feb,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 3 AND 3  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as mar,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 4 AND 4  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as apr,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 5 AND 5  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as mei,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 6 AND 6  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jun,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 7 AND 7  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as jul,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 8 AND 8  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as aug,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 9 AND 9  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as sep,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 10 AND 10  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as okt,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 11 AND 11  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as nov,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 12 AND 12  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as des,
                                        SUM(CASE WHEN MONTH(tm_waktu_k.date_k) BETWEEN 1 AND 12  AND YEAR(tm_waktu_k.date_k)='$year' THEN potong_cuti ELSE 0 END) as total
                                        FROM
                                        tm_pegawai
                                        INNER JOIN tm_unit ON tm_unit.id_unit=tm_pegawai.id_unit
                                        INNER JOIN tm_waktu_k on tm_pegawai.id_user=tm_waktu_k.id_user
                                        WHERE tm_pegawai.`status`='AKTIF' AND tm_unit.id_unit LIKE '%$id_unit%'
                                        GROUP BY tm_pegawai.nip ORDER BY tm_unit.nama_unit ASC ");
                                    while ($row = fetch_array($sql)) {
                        $id_user1 = $row['id_user'];
                        $hct = getOne("Select sisa_cuti from tm_validasi_cuti where id_user= '$id_user1' and tahun ='$year'");
                        $hct_tambah = getOne("Select IFNULL(sum(sisa_cuti_tambah),'0') as sisa_cuti_tambah from tm_validasi_cuti_tambah where id_user= '$id_user1' and tahun ='$year'");
                        $cuti_disetujui = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie='Y'");
                        $cuti_diambil = getOne("SELECT COUNT(tm_hari_cuti.id_cuti) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_hari_cuti.id_cuti = tm_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and tm_cuti.tahun_cuti = '$year' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'  AND jenis_cuti != 'CUTI MELAHIRKAN'");
                        $potong_cuti = getOne("SELECT sum(potong_cuti) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$year'");

                        $cutimanual = getOne("SELECT sum(ct_sakit_k + ct_alasan_k) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$thnini'");
                        $cutilahiran = getOne("SELECT sum(ct_persalinan_k) FROM `tm_waktu_k` WHERE id_user = '$id_user1' and year(date_k) = '$thnini'");
                        $jumlah_sisa_1 = getOne("SELECT ($hct-$cutilahiran-$cuti_diambil)");
                        if ($jumlah_sisa_1 < 0) {
                        $cuti1 = 0+$hct_tambah ;
                        } else {
                        $cuti1 = $jumlah_sisa_1+$hct_tambah;
                        }
                        $cutisistem = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y')");

                        $hctkmrn = getOne("Select sisa_cuti from tm_validasi_cuti where id_user='$id_user1' and tahun='$thnkmrn'");
                        $jumlah_sisa_2 = getOne("SELECT ($hctkmrn-IFNULL(sum(tm_cuti.jumlah_hari),0)) as jumlah from tm_cuti where tm_cuti.id_user='$id_user1' and tm_cuti.tahun_cuti='$thnkmrn' and jenis_cuti!='CUTI MELAHIRKAN' and (acc_kasie='Y')");
                        if ($jumlah_sisa_2 == '') {
                            $cuti2 = '0';
                        } else {
                            if ($jumlah_sisa_2 >6) {
                                $cuti2 = '6';
                            } else {
                                $cuti2 = $jumlah_sisa_2;
                            }
                        }

                        $jan = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '1' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $feb = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '2' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $mar = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '3' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $apr = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '4' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $may = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '5' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $jun = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '6' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $jul = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '7' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $aug = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '8' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $sep = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '9' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $okt = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '10' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $nov = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '11' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $des = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and MONTH(tanggal) = '12' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");
                        $total = getOne("SELECT (IFNULL(count(tm_hari_cuti.tanggal),0)) as jumlah from tm_hari_cuti INNER JOIN tm_cuti on tm_cuti.id_cuti = tm_hari_cuti.id_cuti where tm_cuti.id_user = '$id_user1' and YEAR(tm_hari_cuti.tanggal)= '$thnini' and jenis_cuti!='CUTI MELAHIRKAN' and acc_kasie!='T' AND acc_pengganti != 'T' AND acc_pj != 'T' AND acc_kepegawaian != 'T'");

                        $total_cuti = $cuti_diambil;
                        $sisacuti = $cuti1;
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama_pegawai']; ?></td>
                                            <td><?= $row['nama_unit']; ?></td>
                                            <td><?= $row['tahun']; ?></td>
                                            <td><?= $jan+$row['jan']; ?></td>
                                            <td><?= $feb+$row['feb']; ?></td>
                                            <td><?= $mar+$row['mar']; ?></td>
                                            <td><?= $apr+$row['apr']; ?></td>
                                            <td><?= $may+$row['mei']; ?></td>
                                            <td><?= $jun+$row['jun']; ?></td>
                                            <td><?= $jul+$row['jul']; ?></td>
                                            <td><?= $aug+$row['aug']; ?></td>
                                            <td><?= $sep+$row['sep']; ?></td>
                                            <td><?= $okt+$row['okt']; ?></td>
                                            <td><?= $nov+$row['nov']; ?></td>
                                            <td><?= $des+$row['des']; ?></td>
                                            <td><?= $total_cuti; ?></td>
                                            <td><?= $potong_cuti; ?></td>
                                            <td><?= $hct-($potong_cuti+$total_cuti)+$hct_tambah; ?></td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        break;

    case "list-data-maping-cuti-pegawai":
        $tampil_kalender = 'yes';
        $cuti = bukaquery("SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, tm_pegawai.nama_pegawai
                                        FROM tm_hari_cuti
                                        INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
                                        WHERE acc_pengganti != 'T' and acc_pj != 'T' and acc_kepegawaian != 'T' and acc_kasie != 'T' and jenis_cuti != 'CUTI MELAHIRKAN' ");
        $yanghamil = bukaquery("SELECT
    nama_pegawai,
    substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ', 1 ) AS tgl_mulai,
    substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ',- 1 ) AS tgl_selesai 
FROM
    tm_cuti
    INNER JOIN tm_pegawai ON tm_pegawai.id_user = tm_cuti.id_user 
WHERE
    acc_pengganti != 'T' 
    AND acc_pj != 'T' 
    AND acc_kepegawaian != 'T' 
    AND acc_kasie != 'T' 
    AND jenis_cuti = 'CUTI MELAHIRKAN' 
ORDER BY
    nama_pegawai,tgl_mulai");
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PENGAJUAN CUTI SEMUA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="cuti"></div>
            </div>
        </div>

    <?php
        break;

    case "teslist-data-maping-cuti-pegawai":
        $tampil_tes = 'yes';
        $cuti = bukaquery("SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, tm_pegawai.nama_pegawai
                                        FROM tm_hari_cuti
                                        INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
                                        WHERE acc_pengganti != 'T' and acc_pj != 'T' and acc_kepegawaian != 'T' and acc_kasie != 'T' and jenis_cuti != 'CUTI MELAHIRKAN'");
        $yanghamil = bukaquery("SELECT
                            nama_pegawai,
                            substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ', 1 ) AS tgl_mulai,
                            substring_index( REPLACE ( periode_cuti, ',', ' ' ), ' ',- 1 ) AS tgl_selesai 
                        FROM
                            tm_cuti
                            INNER JOIN tm_pegawai ON tm_pegawai.id_user = tm_cuti.id_user 
                        WHERE
                            acc_pengganti != 'T' 
                            AND acc_pj != 'T' 
                            AND acc_kepegawaian != 'T' 
                            AND acc_kasie != 'T' 
                            AND jenis_cuti = 'CUTI MELAHIRKAN' 
                        ORDER BY
                            tgl_mulai");
                            ?>
        <div class="p-5">
  <h2 class="mb-4">Full Calendar</h2>
  <div class="card">
    <div class="card-body p-0">
      <div id="kalender"></div>
    </div>
  </div>
</div>

<!-- calendar modal -->
<div id="modal-view-event" class="modal modal-top fade calendar-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title"><span class="event-icon"></span><span class="event-title"></span></h4>
                    <div class="event-body"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<div id="modal-view-event-add" class="modal modal-top fade calendar-modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="add-event">
        <div class="modal-body">
        <h4>Add Event Detail</h4>        
          <div class="form-group">
            <label>Event name</label>
            <input type="text" class="form-control" name="ename">
          </div>
          <div class="form-group">
            <label>Event Date</label>
            <input type='text' class="datetimepicker form-control" name="edate">
          </div>        
          <div class="form-group">
            <label>Event Description</label>
            <textarea class="form-control" name="edesc"></textarea>
          </div>
          <div class="form-group">
            <label>Event Color</label>
            <select class="form-control" name="ecolor">
              <option value="fc-bg-default">fc-bg-default</option>
              <option value="fc-bg-blue">fc-bg-blue</option>
              <option value="fc-bg-lightgreen">fc-bg-lightgreen</option>
              <option value="fc-bg-pinkred">fc-bg-pinkred</option>
              <option value="fc-bg-deepskyblue">fc-bg-deepskyblue</option>
            </select>
          </div>
          <div class="form-group">
            <label>Event Icon</label>
            <select class="form-control" name="eicon">
              <option value="circle">circle</option>
              <option value="cog">cog</option>
              <option value="group">group</option>
              <option value="suitcase">suitcase</option>
              <option value="calendar">calendar</option>
            </select>
          </div>        
      </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" >Save</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>        
      </div>
      </form>
    </div>
  </div>
</div>

    <?php
        break;

    case "list-data-maping-cuti-kepegawaian":
    ?>
    <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN PENGGANTI CUTI PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab"> DATA CUTI BERJALAN</a></li>
                        <li><a href="#tab_2" data-toggle="tab">HISTORY CUTI DISETUJUI</a></li>
                        <li><a href="#tab_3" data-toggle="tab">HISTORY CUTI DITOLAK</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example_normal" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Nama Pemohon</th>
                                            <th>Jenis Pegawai</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <!--<th>Tanggal Cuti</th>-->
                                            <th>Alasan Cuti</th>
                                            <th>Acc Pengganti</th>
                                            <th>Acc PJ/Kasatpel</th>
                                            <th>Acc Kepegawaian</th>
                                            <th>Acc Kasie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti,tm_cuti.acc_pj,tm_cuti.acc_kepegawaian,tm_cuti.acc_kasie, tm_pegawai.nama_pegawai,tm_pegawai.status_pegawai,tm_cuti.nip_pengganti, tm_cuti.nip_pj 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.acc_kasie = '-' and acc_pengganti != 'T' and acc_pj != 'T' and acc_kepegawaian != 'T' AND year(tm_cuti.tgl_permohonan) = '$thn_now' ORDER by tm_cuti.tgl_permohonan asc  ");

                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                            $nm_pengganti = getOne("SELECT nama_pegawai
                                                        FROM tm_pegawai
                                                        where tm_pegawai.nip = '$row[nip_pengganti]'");
                                            $nm_pj = getOne("SELECT nama_pegawai
                                                        FROM tm_pegawai
                                                        where tm_pegawai.nip = '$row[nip_pj]'");
                                        ?>
                                            
                                
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo $row['status_pegawai']; ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <!--<td><?php echo $row['periode_cuti']; ?></td>-->
                                                <td><?php echo strtoupper($row['alasan_cuti']); ?></td>
                                                <td><?php echo $nm_pengganti; ?><br>
                                                    <?php if ($row['acc_pengganti'] == 'Y') { ?>
                                                       <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } elseif ($row['acc_pengganti'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span  class="btn-xs btn fa fa-spinner"></span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $nm_pj; ?><br>
                                                    <?php if ($row['acc_pj'] == 'Y') { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } elseif ($row['acc_pj'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span  class="btn-xs btn fa fa-spinner"></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['acc_kepegawaian'] == 'Y') { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                        
                                                    <?php } elseif ($row['acc_kepegawaian'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span  class="btn-xs btn fa fa-spinner"></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['acc_kasie'] == 'Y') { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } elseif ($row['acc_kasie'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span  class="btn-xs btn fa fa-spinner"></span>
                                                    <?php } ?>
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
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Nama Pemohon</th>
                                            <th>Jenis Pegawai</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <!--<th>Tangal Cuti</th>-->
                                            <th>Alasan Cuti</th>
                                            <th>Nama Pengganti</th>
                                            <th>Nama PJ</th>
                                            <th>Surat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti, tm_pegawai.nama_pegawai, nip_pengganti, nip_pj, tm_pegawai.status_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.acc_kasie = 'Y' and tm_pegawai.status_pegawai != 'PJLP' and year(tm_cuti.tgl_permohonan)='$thn_now'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo $row['status_pegawai']; ?></td>
                                                
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <!--<td><?php echo $row['periode_cuti']; ?></td>-->
                                                <td><?php echo strtoupper($row['alasan_cuti']); ?></td>
                                                <td><?php echo getOne("SELECT nama_pegawai
                                                        FROM tm_pegawai
                                                        where tm_pegawai.nip = '$row[nip_pengganti]'"); ?></td>
                                                <td><?php echo getOne("SELECT nama_pegawai
                                                        FROM tm_pegawai
                                                        where tm_pegawai.nip = '$row[nip_pj]'"); ?></td>
                                                <td><a href="<?php echo 'cetak-surat-cuti-' . $row['id_cuti'] . ''; ?>" target="_blank"><span data-toggle="modal" title="Cetak" class="btn-xs btn-success fa fa-file-pdf-o"> Cetak Surat</span></a>
                                                    
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_3">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti, acc_pj, acc_kepegawaian, acc_kasie, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.acc_pj = 'T' or tm_cuti.acc_pengganti = 'T' or tm_cuti.acc_kepegawaian = 'T' or tm_cuti.acc_kasie = 'T' and year(tm_cuti.tgl_permohonan)='$thn_now'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td><span title="Ditolak" class="btn-xs btn-danger fa fa-close"></span>
                                                    
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

    case "list-data-maping-cuti-pegawai1":
        $tampil_cuti = 'yes';
        $cuti = bukaquery("SELECT tm_hari_cuti.id_cuti, tm_hari_cuti.tanggal, tm_pegawai.nama_pegawai
                                        FROM tm_hari_cuti
                                        INNER JOIN tm_cuti ON tm_cuti.id_cuti=tm_hari_cuti.id_cuti
                                        INNER JOIN tm_pegawai ON tm_pegawai.id_user=tm_cuti.id_user
                                        WHERE acc_kasie='Y'");
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PENGAJUAN CUTI PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="calendar"></div>
            </div>
        </div>

    <?php
        break;


    case "list-data-assesment-pengganti-cuti-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN PENGGANTI CUTI PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab"> PERMOHONAN</a></li>
                        <li><a href="#tab_2" data-toggle="tab">HISTORY ASSESMENT</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example_normal" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Acc Pengganti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.nip_pengganti='$nip' and tm_cuti.acc_pengganti='-'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-assesment-<?php echo $row['id_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=assesment-pengajuan-cuti&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Assesment Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENYETUJUI pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-reject-<?php echo $row['id_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=reject-pengajuan-cuti&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Reject Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENOLAK pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_pengganti'] == '-') { ?>
                                                        <span data-toggle="modal" data-target="#modal-reject-<?php echo $row['id_cuti']; ?>" title="Tolak" class="btn-xs btn-warning fa fa-close"></span>
                                                        <span data-toggle="modal" data-target="#modal-assesment-<?php echo $row['id_cuti']; ?>" title="Setujui" class="btn-xs btn-danger fa fa-check"></span>
                                                    <?php } elseif ($row['acc_pengganti'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pengganti, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.nip_pengganti='$nip' and tm_cuti.acc_pengganti!='-' and year(tm_cuti.tgl_permohonan)='$thn_now'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_pengganti'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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


    case "list-data-assesment-pj-cuti-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN CUTI ASSESMENT PJ</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab"> PERMOHONAN</a></li>
                        <li><a href="#tab_2" data-toggle="tab">HISTORY ASSESMENT</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Acc PJ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pj, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-' and tm_cuti.nip_pj='$nip'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            
                                            $no++;
                                        ?>
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-assesment-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=assesment-pengajuan-cuti-pj&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Assesment Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENYETUJUI pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-reject-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=reject-pengajuan-cuti-pj&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Reject Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENOLAK pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_pj'] == '-') { ?>
                                                        <span data-toggle="modal" data-target="#modal-reject-<?php echo $row['id_cuti']; ?>" title="Tolak" class="btn-xs btn-warning fa fa-close"></span>
                                                        <span data-toggle="modal" data-target="#modal-assesment-<?php echo $row['id_cuti']; ?>" title="Setujui" class="btn-xs btn-danger fa fa-check"></span>
                                                    <?php } elseif ($row['acc_pj'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example_normal" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pj, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where year(tm_cuti.tgl_permohonan)='$thn_now' and tm_cuti.acc_pj!='-' and tm_cuti.nip_pj='$nip'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_pj'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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

        case "list-data-assesment-satpel-cuti-pegawai":
            ?>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN CUTI ASSESMENT KASATPEL</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_4" data-toggle="tab"> PERMOHONAN</a></li>
                                <li><a href="#tab_5" data-toggle="tab">HISTORY ASSESMENT</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_4">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Pemohon</th>
                                                    <th>Tanggal Permohonan</th>
                                                    <th>Jenis Cuti</th>
                                                    <th>Periode Cuti</th>
                                                    <th>Alasan Cuti</th>
                                                    <th>Acc Kasatpel</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pj, tm_pegawai.nama_pegawai 
                                                                FROM tm_cuti
                                                                inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                                where tm_cuti.acc_pengganti='Y' and tm_cuti.acc_pj='-' and tm_cuti.nip_pj='$nip'");
                                                while ($row = fetch_array($tm_cuti)) {
                                                    
                                                    $no++;
                                                ?>
                                                    <!-- Assesment Suti -->
                                                    <div class="modal fade" id="modal-assesment-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=assesment-pengajuan-cuti-pj&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title" id="myModalLabel">Assesment Pengajuan Cuti</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <div class="form-group">
                                                                                <label>
                                                                                    Anda akan MENYETUJUI pengajuan cuti, atas nama 
                                                                                    <?php 
                                                                                        echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                                     FROM tm_pegawai 
                                                                                                     WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                                    ?> 
                                                                                    periode 
                                                                                    <?php 
                                                                                        echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                                    ?> 
                                                                                    ??
                                                                                </label>
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
                                                    <!-- Tutup Assestmen Cuti -->
                                                    <!-- Assesment Suti -->
                                                    <div class="modal fade" id="modal-reject-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=reject-pengajuan-cuti-pj&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title" id="myModalLabel">Reject Pengajuan Cuti</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <div class="form-group">
                                                                                <label>
                                                                                    Anda akan MENOLAK pengajuan cuti, atas nama 
                                                                                    <?php 
                                                                                        echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                                     FROM tm_pegawai 
                                                                                                     WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                                    ?> 
                                                                                    periode 
                                                                                    <?php 
                                                                                        echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                                    ?> 
                                                                                    ??
                                                                                </label>
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
                                                    <!-- Tutup Assestmen Cuti -->
                                                    <tr>
                                                        <td><?php echo $no; ?></td>
                                                        <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                        <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                        <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                        <td>
                                                            <p>
                                                                <?php
                                                                    echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                                ?>
                                                            </p>
                                                        </td>
                                                        <td><?php echo $row['alasan_cuti']; ?></td>
                                                        <td>
                                                            <?php if ($row['acc_pj'] == '-') { ?>
                                                                <span data-toggle="modal" data-target="#modal-reject-<?php echo $row['id_cuti']; ?>" title="Tolak" class="btn-xs btn-warning fa fa-close"></span>
                                                                <span data-toggle="modal" data-target="#modal-assesment-<?php echo $row['id_cuti']; ?>" title="Setujui" class="btn-xs btn-danger fa fa-check"></span>
                                                            <?php } elseif ($row['acc_pj'] == 'T') { ?>
                                                                <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                            <?php } else { ?>
                                                                <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_5">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="example_normal" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Pemohon</th>
                                                    <th>Tanggal Permohonan</th>
                                                    <th>Jenis Cuti</th>
                                                    <th>Periode Cuti</th>
                                                    <th>Alasan Cuti</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_pj, tm_pegawai.nama_pegawai 
                                                                FROM tm_cuti
                                                                inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                                where year(tm_cuti.tgl_permohonan)='$thn_now' and tm_cuti.acc_pj!='-' and tm_cuti.nip_pj='$nip'");
                                                while ($row = fetch_array($tm_cuti)) {
                                                    $no++;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $no; ?></td>
                                                        <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                        <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                        <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                        <td>
                                                            <p>
                                                                <?php
                                                                    echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                                ?>
                                                            </p>
                                                        </td>
                                                        <td><?php echo $row['alasan_cuti']; ?></td>
                                                        <td>
                                                            <?php if ($row['acc_pj'] == 'T') { ?>
                                                                <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                            <?php } else { ?>
                                                                <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                            <?php } ?>
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

    case "list-data-assesment-kepegawaian-cuti-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN CUTI ASSESMENT KEPEGAWAIAAN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab"> PERMOHONAN</a></li>
                        <li><a href="#tab_2" data-toggle="tab">HISTORY ASSESMENT</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Acc Kepegawaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari,tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_kepegawaian, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where tm_cuti.acc_pj='Y' and tm_cuti.acc_kepegawaian='-'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-assesment-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=assesment-pengajuan-cuti-kepegawaian&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Assesment Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENYETUJUI pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-reject-<?php echo $row['id_cuti']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=reject-pengajuan-cuti-kepegawaian&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Reject Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENOLAK pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_kepegawaian'] == '-') { ?>
                                                        <span data-toggle="modal" data-target="#modal-reject-<?php echo $row['id_cuti']; ?>" title="Tolak" class="btn-xs btn-warning fa fa-close"></span>
                                                        <span data-toggle="modal" data-target="#modal-assesment-<?php echo $row['id_cuti']; ?>" title="Setujui" class="btn-xs btn-danger fa fa-check"></span>
                                                    <?php } elseif ($row['acc_kepegawaian'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example_normal" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari, tm_cuti.id_user,tm_cuti.tahun_cuti, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_kepegawaian, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where year(tm_cuti.tgl_permohonan)='$thn_now' and tm_cuti.acc_pj='Y' and tm_cuti.acc_kepegawaian!='-'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti'] ?></td>
                                                <td>
                                                    <?php if ($row['acc_kepegawaian'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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

    case "list-data-assesment-kasie-cuti-pegawai":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> LIST DATA PERMOHONAN CUTI ASSESMENT KASIE</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab"> PERMOHONAN</a></li>
                        <li><a href="#tab_2" data-toggle="tab">HISTORY ASSESMENT</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Acc Kasie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti,tm_cuti.jumlah_hari, tm_cuti.tahun_cuti, tm_cuti.id_user, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_kasie, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where acc_kasie='-' and acc_kepegawaian='Y' and tm_cuti.nip_kasie='$nip'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-assesment-<?php echo $row['id_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=assesment-pengajuan-cuti-kasie&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Assesment Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENYETUJUI pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <!-- Assesment Suti -->
                                            <div class="modal fade" id="modal-reject-<?php echo $row['id_cuti']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=cuti-pegawai&act=reject-pengajuan-cuti-kasie&id=' . $row['id_cuti'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">Reject Pengajuan Cuti</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-group">
                                                                        <label>
                                                                            Anda akan MENOLAK pengajuan cuti, atas nama 
                                                                            <?php 
                                                                                echo getOne("SELECT tm_pegawai.nama_pegawai 
                                                                                             FROM tm_pegawai 
                                                                                             WHERE tm_pegawai.id_user='$row[id_user]'");
                                                                            ?> 
                                                                            periode 
                                                                            <?php 
                                                                                echo "(" . $row['jumlah_hari'] . " Hari) " . formatRentangCutiMultiBulan($row['id_cuti']); 
                                                                            ?> 
                                                                            ??
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label">Alasan Penolakan<font color="red">*</font></label>
                                                                        <?php echo enumDropdown("tm_cuti", "alasan", "", "-Pilih Alasan-"); ?>
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
                                            <!-- Tutup Assestmen Cuti -->
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_kasie'] == '-') { ?>
                                                        <span data-toggle="modal" data-target="#modal-reject-<?php echo $row['id_cuti']; ?>" title="Tolak" class="btn-xs btn-warning fa fa-close"></span>
                                                        <span data-toggle="modal" data-target="#modal-assesment-<?php echo $row['id_cuti']; ?>" title="Setujui" class="btn-xs btn-danger fa fa-check"></span>
                                                    <?php } elseif ($row['acc_kasie'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-warning fa fa-close"></span>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                <table id="example_normal" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemohon</th>
                                            <th>Tanggal Permohonan</th>
                                            <th>Jenis Cuti</th>
                                            <th>Periode Cuti</th>
                                            <th>Alasan Cuti</th>
                                            <th>Assesment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        $tm_cuti = bukaquery("SELECT tm_cuti.id_cuti, tm_cuti.id_user,tm_cuti.tahun_cuti, tm_cuti.tgl_permohonan, tm_cuti.jenis_cuti, tm_cuti.alasan_cuti, tm_cuti.no_tlp, tm_cuti.periode_cuti, tm_cuti.acc_kasie, tm_pegawai.nama_pegawai 
                                                        FROM tm_cuti
                                                        inner join tm_pegawai on tm_cuti.id_user=tm_pegawai.id_user
                                                        where year(tm_cuti.tgl_permohonan)='$thn_now' and tm_cuti.nip_kasie='$nip' and acc_kasie!='-' and year(tm_cuti.tgl_permohonan)='$thn_now'");
                                        while ($row = fetch_array($tm_cuti)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?php echo $no; ?></td>
                                                <td><?php echo getOne("select tm_pegawai.nama_pegawai from tm_pegawai where tm_pegawai.id_user='$row[id_user]'"); ?></td>
                                                <td><?php echo FormatTgl('d/m/Y', $row['tgl_permohonan']); ?></td>
                                                <td><?php echo $row['jenis_cuti'] . " (" . $row['tahun_cuti'] . ")"; ?></td>
                                                <td>
                                                    <p>
                                                        <?php
                                                            echo "(" . $row['jumlah_hari'] . " hari) " . formatRentangCutiMultiBulan($row['id_cuti']);
                                                        ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $row['alasan_cuti']; ?></td>
                                                <td>
                                                    <?php if ($row['acc_kasie'] == 'T') { ?>
                                                        <span title="Ditolak" class="btn-xs btn-danger fa fa-close"></span>
                                                        <a href="<?php echo 'cetak-surat-penolakan-cuti-' . $row['id_cuti'] . ''; ?>" target="_blank"><span data-toggle="modal" title="Cetak" class="btn-xs btn-warning fa fa-file-pdf-o"> Cetak Surat Penolakan</span></a>
                                                    <?php } else { ?>
                                                        <span title="Disetujui" class="btn-xs btn-success fa fa-check"></span>
                                                    <?php } ?>
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
}
?>
