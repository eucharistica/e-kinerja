<?php
$aksi = "module/absensi/aksi-absensi-pegawai.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
$logid = $id_user;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default:
        echo "default";
        header("location:error404");
        break;

    case "absensi-pegawai":
    // =========================
    // Penentuan bulan laporan
    // =========================
    date_default_timezone_set('Asia/Jakarta');

    $sel_bulan = $_POST['bulan'] ?? $_GET['bulan'] ?? '';
    $sel_tahun = $_POST['tahun'] ?? $_GET['tahun'] ?? '';

    $sel_bulan = trim((string)$sel_bulan);
    $sel_tahun = trim((string)$sel_tahun);

    $valid_bulan = preg_match('/^(0?[1-9]|1[0-2])$/', $sel_bulan);
    $valid_tahun = preg_match('/^\d{4}$/', $sel_tahun);

    if ($valid_bulan && $valid_tahun) {
        $base = DateTime::createFromFormat(
            'Y-m-d',
            sprintf('%04d-%02d-01', (int)$sel_tahun, (int)$sel_bulan)
        );
        if (!$base) {
            $base = new DateTime('first day of this month');
        }
    } else {
        $base = new DateTime('first day of this month');
    }

    $bulan = $base->format('m');
    $tahun = $base->format('Y');

    $monthStart = $base->format('Y-m-01');
    $monthEnd   = $base->format('Y-m-t');
    ?>
    <div class="box">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title fa fa-list"> ABSENSI PEGAWAI</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>

        <div class="box-body">
          <form action="" method="post">
            <div class="form-group">
              <table align="left">
                <tr>
                  <td><label>Bulan</label></td>
                  <td><label>Tahun</label></td>
                  <td></td>
                </tr>
                <tr>
                  <td>
                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                      <?php loadBln('-Pilih Bulan-', $bulan); ?>
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                      <?php loadThn('-Pilih Tahun-', $tahun); ?>
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

          <br><br><br><br>
          <hr>

          <?php
          // =========================
          // Data pegawai
          // =========================
          if (empty($nip)) {
              echo '<div class="alert alert-danger">NIP pegawai tidak ditemukan di sesi.</div>';
              break;
          }

          $biodata   = fetch_array(bukaquery("SELECT log_finger, nama_pegawai FROM tm_pegawai WHERE nip='".addslashes($nip)."'"));
          $logFinger = $biodata['log_finger'] ?? null;
          $namaPeg   = $biodata['nama_pegawai'] ?? '-';

          if (!$logFinger) {
              echo '<div class="alert alert-danger">log_finger pegawai tidak ditemukan.</div>';
              break;
          }

          // =========================
          // Parameter & agregat
          // =========================
          $MAX_TELAT_MIN         = 225;
          $TODAY_YMD             = date('Y-m-d');
          $NOW_TS                = time();
          $MIN_OUT_GAP_MIN       = 225;
          $EARLY_LEAVE_GRACE_MIN = 5;

          $totTelatIn  = 0;
          $totTelatOut = 0;
          $totTotal    = 0;

          $labelBulan  = konversiBulanTahun($monthStart);

          $minPunch = function($user, $startTs, $endTs) {
              return getOne("SELECT MIN(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
          };

          $maxPunch = function($user, $startTs, $endTs) {
              return getOne("SELECT MAX(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
          };

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

          if (!isset($statusPegawai) || !is_array($statusPegawai) || !array_key_exists('status_pegawai', $statusPegawai)) {
              $statusPegawai = ['status_pegawai' => 'NON_SPESIALIS'];
          }
          ?>

          <div class="box-header with-border">
            <h3 class="box-title fa fa-list">
              <label align="center">Laporan Absensi <?php echo htmlspecialchars($labelBulan, ENT_QUOTES, 'UTF-8'); ?></label>
            </h3>
            <div class="box-tools pull-right"></div>
          </div>

          <div class="box-body table-responsive">
            <table id="laporan" class="table table-bordered table-striped">
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
                      WHERE j.nip_pegawai = '".addslashes($nip)."' AND j.tanggal_jadwal = '$hariYmd'
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
    <?php
    break;

    case "cek-absensi-pegawai":
    ?>
    <div class="box">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title fa fa-list"> VALIDASI FINGER PEGAWAI</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
    
        <?php
        date_default_timezone_set('Asia/Jakarta');
    
        if (!function_exists('namaBulanIndo')) {
          function namaBulanIndo($m){
            $arr=[1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return $arr[(int)$m] ?? $m;
          }
        }
    
        $sel_bulan = $_POST['bulan'] ?? $_GET['bulan'] ?? '';
        $sel_tahun = $_POST['tahun'] ?? $_GET['tahun'] ?? '';
    
        $sel_bulan = trim((string)$sel_bulan);
        $sel_tahun = trim((string)$sel_tahun);
    
        $valid_bulan = preg_match('/^(0?[1-9]|1[0-2])$/', $sel_bulan);
        $valid_tahun = preg_match('/^\d{4}$/', $sel_tahun);
    
        if ($valid_bulan && $valid_tahun) {
          $base = DateTime::createFromFormat(
            'Y-m-d',
            sprintf('%04d-%02d-01', (int)$sel_tahun, (int)$sel_bulan)
          );
          if (!$base) {
            $base = new DateTime('first day of this month');
          }
        } else {
          $base = new DateTime('first day of this month');
        }
    
        $sel_bulan = $base->format('m');
        $sel_tahun = $base->format('Y');
    
        $bulan  = $sel_bulan;
        $tahun  = $sel_tahun;
    
        $monthStart = $base->format('Y-m-01');
        $monthEnd   = $base->format('Y-m-t');
        $labelBulan = namaBulanIndo((int)$sel_bulan) . ' ' . $sel_tahun;
    
        $biodata   = fetch_array(bukaquery("SELECT log_finger, nama_pegawai FROM tm_pegawai WHERE nip='".addslashes($id)."'"));
        $logFinger = $biodata['log_finger'] ?? null;
        $namaPeg   = $biodata['nama_pegawai'] ?? '-';
    
        if (!$logFinger) {
          echo '<div class="alert alert-danger">log_finger pegawai tidak ditemukan.</div>';
          break;
        }
    
        $MAX_TELAT_MIN         = 225;
        $TODAY_YMD             = date('Y-m-d');
        $NOW_TS                = time();
        $MIN_OUT_GAP_MIN       = 225;
        $EARLY_LEAVE_GRACE_MIN = 5;
    
        $totTelatIn  = 0;
        $totTelatOut = 0;
        $totTotal    = 0;
    
        $minPunch = function($user, $startTs, $endTs) {
          return getOne("SELECT MIN(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
        };
    
        $maxPunch = function($user, $startTs, $endTs) {
          return getOne("SELECT MAX(log.tanggal) FROM log WHERE `user`='".addslashes($user)."' AND log.tanggal BETWEEN '$startTs' AND '$endTs'");
        };
    
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
    
        if (!isset($statusPegawai) || !is_array($statusPegawai) || !array_key_exists('status_pegawai', $statusPegawai)) {
          $statusPegawai = ['status_pegawai' => 'NON_SPESIALIS'];
        }
        ?>
    
        <div class="box-body">
          <form action="" method="post">
            <div class="form-group">
              <table align="left">
                <tr>
                  <td><label>Bulan</label></td>
                  <td><label>Tahun</label></td>
                  <td></td>
                </tr>
                <tr>
                  <td>
                    <select class="form-control select2" name="bulan" required>
                      <option value="">-Pilih Bulan-</option>
                      <?php
                      for ($m=1; $m<=12; $m++) {
                        $v = sprintf('%02d', $m);
                        $sel = ($sel_bulan === $v) ? 'selected' : '';
                        echo "<option value='{$v}' {$sel}>".htmlspecialchars(namaBulanIndo($m), ENT_QUOTES, 'UTF-8')."</option>";
                      }
                      ?>
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2" name="tahun" required>
                      <option value="">-Pilih Tahun-</option>
                      <?php
                      $cy = (int)date('Y');
                      for ($y=$cy-3; $y<=$cy+3; $y++) {
                        $sel = ($sel_tahun == (string)$y) ? 'selected' : '';
                        echo "<option value='{$y}' {$sel}>{$y}</option>";
                      }
                      ?>
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
    
          <br><br><br><br>
          <hr>
    
          <div class="box-header with-border">
            <h3 class="box-title fa fa-list">
              <label align="center">Laporan Absensi <?php echo htmlspecialchars($labelBulan, ENT_QUOTES, 'UTF-8'); ?></label>
            </h3>
            <div class="box-tools pull-right"></div>
          </div>

          <div class="box-body table-responsive">
            <table id="laporan" class="table table-bordered table-striped">
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
                      SELECT 
                          s.jam_masuk, s.jam_pulang, s.nama_shift, s.kode_shift, s.color, s.ai,
                          j.acc_kepegawaian, j.id_jadwal, j.id_unit, j.id_absensi AS curr_id_absensi
                      FROM tm_jadwal j
                      JOIN tm_shift s ON s.id_absensi = j.id_absensi
                      WHERE j.nip_pegawai = '".addslashes($id)."' 
                        AND j.tanggal_jadwal = '$hariYmd'
                      LIMIT 1
                    ";
                    $mapping = fetch_array(bukaquery($sqlMap));
                    if (!$mapping) {
                        $mapping = [
                          'jam_masuk'       => null,
                          'jam_pulang'      => null,
                          'nama_shift'      => '-',
                          'kode_shift'      => '-',
                          'color'           => 'white',
                          'ai'              => null,
                          'acc_kepegawaian' => '-',
                          'id_jadwal'       => null,
                          'id_unit'         => '',
                          'curr_id_absensi' => null,
                        ];
                    }

                    $kodeShift   = $mapping['kode_shift'];
                    $accPegawai  = $mapping['acc_kepegawaian'];
                    $jam_masuk   = $mapping['jam_masuk'] ?: '';
                    $jam_pulang  = $mapping['jam_pulang'] ?: '';
                    $namaShift   = $mapping['nama_shift'] ?: '-';
                    $badgeColor  = $mapping['color'] ?: 'white';
                    $logjadwal   = $mapping['id_jadwal'];
                    $currIdAbs   = $mapping['curr_id_absensi'];
                    $aiCutTs     = $computeAiCutTs($hariYmd, $jam_masuk ?: null, $mapping['ai']);
                    
                    // Label tampilan & value tanggal untuk form
                    $tglLabelDisplay = konversitanggal($hariYmd) . " (" . hariindo($hariYmd) . ")";
                    $tglValueForForm = date('m/d/Y', strtotime($hariYmd)); // aman untuk input

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
                    $modalId = 'modal-add-validasi-'. ($logjadwal ?: ('tgl-'.$hariYmd));
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($tglLabel); ?></td>
                      <td><?php echo htmlspecialchars($namaPeg); ?></td>
                      <td>
                          <button type='button' 
                                  class='btn btn-xs bg-<?php echo htmlspecialchars($badgeColor); ?>' 
                                  data-toggle='modal' 
                                  data-target='#<?php echo $modalId; ?>'>
                            <?php echo htmlspecialchars($namaShift); ?>
                          </button>
                      </td>
                      <td><?php echo htmlspecialchars($absen_in); ?></td>
                      <td><?php echo htmlspecialchars($absen_out); ?></td>
                      <td><?php echo (int)($telat_in + $pen_in); ?></td>
                      <td><?php echo (int)($telat_out + $pen_out); ?></td>
                      <td><?php echo (int)$totalHari; ?></td>
                      <td><?php echo htmlspecialchars($status); ?></td>
                    </tr>
                    
                    <!--awal modal-->
                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $modalId; ?>-label">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title fa fa-plus" id="<?php echo $modalId; ?>-label">
                              Validasi Jadwal — <?php echo htmlspecialchars($namaPeg); ?> — <?php echo htmlspecialchars($tglLabelDisplay); ?>
                            </h4>
                          </div>
                    
                          <form role="form" name="pilihan1" 
                                action="<?php echo $aksi . paramEncrypt('module=absensi-pegawai&act=update-validasi-absensi&id='.$logjadwal.'&nip='.$id.'&unit='.$mapping['id_unit']); ?>" 
                                method="post">
                            <div class="modal-body">
                              <div class="form-group">
                                <label class="control-label">Tanggal Absensi</label>
                                <?php 
                                  // id datepicker unik per modal
                                  $datepickerId = 'datepicker11-'. ($logjadwal ?: ('tgl-'.$hariYmd));
                                ?>
                                <input type="text" 
                                       class="form-control" 
                                       id="<?php echo $datepickerId; ?>" 
                                       value="<?php echo htmlspecialchars($tglValueForForm); ?>" 
                                       readonly>
                                <!-- kirim nilai Y-m-d yang akurat ke server -->
                                <input type="hidden" name="tanggal_jadwal" value="<?php echo htmlspecialchars($hariYmd); ?>">
                    
                                <label class="m-t-sm">Jadwal</label>
                                <select class="form-control select2" name="id_absensi" data-placeholder="-Pilih Shift-" style="width:100%;" required>
                                  <option value="">-Pilih Shift-</option>
                                  <?php
                                  $unit = addslashes($mapping['id_unit']);
                                  $tm_shift = bukaquery("
                                    SELECT tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang
                                    FROM tm_shift 
                                    INNER JOIN set_shift ON set_shift.id_absensi = tm_shift.id_absensi 
                                    WHERE tm_shift.status = '1' 
                                    GROUP BY tm_shift.id_absensi 
                                    ORDER BY nama_shift DESC
                                  ");
                                  while ($row = fetch_array($tm_shift)) {
                                      $sel = ($currIdAbs && $currIdAbs == $row['id_absensi']) ? ' selected="selected"' : '';
                                      echo '<option value="'.htmlspecialchars($row['id_absensi']).'"'.$sel.'>'.
                                             htmlspecialchars($row['nama_shift']).' ('.htmlspecialchars($row['jam_masuk']).' - '.htmlspecialchars($row['jam_pulang']).')'.
                                           '</option>';
                                  }
                                  ?>
                                </select>
                    
                                <label class="m-t-sm">Keterangan Absensi</label>
                                <?php enumDropdown("tm_jadwal", "acc_kepegawaian", $accPegawai, "-Pilih Keterangan-"); ?>
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
                    <!--end modal-->
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
    <?php
    break;
    
    case "cek-absensi-spesialis":
       ?>
    <div class="box">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title fa fa-list"> ABSENSI PEGAWAI</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>

        <div class="box-body">
          <form action="" method="post">
            <div class="form-group">
              <table align="Left">
                <tr>
                  <td><label>Bulan</label></td>
                  <td><label>Tahun</label></td>
                  <td></td>
                </tr>
                <tr>
                  <td>
                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                      <?php loadBln('-Pilih Bulan-'); ?>
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                      <?php loadThn('-Pilih Tahun-'); ?>
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

          <br><br><br><br>
          <hr>

          <?php
          // =========================
          // Penentuan bulan laporan
          // =========================
          $bulan_post = $_POST['bulan'] ?? '';
          $tahun_post = $_POST['tahun'] ?? '';

          if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
              $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
          } else {
              $bulan = date('m');
          }
          if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
              $tahun = (string)$tahun_post;
          } else {
              $tahun = date('Y');
          }

          $monthStart = (new DateTimeImmutable("$tahun-$bulan-01"))->format('Y-m-d');
          $monthEnd   = (new DateTimeImmutable($monthStart))->modify('last day of this month')->format('Y-m-d');

          // =========================
          // Data pegawai
          // =========================
          
          $biodata   = fetch_array(bukaquery("SELECT log_finger, nama_pegawai FROM tm_pegawai WHERE nip='$id'"));
          $logFinger = $biodata['log_finger'] ?? null;
          $namaPeg   = $biodata['nama_pegawai'] ?? '-';
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

          $MIN_OUT_GAP_MIN        = 60;            // OUT valid jika >= 60 menit setelah IN
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

          <div class="box-header with-border">
            <h3 class="box-title fa fa-list">
              <label align="center">Laporan Absensi <?php echo htmlspecialchars($labelBulan); ?></label>
            </h3>
            <div class="box-tools pull-right"></div>
          </div>

          <div class="box-body table-responsive">
            <table id="laporan" class="table table-bordered table-striped">
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
                      WHERE j.nip_pegawai = '$id' AND j.tanggal_jadwal = '$hariYmd'
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
    <?php
    break;

    case "rekap-absensi-pegawai":
        
          $bulan_post = $_POST['bulan'] ?? '';
          $tahun_post = $_POST['tahun'] ?? '';

          if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
              $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
          } else {
              $bulan = date('m');
          }
          if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
              $tahun = (string)$tahun_post;
          } else {
              $tahun = date('Y');
          }
          
          $tanggalAkhir = date("Y-m-t", strtotime("$tahun-$bulan-01"));
    ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title fa fa-list">
                VALIDASI ABSENSI PEGAWAI NON PNS BULAN <?= strtoupper(konversiBulanTahun($tanggalAkhir)) ?>
            </h3>
    
            <div style="margin-top:10px;">
                <div class="box-body">
                  <form action="" method="post">
                    <div class="form-group">
                      <table align="Left">
                        <tr>
                          <td><label>Bulan</label></td>
                          <td><label>Tahun</label></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>
                            <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                              <?php loadBln('-Pilih Bulan-'); ?>
                            </select>
                          </td>
                          <td>
                            <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                              <?php loadThn('-Pilih Tahun-'); ?>
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
            </div>
        </div>
    
        <div class="box-body table-responsive">
            <table id="laporan" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Nama Unit</th>
                        <th>Alpha</th>
                        <th>Sakit 1-2</th>
                        <th>Sakit >2</th>
                        <th>Izin</th>
                        <th>Cuti</th>
                        <th>Telat (Menit)</th>
                        <th>Pulang Cepat (Menit)</th>
                        <th>Hari Kerja</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    $sql = bukaquery("
    SELECT 
        p.id_user,
        p.nip,
        p.nama_pegawai,
        u.id_unit,
        u.nama_unit,
    
        IFNULL(wk.alpha,0) as alpha,
        IFNULL(wk.sakit1,0) as sakit1,
        IFNULL(wk.sakit2,0) as sakit2,
        IFNULL(wk.izin,0) as izin,
        IFNULL((wk.ct_alasan_k + wk.ct_sakit_k + wk.ct_persalinan_k),0) as total_cuti,
        IFNULL(wk.telat,0) as telat,
        IFNULL(wk.pulang_cepat,0) as pulang_cepat,
    
        IFNULL(ws.jumlah_hari,0) as jumlah_hari,
    
        COUNT(DISTINCT twk.id_waktu_k) as cv_waktu_pengurangan,
        COUNT(DISTINCT twt.id_waktu_t) as cv_waktu_penambahan,
        COUNT(DISTINCT tws.id_waktu_s) as cv_waktu_shift,
        COUNT(DISTINCT kd.id_disiplin) as cv_kedisiplinan,
        COUNT(DISTINCT kp.id_kompetensi) as cv_kompetensi
    
    FROM tm_pegawai p
    INNER JOIN tm_unit u ON p.id_unit=u.id_unit
    
    LEFT JOIN tm_waktu_k wk 
        ON wk.id_user=p.id_user 
        AND wk.date_k='$tanggalAkhir'
    
    LEFT JOIN (
        SELECT id_user,
        (j_hkm+j_hks+j_hlm+j_hlp+j_hls+j_hrm+j_hrp+j_hrs+j_ns) as jumlah_hari,
        date_s
        FROM tm_waktu_s
    ) ws ON ws.id_user=p.id_user AND ws.date_s='$tanggalAkhir'
    
    LEFT JOIN tm_waktu_k twk 
        ON twk.id_user=p.id_user 
        AND MONTH(twk.date_k)='$bulan'
        AND YEAR(twk.date_k)='$tahun'
    
    LEFT JOIN tm_waktu_t twt 
        ON twt.id_user=p.id_user 
        AND MONTH(twt.date_t)='$bulan'
        AND YEAR(twt.date_t)='$tahun'
    
    LEFT JOIN tm_waktu_s tws 
        ON tws.id_user=p.id_user 
        AND MONTH(tws.date_s)='$bulan'
        AND YEAR(tws.date_s)='$tahun'
    
    LEFT JOIN tm_kedisiplinan kd
        ON kd.id_user=p.id_user
        AND MONTH(kd.date_d)='$bulan'
        AND YEAR(kd.date_d)='$tahun'
    
    LEFT JOIN tm_kompetensi kp
        ON kp.id_user=p.id_user
        AND MONTH(kp.date_kompetensi)='$bulan'
        AND YEAR(kp.date_kompetensi)='$tahun'
    
    WHERE p.status_pegawai IN ('NON PNS')
    AND p.status='AKTIF'
    AND p.tgl_masuk <= '$tanggalAkhir'
    
    GROUP BY p.id_user
    ORDER BY u.nama_unit DESC
    ");
    
    $no = 1;
    while ($row = fetch_array($sql)):
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['nama_pegawai'] ?></td>
        <td><?= $row['nama_unit'] ?></td>
        <td><?= $row['alpha'] ?></td>
        <td><?= $row['sakit1'] ?></td>
        <td><?= $row['sakit2'] ?></td>
        <td><?= $row['izin'] ?></td>
        <td><?= $row['total_cuti'] ?></td>
        <td><?= $row['telat'] ?></td>
        <td><?= $row['pulang_cepat'] ?></td>
        <td><?= $row['jumlah_hari'] ?> Hari</td>
        <td>
            <?php
            echo btnValidasi(
                $row['cv_waktu_pengurangan'],
                paramEncrypt("module=validasi-pegawai&act=validasi-waktu-pengurangan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                "fa-minus-circle",
                "Validasi Pengurangan"
            );
    
            echo btnValidasi(
                $row['cv_waktu_penambahan'],
                paramEncrypt("module=validasi-pegawai&act=validasi-waktu-penambahan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                "fa-plus-circle",
                "Validasi Penambahan"
            );
    
            echo btnValidasi(
                $row['cv_waktu_shift'],
                paramEncrypt("module=validasi-pegawai&act=validasi-waktu-shift&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                "fa-clock-o",
                "Validasi Shift"
            );
    
            echo btnValidasi(
                $row['cv_kedisiplinan'],
                paramEncrypt("module=validasi-pegawai&act=validasi-kedisiplinan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                "fa-thumbs-up",
                "Validasi Kedisiplinan"
            );
    
            echo btnValidasi(
                $row['cv_kompetensi'],
                paramEncrypt("module=validasi-pegawai&act=validasi-kompetensi&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                "fa-calendar-check-o",
                "Validasi Kompetensi"
            );
            ?>
    
            <a href="?<?= paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id={$row['nip']}&dpa={$row['id_unit']}&bulan=$bulan&tahun=$tahun") ?>">
                <span class="btn-xs btn-info fa fa-eye"> Absensi</span>
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

            </tbody>
        </table>
    </div>
</div>
<?php
break;

        case "rekap-absensi-spesialis":
                
                  $bulan_post = $_POST['bulan'] ?? '';
                  $tahun_post = $_POST['tahun'] ?? '';
        
                  if (preg_match('/^(0?[1-9]|1[0-2])$/', (string)$bulan_post)) {
                      $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
                  } else {
                      $bulan = date('m');
                  }
                  if (preg_match('/^\d{4}$/', (string)$tahun_post)) {
                      $tahun = (string)$tahun_post;
                  } else {
                      $tahun = date('Y');
                  }
                  
                  $tanggalAkhir = date("Y-m-t", strtotime("$tahun-$bulan-01"));
            ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title fa fa-list">
                        VALIDASI ABSENSI DOKTER SPESIALIS BULAN <?= strtoupper(konversiBulanTahun($tanggalAkhir)) ?>
                    </h3>
            
                    <div style="margin-top:10px;">
                        <div class="box-body">
                          <form action="" method="post">
                            <div class="form-group">
                              <table align="Left">
                                <tr>
                                  <td><label>Bulan</label></td>
                                  <td><label>Tahun</label></td>
                                  <td></td>
                                </tr>
                                <tr>
                                  <td>
                                    <select class="form-control select2" name="bulan" data-placeholder="-Pilih Bulan-" required>
                                      <?php loadBln('-Pilih Bulan-'); ?>
                                    </select>
                                  </td>
                                  <td>
                                    <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                      <?php loadThn('-Pilih Tahun-'); ?>
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
                    </div>
                </div>
            
                <div class="box-body table-responsive">
                    <table id="laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pegawai</th>
                                <th>Nama Unit</th>
                                <th>Alpha</th>
                                <th>Sakit 1-2</th>
                                <th>Sakit >2</th>
                                <th>Izin</th>
                                <th>Cuti</th>
                                <th>Telat (Menit)</th>
                                <th>Pulang Cepat (Menit)</th>
                                <th>Hari Kerja</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php
            $sql = bukaquery("
            SELECT 
                p.id_user,
                p.nip,
                p.nama_pegawai,
                u.id_unit,
                u.nama_unit,
            
                IFNULL(wk.alpha,0) as alpha,
                IFNULL(wk.sakit1,0) as sakit1,
                IFNULL(wk.sakit2,0) as sakit2,
                IFNULL(wk.izin,0) as izin,
                IFNULL((wk.ct_alasan_k + wk.ct_sakit_k + wk.ct_persalinan_k),0) as total_cuti,
                IFNULL(wk.telat,0) as telat,
                IFNULL(wk.pulang_cepat,0) as pulang_cepat,
            
                IFNULL(ws.jumlah_hari,0) as jumlah_hari,
            
                COUNT(DISTINCT twk.id_waktu_k) as cv_waktu_pengurangan,
                COUNT(DISTINCT twt.id_waktu_t) as cv_waktu_penambahan,
                COUNT(DISTINCT tws.id_waktu_s) as cv_waktu_shift,
                COUNT(DISTINCT kd.id_disiplin) as cv_kedisiplinan,
                COUNT(DISTINCT kp.id_kompetensi) as cv_kompetensi
            
            FROM tm_pegawai p
            INNER JOIN tm_unit u ON p.id_unit=u.id_unit
            
            LEFT JOIN tm_waktu_k wk 
                ON wk.id_user=p.id_user 
                AND wk.date_k='$tanggalAkhir'
            
            LEFT JOIN (
                SELECT id_user,
                (j_hkm+j_hks+j_hlm+j_hlp+j_hls+j_hrm+j_hrp+j_hrs+j_ns) as jumlah_hari,
                date_s
                FROM tm_waktu_s
            ) ws ON ws.id_user=p.id_user AND ws.date_s='$tanggalAkhir'
            
            LEFT JOIN tm_waktu_k twk 
                ON twk.id_user=p.id_user 
                AND MONTH(twk.date_k)='$bulan'
                AND YEAR(twk.date_k)='$tahun'
            
            LEFT JOIN tm_waktu_t twt 
                ON twt.id_user=p.id_user 
                AND MONTH(twt.date_t)='$bulan'
                AND YEAR(twt.date_t)='$tahun'
            
            LEFT JOIN tm_waktu_s tws 
                ON tws.id_user=p.id_user 
                AND MONTH(tws.date_s)='$bulan'
                AND YEAR(tws.date_s)='$tahun'
            
            LEFT JOIN tm_kedisiplinan kd
                ON kd.id_user=p.id_user
                AND MONTH(kd.date_d)='$bulan'
                AND YEAR(kd.date_d)='$tahun'
            
            LEFT JOIN tm_kompetensi kp
                ON kp.id_user=p.id_user
                AND MONTH(kp.date_kompetensi)='$bulan'
                AND YEAR(kp.date_kompetensi)='$tahun'
            
            WHERE p.status_pegawai ='SPESIALIS'
            AND p.status='AKTIF'
            AND p.tgl_masuk <= '$tanggalAkhir'
            
            GROUP BY p.id_user
            ORDER BY u.nama_unit DESC
            ");
            
            $no = 1;
            while ($row = fetch_array($sql)):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_pegawai'] ?></td>
                <td><?= $row['nama_unit'] ?></td>
                <td><?= $row['alpha'] ?></td>
                <td><?= $row['sakit1'] ?></td>
                <td><?= $row['sakit2'] ?></td>
                <td><?= $row['izin'] ?></td>
                <td><?= $row['total_cuti'] ?></td>
                <td><?= $row['telat'] ?></td>
                <td><?= $row['pulang_cepat'] ?></td>
                <td><?= $row['jumlah_hari'] ?> Hari</td>
                <td>
                    <?php
                    echo btnValidasi(
                        $row['cv_waktu_pengurangan'],
                        paramEncrypt("module=validasi-pegawai&act=validasi-waktu-pengurangan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                        "fa-minus-circle",
                        "Validasi Pengurangan"
                    );
            
                    echo btnValidasi(
                        $row['cv_waktu_penambahan'],
                        paramEncrypt("module=validasi-pegawai&act=validasi-waktu-penambahan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                        "fa-plus-circle",
                        "Validasi Penambahan"
                    );
            
                    echo btnValidasi(
                        $row['cv_waktu_shift'],
                        paramEncrypt("module=validasi-pegawai&act=validasi-waktu-shift&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                        "fa-clock-o",
                        "Validasi Shift"
                    );
            
                    echo btnValidasi(
                        $row['cv_kedisiplinan'],
                        paramEncrypt("module=validasi-pegawai&act=validasi-kedisiplinan&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                        "fa-thumbs-up",
                        "Validasi Kedisiplinan"
                    );
            
                    echo btnValidasi(
                        $row['cv_kompetensi'],
                        paramEncrypt("module=validasi-pegawai&act=validasi-kompetensi&id={$row['id_user']}&bulan=$bulan&tahun=$tahun"),
                        "fa-calendar-check-o",
                        "Validasi Kompetensi"
                    );
                    ?>
            
                    <a href="?<?= paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id={$row['nip']}&dpa={$row['id_unit']}&bulan=$bulan&tahun=$tahun") ?>">
                        <span class="btn-xs btn-info fa fa-eye"> Absensi</span>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        break;


    case "rekap-absensi-pegawai-pjlp":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> VALIDASI ABSENSI PJLP BULAN
                                    <?php echo strtoupper((konversiBulanTahun(TanggalAkhirBulanKemarin()))); ?></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            
                            <div class="box-body">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Alpha/Hari</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Cuti</th>
                                                <th>Telat/Menit</th>
                                                <th>Pulang Cepat/Menit</th>
                                                <th>Hari Kerja</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            $tmtcek = TanggalAkhirBulanKemarin();
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai = 'PJLP' and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bln_sebelumnya' and year(tm_waktu_k.date_k)='$thn' and tm_waktu_k.id_user='$row[id_user]'");
                                                $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]'"));
                                                $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]'"));
                                                $cuti = fetch_array(bukaquery("SELECT sum(ct_alasan_k+ct_sakit_k+ct_persalinan_k) as cuti FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]'"));
                                                //                                        $shift = fetch_array(bukaquery("SELECT * FROM tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"));
                                                $jhk = fetch_array(bukaquery("SELECT (tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"));
                                                $logid = $row['id_user'];
                                                $lognama = $row['nama_pegawai'];
                                                
                                                ?>
                                                
                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <td><?php echo $pengurangan['alpha']; ?></td>
                                                    <td><?php echo $pengurangan['sakit1']; ?></td>
                                                    <td><?php echo $pengurangan['sakit2']; ?></td>
                                                    <td><?php echo $pengurangan['izin']; ?></td>
                                                    <td><?php echo $pengurangan['ct_alasan_k'] + $pengurangan['ct_sakit_k'] + $pengurangan['ct_persalinan_k']; ?>
                                                    </td>
                                                    <td><?php echo $pengurangan['telat']; ?></td>
                                                    <td><?php echo $pengurangan['pulang_cepat']; ?></td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
                                                    }
                                                    ?></td>
                                                    <td>
                                                        <?php if ($cv_waktu_penambahan != '0') { ?>
                                                            <a
                                                                href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan-pjlp&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU PENGURANGAN SELESAI"
                                                                    class="btn-xs btn-success fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a
                                                                href="?<?php echo paramEncrypt('module=validasi-pegawai&act=validasi-waktu-pengurangan-pjlp&id=' . $row['id_user']); ?>">
                                                                <i title="VALIDASI WAKTU PENGURANGAN BELUM"
                                                                    class="btn-xs btn-danger fa fa-clock-o"> -</i>
                                                            </a>
                                                        <?php } ?>

                                                        <a
                                                            href="?<?php echo paramEncrypt("module=absensi-pegawai&act=cek-absensi-pegawai&id=" . $row['nip'] . "&dpa=" . $row['id_unit'] . ""); ?>"><span
                                                                title="Absensi" class="btn-xs btn-info fa fa-eye">Absensi</span></a>
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


    case "laporan-absensi-pegawai":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> LAPORAN ABSENSI PEGAWAI PER BULAN </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <table>
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
                                                    <select class="form-control select2" name="bulan"
                                                        data-placeholder="-Pilih Bulan-" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="tahun"
                                                        data-placeholder="-Pilih Tahun-" required>
                                                        <?php loadThn('-Pilih Tahun-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="submit" name="search" name="submit" value="submit"
                                                        class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <?php
                                $hari_post = isset($_POST['hari']) ? $_POST['hari'] : null;
                                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                $tmtcek = FormatTgl('Y-m-d', $tahun_post . "-" . $bulan_post . "-01");
                                if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                    $bulan_absensi = date('Y-m-d');
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    $bulan_absensi = $tmtcek;
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi
                                                <?php echo konversiBulanTahun($bulan_absensi); ?></label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <form action="cetak-lap-absensi" method="post" target="_Blank">
                                        <table>
                                            <tr>
                                                <td><input type="hidden" class="form-control" name="bulan" width="10px"
                                                        value="<?php echo $bulan_post; ?>" readonly required></td>
                                                <td><input type="hidden" class="form-control" name="tahun"
                                                        value="<?php echo $tahun_post; ?>" readonly required></td>
                                                <td>
                                                    <button type="submit" name="submit" title="Print Lap Absensi"
                                                        class="btn btn-info"><i class="fa fa-print"> Print Lap Absensi</i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                <?php }
                                ?>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Alpha/Hari</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Telat/Menit</th>
                                                <th>Pulang Cepat/Menit</th>
                                                <th>Hari Kerja</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            $tmtcek = TanggalAkhirBulanKemarin();
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai NOT IN ('PNS','PJLP') and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and year(tm_waktu_k.date_k)='$tahun_post' and tm_waktu_k.id_user='$row[id_user]'");
                                                $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bulan_post' and year(tm_waktu_t.date_t)='$tahun_post' and tm_waktu_t.id_user='$row[id_user]'");
                                                $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan_post' and year(tm_waktu_s.date_s)='$tahun_post' and tm_waktu_s.id_user='$row[id_user]'");
                                                $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bulan_post' and year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]'");
                                                $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]'"));
                                                $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]'"));
                                                $jhk = fetch_array(bukaquery("SELECT (tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"))
                                                    ?>

                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nip']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <td><?php echo $pengurangan['alpha']; ?></td>
                                                    <td><?php echo $pengurangan['sakit1']; ?></td>
                                                    <td><?php echo $pengurangan['sakit2']; ?></td>
                                                    <td><?php echo $pengurangan['izin']; ?></td>
                                                    <td><?php echo $pengurangan['telat']; ?></td>
                                                    <td><?php echo $pengurangan['pulang_cepat']; ?></td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
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

    case "laporan-absensi-pjlp":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> LAPORAN ABSENSI PEGAWAI </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <table>
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
                                                    <select class="form-control select2" name="bulan"
                                                        data-placeholder="-Pilih Bulan-" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="tahun"
                                                        data-placeholder="-Pilih Tahun-" required>
                                                        <?php loadThn('-Pilih Tahun-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="submit" name="search" name="submit" value="submit"
                                                        class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <?php
                                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                $tmtcek = FormatTgl('Y-m-t', $tahun_post . "-" . $bulan_post . "-01");
                                if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                    $bulan_absensi = date('Y-m-d');
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    $bulan_absensi = $tmtcek;
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi
                                                <?php echo konversiBulanTahun($bulan_absensi); ?></label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <form action="cetak-lap-absensi" method="post" target="_Blank">
                                        <table>
                                            <tr>
                                                <td><input type="hidden" class="form-control" name="bulan" width="10px"
                                                        value="<?php echo $bulan_post; ?>" readonly required></td>
                                                <td><input type="hidden" class="form-control" name="tahun"
                                                        value="<?php echo $tahun_post; ?>" readonly required></td>
                                                <td>
                                                    <button type="submit" name="submit" title="Print Lap Absensi"
                                                        class="btn btn-info"><i class="fa fa-print"> Print Lap Absensi</i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                <?php }
                                ?>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Alpha/Hari</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Telat/Menit</th>
                                                <th>Pulang Cepat/Menit</th>
                                                <th>Hari Kerja</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            //                                    $tmtcek = TanggalAkhirBulanKemarin();
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai = 'PJLP' and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and year(tm_waktu_k.date_k)='$tahun_post' and tm_waktu_k.id_user='$row[id_user]'");
                                                $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bulan_post' and year(tm_waktu_t.date_t)='$tahun_post' and tm_waktu_t.id_user='$row[id_user]'");
                                                $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan_post' and year(tm_waktu_s.date_s)='$tahun_post' and tm_waktu_s.id_user='$row[id_user]'");
                                                $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bulan_post' and year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]'");
                                                $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]'"));
                                                $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]'"));
                                                $jhk = fetch_array(bukaquery("SELECT (tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"))
                                                    ?>

                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nip']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <td><?php echo $pengurangan['alpha']; ?></td>
                                                    <td><?php echo $pengurangan['sakit1']; ?></td>
                                                    <td><?php echo $pengurangan['sakit2']; ?></td>
                                                    <td><?php echo $pengurangan['izin']; ?></td>
                                                    <td><?php echo $pengurangan['telat']; ?></td>
                                                    <td><?php echo $pengurangan['pulang_cepat']; ?></td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
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

    case "laporan-absensi-pegawai-tahun":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> LAPORAN ABSENSI PEGAWAI / TAHUN </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <table>
                                            <!-- <tr>
                                            <td>
                                                <label>Tahun</label>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                                    <?php //loadThn('-Pilih Tahun-'); 
                                                            ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                            </td>
                                        </tr> -->
                                            <tr>
                                                <td>
                                                    <label>Dari Tanggal = </label>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="datepicker1"
                                                        placeholder="mm/dd/yyyy" name="tanggal_absensi" required readonly>
                                                    <?php //$cari = $_POST['tanggal_surat'];
                                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Ke Tanggal = </label>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="datepicker"
                                                        placeholder="mm/dd/yyyy" name="tanggal_absensi1" required readonly>
                                                    <?php //$cari_ = $_POST['tanggal_surat'];
                                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp&nbsp&nbsp&nbsp</td>
                                                <td>
                                                    <button type="submit" name="search" name="submit" value="submit"
                                                        class="btn btn-info">Cari Data <i class="fa fa-search"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <?php
                                $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                                $cari_ = FormatTgl('Y-m-d', $_POST['tanggal_absensi1']);
                                if ($cari == 0 or $cari_ == 0) {
                                    // $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                    // $tmtcek = FormatTgl('Y-m-t', $tahun_post . "-");
                                    // if ($tahun_post == 'Tidak Boleh') {
                                    //     $bulan_absensi = date('Y-m-d');
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label>Tahun Tidak Sesuai</label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    // $bulan_absensi = $tmtcek;
                                    ?>
                                    <div class="box-header with-border">
                                        <!-- <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi <?php //echo konversiBulanTahun($bulan_absensi); 
                                                    ?></label></h3> -->
                                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi =
                                                <?php echo konversiTanggal($cari); ?></label></h3>
                                        <h3 class="box-title fa "> <label align="center">Sampai
                                                <?php echo konversiTanggal($cari_); ?></label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <!-- <form action="cetak-lap-absensi" method="post" target="_Blank"> -->
                                    <!-- <table id="absensi" class="table table-bordered table-striped"> -->
                                    <table>
                                        <tr>
                                            <td><input type="hidden" class="form-control" name="tahun"
                                                    value="<?php echo $tahun_post; ?>" readonly required></td>
                                            <!-- <td>
                                            <button type="submit" name="submit" title="Print Lap Absensi" class="btn btn-info"><i class="fa fa-print"> Print Lap Absensi</i></button>
                                        </td> -->
                                        </tr>
                                    </table>
                                    </form>
                                <?php }
                                ?>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Cuti</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Telat/Menit</th>
                                                <th>Izin di Jadwal</th>
                                                <th>Sakit di Jadwal</th>
                                                <th>Cuti di Jadwal</th>
                                                <th>Hari Kerja</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            $tmtcek = TanggalAkhirBulanKemarin();
                                            $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                                            $cari_ = FormatTgl('Y-m-d', $_POST['tanggal_absensi1']);
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai NOT IN ('PNS','PJLP') and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                // $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where year(tm_waktu_k.date_k)='$tahun_post' and tm_waktu_k.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where year(tm_waktu_t.date_t)='$tahun_post' and tm_waktu_t.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where year(tm_waktu_s.date_s)='$tahun_post' and tm_waktu_s.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]' BETWEEN tm_waktu_k.date_k='$cari' and '$cari_'"));
                                                // $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]' BETWEEN tm_waktu_k.date_k='$cari' and '$cari_'"));
                                                $pengurangan_ = fetch_array(bukaquery("SELECT
                                            sum(ct_alasan_k) as cuti, 
                                            sum(sakit1) as sakit1,
                                            sum(sakit2) as sakit2,
                                            sum(izin) as izin,
                                            sum(telat) as telat,
                                            sum(pulang_cepat) as pulang_cepat
                                            from tm_waktu_k where tm_waktu_k.id_user='$row[id_user]' and tm_waktu_k.date_k BETWEEN '$cari' and '$cari_'"));
                                                $sakitizin = fetch_array(bukaquery("SELECT id_user,nip_pegawai, sum(id_absensi IN ('ABS-000030')) as sakit, sum(id_absensi IN ('ABS-000031')) as izin, sum(id_absensi IN ('ABS-000032')) as cuti FROM tm_jadwal where tm_jadwal.nip_pegawai= '$row[nip]' and tanggal_jadwal BETWEEN '$cari' and '$cari_'"));
                                                $jhk = fetch_array(bukaquery("SELECT sum(tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.id_user='$row[id_user]' and tm_waktu_s.date_s BETWEEN '$cari' and '$cari_'"))
                                                    ?>

                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nip']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <!-- <td><?php //echo $pengurangan_['cuti']; 
                                                                ?></td>
                                                <td><?php //echo $pengurangan_['sakit1']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['sakit2']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['izin']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['telat']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['pulang_cepat']; 
                                                            ?></td> -->
                                                    <td><?php
                                                    if ($pengurangan_['cuti'] != '') {
                                                        echo $pengurangan_['cuti'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['sakit1'] != '') {
                                                        echo $pengurangan_['sakit1'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['sakit2'] != '') {
                                                        echo $pengurangan_['sakit2'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['izin'] != '') {
                                                        echo $pengurangan_['izin'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['telat'] != '') {
                                                        echo $pengurangan_['telat'];
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($sakitizin['izin'] != '') {
                                                        echo $sakitizin['izin'];
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($sakitizin['sakit'] != '') {
                                                        echo $sakitizin['sakit'];
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($sakitizin['cuti'] != '') {
                                                        echo $sakitizin['cuti'];
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
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

    case "laporan-absensi-pjlp-tahun":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> LAPORAN ABSENSI PEGAWAI / TAHUN </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <table>
                                            <!-- <tr>
                                            <td>
                                                <label>Tahun</label>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select class="form-control select2" name="tahun" data-placeholder="-Pilih Tahun-" required>
                                                    <?php //loadThn('-Pilih Tahun-'); 
                                                            ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="submit" name="search" name="submit" value="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                            </td>
                                        </tr> -->
                                            <tr>
                                                <td>
                                                    <label>Dari Tanggal = </label>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="datepicker1"
                                                        placeholder="mm/dd/yyyy" name="tanggal_absensi" required readonly>
                                                    <?php //$cari = $_POST['tanggal_surat'];
                                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Ke Tanggal = </label>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="datepicker"
                                                        placeholder="mm/dd/yyyy" name="tanggal_absensi1" required readonly>
                                                    <?php //$cari_ = $_POST['tanggal_surat'];
                                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp&nbsp&nbsp&nbsp</td>
                                                <td>
                                                    <button type="submit" name="search" name="submit" value="submit"
                                                        class="btn btn-info">Cari Data <i class="fa fa-search"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <?php
                                $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                                $cari_ = FormatTgl('Y-m-d', $_POST['tanggal_absensi1']);
                                if ($cari == 0 or $cari_ == 0) {
                                    // $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                    // $tmtcek = FormatTgl('Y-m-t', $tahun_post . "-");
                                    // if ($tahun_post == 'Tidak Boleh') {
                                    //     $bulan_absensi = date('Y-m-d');
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label>Tahun Tidak Sesuai</label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    // $bulan_absensi = $tmtcek;
                                    ?>
                                    <div class="box-header with-border">
                                        <!-- <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi <?php //echo konversiBulanTahun($bulan_absensi); 
                                                    ?></label></h3> -->
                                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi =
                                                <?php echo konversiTanggal($cari); ?></label></h3>
                                        <h3 class="box-title fa "> <label align="center">Sampai
                                                <?php echo konversiTanggal($cari_); ?></label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <!-- <form action="cetak-lap-absensi" method="post" target="_Blank"> -->
                                    <!-- <table id="absensi" class="table table-bordered table-striped"> -->
                                    <table>
                                        <tr>
                                            <td><input type="hidden" class="form-control" name="tahun"
                                                    value="<?php echo $tahun_post; ?>" readonly required></td>
                                            <!-- <td>
                                            <button type="submit" name="submit" title="Print Lap Absensi" class="btn btn-info"><i class="fa fa-print"> Print Lap Absensi</i></button>
                                        </td> -->
                                        </tr>
                                    </table>
                                    </form>
                                <?php }
                                ?>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Alpha/Hari</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Telat/Menit</th>
                                                <th>Pulang Cepat/Menit</th>
                                                <th>Hari Kerja</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            $tmtcek = TanggalAkhirBulanKemarin();
                                            $cari = Formattgl("Y-m-d", $_POST['tanggal_absensi']);
                                            $cari_ = FormatTgl('Y-m-d', $_POST['tanggal_absensi1']);
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai = 'PJLP' and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                // $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where year(tm_waktu_k.date_k)='$tahun_post' and tm_waktu_k.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where year(tm_waktu_t.date_t)='$tahun_post' and tm_waktu_t.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where year(tm_waktu_s.date_s)='$tahun_post' and tm_waktu_s.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]' BETWEEN '$cari' and '$cari_'");
                                                // $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]' BETWEEN tm_waktu_k.date_k='$cari' and '$cari_'"));
                                                // $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]' BETWEEN tm_waktu_k.date_k='$cari' and '$cari_'"));
                                                $pengurangan_ = fetch_array(bukaquery("SELECT
                                            sum(DISTINCT alpha) as alpha, 
                                            sum(DISTINCT sakit1) as sakit1,
                                            sum(DISTINCT sakit2) as sakit2,
                                            sum(DISTINCT izin) as izin,
                                            sum(DISTINCT telat) as telat,
                                            sum(DISTINCT pulang_cepat) as pulang_cepat
                                            from tm_waktu_k where tm_waktu_k.id_user='$row[id_user]' and tm_waktu_k.date_k BETWEEN '$cari' and '$cari_'"));
                                                $jhk = fetch_array(bukaquery("SELECT sum(tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.id_user='$row[id_user]' and tm_waktu_s.date_s BETWEEN '$cari' and '$cari_'"))
                                                    ?>

                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nip']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <!-- <td><?php //echo $pengurangan_['alpha']; 
                                                                ?></td>
                                                <td><?php //echo $pengurangan_['sakit1']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['sakit2']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['izin']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['telat']; 
                                                            ?></td>
                                                <td><?php //echo $pengurangan_['pulang_cepat']; 
                                                            ?></td> -->
                                                    <td><?php
                                                    if ($pengurangan_['alpha'] != '') {
                                                        echo $pengurangan_['alpha'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['sakit1'] != '') {
                                                        echo $pengurangan_['sakit1'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['sakit2'] != '') {
                                                        echo $pengurangan_['sakit2'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['izin'] != '') {
                                                        echo $pengurangan_['izin'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['telat'] != '') {
                                                        echo $pengurangan_['telat'];
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($pengurangan_['pulang_cepat'] != '') {
                                                        echo $pengurangan_['pulang_cepat'] . " Menit";
                                                    }
                                                    ?>
                                                    </td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
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


    case "laporan-absensi-pegawai-tanggal":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> LAPORAN ABSENSI PEGAWAI </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <table>
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
                                                    <select class="form-control select2" name="bulan"
                                                        data-placeholder="-Pilih Bulan-" required>
                                                        <?php loadBln('-Pilih Bulan-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" name="tahun"
                                                        data-placeholder="-Pilih Tahun-" required>
                                                        <?php loadThn('-Pilih Tahun-'); ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="submit" name="search" name="submit" value="submit"
                                                        class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <?php
                                $bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : null;
                                $tahun_post = isset($_POST['tahun']) ? $_POST['tahun'] : null;
                                $tmtcek = FormatTgl('Y-m-t', $tahun_post . "-" . $bulan_post . "-01");
                                if ($bulan_post == '-Pilih Bulan-' or $tahun_post == 'Tidak Boleh') {
                                    $bulan_absensi = date('Y-m-d');
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label>Bulan dan Tahun Tidak Sesuai</label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    $bulan_absensi = $tmtcek;
                                    ?>
                                    <div class="box-header with-border">
                                        <h3 class="box-title fa fa-list"> <label align="center">Laporan Absensi
                                                <?php echo konversiBulanTahun($bulan_absensi); ?></label></h3>
                                        <div class="box-tools pull-right">
                                        </div>
                                    </div>
                                    <form action="cetak-lap-absensi" method="post" target="_Blank">
                                        <table>
                                            <tr>
                                                <td><input type="hidden" class="form-control" name="bulan" width="10px"
                                                        value="<?php echo $bulan_post; ?>" readonly required></td>
                                                <td><input type="hidden" class="form-control" name="tahun"
                                                        value="<?php echo $tahun_post; ?>" readonly required></td>
                                                <td>
                                                    <button type="submit" name="submit" title="Print Lap Absensi"
                                                        class="btn btn-info"><i class="fa fa-print"> Print Lap Absensi</i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                <?php }
                                $from = FormatTgl('00-m-Y', $bulan_absensi);
                                $to = FormatTgl('t-m-Y', $bulan_absensi);
                                ?>
                                <div class="box-body table-responsive">
                                    <table id="laporan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama Pegawai</th>
                                                <th>Nama Unit</th>
                                                <th>Alpha/Hari</th>
                                                <th>Sakit 1-2/Hari</th>
                                                <th>Sakit >2/Hari</th>
                                                <th>Izin/Hari</th>
                                                <th>Telat/Menit</th>
                                                <th>Pulang Cepat/Menit</th>
                                                <th>Hari Kerja</th>
                                                <?php while (strtotime($from) < strtotime($to)) {
                                                    $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                                    $from = date("Y-m-d ", $from);
                                                    $tanggalan = konversitanggal($from);
                                                    echo "<th>" . $tanggalan . "</th>";
                                                }
                                                ?>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            //                                    $tmtcek = TanggalAkhirBulanKemarin();
                                            $sql_pegawai = bukaquery("SELECT tm_pegawai.id_user, tm_pegawai.log_finger,
                                            tm_pegawai.nip, tm_pegawai.nama_pegawai, tm_unit.id_unit , tm_unit.nama_unit                                
                                            FROM tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit=tm_unit.id_unit
                                            where tm_pegawai.status_pegawai!='PNS' and tm_pegawai.status='AKTIF' and tm_pegawai.tgl_masuk <= '$tmtcek' order by tm_unit.nama_unit desc ");
                                            while ($row = fetch_array($sql_pegawai)) {
                                                $no++;
                                                $cv_waktu_penambahan = getOne("select count(id_waktu_k) from tm_waktu_k where month(tm_waktu_k.date_k)='$bulan_post' and year(tm_waktu_k.date_k)='$tahun_post' and tm_waktu_k.id_user='$row[id_user]'");
                                                $cv_waktu_pengurangan = getOne("select count(id_waktu_t) from tm_waktu_t where month(tm_waktu_t.date_t)='$bulan_post' and year(tm_waktu_t.date_t)='$tahun_post' and tm_waktu_t.id_user='$row[id_user]'");
                                                $cv_waktu_shift = getOne("select count(id_waktu_s) from tm_waktu_s where month(tm_waktu_s.date_s)='$bulan_post' and year(tm_waktu_s.date_s)='$tahun_post' and tm_waktu_s.id_user='$row[id_user]'");
                                                $cv_potongan_pegawai = getOne("select count(id_potongan_p) from tm_potongan_p where month(tm_potongan_p.date_p)='$bulan_post' and year(tm_potongan_p.date_p)='$tahun_post' and tm_potongan_p.id_user='$row[id_user]'");
                                                $penambahan = fetch_array(bukaquery("SELECT * FROM tm_waktu_t where tm_waktu_t.date_t='$tmtcek' and tm_waktu_t.id_user='$row[id_user]'"));
                                                $pengurangan = fetch_array(bukaquery("SELECT * FROM tm_waktu_k where tm_waktu_k.date_k='$tmtcek' and tm_waktu_k.id_user='$row[id_user]'"));
                                                $jhk = fetch_array(bukaquery("SELECT (tm_waktu_s.j_hkm+tm_waktu_s.j_hks+tm_waktu_s.j_hlm+tm_waktu_s.j_hlp+tm_waktu_s.j_hls+tm_waktu_s.j_hrm+tm_waktu_s.j_hrp+tm_waktu_s.j_hrs+tm_waktu_s.j_ns) as jumlah from tm_waktu_s where tm_waktu_s.date_s='$tmtcek' and tm_waktu_s.id_user='$row[id_user]'"))
                                                    //$biodata = fetch_array(bukaquery("select log_finger,nama_pegawai from tm_pegawai where nip='$nip'");
                                                    //$in = getOne("SELECT log.tanggal FROM  log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user` where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and log.status='I' ORDER BY log.tanggal asc ");
                                                    //$out = getOne("SELECT log.tanggal FROM log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user` where tm_pegawai.log_finger='$biodata[log_finger]' AND log.tanggal LIKE '%$from%' and log.status='O' ORDER BY log.tanggal desc"))
                                                    ?>

                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nip']; ?></td>
                                                    <td><?php echo $row['nama_pegawai']; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <td><?php echo $pengurangan['alpha']; ?></td>
                                                    <td><?php echo $pengurangan['sakit1']; ?></td>
                                                    <td><?php echo $pengurangan['sakit2']; ?></td>
                                                    <td><?php echo $pengurangan['izin']; ?></td>
                                                    <td><?php echo $pengurangan['telat']; ?></td>
                                                    <td><?php echo $pengurangan['pulang_cepat']; ?></td>
                                                    <td><?php
                                                    if ($jhk['jumlah'] != '') {
                                                        echo $jhk['jumlah'] . " Hari";
                                                    }
                                                    ?>
                                                    </td>
                                                    <?php
                                                    for ($a = 1; $a <= FormatTgl('d', $from); $a++) {
                                                        $tmtcek_ = $tahun_post . "-" . $bulan_post . "-" . $a;
                                                        $in = getOne("SELECT log.tanggal
                                                                FROM
                                                                log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                                where tm_pegawai.log_finger='$row[log_finger]' AND log.tanggal LIKE '%$tmtcek_%' and log.status='I' ORDER BY log.tanggal asc ");
                                                        $out = getOne("SELECT log.tanggal
                                                                FROM
                                                                log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                                where tm_pegawai.log_finger='$row[log_finger]' AND log.tanggal LIKE '%$tmtcek_%' and log.status='O' ORDER BY log.tanggal Desc ");
                                                        echo "<td>" . FormatTgl('H:i:s', $in) . " " . FormatTgl('H:i:s', $out) . "</td>";
                                                    }

                                                    // while (strtotime($from) < strtotime($to)) {
                                                    //     $from = mktime(0, 0, 0, date("m", strtotime($from)), date("d", strtotime($from)) + 1, date("Y", strtotime($from)));
                                                    //     $from = date("Y-m-d ", $from);
                                                    //     $tanggalan = konversitanggal($from);
                                                    //$in = getOne("SELECT log.tanggal
                                                    //FROM
                                                    //log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                    //where tm_pegawai.log_finger='$row[log_finger]' AND log.tanggal LIKE '%$from%' and log.status='I' ORDER BY log.tanggal asc ");
                                                    //$out = getOne("SELECT log.tanggal
                                                    //FROM
                                                    //log INNER JOIN tm_pegawai ON tm_pegawai.log_finger=log.`user`
                                                    //where tm_pegawai.log_finger='$row[log_finger]' AND log.tanggal LIKE '%$from%' and log.status='O' ORDER BY log.tanggal Desc ");
                                                    // echo "<td> tess ".$tanggalan." - ". $tanggalan."</td>";
                                                    // }
                                                    ?>
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

    case "pengaturan-absensi":
        ?>
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title fa fa-list"> KONFIGURASI ABSENSI</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                        title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal"
                                    data-target="#modal-add-pengaturan">
                                    Buat Pengaturan
                                </button>
                                <br>
                                <!--Modal Add SKP -->
                                <div class="modal fade" id="modal-add-pengaturan">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title fa fa-plus"> Konfigurasi Shift</h4>
                                            </div>
                                            <form role="form"
                                                action="<?php echo $aksi . paramEncrypt('module=pengaturan&act=add-pengaturan'); ?>"
                                                method="post">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Bagian/Unit</label>
                                                        <select class="form-control select2" name="id_unit"
                                                            data-placeholder="-Pilih Bagian/Unit-" style="width: 100%;"
                                                            required>
                                                            <option selected="selected" value="">-Pilih Bagian/Unit-</option>
                                                            <?php
                                                            $tm_bagian = bukaquery("select tm_unit.id_unit, tm_unit.nama_unit from tm_unit");
                                                            while ($row = fetch_array($tm_bagian)) {
                                                                echo "<option value=" . $row['id_unit'] . ">" . $row['nama_unit'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <label>Shift</label>
                                                        <select class="form-control select2" name="id_absensi"
                                                            data-placeholder="-Pilih Shift-" style="width: 100%;" required>
                                                            <option selected="selected" value="">-Pilih Shift-</option>
                                                            <?php
                                                            $tm_shift = bukaquery("select tm_shift.id_absensi, tm_shift.nama_shift from tm_shift WHERE tm_shift.`status` = '1'");
                                                            while ($row = fetch_array($tm_shift)) {
                                                                echo "<option value=" . $row['id_absensi'] . ">" . $row['nama_shift'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <br><br><br>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
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
                                                <th>Nama Unit</th>
                                                <th>Nama Shift</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 0;
                                            $tt_shift = bukaquery("select set_shift.id_shift, tm_unit.id_unit, tm_unit.nama_unit, tm_shift.id_absensi, tm_shift.nama_shift from set_shift
                                    inner join tm_shift on set_shift.id_absensi=tm_shift.id_absensi
                                    inner join tm_unit on set_shift.id_unit=tm_unit.id_unit WHERE tm_shift.`status` = '1' ");
                                            while ($row = fetch_array($tt_shift)) {
                                                $no++;
                                                ?>
                                                <!-- Edit Modal SKP -->

                                                <!-- Tutup Edit Modal permintaan -->
                                                <!-- Edit Modal SKP -->
                                                <div class="modal fade" id="modal-hapus-<?php echo $row['id_shift']; ?>"
                                                    tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form
                                                                action="<?php echo $aksi . paramEncrypt('module=pengaturan&act=delete-pengaturan&id=' . $row['id_shift'] . ''); ?>"
                                                                method="POST" role="form">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                    <h4 class="modal-title" id="myModalLabel">Delete Surat
                                                                        Peringatan</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="form-group">
                                                                            <label>Yakin Anda ingin Menghapus Data ini,klik Tombol
                                                                                Ya? Untuk Menghapusnya</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default"
                                                                        data-dismiss="modal">No</button>
                                                                    <button type="submit" class="btn btn-danger"
                                                                        autofocus>Ya</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Tutup Edit Modal permintaan -->
                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $row['nama_unit']; ?></td>
                                                    <td><?php echo $row['nama_shift'] ?></td>
                                                    <td>
                                                        <!-- <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_shift']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span> -->
                                                        <span data-toggle="modal"
                                                            data-target="#modal-hapus-<?php echo $row['id_shift']; ?>" title="Hapus"
                                                            class="btn-xs btn-danger fa fa-trash"></span>
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
                        
    case "bulk-acc-absensi":
    ?>
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title fa fa-tasks"> BULK VALIDASI ABSENSI</h3>
      </div>
      <div class="box-body">
    
        <!-- Filter Bulan/Tahun -->
        <form action="" method="post" class="form-inline">
          <input type="hidden" name="act" value="bulk-acc-absensi">
          <div class="form-group">
            <label>Bulan</label>
            <select class="form-control select2" name="bulan" required>
              <?php loadBln('-Pilih Bulan-'); ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tahun</label>
            <select class="form-control select2" name="tahun" required>
              <?php loadThn('-Pilih Tahun-'); ?>
            </select>
          </div>
          <button type="submit" name="search" value="1" class="btn btn-info"><i class="fa fa-search"></i> Tampilkan</button>
        </form>
    
        <hr>
    
    <?php
    $bulan_post = $_POST['bulan'] ?? '';
    $tahun_post = $_POST['tahun'] ?? '';
    if ($bulan_post && $tahun_post) {
    
        // tentukan range bulan
        $bulan = str_pad((string)$bulan_post, 2, '0', STR_PAD_LEFT);
        $tahun = (string)$tahun_post;
        $monthStart = (new DateTimeImmutable("$tahun-$bulan-01"))->format('Y-m-d');
        $monthEnd   = (new DateTimeImmutable($monthStart))->modify('last day of this month')->format('Y-m-d');
    
        // ambil pegawai (nip di param/url sama seperti halaman cek)
        $nipPeg = addslashes($id);
        $bio    = fetch_array(bukaquery("SELECT nama_pegawai, log_finger FROM tm_pegawai WHERE nip='$nipPeg'"));
        $nama   = $bio['nama_pegawai'] ?? '-';
        $finger = $bio['log_finger'] ?? null;
    
        if (!$finger) {
            echo '<div class="alert alert-danger">log_finger pegawai tidak ditemukan.</div>';
        } else {
            // tampil list hari dalam bulan tsb
            $TODAY_YMD = date('Y-m-d');
            ?>
            <!-- Form apply bulk -->
            <form action="<?php echo $aksi . paramEncrypt('module=absensi-pegawai&act=bulk-acc-apply&nip='.$id); ?>" method="post" id="bulkForm">
              <div class="row" style="margin-bottom:10px">
                <div class="col-sm-6">
                  <strong><?php echo htmlspecialchars($nama); ?></strong> — Periode: <?php echo htmlspecialchars(konversiBulanTahun($monthStart)); ?>
                </div>
                <div class="col-sm-6 text-right">
                  <div class="form-inline">
                    <label style="margin-right:6px">Set ACC:</label>
                    <?php
                      // pakai enum dropdown, return string (bukan echo) atau versimu dengan $print=false
                      ob_start();
                      enumDropdown("tm_jadwal", "acc_kepegawaian", "", "-Pilih Keterangan-", false);
                      $accDropdown = ob_get_clean();
                      // ubah name supaya khusus bulk:
                      $accDropdown = str_replace('name="acc_kepegawaian"', 'name="acc_kepegawaian_bulk"', $accDropdown);
                      echo $accDropdown;
                    ?>
    
                    <!-- OPTIONAL: ganti shift massal -->
                    <select name="id_absensi_bulk" class="form-control select2" style="min-width:220px; margin-left:6px">
                      <option value="">(Tidak ubah shift)</option>
                      <?php
                        // ambil daftar shift yang relevan dengan unit pegawai (ambil unit dari jadwal manapun di bulan tsb)
                        $idUnit = getOne("SELECT j.id_unit 
                                          FROM tm_jadwal j 
                                          WHERE j.nip_pegawai='$nipPeg' 
                                            AND j.tanggal_jadwal BETWEEN '$monthStart' AND '$monthEnd'
                                          ORDER BY j.tanggal_jadwal DESC LIMIT 1");
                        $idUnit = addslashes($idUnit ?: '');
                        $rsShift = bukaquery("
                          SELECT tm_shift.id_absensi, tm_shift.nama_shift, tm_shift.jam_masuk, tm_shift.jam_pulang
                          FROM tm_shift 
                          INNER JOIN set_shift ON set_shift.id_absensi = tm_shift.id_absensi
                          WHERE ('$idUnit'='' OR set_shift.id_unit LIKE '%$idUnit%')
                            AND tm_shift.status='1'
                          GROUP BY tm_shift.id_absensi
                          ORDER BY tm_shift.nama_shift ASC
                        ");
                        while ($r = fetch_array($rsShift)) {
                          echo '<option value="'.htmlspecialchars($r['id_absensi']).'">'.
                                htmlspecialchars($r['nama_shift']).' ('.htmlspecialchars($r['jam_masuk']).' - '.htmlspecialchars($r['jam_pulang']).')</option>';
                        }
                      ?>
                    </select>
    
                    <button type="submit" class="btn btn-primary" style="margin-left:6px">
                      <i class="fa fa-check"></i> Terapkan ke terpilih
                    </button>
                  </div>
                </div>
              </div>
    
              <table class="table table-bordered table-striped" id="bulkTable">
                <thead>
                  <tr>
                    <th style="width:28px"><input type="checkbox" id="checkAll"></th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>IN</th>
                    <th>OUT</th>
                    <th>Status/Talangan</th>
                    <th>ACC Saat Ini</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $cur  = new DateTimeImmutable($monthStart);
                $last = new DateTimeImmutable($monthEnd);
                while ($cur <= $last) {
                    $hariYmd = $cur->format('Y-m-d');
                    $isFuture = ($hariYmd > $TODAY_YMD);
    
                    // ambil jadwal hari itu
                    $m = fetch_array(bukaquery("
                      SELECT j.id_jadwal, j.acc_kepegawaian, j.id_absensi, s.nama_shift, s.kode_shift, s.jam_masuk, s.jam_pulang
                      FROM tm_jadwal j
                      LEFT JOIN tm_shift s ON s.id_absensi=j.id_absensi
                      WHERE j.nip_pegawai='$nipPeg' AND j.tanggal_jadwal='$hariYmd' LIMIT 1
                    "));
    
                    $id_jadwal = $m['id_jadwal'] ?? null;
                    $accNow    = $m['acc_kepegawaian'] ?? '-';
                    $namaShift = $m['nama_shift'] ?? '-';
                    $kodeShift = $m['kode_shift'] ?? '-';
                    $jm        = $m['jam_masuk'] ?? '';
                    $jp        = $m['jam_pulang'] ?? '';
    
                    // hitung cepat status IN/OUT untuk membantu filter
                    // sederhana: cek ada punch min & max di hari tsb
                    $inTs  = getOne("SELECT MIN(log.tanggal) FROM log WHERE `user`='".addslashes($finger)."' AND log.tanggal BETWEEN '$hariYmd 00:00:00' AND '$hariYmd 23:59:59'");
                    $outTs = getOne("SELECT MAX(log.tanggal) FROM log WHERE `user`='".addslashes($finger)."' AND log.tanggal BETWEEN '$hariYmd 00:00:00' AND '$hariYmd 23:59:59'");
    
                    $noIn  = empty($inTs);
                    $noOut = empty($outTs);
                    $statusShort = '-';
                    if ($kodeShift==='OFF') $statusShort='Libur';
                    elseif ($kodeShift==='C') $statusShort='Cuti';
                    elseif ($kodeShift==='DL') $statusShort='Dinas Luar';
                    elseif ($kodeShift==='Skt') $statusShort='Sakit';
                    elseif ($kodeShift==='I') $statusShort='Izin';
                    else {
                      if ($noIn && $noOut) $statusShort='Tidak ada IN & OUT';
                      else if ($noIn)      $statusShort='Tidak ada IN';
                      else if ($noOut)     $statusShort='Tidak ada OUT';
                      else                 $statusShort='Ada IN/OUT';
                    }
    
                    $tglLabel = konversitanggal($hariYmd) . " (" . hariindo($hariYmd) . ")";
    
                    // default cek otomatis: centang yang problem (tidak ada IN/OUT) dan bukan future
                    $autoCheck = (!$isFuture && ($noIn || $noOut) && $kodeShift!=='OFF' && $kodeShift!=='C' && $kodeShift!=='DL' && $kodeShift!=='Skt' && $kodeShift!=='I');
    
                    echo '<tr data-status="'.htmlspecialchars($statusShort).'">';
                    echo '  <td>'.($id_jadwal ? '<input type="checkbox" name="selected_ids[]" value="'.htmlspecialchars($id_jadwal).'" '.($autoCheck?'checked':'').'>' : '<span class="text-muted" title="tidak ada jadwal">-</span>').'</td>';
                    echo '  <td>'.htmlspecialchars($tglLabel).'</td>';
                    echo '  <td>'.htmlspecialchars($namaShift).'</td>';
                    echo '  <td>'.($inTs ? htmlspecialchars(date('H:i:s', strtotime($inTs))) : '-').'</td>';
                    echo '  <td>'.($outTs? htmlspecialchars(date('H:i:s', strtotime($outTs))) : '-').'</td>';
                    echo '  <td>'.htmlspecialchars($statusShort).'</td>';
                    echo '  <td>'.htmlspecialchars($accNow).'</td>';
                    echo '</tr>';
    
                    $cur = $cur->modify('+1 day');
                } // while
                ?>
                </tbody>
              </table>
    
              <!-- tombol bantuan pilih -->
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm" id="pickNoIN">Pilih yg "Tidak ada IN"</button>
                <button type="button" class="btn btn-default btn-sm" id="pickNoOUT">Pilih yg "Tidak ada OUT"</button>
                <button type="button" class="btn btn-default btn-sm" id="pickNoBoth">Pilih yg "Tidak ada IN & OUT"</button>
                <button type="button" class="btn btn-default btn-sm" id="clearPick">Bersihkan pilihan</button>
              </div>
    
            </form>
    
            <script>
            (function(){
              var $t = $('#bulkTable');
              $('#checkAll').on('change', function(){
                $t.find('tbody input[type=checkbox]').prop('checked', this.checked);
              });
              function pick(status){
                $('#bulkTable tbody tr').each(function(){
                  var s = $(this).data('status');
                  if (s === status) {
                    $(this).find('input[type=checkbox]').prop('checked', true);
                  }
                });
              }
              $('#pickNoIN').on('click', function(){ pick('Tidak ada IN'); });
              $('#pickNoOUT').on('click', function(){ pick('Tidak ada OUT'); });
              $('#pickNoBoth').on('click', function(){ pick('Tidak ada IN & OUT'); });
              $('#clearPick').on('click', function(){ $('#bulkTable tbody input[type=checkbox]').prop('checked', false); });
            })();
            </script>
            <?php
        } // finger ok
    } // if bulan/tahun
    ?>
      </div>
    </div>
    <?php
    break;
                    

}
?>