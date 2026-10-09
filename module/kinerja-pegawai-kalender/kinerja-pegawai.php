<?php
$aksi = "module/kinerja-pegawai/aksi-kinerja-pegawai.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header("location:error404");
        break;

    case "list-data-skp-tahunan-pegawai":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA SKP TAHUNAN</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
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
                                        <select class="form-control select2" name="tahun"  data-placeholder="-Pilih Tahun-"  required>
                                        <?php loadThnnow(isset($_POST['tahun'])? $_POST['tahun'] : null); ?>               
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
                 ?>
            <div class="box-body">                
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-skp-tahunan">
                    Buat Master Data SKP Tahunan
                </button>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-skp-tahunan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-plus"> Tambah Data SKP Tahunan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=add-skp-tahunan&id=' . $id_user . ''); ?>" method="post"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Aktifitas</label>
                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                            <?php
                                            $tm_skp = bukaquery("select * from tm_skp");
                                            while ($row = fetch_array($tm_skp)) {
                                                echo"<option value=" . $row['kd_skp'] . ">" . $row['skp'] . " [" . $row['waktu'] . " Menit]</option>";
                                            }
                                            ?>               
                                        </select>
                                        <label>Kuantitas</label>
                                        <input type="number" class="form-control" name="kuantitas" placeholder="Kuantitas" required>
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
                                <th>Nama Kegiatan (SKP)</th>
                                <th>Unit</th>
                                <th>Kuantitas <br>(Target)</th>
                                <th><p class="text-red">Kuantitas <br>(Pencapaian)</p></th>
                        <th>Kualitas <br> (Target)</th>
                        <th><p class="text-red">Kualitas <br> (Pencapaian)</p></th>
                        <th>Capaian Waktu <br> (Target)</th>
                        <th><p class="text-red">Capaian Waktu <br> (Pencapaian)</p></th>
                        <th>Tgl. Buat</th>
                        <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $tt_skptahunan = bukaquery("SELECT tm_pegawai.nip, tm_unit.nama_unit, tm_skp.skp, tt_skptahunan.id_skp, tt_skptahunan.kd_skp, tt_skptahunan.kuantitas, tt_skptahunan.tgl_buat, tt_skptahunan.tgl_update, tm_skp.waktu
                                                        FROM tt_skptahunan
                                                        INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp
                                                        INNER JOIN tm_pegawai ON tt_skptahunan.id_user = tm_pegawai.id_user
                                                        INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                                        where tt_skptahunan.id_user='$id_user' and year(tt_skptahunan.tgl_buat) = '$tahun'");
                            while ($row = fetch_array($tt_skptahunan)) {
                                $no++;
                                ?>
                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-ubah-<?php echo $row['id_skp']; ?>"  role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=update-skp-tahunan&id=' . $row['id_skp'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Update SKP Tahunan</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>Aktifitas</label>
                                                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                                                            <?php
                                                            $tm_skp = bukaquery("select * from tm_skp ");
                                                            while ($skp = fetch_array($tm_skp)) {
                                                                if ($row['kd_skp'] == $skp['kd_skp']) {
                                                                    echo"<option value=" . $skp['kd_skp'] . " selected=" . $row['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                                                } else {
                                                                    echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                                                }
                                                            }
                                                            ?>               
                                                        </select>
                                                        <label>Kuantitas</label>
                                                        <input type="number" class="form-control" name="kuantitas" value="<?php echo $row['kuantitas']; ?>" required>
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
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_skp']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=delete-skp-tahunan&id=' . $row['id_skp'] . ''); ?>" method="POST" role="form">
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
                                <td><?php echo $row['skp'] . " (" . $row['waktu'] . " Menit)"; ?></td>
                                <td><?php echo $row['nama_unit'] ?></td>
                                <td><?php echo $row['kuantitas']; ?></td>
                                <td><?php $timer = getOne("SELECT SUM(ROUND(TIMESTAMPDIFF( MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir )/tm_skp.waktu)) AS volume FROM
    tt_kinerja
    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user where tt_kinerja.kd_skp='$row[kd_skp]' and tm_pegawai.id_user='$id_user' and YEAR(tanggal_kinerja)='$tahun'");
    if ($timer != null) {echo $timer;}
    else {echo "0";} ?></td>
                                <td>100%</td>
                                <td><?php echo number_format((getOne("SELECT SUM(ROUND(TIMESTAMPDIFF( MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir )/tm_skp.waktu)) AS volume FROM
    tt_kinerja
    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user where tt_kinerja.kd_skp='$row[kd_skp]' and tm_pegawai.id_user='$id_user' and YEAR(tanggal_kinerja)='$tahun'") / $row['kuantitas'] * 100), 2) . "%"; ?></td>
                                <td><?php echo $row['kuantitas'] * $row['waktu'] . " Menit"; ?></td>
                                <td><?php echo getOne("SELECT SUM(ROUND(TIMESTAMPDIFF( MINUTE, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir )/tm_skp.waktu)) AS volume FROM
    tt_kinerja
    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user where tt_kinerja.kd_skp='$row[kd_skp]' and tm_pegawai.id_user='$id_user' and YEAR(tanggal_kinerja)='$tahun'") * $row['waktu'] . " Menit"; ?></td>
                                <td><?php echo FormatTgl('d-m-Y', $row['tgl_buat']); ?></td>
                                <td>
                                <?php 
                                if ($tahun==$thn_now){
                                ?>
                                    <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_skp']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
                                    <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_skp']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
                                <?php } ?>
                                </td>
                            </tr>                                  
                            <?php
                        }
                        ?>    
                        </tbody>
                        <thead>
                        <th colspan="8"></th>
                        <th colspan="4"><?php
                            $total_target = getOne("SELECT sum(tt_skptahunan.kuantitas * tm_skp.waktu) from tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp where tt_skptahunan.id_user='$id_user' and year(tt_skptahunan.tgl_buat) = '$tahun'");
                            if ($total_target > 115200) {
                                echo $total_target . " Menit <p class='text-red'>Target anda Tidak Boleh Melebihi 115200 menit";
                            } else {
                                echo $total_target . " Menit";
                            }
                            ?></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <?php
        break;
    case "list-data-kinerja-pegawai":
    
      // ---------- Aturan edit berdasarkan setup.tutup_kinerja ----------
      $tutup_kinerja = (int)getOne("SELECT setup.tutup_kinerja FROM setup"); // mis. 5, 10, dst
      $today = new DateTime();
      $dayNow = (int)$today->format('d');
      // Bulan yang BISA diinput:
      // - Jika hari ini <= tutup_kinerja => hanya BULAN KEMARIN
      // - Jika hari ini  > tutup_kinerja => hanya BULAN INI
      $allowed = ($dayNow <= $tutup_kinerja)
               ? (clone $today)->modify('first day of last month')
               : (clone $today)->modify('first day of this month');
    
      $allowedYear  = (int)$allowed->format('Y');
      $allowedMonth = (int)$allowed->format('m');
    
      // ---------- Endpoint ----------
      $apiBase  = "module/kinerja-pegawai/api-kinerja.php";
      $aksiBase = "module/kinerja-pegawai/aksi-kinerja-pegawai.php?";
    
      $aksiBase       = "module/kinerja-pegawai/aksi-kinerja-pegawai.php?";
      $urlAdd         = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=add-kinerja&id='.$id_user); // ADD (status dari radio)
      $urlUpdateNoId  = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=update-kinerja');          // UPDATE (id via POST)
      $urlDeleteNoId  = $aksiBase . paramEncrypt('module=kinerja-pegawai&act=delete-kinerja');
    
      // ---------- Opsi SKP ----------
      function buildOptions($sql){
        $out=''; $q=bukaquery($sql);
        while($r=fetch_array($q)){ $out.='<option value="'.$r['kd_skp'].'">'.$r['skp'].' ['.$r['waktu'].' Menit]</option>'; }
        return $out ?: '<option value="">(Tidak tersedia)</option>';
      }
      $optUtama    = buildOptions("
          SELECT tm_skp.kd_skp, tm_skp.skp, tm_skp.waktu
          FROM tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp=tm_skp.kd_skp
          WHERE tt_skptahunan.id_user='$id_user' AND YEAR(tt_skptahunan.tgl_buat)='".date('Y')."'
          ORDER BY tm_skp.skp
      ");
      $optTambahan = buildOptions("SELECT kd_skp, skp, waktu FROM tm_skp ORDER BY skp");
    
    ?>
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title fa fa-calendar">  Input Kinerja (Kalender)</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
      </div>
      
      <?php $tampil_kinerja='yes'; ?>
      <div class="box-body">
        <div class="row">
          <div class="col-md-8">
            <div id="calendar-kinerja"></div>
            <p class="help-block">
              • Klik nomor tanggal untuk melihat aktivitas pada tanggal itu.  
              • Tanggal <b>boleh diedit</b> hanya untuk <b><?php echo konversiBulanTahun($allowed->format('Y-m-d')); ?></b>.  
              • Badge angka di kotak tanggal = jumlah aktivitas yang diinput.
            </p>
          </div>
          <div class="col-md-4">
            <div class="box box-primary">
              <div class="box-header with-border"><h3 class="box-title">Ringkasan Bulan</h3></div>
              <div class="box-body">
                <div><b id="label-periode"></b></div><hr>
                <p>Total aktivitas: <b id="sum-aktivitas">0</b></p>
                <p>Total waktu: <b id="sum-menit">0</b> menit</p>
                <p>Belum divalidasi: <b id="sum-belum">0</b></p>
                <p>Disetujui: <b id="sum-setuju">0</b></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal daftar aktivitas per hari -->
    <div class="modal fade" id="modalDay" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-aqua">
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title" id="modalDayTitle">Daftar Aktivitas</h4>
          </div>
          <div class="modal-body">
            <div class="text-right" style="margin-bottom:10px">
              <button id="btnAddFromDay" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah</button>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="tblDay">
                <thead>
                  <tr>
                    <th style="width:120px">Jam</th>
                    <th style="width:90px">Jenis</th>
                    <th>Nama Aktivitas</th>
                    <th>Laporan</th>
                    <th style="width:85px">Waktu</th>
                    <th style="width:85px">Volume</th>
                    <th style="width:160px">Aksi</th>
                  </tr>
                </thead>
                <tbody><!-- via JS --></tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Drawer kanan: Detil / Ubah -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
      <div class="modal-dialog" style="width:520px;">
        <form id="formDetail" method="post">
          <input type="hidden" id="detailActionUrl">
          <input type="hidden" id="detail_kd_skp" name="kd_skp">
          <input type="hidden" id="detail_id" name="id"> 
          <div class="modal-content">
            <div class="modal-header bg-light-blue">
              <button class="close" data-dismiss="modal"><span>&times;</span></button>
              <h4 class="modal-title"><i class="fa fa-list"></i> Detil Aktivitas</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="margin-bottom:10px">
                <div class="col-xs-6">
                  <label>Tanggal</label>
                  <input class="form-control" id="detail_tanggal" name="tanggal_kinerja" readonly>
                </div>
                <div class="col-xs-3">
                  <label>Jam Mulai</label>
                  <input type="text" class="form-control" id="detail_mulai"  name="waktu_mulai" autocomplete="off">
                </div>
                <div class="col-xs-3">
                  <label>Jam Selesai</label>
                  <input type="text" class="form-control" id="detail_selesai" name="waktu_akhir" autocomplete="off">
                </div>
              </div>
            
              <!-- Jenis: Utama/Tambahan -->
              <div class="form-group">
                <label>Jenis</label><br>
                <label class="radio-inline"><input type="radio" name="jenisRadio" value="Utama" checked> Utama</label>
                <label class="radio-inline"><input type="radio" name="jenisRadio" value="Tambahan"> Tambahan</label>
              </div>
            
              <!-- SKP Utama -->
              <div class="form-group" id="wrap_detail_skp_utama">
                <label>Aktivitas (SKP Utama)</label>
                <select class="form-control select2" id="detail_skp_utama" style="width:100%">
                  <option value="">-Pilih Aktifitas-</option>
                  <?php echo $optUtama; ?>
                </select>
              </div>
              <!-- SKP Tambahan -->
              <div class="form-group" id="wrap_detail_skp_tambahan" style="display:none">
                <label>Aktivitas (SKP Tambahan)</label>
                <select class="form-control select2" id="detail_skp_tambahan" style="width:100%">
                  <option value="">-Pilih Aktifitas-</option>
                  <?php echo $optTambahan; ?>
                </select>
              </div>
            
              <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" rows="4" name="uraian" id="detail_uraian" maxlength="4000"></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button id="btnDelete" type="button" class="btn btn-danger pull-left"><i class="fa fa-trash"></i> Hapus</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
              <button id="btnSave" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <style>
    /* Pusatkan badge di tengah kotak tanggal */
    #calendar-kinerja td.fc-day { position: relative; }
    .fc-mini-badge{
      position:absolute; left:50%; top:50%;
      transform: translate(-50%,-50%);
      min-width:24px; height:24px; line-height:24px;
      border-radius:12px; padding:0 8px;
      font-size:12px; text-align:center; color:#fff; background:#3c8dbc;
      pointer-events:none;   /* tidak menghalangi klik */
      z-index:1;
    }
    /* Kunci tanggal masa depan (bulan berjalan) */
    .fc-disabled-day { background:#f5f5f5 !important; cursor:not-allowed; }
    .fc-disabled-day .fc-day-number { opacity:.5; }
    </style>
    <?php
    break;

    case"update-kinerja-pegawai":
        $id = isset($url['id']) ? $url['id'] : null;
        $tt_kinerja = fetch_array(bukaquery("SELECT tm_pegawai.nip, tm_skp.skp, tt_kinerja.id_kinerja, tt_kinerja.uraian, tt_kinerja.waktu_mulai, tt_kinerja.waktu_akhir, tt_kinerja.date, tm_skp.waktu, tt_kinerja.kd_skp, tt_kinerja.tanggal_kinerja, tt_kinerja.`status`
                                                    FROM tt_kinerja
                                                    INNER JOIN tm_skp ON tt_kinerja.kd_skp = tm_skp.kd_skp
                                                    INNER JOIN tm_pegawai ON tt_kinerja.id_user = tm_pegawai.id_user
                                                    where tt_kinerja.id_kinerja='$id'"));
        $tanggal = date('m/d/Y', strtotime($tt_kinerja['tanggal_kontrak']));
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-edit">  UPDATE DATA KINERJA PEGAWAI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <form action="<?php echo $aksi . paramEncrypt('module=kinerja-pegawai&act=update-kinerja-utama&id=' . $tt_kinerja['id_kinerja'] . ''); ?>" method="POST" role="form">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" class="form-control" value="<?php echo FormatTgl('m/d/Y', $tt_kinerja['tanggal_kinerja']); ?>" name="tanggal_kinerja" readonly="" required="">
                        <label>Aktifitas</label>
                        <select class="form-control select2" name="kd_skp"  data-placeholder="-Pilih Aktifitas-" style="width: 100%;" required>
                            <option selected="selected" value="">-Pilih Aktifitas-</option>
                            <?php
                            if ($tt_kinerja['status'] == 'Utama') {
                                $tm_skptahunan = bukaquery("SELECT tt_skptahunan.kd_skp, tm_skp.skp, tm_skp.waktu
                                                                                        FROM tt_skptahunan INNER JOIN tm_skp ON tt_skptahunan.kd_skp = tm_skp.kd_skp where tt_skptahunan.id_user=" . $id_user . " ");
                                while ($skp = fetch_array($tm_skptahunan)) {
                                    if ($tt_kinerja['kd_skp'] == $skp['kd_skp']) {
                                        echo"<option value=" . $skp['kd_skp'] . " selected=" . $tt_kinerja['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    } else {
                                        echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    }
                                }
                            } else {
                                $tm_skp = bukaquery("select * from tm_skp ");
                                while ($skp = fetch_array($tm_skp)) {
                                    if ($tt_kinerja['kd_skp'] == $skp['kd_skp']) {
                                        echo"<option value=" . $skp['kd_skp'] . " selected=" . $tt_kinerja['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    } else {
                                        echo"<option value=" . $skp['kd_skp'] . ">" . $skp['skp'] . " [" . $skp['waktu'] . " Menit]</option>";
                                    }
                                }
                            }
                            ?>               
                        </select>
                        <label>Uraian Aktifitas</label>
                        <textarea class="form-control" name="uraian" rows="5"><?php echo $tt_kinerja['uraian']; ?></textarea>
                        <label>Waktu Mulai</label>
                        <div class="input-group">
                            <input type="text"  class="form-control" name="waktu_mulai" value="<?php echo $tt_kinerja['waktu_mulai'] ?>" data-inputmask='"mask": "99:99:99"' data-mask required autofocus>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                        <label>Jam Selesai </label>
                        <div class="input-group">
                            <input type="text"  class="form-control"  name="waktu_akhir" value="<?php echo $tt_kinerja['waktu_akhir'] ?>" data-inputmask='"mask": "99:99:99"' data-mask required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="?<?php echo paramEncrypt('module=kinerja-pegawai&act=list-data-kinerja-pegawai') ?>"><button type="button" class="btn btn-warning fa fa-undo" > Kembali</button></a>
                        <button type="submit" class="btn btn-primary fa fa-save"> Update</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
        break;
}
?>


