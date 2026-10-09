<?php
// ============= JADWAL PEGAWAI (AJAX MODAL) =============
// Path aksi & parameter URL
$aksi = "module/jadwal-pegawai/aksi-jadwal.php?";
$id   = isset($url['id'])  ? $url['id']  : null;
$dpa  = isset($url['dpa']) ? $url['dpa'] : null;

// Helper fallback (kalau conf belum ada RentangBulanAktif)
if (!function_exists('RentangBulanAktif')) {
  function RentangBulanAktif() {
    $today = new DateTimeImmutable('now');
    $base  = ((int)$today->format('d'))
            ? $today->modify('first day of this month')
            : $today->modify('first day of this month');
    return [
      'awal' => $base->format('Y-m-d'),
      'akhir'=> $base->modify('last day of this month')->format('Y-m-d'),
      'base' => $base
    ];
  }
}

if (!function_exists('namaBulanIndo')) {
  function namaBulanIndo($m){
    $map = [
      1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
      5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
      9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
    ];
    return $map[(int)$m] ?? $m;
  }
}

switch ((isset($url['act']) ? $url['act'] : '')) {
default:
    header("location:error404");
    exit;
    break;

case "list-data-jadwal-pegawai":

    // Tentukan rentang bulan aktif
    $aktif = RentangBulanAktif();
    $awal  = $aktif['awal'];     // 'Y-m-d'
    $akhir = $aktif['akhir'];    // 'Y-m-d'
    /** @var DateTimeImmutable $base */
    $base  = $aktif['base'];     // tgl 1 bulan aktif

    // jumlah hari di bulan aktif
    $lastDay = (int)date('d', strtotime($akhir));
    
    // ➜ tambahan label:
    $labelBulan = namaBulanIndo((int)$base->format('m')) . ' ' . $base->format('Y');
?>
<!-- MODAL: Input/Update Shift -->
<div class="modal fade" id="modalShift" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formShift">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Input/Update Shift</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="module" value="jadwal-pegawai">
        <input type="hidden" name="ajax" value="1"> <!-- penanda AJAX -->
        <input type="hidden" name="id_jadwal" id="f-id-jadwal">
        <input type="hidden" name="id_user"   id="f-id-user">
        <input type="hidden" name="nip"       id="f-nip">
        <input type="hidden" name="id_unit"   id="f-id-unit">

        <div class="form-group">
          <label>Tanggal</label>
          <input type="text" class="form-control" id="f-tanggal-label" disabled>
          <input type="hidden" name="tanggal_jadwal" id="f-tanggal">
        </div>

        <div class="form-group">
          <label>Pilih Shift</label>
          <select class="form-control select2" name="id_absensi" id="f-id-absensi" required style="width:100%;">
            <option value="">-Pilih Shift-</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" type="button" data-dismiss="modal">Tutup</button>
        <button class="btn btn-primary" id="btnSimpan" type="submit">
          <i id="spinSave" class="fa fa-spinner fa-spin" style="display:none;"></i>
          <span>Simpan</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title fa fa-list">
      INPUT JADWAL PEGAWAI
      <class="text-muted" style="margin-left:8px;">
        Periode:
        <span class="label label-info"><?php echo $labelBulan; ?></span>
    </h3>

    <!-- Nav panah: kiri = bulan sebelumnya (validasi), kanan = bulan depan -->
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body">
    <div class="box-body table-responsive">
        <a class="btn btn-default" title="Bulan Sebelumnya"
         href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=input-jadwal-bulan-sebelumnya'); ?>">
        <i class="fa fa-chevron-left"><class="text-muted" style="margin-left:8px;">
                <span class="label label-info"><?php echo konversiNamaBulan(TanggalAkhirBulanKemarin()); ?></span>
        </i>
      </a>
      <a class="btn btn-default" title="Bulan Depan"
         href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=input-jadwal-bulan-depan'); ?>"><class="text-muted" style="margin-left:8px;">
                <span class="label label-info"><?php echo konversiNamaBulan(TanggalAkhirBulanDepan()); ?></span>
        <i class="fa fa-chevron-right">
        </i>
      </a>
      <table id="jadwal" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2" class="col-nama">Nama Pegawai</th>
            <?php
            // Header tanggal: dari $awal s/d $akhir (inklusif)
            $cursor = $awal;
            while (strtotime($cursor) <= strtotime($akhir)) {
                $tanggalan = konversiTanggalDoang($cursor);
                $libur     = fetch_array(bukaquery("
                    SELECT jenis, keterangan
                    FROM tm_hari_libur
                    WHERE tanggal='$cursor'
                "));
                if (!empty($libur['jenis'])) {
                    echo "<th rowspan='1' style='background-color:red' title='"
                         . $libur['jenis'] . " (" . $libur['keterangan'] . ")'>"
                         . $tanggalan . "</th>";
                } else {
                    echo "<th rowspan='1'>"
                         . $tanggalan . "</th>";
                }
                $cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
            }
            ?>
            <th title='Hari Kerja Pagi'  style="text-align:center;background-color:lightblue;">HKP</th>
            <th title='Hari Kerja Sore'  style="text-align:center;background-color:lightblue;">HKS</th>
            <th title='Hari Kerja Malam' style="text-align:center;background-color:lightblue;">HKM</th>

            <th title='Hari Libur Pagi'  style="text-align:center;background-color:yellow;">HLP</th>
            <th title='Hari Libur Sore'  style="text-align:center;background-color:yellow;">HLS</th>
            <th title='Hari Libur Malam' style="text-align:center;background-color:yellow;">HLM</th>

            <th title='Hari Raya Pagi'   style="text-align:center;background-color:pink;">HRP</th>
            <th title='Hari Raya Sore'   style="text-align:center;background-color:pink;">HRS</th>
            <th title='Hari Raya Malam'  style="text-align:center;background-color:pink;">HRM</th>

            <th style="text-align:center;background-color:deeppink;">Total Hari</th>
            <th style="text-align:center;background-color:deeppink;">Total Off</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Menit</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Jam</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;

        $sql_pegawai = bukaquery("
            SELECT
              tm_pegawai.id_user,
              tm_pegawai.nip,
              tm_pegawai.nama_pegawai,
              tm_unit.id_unit,
              tm_unit.nama_unit,
              SUM(CASE WHEN kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS OFF,
              SUM(CASE WHEN kode_shift='NS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS NS,
              SUM(CASE WHEN kode_shift='P'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS P,
              SUM(CASE WHEN kode_shift='S'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS S,
              SUM(CASE WHEN kode_shift='M'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS M,
              SUM(CASE WHEN kode_shift='PS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS PS,
              SUM(CASE WHEN kode_shift='SM'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS SM,
              SUM(CASE WHEN kode_shift='C'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Cuti,
              SUM(CASE WHEN kode_shift='I'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Izin,
              SUM(CASE WHEN kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Sakit,
              SUM(CASE WHEN kode_shift='DL'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS DL,
              SUM(CASE WHEN kode_shift NOT IN ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS hari_kerja
            FROM tm_pegawai
            INNER JOIN tm_unit  ON tm_pegawai.id_unit = tm_unit.id_unit
            LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip
            LEFT JOIN tm_shift  ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            WHERE tm_pegawai.STATUS='AKTIF'
              AND tm_pegawai.id_unit LIKE '%$id_unit%'
            GROUP BY tm_pegawai.id_user
            ORDER BY tm_pegawai.nama_pegawai ASC
        ");

        while ($row = fetch_array($sql_pegawai)) {
          $no++;
          $nip = $row['nip'];

          $harikerja = $row['hari_kerja'];

          $kerjashift = fetch_array(bukaquery("
            SELECT id_user, nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID','SPL1')),0) AS kerjapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)     AS kerjasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)          AS kerjamalam,
                   SUM(jumlah_menit)                                AS kerjamenit
            FROM tm_jadwal
            INNER JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
            WHERE nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $liburshift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS liburpagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS libursore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS liburmalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis IN ('Libur Kerja','Libur Nasional','Cuti Bersama')
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $rayashift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS rayapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS rayasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS rayamalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis='Libur Hari Raya'
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $jum_menit = $kerjashift['kerjamenit'];
          ?>
          <tr>
            <td><?php echo $no; ?></td>
            <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
            <?php
            for ($a = 1; $a <= $lastDay; $a++) {
              $tmtcek_ = $base->setDate((int)$base->format('Y'), (int)$base->format('m'), $a)->format('Y-m-d');

              $mapping = fetch_array(bukaquery("
                SELECT tj.id_jadwal, tj.id_absensi,
                       ts.nama_shift, ts.kode_shift, ts.color AS color_key,
                       CASE 
                         WHEN ts.color='A' THEN 'red-gradient'
                         WHEN ts.color='B' THEN 'aqua-gradient'
                         WHEN ts.color='C' THEN 'blue'
                         WHEN ts.color='D' THEN 'red'
                         WHEN ts.color='E' THEN 'green'
                         WHEN ts.color='F' THEN 'pink'
                         WHEN ts.color='G' THEN 'purple'
                         WHEN ts.color='H' THEN 'orange'
                         WHEN ts.color='I' THEN 'Navy'
                         WHEN ts.color='J' THEN 'teal'
                         WHEN ts.color='K' THEN 'gray'
                         WHEN ts.color='L' THEN 'purple-gradient'
                         WHEN ts.color='M' THEN 'blue-gradient'
                         WHEN ts.color='N' THEN 'black'
                         ELSE 'white' END AS color_css
                FROM tm_jadwal tj
                INNER JOIN tm_shift ts ON ts.id_absensi = tj.id_absensi
                WHERE tj.nip_pegawai='{$row['nip']}' AND tj.tanggal_jadwal='$tmtcek_'
              "));

              $kode  = $mapping['kode_shift'] ?? '';
              $color = $mapping['color_css']  ?? 'white';
              $idJ   = $mapping['id_jadwal']  ?? '';
              $idAbs = $mapping['id_absensi'] ?? '';

              echo "<td title='".($mapping['nama_shift']??'')."'>
                      <button type='button'
                        class='btn btn-xs schedule-cell bg-$color'
                        data-nip='{$row['nip']}'
                        data-id_user='{$row['id_user']}'
                        data-id_unit='{$row['id_unit']}'
                        data-tanggal='$tmtcek_'
                        data-id_jadwal='$idJ'
                        data-id_absensi='$idAbs'
                        data-kode_shift='$kode'
                      >".($kode ?: '—')."</button>
                    </td>";
            }
            ?>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>

            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:deeppink;"><?php echo $harikerja; ?></td>
            <td style="text-align:center;background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
          </tr>
          <?php
        } // while pegawai
        ?>
        </tbody>
      </table>
    </div> <!-- /.table-responsive -->
  </div> <!-- /.box-body -->

  <script>
    window.addEventListener('load', function(){
      var $ = window.jQuery;
      if (!$ || !$.fn) {
        console.error('jQuery belum terload. Cek urutan <script> di layout.');
        return;
      }

      var ACTION_URL = 'module/jadwal-pegawai/aksi-jadwal.php';
      var SHIFT_URL  = 'module/jadwal-pegawai/ajax-get-shift.php';

      var $table = $('#jadwal');
      var $modal = $('#modalShift');
      var $form  = $('#formShift');
      var $btn   = $('#btnSimpan');
      var $spin  = $('#spinSave');

      var currentBtn = null;

      function showModal(){ $modal.modal('show'); }
      function hideModal(){ $modal.modal('hide'); }

      function toast(msg, danger){
        var $t = $('<div/>').text(msg).css({
          position:'fixed', right:'16px', bottom:'16px', zIndex:9999,
          padding:'10px 14px', borderRadius:'8px', color:'#fff',
          background: danger ? '#ef4444' : '#16a34a',
          boxShadow:'0 6px 18px rgba(0,0,0,.15)', fontWeight:600
        });
        $('body').append($t); setTimeout(function(){ $t.remove(); }, 2500);
      }

      // Klik sel → isi form → tampilkan modal → load shift
      $table.on('click', '.schedule-cell', function(){
        var $b = $(this); currentBtn = this;

        $('#f-id-jadwal').val($b.data('id_jadwal') || '');
        $('#f-id-user').val($b.data('id_user'));
        $('#f-nip').val($b.data('nip'));
        $('#f-id-unit').val($b.data('id_unit'));
        $('#f-tanggal').val($b.data('tanggal'));
        $('#f-tanggal-label').val($b.data('tanggal'));

        var $sel = $('#f-id-absensi');
        $sel.html('<option value="">Memuat shift…</option>');
        showModal();

        $.getJSON(SHIFT_URL, { id_unit: $b.data('id_unit') })
          .done(function(j){
            $sel.html(j && j.ok ? ('<option value="">-Pilih Shift-</option>'+j.html)
                                : '<option value="">(gagal memuat)</option>');
            if ($b.data('id_absensi')) $sel.val($b.data('id_absensi'));
            if ($sel.hasClass('select2') && typeof $sel.select2 === 'function') {
              $sel.select2({ dropdownParent: $modal });
            }
          })
          .fail(function(xhr){
            console.error('Gagal load shift', xhr.status, xhr.statusText);
            $sel.html('<option value="">(gagal memuat)</option>');
          });
      });

      // Submit AJAX
      $form.on('submit', function(e){
        e.preventDefault(); if(!currentBtn) return;

        var isUpdate = !!$('#f-id-jadwal').val();
        var act      = isUpdate ? 'update-jadwal' : 'add-jadwal-pegawai';

        $btn.prop('disabled', true); $spin.show();

        $.ajax({
          url: ACTION_URL + '?module=jadwal-pegawai&act=' + act,
          type: 'POST',
          data: new FormData(this),
          processData: false,
          contentType: false,
          dataType: 'json',
          headers: {'X-Requested-With':'XMLHttpRequest'}
        })
        .done(function(json){
          if(!json || !json.ok){
            toast('Gagal: ' + (json && json.message ? json.message : 'Tidak diketahui'), true);
            return;
          }
          var $b = $(currentBtn);
          $b.text(json.data.kode_shift || '—');
          $b.attr('data-id_jadwal',  json.data.id_jadwal);
          $b.attr('data-id_absensi', json.data.id_absensi);
          $b.attr('data-kode_shift', json.data.kode_shift);
          $b.removeClass().addClass('btn btn-xs schedule-cell bg-' + (json.data.color_css || 'white'));

          hideModal();
          toast('Jadwal tersimpan');
        })
        .fail(function(xhr){
          console.error('AJAX save error', xhr.status, xhr.statusText);
          toast('Gagal: ' + xhr.status + ' ' + xhr.statusText, true);
        })
        .always(function(){
          $btn.prop('disabled', false); $spin.hide();
        });
      });
    });
  </script>

</div> <!-- /.box -->
<?php
break; // end case list



/* =========================================================
 *  CASE 2: VALIDASI — BULAN KEMARIN (selalu bulan sebelumnya)
 * =======================================================*/
case "input-jadwal-bulan-sebelumnya":
    $base  = (new DateTimeImmutable('now'))->modify('first day of last month');
    $awal  = $base->format('Y-m-d');
    $akhir = $base->modify('last day of this month')->format('Y-m-d');
    $lastDay = (int)date('d', strtotime($akhir));
    $labelBulan = namaBulanIndo((int)$base->format('m')) . ' ' . $base->format('Y');
?>
<!-- MODAL (reuse) -->
<div class="modal fade" id="modalShift" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formShift">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Validasi Jadwal (Bulan Kemarin)</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="module" value="jadwal-pegawai">
        <input type="hidden" name="ajax" value="1">
        <input type="hidden" name="id_jadwal" id="f-id-jadwal">
        <input type="hidden" name="id_user"   id="f-id-user">
        <input type="hidden" name="nip"       id="f-nip">
        <input type="hidden" name="id_unit"   id="f-id-unit">

        <div class="form-group">
          <label>Tanggal</label>
          <input type="text" class="form-control" id="f-tanggal-label" disabled>
          <input type="hidden" name="tanggal_jadwal" id="f-tanggal">
        </div>

        <div class="form-group">
          <label>Pilih Shift</label>
          <select class="form-control select2" name="id_absensi" id="f-id-absensi" required style="width:100%;">
            <option value="">-Pilih Shift-</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-default" href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
          <i class="fa fa-arrow-left"></i> Kembali ke Input Bulan Aktif
        </a>
        <button class="btn btn-primary" id="btnSimpan" type="submit">
          <i id="spinSave" class="fa fa-spinner fa-spin" style="display:none;"></i>
          <span>Simpan</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title fa fa-check-square-o">
      VALIDASI JADWAL PEGAWAI (BULAN KEMARIN)
      <class="text-muted" style="margin-left:8px;">
        Periode:
        <span class="label label-primary"><?php echo $labelBulan; ?></span>
      
    </h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body">
    <div class="box-body table-responsive" style="margin-top:10px;">
      <a class="btn btn-default" title="Bulan Aktif"
         href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
        <i class="fa fa-home"> Bulan Aktif</i>
      </a>  
      <table id="jadwal" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2" class="col-nama">Nama Pegawai</th>
            <?php
            $cursor = $awal;
            while (strtotime($cursor) <= strtotime($akhir)) {
                $tanggalan = konversiTanggalDoang($cursor);
                $libur     = fetch_array(bukaquery("
                    SELECT jenis, keterangan
                    FROM tm_hari_libur
                    WHERE tanggal='$cursor'
                "));
                if (!empty($libur['jenis'])) {
                    echo "<th rowspan='2' style='background-color:red' title='"
                         . $libur['jenis'] . " (" . $libur['keterangan'] . ")'>"
                         . $tanggalan . "</th>";
                } else {
                    echo "<th rowspan='2'>"
                         . $tanggalan . "</th>";
                }
                $cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
            }
            ?>
            <th title='Hari Kerja Pagi'  style="text-align:center;background-color:lightblue;">HKP</th>
            <th title='Hari Kerja Sore'  style="text-align:center;background-color:lightblue;">HKS</th>
            <th title='Hari Kerja Malam' style="text-align:center;background-color:lightblue;">HKM</th>

            <th title='Hari Libur Pagi'  style="text-align:center;background-color:yellow;">HLP</th>
            <th title='Hari Libur Sore'  style="text-align:center;background-color:yellow;">HLS</th>
            <th title='Hari Libur Malam' style="text-align:center;background-color:yellow;">HLM</th>

            <th title='Hari Raya Pagi'   style="text-align:center;background-color:pink;">HRP</th>
            <th title='Hari Raya Sore'   style="text-align:center;background-color:pink;">HRS</th>
            <th title='Hari Raya Malam'  style="text-align:center;background-color:pink;">HRM</th>

            <th style="text-align:center;background-color:deeppink;">Total Hari</th>
            <th style="text-align:center;background-color:deeppink;">Total Off</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Menit</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Jam</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;

        $sql_pegawai = bukaquery("
            SELECT
              tm_pegawai.id_user,
              tm_pegawai.nip,
              tm_pegawai.nama_pegawai,
              tm_unit.id_unit,
              tm_unit.nama_unit,
              SUM(CASE WHEN kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS OFF,
              SUM(CASE WHEN kode_shift='NS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS NS,
              SUM(CASE WHEN kode_shift='P'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS P,
              SUM(CASE WHEN kode_shift='S'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS S,
              SUM(CASE WHEN kode_shift='M'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS M,
              SUM(CASE WHEN kode_shift='PS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS PS,
              SUM(CASE WHEN kode_shift='SM'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS SM,
              SUM(CASE WHEN kode_shift='C'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Cuti,
              SUM(CASE WHEN kode_shift='I'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Izin,
              SUM(CASE WHEN kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Sakit,
              SUM(CASE WHEN kode_shift='DL'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS DL,
              SUM(CASE WHEN kode_shift NOT IN ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS hari_kerja
            FROM tm_pegawai
            INNER JOIN tm_unit  ON tm_pegawai.id_unit = tm_unit.id_unit
            LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip
            LEFT JOIN tm_shift  ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            WHERE tm_pegawai.STATUS='AKTIF'
              AND tm_pegawai.id_unit LIKE '%$id_unit%'
            GROUP BY tm_pegawai.id_user
            ORDER BY tm_pegawai.nama_pegawai ASC
        ");

        while ($row = fetch_array($sql_pegawai)) {
          $no++;
          $nip = $row['nip'];

          $harikerja = $row['hari_kerja'];

          $kerjashift = fetch_array(bukaquery("
            SELECT id_user, nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID','SPL1')),0) AS kerjapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)     AS kerjasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)          AS kerjamalam,
                   SUM(jumlah_menit)                                AS kerjamenit
            FROM tm_jadwal
            INNER JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
            WHERE nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $liburshift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS liburpagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS libursore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS liburmalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis IN ('Libur Kerja','Libur Nasional','Cuti Bersama')
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $rayashift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS rayapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS rayasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS rayamalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis='Libur Hari Raya'
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $jum_menit = $kerjashift['kerjamenit'];
          ?>
          <tr>
            <td><?php echo $no; ?></td>
            
            <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
            <?php
            for ($a = 1; $a <= $lastDay; $a++) {
              $tmtcek_ = $base->setDate((int)$base->format('Y'), (int)$base->format('m'), $a)->format('Y-m-d');

              $mapping = fetch_array(bukaquery("
                SELECT tj.id_jadwal, tj.id_absensi,
                       ts.nama_shift, ts.kode_shift, ts.color AS color_key,
                       CASE 
                         WHEN ts.color='A' THEN 'red-gradient'
                         WHEN ts.color='B' THEN 'aqua-gradient'
                         WHEN ts.color='C' THEN 'blue'
                         WHEN ts.color='D' THEN 'red'
                         WHEN ts.color='E' THEN 'green'
                         WHEN ts.color='F' THEN 'pink'
                         WHEN ts.color='G' THEN 'purple'
                         WHEN ts.color='H' THEN 'orange'
                         WHEN ts.color='I' THEN 'Navy'
                         WHEN ts.color='J' THEN 'teal'
                         WHEN ts.color='K' THEN 'gray'
                         WHEN ts.color='L' THEN 'purple-gradient'
                         WHEN ts.color='M' THEN 'blue-gradient'
                         WHEN ts.color='N' THEN 'black'
                         ELSE 'white' END AS color_css
                FROM tm_jadwal tj
                INNER JOIN tm_shift ts ON ts.id_absensi = tj.id_absensi
                WHERE tj.nip_pegawai='{$row['nip']}' AND tj.tanggal_jadwal='$tmtcek_'
              "));

              $kode  = $mapping['kode_shift'] ?? '';
              $color = $mapping['color_css']  ?? 'white';
              $idJ   = $mapping['id_jadwal']  ?? '';
              $idAbs = $mapping['id_absensi'] ?? '';

              echo "<td title='".($mapping['nama_shift']??'')."'>
                      <button type='button'
                        class='btn btn-xs schedule-cell bg-$color'
                        data-nip='{$row['nip']}'
                        data-id_user='{$row['id_user']}'
                        data-id_unit='{$row['id_unit']}'
                        data-tanggal='$tmtcek_'
                        data-id_jadwal='$idJ'
                        data-id_absensi='$idAbs'
                        data-kode_shift='$kode'
                      >".($kode ?: '—')."</button>
                    </td>";
            }
            ?>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>

            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:deeppink;"><?php echo $harikerja; ?></td>
            <td style="text-align:center;background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
          </tr>
          <?php
        } // while pegawai
        ?>
        </tbody>
      </table>
    </div>

    <script>
      // Script AJAX: sama seperti case pertama
      window.addEventListener('load', function(){
        var $ = window.jQuery;
        if (!$ || !$.fn) {
          console.error('jQuery belum terload. Cek urutan <script> di layout.');
          return;
        }

        var ACTION_URL = 'module/jadwal-pegawai/aksi-jadwal.php';
        var SHIFT_URL  = 'module/jadwal-pegawai/ajax-get-shift.php';

        var $table = $('#jadwal');
        var $modal = $('#modalShift');
        var $form  = $('#formShift');
        var $btn   = $('#btnSimpan');
        var $spin  = $('#spinSave');

        var currentBtn = null;

        function showModal(){ $modal.modal('show'); }
        function hideModal(){ $modal.modal('hide'); }

        function toast(msg, danger){
          var $t = $('<div/>').text(msg).css({
            position:'fixed', right:'16px', bottom:'16px', zIndex:9999,
            padding:'10px 14px', borderRadius:'8px', color:'#fff',
            background: danger ? '#ef4444' : '#16a34a',
            boxShadow:'0 6px 18px rgba(0,0,0,.15)', fontWeight:600
          });
          $('body').append($t); setTimeout(function(){ $t.remove(); }, 2500);
        }

        $table.on('click', '.schedule-cell', function(){
          var $b = $(this); currentBtn = this;

          $('#f-id-jadwal').val($b.data('id_jadwal') || '');
          $('#f-id-user').val($b.data('id_user'));
          $('#f-nip').val($b.data('nip'));
          $('#f-id-unit').val($b.data('id_unit'));
          $('#f-tanggal').val($b.data('tanggal'));
          $('#f-tanggal-label').val($b.data('tanggal'));

          var $sel = $('#f-id-absensi');
          $sel.html('<option value="">Memuat shift…</option>');
          showModal();

          $.getJSON(SHIFT_URL, { id_unit: $b.data('id_unit') })
            .done(function(j){
              $sel.html(j && j.ok ? ('<option value="">-Pilih Shift-</option>'+j.html)
                                  : '<option value="">(gagal memuat)</option>');
              if ($b.data('id_absensi')) $sel.val($b.data('id_absensi'));
              if ($sel.hasClass('select2') && typeof $sel.select2 === 'function') {
                $sel.select2({ dropdownParent: $modal });
              }
            })
            .fail(function(xhr){
              console.error('Gagal load shift', xhr.status, xhr.statusText);
              $sel.html('<option value="">(gagal memuat)</option>');
            });
        });

        $form.on('submit', function(e){
          e.preventDefault(); if(!currentBtn) return;

          var isUpdate = !!$('#f-id-jadwal').val();
          var act      = isUpdate ? 'update-jadwal' : 'add-jadwal-pegawai';

          $btn.prop('disabled', true); $spin.show();

          $.ajax({
            url: ACTION_URL + '?module=jadwal-pegawai&act=' + act,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {'X-Requested-With':'XMLHttpRequest'}
          })
          .done(function(json){
            if(!json || !json.ok){
              toast('Gagal: ' + (json && json.message ? json.message : 'Tidak diketahui'), true);
              return;
            }
            var $b = $(currentBtn);
            $b.text(json.data.kode_shift || '—');
            $b.attr('data-id_jadwal',  json.data.id_jadwal);
            $b.attr('data-id_absensi', json.data.id_absensi);
            $b.attr('data-kode_shift', json.data.kode_shift);
            $b.removeClass().addClass('btn btn-xs schedule-cell bg-' + (json.data.color_css || 'white'));

            hideModal();
            toast('Jadwal tersimpan');
          })
          .fail(function(xhr){
            console.error('AJAX save error', xhr.status, xhr.statusText);
            toast('Gagal: ' + xhr.status + ' ' + xhr.statusText, true);
          })
          .always(function(){
            $btn.prop('disabled', false); $spin.hide();
          });
        });
      });
    </script>

</div>
<?php
break; // end case validasi



/* =========================================================
 *  CASE 3: INPUT — BULAN DEPAN (selalu bulan berikutnya)
 * =======================================================*/
case "input-jadwal-bulan-depan":
    $base  = (new DateTimeImmutable('now'))->modify('first day of next month');
    $awal  = $base->format('Y-m-d');
    $akhir = $base->modify('last day of this month')->format('Y-m-d');
    $lastDay = (int)date('d', strtotime($akhir));
    $labelBulan = namaBulanIndo((int)$base->format('m')) . ' ' . $base->format('Y');
?>
<!-- MODAL (reuse) -->
<div class="modal fade" id="modalShift" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formShift">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Input Jadwal (Bulan Depan)</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="module" value="jadwal-pegawai">
        <input type="hidden" name="ajax" value="1">
        <input type="hidden" name="id_jadwal" id="f-id-jadwal">
        <input type="hidden" name="id_user"   id="f-id-user">
        <input type="hidden" name="nip"       id="f-nip">
        <input type="hidden" name="id_unit"   id="f-id-unit">

        <div class="form-group">
          <label>Tanggal</label>
          <input type="text" class="form-control" id="f-tanggal-label" disabled>
          <input type="hidden" name="tanggal_jadwal" id="f-tanggal">
        </div>

        <div class="form-group">
          <label>Pilih Shift</label>
          <select class="form-control select2" name="id_absensi" id="f-id-absensi" required style="width:100%;">
            <option value="">-Pilih Shift-</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-default" href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
          <i class="fa fa-arrow-left"></i> Kembali ke Bulan Aktif
        </a>
        <button class="btn btn-primary" id="btnSimpan" type="submit">
          <i id="spinSave" class="fa fa-spinner fa-spin" style="display:none;"></i>
          <span>Simpan</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title fa fa-calendar-plus-o">
      INPUT JADWAL BULAN DEPAN
      <class="text-muted" style="margin-left:8px;">
        Periode:
        <span class="label label-success"><?php echo $labelBulan; ?></span>
      
    </h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body">
    <div class="box-body table-responsive">
      <a class="btn btn-default" title="Bulan Aktif"
         href="?<?php echo paramEncrypt('module=jadwal-pegawai&act=list-data-jadwal-pegawai'); ?>">
        <i class="fa fa-home"> Bulan Aktif</i>
      </a>
      <table id="jadwal" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2" class="col-nama">Nama Pegawai</th>
            <?php
            $cursor = $awal;
            while (strtotime($cursor) <= strtotime($akhir)) {
                $tanggalan = konversiTanggalDoang($cursor);
                $libur     = fetch_array(bukaquery("
                    SELECT jenis, keterangan
                    FROM tm_hari_libur
                    WHERE tanggal='$cursor'
                "));
                if (!empty($libur['jenis'])) {
                    echo "<th rowspan='2' style='background-color:red' title='"
                         . $libur['jenis'] . " (" . $libur['keterangan'] . ")'>"
                         . $tanggalan . "</th>";
                } else {
                    echo "<th rowspan='2'>"
                         . $tanggalan . "</th>";
                }
                $cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
            }
            ?>
            <th title='Hari Kerja Pagi'  style="text-align:center;background-color:lightblue;">HKP</th>
            <th title='Hari Kerja Sore'  style="text-align:center;background-color:lightblue;">HKS</th>
            <th title='Hari Kerja Malam' style="text-align:center;background-color:lightblue;">HKM</th>

            <th title='Hari Libur Pagi'  style="text-align:center;background-color:yellow;">HLP</th>
            <th title='Hari Libur Sore'  style="text-align:center;background-color:yellow;">HLS</th>
            <th title='Hari Libur Malam' style="text-align:center;background-color:yellow;">HLM</th>

            <th title='Hari Raya Pagi'   style="text-align:center;background-color:pink;">HRP</th>
            <th title='Hari Raya Sore'   style="text-align:center;background-color:pink;">HRS</th>
            <th title='Hari Raya Malam'  style="text-align:center;background-color:pink;">HRM</th>

            <th style="text-align:center;background-color:deeppink;">Total Hari</th>
            <th style="text-align:center;background-color:deeppink;">Total Off</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Menit</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Jam</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;

        $sql_pegawai = bukaquery("
            SELECT
              tm_pegawai.id_user,
              tm_pegawai.nip,
              tm_pegawai.nama_pegawai,
              tm_unit.id_unit,
              tm_unit.nama_unit,
              SUM(CASE WHEN kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS OFF,
              SUM(CASE WHEN kode_shift='NS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS NS,
              SUM(CASE WHEN kode_shift='P'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS P,
              SUM(CASE WHEN kode_shift='S'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS S,
              SUM(CASE WHEN kode_shift='M'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS M,
              SUM(CASE WHEN kode_shift='PS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS PS,
              SUM(CASE WHEN kode_shift='SM'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS SM,
              SUM(CASE WHEN kode_shift='C'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Cuti,
              SUM(CASE WHEN kode_shift='I'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Izin,
              SUM(CASE WHEN kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Sakit,
              SUM(CASE WHEN kode_shift='DL'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS DL,
              SUM(CASE WHEN kode_shift NOT IN ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS hari_kerja
            FROM tm_pegawai
            INNER JOIN tm_unit  ON tm_pegawai.id_unit = tm_unit.id_unit
            LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip
            LEFT JOIN tm_shift  ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            WHERE tm_pegawai.STATUS='AKTIF'
              AND tm_pegawai.id_unit LIKE '%$id_unit%'
            GROUP BY tm_pegawai.id_user
            ORDER BY tm_pegawai.nama_pegawai ASC
        ");

        while ($row = fetch_array($sql_pegawai)) {
          $no++;
          $nip = $row['nip'];

          $harikerja = $row['hari_kerja'];

          $kerjashift = fetch_array(bukaquery("
            SELECT id_user, nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID','SPL1')),0) AS kerjapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)     AS kerjasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)          AS kerjamalam,
                   SUM(jumlah_menit)                                AS kerjamenit
            FROM tm_jadwal
            INNER JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
            WHERE nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $liburshift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS liburpagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS libursore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS liburmalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis IN ('Libur Kerja','Libur Nasional','Cuti Bersama')
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $rayashift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS rayapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS rayasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS rayamalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis='Libur Hari Raya'
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $jum_menit = $kerjashift['kerjamenit'];
          ?>
          <tr>
            <td><?php echo $no; ?></td>
            
            <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
            <?php
            for ($a = 1; $a <= $lastDay; $a++) {
              $tmtcek_ = $base->setDate((int)$base->format('Y'), (int)$base->format('m'), $a)->format('Y-m-d');

              $mapping = fetch_array(bukaquery("
                SELECT tj.id_jadwal, tj.id_absensi,
                       ts.nama_shift, ts.kode_shift, ts.color AS color_key,
                       CASE 
                         WHEN ts.color='A' THEN 'red-gradient'
                         WHEN ts.color='B' THEN 'aqua-gradient'
                         WHEN ts.color='C' THEN 'blue'
                         WHEN ts.color='D' THEN 'red'
                         WHEN ts.color='E' THEN 'green'
                         WHEN ts.color='F' THEN 'pink'
                         WHEN ts.color='G' THEN 'purple'
                         WHEN ts.color='H' THEN 'orange'
                         WHEN ts.color='I' THEN 'Navy'
                         WHEN ts.color='J' THEN 'teal'
                         WHEN ts.color='K' THEN 'gray'
                         WHEN ts.color='L' THEN 'purple-gradient'
                         WHEN ts.color='M' THEN 'blue-gradient'
                         WHEN ts.color='N' THEN 'black'
                         ELSE 'white' END AS color_css
                FROM tm_jadwal tj
                INNER JOIN tm_shift ts ON ts.id_absensi = tj.id_absensi
                WHERE tj.nip_pegawai='{$row['nip']}' AND tj.tanggal_jadwal='$tmtcek_'
              "));

              $kode  = $mapping['kode_shift'] ?? '';
              $color = $mapping['color_css']  ?? 'white';
              $idJ   = $mapping['id_jadwal']  ?? '';
              $idAbs = $mapping['id_absensi'] ?? '';

              echo "<td title='".($mapping['nama_shift']??'')."'>
                      <button type='button'
                        class='btn btn-xs schedule-cell bg-$color'
                        data-nip='{$row['nip']}'
                        data-id_user='{$row['id_user']}'
                        data-id_unit='{$row['id_unit']}'
                        data-tanggal='$tmtcek_'
                        data-id_jadwal='$idJ'
                        data-id_absensi='$idAbs'
                        data-kode_shift='$kode'
                      >".($kode ?: '—')."</button>
                    </td>";
            }
            ?>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>

            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:deeppink;"><?php echo $harikerja; ?></td>
            <td style="text-align:center;background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
          </tr>
          <?php
        } // while pegawai
        ?>
        </tbody>
      </table>
    </div>

    <script>
      // Script AJAX: sama pattern
      window.addEventListener('load', function(){
        var $ = window.jQuery;
        if (!$ || !$.fn) {
          console.error('jQuery belum terload. Cek urutan <script> di layout.');
          return;
        }

        var ACTION_URL = 'module/jadwal-pegawai/aksi-jadwal.php';
        var SHIFT_URL  = 'module/jadwal-pegawai/ajax-get-shift.php';

        var $table = $('#jadwal');
        var $modal = $('#modalShift');
        var $form  = $('#formShift');
        var $btn   = $('#btnSimpan');
        var $spin  = $('#spinSave');

        var currentBtn = null;

        function showModal(){ $modal.modal('show'); }
        function hideModal(){ $modal.modal('hide'); }

        function toast(msg, danger){
          var $t = $('<div/>').text(msg).css({
            position:'fixed', right:'16px', bottom:'16px', zIndex:9999,
            padding:'10px 14px', borderRadius:'8px', color:'#fff',
            background: danger ? '#ef4444' : '#16a34a',
            boxShadow:'0 6px 18px rgba(0,0,0,.15)', fontWeight:600
          });
          $('body').append($t); setTimeout(function(){ $t.remove(); }, 2500);
        }

        $table.on('click', '.schedule-cell', function(){
          var $b = $(this); currentBtn = this;

          $('#f-id-jadwal').val($b.data('id_jadwal') || '');
          $('#f-id-user').val($b.data('id_user'));
          $('#f-nip').val($b.data('nip'));
          $('#f-id-unit').val($b.data('id_unit'));
          $('#f-tanggal').val($b.data('tanggal'));
          $('#f-tanggal-label').val($b.data('tanggal'));

          var $sel = $('#f-id-absensi');
          $sel.html('<option value="">Memuat shift…</option>');
          showModal();

          $.getJSON(SHIFT_URL, { id_unit: $b.data('id_unit') })
            .done(function(j){
              $sel.html(j && j.ok ? ('<option value="">-Pilih Shift-</option>'+j.html)
                                  : '<option value="">(gagal memuat)</option>');
              if ($b.data('id_absensi')) $sel.val($b.data('id_absensi'));
              if ($sel.hasClass('select2') && typeof $sel.select2 === 'function') {
                $sel.select2({ dropdownParent: $modal });
              }
            })
            .fail(function(xhr){
              console.error('Gagal load shift', xhr.status, xhr.statusText);
              $sel.html('<option value="">(gagal memuat)</option>');
            });
        });

        $form.on('submit', function(e){
          e.preventDefault(); if(!currentBtn) return;

          var isUpdate = !!$('#f-id-jadwal').val();
          var act      = isUpdate ? 'update-jadwal' : 'add-jadwal-pegawai';

          $btn.prop('disabled', true); $spin.show();

          $.ajax({
            url: ACTION_URL + '?module=jadwal-pegawai&act=' + act,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {'X-Requested-With':'XMLHttpRequest'}
          })
          .done(function(json){
            if(!json || !json.ok){
              toast('Gagal: ' + (json && json.message ? json.message : 'Tidak diketahui'), true);
              return;
            }
            var $b = $(currentBtn);
            $b.text(json.data.kode_shift || '—');
            $b.attr('data-id_jadwal',  json.data.id_jadwal);
            $b.attr('data-id_absensi', json.data.id_absensi);
            $b.attr('data-kode_shift', json.data.kode_shift);
            $b.removeClass().addClass('btn btn-xs schedule-cell bg-' + (json.data.color_css || 'white'));

            hideModal();
            toast('Jadwal tersimpan');
          })
          .fail(function(xhr){
            console.error('AJAX save error', xhr.status, xhr.statusText);
            toast('Gagal: ' + xhr.status + ' ' + xhr.statusText, true);
          })
          .always(function(){
            $btn.prop('disabled', false); $spin.hide();
          });
        });
      });
    </script>

</div>
<?php
break; // end case input bulan depan

case "jadwal-unit":

    // Tentukan rentang bulan aktif
    $aktif = RentangBulanAktif();
    $awal  = $aktif['awal'];     // 'Y-m-d'
    $akhir = $aktif['akhir'];    // 'Y-m-d'
    /** @var DateTimeImmutable $base */
    $base  = $aktif['base'];     // tgl 1 bulan aktif

    // jumlah hari di bulan aktif
    $lastDay = (int)date('d', strtotime($akhir));
    
    // ➜ tambahan label:
    $labelBulan = namaBulanIndo((int)$base->format('m')) . ' ' . $base->format('Y');
?>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title fa fa-list">
      JADWAL PEGAWAI
      <class="text-muted" style="margin-left:8px;">
        Periode:
        <span class="label label-info"><?php echo $labelBulan; ?></span>
      
    </h3>

    <!-- Nav panah: kiri = bulan sebelumnya (validasi), kanan = bulan depan -->
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i>
      </button>
    </div>
  </div>

  <div class="box-body">
    <div class="box-body table-responsive">
      <table id="jadwal" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2" class="col-nama">Nama Pegawai</th>
            <?php
            // Header tanggal: dari $awal s/d $akhir (inklusif)
            $cursor = $awal;
            while (strtotime($cursor) <= strtotime($akhir)) {
                $tanggalan = konversiTanggalDoang($cursor);
                $libur     = fetch_array(bukaquery("
                    SELECT jenis, keterangan
                    FROM tm_hari_libur
                    WHERE tanggal='$cursor'
                "));
                if (!empty($libur['jenis'])) {
                    echo "<th rowspan='2' style='background-color:red' title='"
                         . $libur['jenis'] . " (" . $libur['keterangan'] . ")'>"
                         . $tanggalan . "</th>";
                } else {
                    echo "<th rowspan='2'>"
                         . $tanggalan . "</th>";
                }
                $cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
            }
            ?>
            <th title='Hari Kerja Pagi'  style="text-align:center;background-color:lightblue;">HKP</th>
            <th title='Hari Kerja Sore'  style="text-align:center;background-color:lightblue;">HKS</th>
            <th title='Hari Kerja Malam' style="text-align:center;background-color:lightblue;">HKM</th>

            <th title='Hari Libur Pagi'  style="text-align:center;background-color:yellow;">HLP</th>
            <th title='Hari Libur Sore'  style="text-align:center;background-color:yellow;">HLS</th>
            <th title='Hari Libur Malam' style="text-align:center;background-color:yellow;">HLM</th>

            <th title='Hari Raya Pagi'   style="text-align:center;background-color:pink;">HRP</th>
            <th title='Hari Raya Sore'   style="text-align:center;background-color:pink;">HRS</th>
            <th title='Hari Raya Malam'  style="text-align:center;background-color:pink;">HRM</th>

            <th style="text-align:center;background-color:deeppink;">Total Hari</th>
            <th style="text-align:center;background-color:deeppink;">Total Off</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Menit</th>
            <th style="text-align:center;background-color:deepskyblue;">Total Jam</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;

        $sql_pegawai = bukaquery("
            SELECT
              tm_pegawai.id_user,
              tm_pegawai.nip,
              tm_pegawai.nama_pegawai,
              tm_unit.id_unit,
              tm_unit.nama_unit,
              SUM(CASE WHEN kode_shift='OFF' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS OFF,
              SUM(CASE WHEN kode_shift='NS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS NS,
              SUM(CASE WHEN kode_shift='P'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS P,
              SUM(CASE WHEN kode_shift='S'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS S,
              SUM(CASE WHEN kode_shift='M'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS M,
              SUM(CASE WHEN kode_shift='PS'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS PS,
              SUM(CASE WHEN kode_shift='SM'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS SM,
              SUM(CASE WHEN kode_shift='C'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Cuti,
              SUM(CASE WHEN kode_shift='I'   AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Izin,
              SUM(CASE WHEN kode_shift='Skt' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Sakit,
              SUM(CASE WHEN kode_shift='DL'  AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS DL,
              SUM(CASE WHEN kode_shift NOT IN ('OFF','C','I','Skt') AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS hari_kerja
            FROM tm_pegawai
            INNER JOIN tm_unit  ON tm_pegawai.id_unit = tm_unit.id_unit
            LEFT JOIN tm_jadwal ON tm_jadwal.nip_pegawai = tm_pegawai.nip
            LEFT JOIN tm_shift  ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            WHERE tm_pegawai.STATUS='AKTIF'
              AND tm_pegawai.id_unit LIKE '%$id_unit%'
            GROUP BY tm_pegawai.id_user
            ORDER BY tm_pegawai.nama_pegawai ASC
        ");

        while ($row = fetch_array($sql_pegawai)) {
          $no++;
          $nip = $row['nip'];

          $harikerja = $row['hari_kerja'];

          $kerjashift = fetch_array(bukaquery("
            SELECT id_user, nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID','SPL1')),0) AS kerjapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)     AS kerjasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)          AS kerjamalam,
                   SUM(jumlah_menit)                                AS kerjamenit
            FROM tm_jadwal
            INNER JOIN tm_shift ON tm_shift.id_absensi = tm_jadwal.id_absensi
            WHERE nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $liburshift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS liburpagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS libursore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS liburmalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis IN ('Libur Kerja','Libur Nasional','Cuti Bersama')
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $rayashift = fetch_array(bukaquery("
            SELECT id_user,nip_pegawai,
                   IFNULL(SUM(kode_shift IN ('P','PS','NS','DL','MID')),0) AS rayapagi,
                   IFNULL(SUM(kode_shift IN ('S','PS','SM','MID','SPL1')),0)      AS rayasore,
                   IFNULL(SUM(kode_shift IN ('SM','M')),0)           AS rayamalam
            FROM tm_jadwal
            INNER JOIN tm_shift      ON tm_shift.id_absensi  = tm_jadwal.id_absensi
            INNER JOIN tm_hari_libur ON tm_jadwal.tanggal_jadwal = tm_hari_libur.tanggal
            WHERE tm_hari_libur.jenis='Libur Hari Raya'
              AND nip_pegawai='$nip' AND tm_jadwal.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
          "));

          $jum_menit = $kerjashift['kerjamenit'];
          ?>
          <tr>
            <td><?php echo $no; ?></td>
            
            <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
            <?php
            for ($a = 1; $a <= $lastDay; $a++) {
              $tmtcek_ = $base->setDate((int)$base->format('Y'), (int)$base->format('m'), $a)->format('Y-m-d');

              $mapping = fetch_array(bukaquery("
                SELECT tj.id_jadwal, tj.id_absensi,
                       ts.nama_shift, ts.kode_shift, ts.color AS color_key,
                       CASE 
                         WHEN ts.color='A' THEN 'red-gradient'
                         WHEN ts.color='B' THEN 'aqua-gradient'
                         WHEN ts.color='C' THEN 'blue'
                         WHEN ts.color='D' THEN 'red'
                         WHEN ts.color='E' THEN 'green'
                         WHEN ts.color='F' THEN 'pink'
                         WHEN ts.color='G' THEN 'purple'
                         WHEN ts.color='H' THEN 'orange'
                         WHEN ts.color='I' THEN 'Navy'
                         WHEN ts.color='J' THEN 'teal'
                         WHEN ts.color='K' THEN 'gray'
                         WHEN ts.color='L' THEN 'purple-gradient'
                         WHEN ts.color='M' THEN 'blue-gradient'
                         WHEN ts.color='N' THEN 'black'
                         ELSE 'white' END AS color_css
                FROM tm_jadwal tj
                INNER JOIN tm_shift ts ON ts.id_absensi = tj.id_absensi
                WHERE tj.nip_pegawai='{$row['nip']}' AND tj.tanggal_jadwal='$tmtcek_'
              "));

              $kode  = $mapping['kode_shift'] ?? '';
              $color = $mapping['color_css']  ?? 'white';
              $idJ   = $mapping['id_jadwal']  ?? '';
              $idAbs = $mapping['id_absensi'] ?? '';

              echo "<td title='".($mapping['nama_shift']??'')."'>
                      <button type='button'
                        class='btn btn-xs schedule-cell bg-$color'
                        data-nip='{$row['nip']}'
                        data-id_user='{$row['id_user']}'
                        data-id_unit='{$row['id_unit']}'
                        data-tanggal='$tmtcek_'
                        data-id_jadwal='$idJ'
                        data-id_absensi='$idAbs'
                        data-kode_shift='$kode'
                      >".($kode ?: '—')."</button>
                    </td>";
            }
            ?>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjapagi'] - $liburshift['liburpagi'] - $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjasore'] - $liburshift['libursore'] - $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:lightblue;"><?php echo $kerjashift['kerjamalam'] - $liburshift['liburmalam'] - $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburpagi']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['libursore']; ?></td>
            <td style="text-align:center;background-color:yellow;"><?php echo $liburshift['liburmalam']; ?></td>

            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayapagi']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayasore']; ?></td>
            <td style="text-align:center;background-color:pink;"><?php echo $rayashift['rayamalam']; ?></td>

            <td style="text-align:center;background-color:deeppink;"><?php echo $harikerja; ?></td>
            <td style="text-align:center;background-color:deeppink;"><?php echo $row['OFF'] + $row['Cuti'] + $row['Izin'] + $row['Sakit']; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
            <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit / 60; ?></td>
          </tr>
          <?php
        } // while pegawai
        ?>
        </tbody>
      </table>
    </div> <!-- /.table-responsive -->
  </div> <!-- /.box-body -->
</div> <!-- /.box -->
<?php
break; // end case list

case "validasi-data-jadwal-pegawai":

    // Ambil pilihan user (jika ada)
    $sel_bulan = isset($_POST['bulan']) ? trim($_POST['bulan']) : '';
    $sel_tahun = isset($_POST['tahun']) ? trim($_POST['tahun']) : '';
    $sel_unit  = isset($_POST['id_unit']) ? trim($_POST['id_unit']) : '';
    
    // TANPA auto-advance > 20: default = bulan ini
    if ($sel_bulan !== '' && $sel_tahun !== '') {
      // user memilih bulan & tahun
      $base = DateTime::createFromFormat(
        'Y-m-d',
        sprintf('%04d-%02d-01', (int)$sel_tahun, (int)$sel_bulan)
      );
      if (!$base) { $base = new DateTime('first day of this month'); } // fallback aman
    } else {
      // default: tetap bulan ini (meskipun hari > 20)
      $base = new DateTime('first day of this month');
      $sel_bulan = $base->format('m');
      $sel_tahun = $base->format('Y');
    }
    
    $awal    = $base->format('Y-m-01');
    $akhir   = $base->format('Y-m-t');
    $lastDay = (int)$base->format('t');
    $ym      = $base->format('Y-m');   // dipakai saat render kolom tanggal harian


  // Label Periode
  if (!function_exists('namaBulanIndo')) {
    function namaBulanIndo($m){
      $arr=[1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
      return $arr[(int)$m] ?? $m;
    }
  }
  $labelPeriode = namaBulanIndo((int)$sel_bulan).' '.$sel_tahun;

  // --- List Unit untuk Select2 ---
  $units = bukaquery("SELECT id_unit, nama_unit FROM tm_unit ORDER BY nama_unit ASC");
  ?>
<!-- MODAL: Input/Update Shift -->
  <div class="modal fade" id="modalShift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="formShift">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Input/Update Shift</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="module" value="jadwal-pegawai">
          <input type="hidden" name="ajax" value="1">
          <input type="hidden" name="id_jadwal" id="f-id-jadwal">
          <input type="hidden" name="id_user"   id="f-id-user">
          <input type="hidden" name="nip"       id="f-nip">
          <input type="hidden" name="id_unit"   id="f-id-unit">

          <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="form-control" id="f-tanggal-label" disabled>
            <input type="hidden" name="tanggal_jadwal" id="f-tanggal">
          </div>

          <div class="form-group">
            <label>Pilih Shift</label>
            <select class="form-control select2" name="id_absensi" id="f-id-absensi" required style="width:100%;">
              <option value="">-Pilih Shift-</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" data-dismiss="modal">Tutup</button>
          <button class="btn btn-primary" id="btnSimpan" type="submit">
            <i id="spinSave" class="fa fa-spinner fa-spin" style="display:none;"></i>
            <span>Simpan</span>
          </button>
        </div>
      </form>
    </div>
  </div>

<div class="box">
    <div class="box-header with-border">
      <h3 class="box-title fa fa-list">
        VALIDASI JADWAL KEPEGAWAIAN
        <class="text-muted" style="margin-left:8px;">
          Periode: <span class="label label-info"><?php echo $labelPeriode; ?></span>
        
      </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    </div>
    
    <div class="box-body">
      <!-- FILTER -->
      <form action="" method="post" class="form-inline" style="margin-bottom:12px;">
        <div class="form-group" style="margin-right:8px;">
          <label style="margin-right:6px;">Unit</label>
          <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit-" style="min-width:260px;">
            <option value="">- Pilih Unit -</option>
            <?php while($u = fetch_array($units)){ ?>
              <option value="<?php echo $u['id_unit']; ?>" <?php echo ($sel_unit===$u['id_unit'] ? 'selected' : ''); ?>>
                <?php echo htmlspecialchars($u['nama_unit'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        
        <div class="form-group" style="margin-right:8px;">
          <label style="margin-right:6px;">Bulan</label>
          <select class="form-control select2" name="bulan" style="min-width:140px;">
            <option value="">- Bulan -</option>
            <?php
            for($m=1;$m<=12;$m++){
              $v = sprintf('%02d',$m);
              $tx = namaBulanIndo($m);
              $sel = ($sel_bulan===$v ? 'selected' : '');
              echo "<option value='$v' $sel>$tx</option>";
            }
            ?>
          </select>
        </div>
        
        <div class="form-group" style="margin-right:8px;">
          <label style="margin-right:6px;">Tahun</label>
          <select class="form-control select2" name="tahun" style="min-width:120px;">
            <option value="">- Tahun -</option>
            <?php
            $cy=(int)date('Y');
            for($y=$cy-3;$y<=$cy+3;$y++){
              $sel=($sel_tahun==$y ? 'selected' : '');
              echo "<option value='$y' $sel>$y</option>";
            }
            ?>
          </select>
        </div>
        <button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
      </form>
      
      <div class="table-responsive">
        <table id="jadwal" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th rowspan="2" style="vertical-align:middle;">No</th>
              <th rowspan="2" style="vertical-align:middle;">Nama Pegawai</th>
              <?php
              // Header tanggal 1..t (tanpa off-by-one)
              for($a=1;$a<=$lastDay;$a++){
                $tgl = sprintf('%s-%02d', $ym, $a);
                $tanggalan = konversiTanggalDoang($tgl);
                $libur = fetch_array(bukaquery("SELECT jenis, keterangan FROM tm_hari_libur WHERE tanggal='$tgl'"));
                if (!empty($libur['jenis'])){
                  $title = htmlspecialchars($libur['jenis'].' ('.$libur['keterangan'].')', ENT_QUOTES, 'UTF-8');
                  echo "<th rowspan='2' style='background-color:red;color:#fff' title='{$title}'>".$tanggalan." (".hariindo1($tgl).")</th>";
                } else {
                  echo "<th rowspan='2'>".$tanggalan." (".hariindo1($tgl).")</th>";
                }
              }
              ?>
              <th title="Hari Kerja Pagi"  style="text-align:center;background-color:lightblue;">HKP</th>
              <th title="Hari Kerja Sore"  style="text-align:center;background-color:lightblue;">HKS</th>
              <th title="Hari Kerja Malam" style="text-align:center;background-color:lightblue;">HKM</th>

              <th title="Hari Libur Pagi"  style="text-align:center;background-color:yellow;">HLP</th>
              <th title="Hari Libur Sore"  style="text-align:center;background-color:yellow;">HLS</th>
              <th title="Hari Libur Malam" style="text-align:center;background-color:yellow;">HLM</th>

              <th title="Hari Raya Pagi"   style="text-align:center;background-color:pink;">HRP</th>
              <th title="Hari Raya Sore"   style="text-align:center;background-color:pink;">HRS</th>
              <th title="Hari Raya Malam"  style="text-align:center;background-color:pink;">HRM</th>

              <th style="text-align:center;background-color:deeppink;">Total Hari</th>
              <th style="text-align:center;background-color:deeppink;">Total Off</th>
              <th style="text-align:center;background-color:deepskyblue;">Total Menit</th>
              <th style="text-align:center;background-color:deepskyblue;">Total Jam</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Filter unit (jika kosong, lepas filter)
            // Lebih aman & fleksibel (hierarki unit)
            $unitFilter = "AND p.id_unit ='-'";
            if ($sel_unit !== '') {
              // sanitize ringan (id_unit dari <select>, jadi aman; ini hanya jaga2)
              $safe_unit = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $sel_unit);
              $unitFilter = "AND p.id_unit LIKE '%$safe_unit%'";
            }


            $sql_pegawai = bukaquery("
              SELECT
                p.id_user, p.nip, p.nama_pegawai,
                u.id_unit, u.nama_unit,
                SUM(CASE WHEN s.kode_shift='OFF' AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS OFF,
                SUM(CASE WHEN s.kode_shift='NS'  AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS NS,
                SUM(CASE WHEN s.kode_shift='P'   AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS P,
                SUM(CASE WHEN s.kode_shift='S'   AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN s.kode_shift='M'   AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS M,
                SUM(CASE WHEN s.kode_shift='PS'  AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS PS,
                SUM(CASE WHEN s.kode_shift='SM'  AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS SM,
                SUM(CASE WHEN s.kode_shift='C'   AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Cuti,
                SUM(CASE WHEN s.kode_shift='I'   AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Izin,
                SUM(CASE WHEN s.kode_shift='Skt' AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS Sakit,
                SUM(CASE WHEN s.kode_shift='DL'  AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS DL,
                SUM(CASE WHEN s.kode_shift NOT IN ('OFF','C','I','Skt') AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir' THEN 1 ELSE 0 END) AS hari_kerja
              FROM tm_pegawai p
              INNER JOIN tm_unit u  ON p.id_unit = u.id_unit
              LEFT JOIN tm_jadwal j ON j.nip_pegawai = p.nip
              LEFT JOIN tm_shift  s ON s.id_absensi  = j.id_absensi
              WHERE p.STATUS='AKTIF' $unitFilter
              GROUP BY p.id_user
              ORDER BY u.nama_unit ASC, p.nama_pegawai ASC
            ");
            
            // sebelum while(...)
            if (!mysqli_num_rows($sql_pegawai)) {
              // 3 kolom (No, Unit, Nama) + $lastDay kolom tanggal + 13 kolom rekap
              $colspan = 3 + $lastDay + 13;
              echo "<tr><td colspan='{$colspan}' class='text-center text-muted'>
                      Tidak ada data untuk periode <b>{$awal}</b> s/d <b>{$akhir}</b>"
                      . ($sel_unit ? " pada unit <b>{$sql_pegawai1}</b>" : " (semua unit)") .
                    "</td></tr>";
            }

            $no=0;
            while($row = fetch_array($sql_pegawai)){
              $no++;
              $nip = $row['nip'];

              $kerjashift = fetch_array(bukaquery("
                SELECT 
                  IFNULL(SUM(s.kode_shift IN ('P','PS','NS','DL')),0) AS kerjapagi,
                  IFNULL(SUM(s.kode_shift IN ('S','PS','SM')),0)     AS kerjasore,
                  IFNULL(SUM(s.kode_shift IN ('SM','M')),0)          AS kerjamalam,
                  SUM(s.jumlah_menit)                                AS kerjamenit
                FROM tm_jadwal j
                INNER JOIN tm_shift s ON s.id_absensi=j.id_absensi
                WHERE j.nip_pegawai='$nip' AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
              "));

              $liburshift = fetch_array(bukaquery("
                SELECT
                  IFNULL(SUM(s.kode_shift IN ('P','PS','NS','DL')),0) AS liburpagi,
                  IFNULL(SUM(s.kode_shift IN ('S','PS','SM')),0)      AS libursore,
                  IFNULL(SUM(s.kode_shift IN ('SM','M')),0)           AS liburmalam
                FROM tm_jadwal j
                INNER JOIN tm_shift s      ON s.id_absensi  = j.id_absensi
                INNER JOIN tm_hari_libur h ON j.tanggal_jadwal = h.tanggal
                WHERE h.jenis IN ('Libur Kerja','Libur Nasional','Cuti Bersama')
                  AND j.nip_pegawai='$nip' AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
              "));

              $rayashift = fetch_array(bukaquery("
                SELECT
                  IFNULL(SUM(s.kode_shift IN ('P','PS','NS','DL')),0) AS rayapagi,
                  IFNULL(SUM(s.kode_shift IN ('S','PS','SM')),0)      AS rayasore,
                  IFNULL(SUM(s.kode_shift IN ('SM','M')),0)           AS rayamalam
                FROM tm_jadwal j
                INNER JOIN tm_shift s      ON s.id_absensi  = j.id_absensi
                INNER JOIN tm_hari_libur h ON j.tanggal_jadwal = h.tanggal
                WHERE h.jenis='Libur Hari Raya'
                  AND j.nip_pegawai='$nip' AND j.tanggal_jadwal BETWEEN '$awal' AND '$akhir'
              "));

              $jum_menit = (int)($kerjashift['kerjamenit'] ?? 0);
              ?>
              <tr>
                <td><?php echo $no; ?></td>
                
                <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
                <?php
                for($a=1;$a<=$lastDay;$a++){
                  $tgl = sprintf('%s-%02d', $ym, $a);
                  $mapping = fetch_array(bukaquery("
                    SELECT j.id_jadwal, j.id_absensi,
                           s.nama_shift, s.kode_shift, s.color,
                           CASE 
                             WHEN s.color='A' THEN 'red-gradient'
                             WHEN s.color='B' THEN 'aqua-gradient'
                             WHEN s.color='C' THEN 'blue'
                             WHEN s.color='D' THEN 'red'
                             WHEN s.color='E' THEN 'green'
                             WHEN s.color='F' THEN 'pink'
                             WHEN s.color='G' THEN 'purple'
                             WHEN s.color='H' THEN 'orange'
                             WHEN s.color='I' THEN 'Navy'
                             WHEN s.color='J' THEN 'teal'
                             WHEN s.color='K' THEN 'gray'
                             WHEN s.color='L' THEN 'purple-gradient'
                             WHEN s.color='M' THEN 'blue-gradient'
                             WHEN s.color='N' THEN 'black'
                             ELSE 'white' END AS color_css
                    FROM tm_jadwal j
                    INNER JOIN tm_shift s ON s.id_absensi = j.id_absensi
                    WHERE j.nip_pegawai='{$row['nip']}' AND j.tanggal_jadwal='$tgl'
                    LIMIT 1
                  "));
                  $kode  = $mapping['kode_shift'] ?? '';
                  $color = $mapping['color_css']  ?? 'white';
                  $idJ   = $mapping['id_jadwal']  ?? '';
                  $idAbs = $mapping['id_absensi'] ?? '';
                  $title = isset($mapping['nama_shift']) ? htmlspecialchars($mapping['nama_shift'], ENT_QUOTES, 'UTF-8') : '';

                  // sama seperti list-data: button (bisa klik, modal AJAX)
                  echo "<td title='{$title}'>
                          <button type='button'
                            class='btn btn-xs schedule-cell bg-{$color}'
                            data-nip='{$row['nip']}'
                            data-id_user='{$row['id_user']}'
                            data-id_unit='{$row['id_unit']}'
                            data-tanggal='{$tgl}'
                            data-id_jadwal='{$idJ}'
                            data-id_absensi='{$idAbs}'
                            data-kode_shift='{$kode}'
                          >".($kode ?: '—')."</button>
                        </td>";
                }
                ?>
                <td style="text-align:center;background-color:lightblue;"><?php echo ($kerjashift['kerjapagi'] ?? 0) - ($liburshift['liburpagi'] ?? 0) - ($rayashift['rayapagi'] ?? 0); ?></td>
                <td style="text-align:center;background-color:lightblue;"><?php echo ($kerjashift['kerjasore'] ?? 0) - ($liburshift['libursore'] ?? 0) - ($rayashift['rayasore'] ?? 0); ?></td>
                <td style="text-align:center;background-color:lightblue;"><?php echo ($kerjashift['kerjamalam'] ?? 0) - ($liburshift['liburmalam'] ?? 0) - ($rayashift['rayamalam'] ?? 0); ?></td>

                <td style="text-align:center;background-color:yellow;"><?php echo ($liburshift['liburpagi'] ?? 0); ?></td>
                <td style="text-align:center;background-color:yellow;"><?php echo ($liburshift['libursore'] ?? 0); ?></td>
                <td style="text-align:center;background-color:yellow;"><?php echo ($liburshift['liburmalam'] ?? 0); ?></td>

                <td style="text-align:center;background-color:pink;"><?php echo ($rayashift['rayapagi'] ?? 0); ?></td>
                <td style="text-align:center;background-color:pink;"><?php echo ($rayashift['rayasore'] ?? 0); ?></td>
                <td style="text-align:center;background-color:pink;"><?php echo ($rayashift['rayamalam'] ?? 0); ?></td>

                <td style="text-align:center;background-color:deeppink;"><?php echo ($row['hari_kerja'] ?? 0); ?></td>
                <td style="text-align:center;background-color:deeppink;"><?php echo ($row['OFF'] ?? 0) + ($row['Cuti'] ?? 0) + ($row['Izin'] ?? 0) + ($row['Sakit'] ?? 0); ?></td>
                <td style="text-align:center;background-color:deepskyblue;"><?php echo $jum_menit; ?></td>
                <td style="text-align:center;background-color:deepskyblue;"><?php echo number_format($jum_menit/60, 2); ?></td>
              </tr>
              <?php
            } // end while
            ?>
          </tbody>
        </table>
      </div>

    </div>

  <script>
    window.addEventListener('load', function(){
      var $ = window.jQuery;
      if (!$ || !$.fn) {
        console.error('jQuery belum terload. Cek urutan <script> di layout.');
        return;
      }

      var ACTION_URL = 'module/jadwal-pegawai/aksi-jadwal.php';
      var SHIFT_URL  = 'module/jadwal-pegawai/ajax-get-shift.php';

      var $table = $('#jadwal');
      var $modal = $('#modalShift');
      var $form  = $('#formShift');
      var $btn   = $('#btnSimpan');
      var $spin  = $('#spinSave');

      var currentBtn = null;

      function showModal(){ $modal.modal('show'); }
      function hideModal(){ $modal.modal('hide'); }

      function toast(msg, danger){
        var $t = $('<div/>').text(msg).css({
          position:'fixed', right:'16px', bottom:'16px', zIndex:9999,
          padding:'10px 14px', borderRadius:'8px', color:'#fff',
          background: danger ? '#ef4444' : '#16a34a',
          boxShadow:'0 6px 18px rgba(0,0,0,.15)', fontWeight:600
        });
        $('body').append($t); setTimeout(function(){ $t.remove(); }, 2500);
      }

      // Klik sel → isi form → tampilkan modal → load shift
      $table.on('click', '.schedule-cell', function(){
        var $b = $(this); currentBtn = this;

        $('#f-id-jadwal').val($b.data('id_jadwal') || '');
        $('#f-id-user').val($b.data('id_user'));
        $('#f-nip').val($b.data('nip'));
        $('#f-id-unit').val($b.data('id_unit'));
        $('#f-tanggal').val($b.data('tanggal'));
        $('#f-tanggal-label').val($b.data('tanggal'));

        var $sel = $('#f-id-absensi');
        $sel.html('<option value="">Memuat shift…</option>');
        showModal();

        $.getJSON(SHIFT_URL, { id_unit: $b.data('id_unit') })
          .done(function(j){
            $sel.html(j && j.ok ? ('<option value="">-Pilih Shift-</option>'+j.html)
                                : '<option value="">(gagal memuat)</option>');
            if ($b.data('id_absensi')) $sel.val($b.data('id_absensi'));
            if ($sel.hasClass('select2') && typeof $sel.select2 === 'function') {
              $sel.select2({ dropdownParent: $modal });
            }
          })
          .fail(function(xhr){
            console.error('Gagal load shift', xhr.status, xhr.statusText);
            $sel.html('<option value="">(gagal memuat)</option>');
          });
      });

      // Submit AJAX
      $form.on('submit', function(e){
        e.preventDefault(); if(!currentBtn) return;

        var isUpdate = !!$('#f-id-jadwal').val();
        var act      = isUpdate ? 'update-jadwal' : 'add-jadwal-pegawai';

        $btn.prop('disabled', true); $spin.show();

        $.ajax({
          url: ACTION_URL + '?module=jadwal-pegawai&act=' + act,
          type: 'POST',
          data: new FormData(this),
          processData: false,
          contentType: false,
          dataType: 'json',
          headers: {'X-Requested-With':'XMLHttpRequest'}
        })
        .done(function(json){
          if(!json || !json.ok){
            toast('Gagal: ' + (json && json.message ? json.message : 'Tidak diketahui'), true);
            return;
          }
          var $b = $(currentBtn);
          $b.text(json.data.kode_shift || '—');
          $b.attr('data-id_jadwal',  json.data.id_jadwal);
          $b.attr('data-id_absensi', json.data.id_absensi);
          $b.attr('data-kode_shift', json.data.kode_shift);
          $b.removeClass().addClass('btn btn-xs schedule-cell bg-' + (json.data.color_css || 'white'));

          hideModal();
          toast('Jadwal tersimpan');
        })
        .fail(function(xhr){
          console.error('AJAX save error', xhr.status, xhr.statusText);
          toast('Gagal: ' + xhr.status + ' ' + xhr.statusText, true);
        })
        .always(function(){
          $btn.prop('disabled', false); $spin.hide();
        });
      });
    });
  </script>

</div> <!-- /.box -->
<?php
break; // end case list

case "validasi-data-absensi":
    ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title fa fa-list"> VALIDASI FINGER</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body">
            <form action="" method="post">
                <div class="form-group">
                    <table>
                        <tr>
                            <td><label>Bulan</label></td>
                            <td><label>Tahun</label></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                    <?php loadBln($_POST['bulan'] ?? date('m')); ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                    <?php loadThn($_POST['tahun'] ?? date('Y')); ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="search" value="submit" class="btn btn-info">
                                    <i class="fa fa-search"></i>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>

            <?php
            date_default_timezone_set('Asia/Jakarta');

            $bulan_post = $_POST['bulan'] ?? date('m');
            $tahun_post = $_POST['tahun'] ?? date('Y');

            $valid_bulan = preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post);
            $valid_tahun = preg_match('/^\d{4}$/', (string)$tahun_post);

            if (!$valid_bulan) {
                $bulan_post = date('m');
            }

            if (!$valid_tahun) {
                $tahun_post = date('Y');
            }
            ?>

            <div class="box-header with-border">
                <h3 class="box-title fa fa-list">
                    <label align="center">
                        Data absensi bulan <?php echo konversiBulan($bulan_post) . ' ' . htmlspecialchars($tahun_post); ?>
                    </label>
                </h3>
                <div class="box-tools pull-right"></div>
            </div>

            <div class="box-body table-responsive">
                <table id="laporan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Nama Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit, tm_unit.nama_unit
                                FROM tm_pegawai
                                INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                WHERE tm_pegawai.status_pegawai != 'PJLP'
                                  AND tm_pegawai.status='AKTIF'
                                ORDER BY tm_pegawai.nama_pegawai ASC");
                        while ($row = fetch_array($sql_pegawai)) {
                            $no++;

                            $url = "module=absensi-pegawai"
                                 . "&act=cek-absensi-pegawai"
                                 . "&id=" . urlencode($row['nip'])
                                 . "&dpa=" . urlencode($row['id_unit'])
                                 . "&bulan=" . urlencode($bulan_post)
                                 . "&tahun=" . urlencode($tahun_post);
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_pegawai']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_unit']); ?></td>
                                <td>
                                    <a href="?<?php echo paramEncrypt($url); ?>">
                                        <span title="Absensi" class="btn-xs btn-info fa fa-eye">Absensi</span>
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

case "validasi-data-absensi-spesialis":
    ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title fa fa-list"> DATA ABSENSI</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="post">
                    <div class="form-group">
                        <table align="">
                            <tr>
                                <td>
                                    <label>Bulan</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                        <?php loadBln('-Pilih Bulan-'); ?>
                                    </select></td>
                                <td>
                                    <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                </td>    
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
                        $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                        if ($bulan_post == '' or $id_unit_post = '') {
                            $bulan_post = $blnsekarang;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label>Data absensi bulan <?php echo konversiBulan($blnsekarang); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php
                        } else {
                            $tgl_absensi = $cari;
                        ?>
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> <label align="center">Data absensi bulan <?php echo konversiBulan($bulan_post); ?></label></h3>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                        <?php }
                ?>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="laporan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;
                                    $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
                                    $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai ='SPESIALIS' and tm_pegawai.status='AKTIF' order by tm_unit.nama_unit desc ");
                                    while ($row = fetch_array($sql_pegawai)) {
                                        $no++;
                                    ?>
                                        <tr>
                                            <td><?php echo $no; ?></td>
                                            <td class="col-nama"><?php echo $row['nama_pegawai']; ?></td>
                                            <td>
                                                <a href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-spesialis&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span title="Absensi" class="btn-xs btn-info fa fa-eye">Absensi</span></a>
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

} // end switch
?>
