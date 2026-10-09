<?php
$aksi = "module/simpeg/aksi-simpeg.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
$main_tab = isset($_GET['main_tab']) ? $_GET['main_tab'] : 'tab_1';
$sub_tab  = isset($_GET['sub_tab']) ? $_GET['sub_tab'] : '';
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header('location:error.php');
        break;
    case "ganti-password":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-lock">  GANTI PASSWORD</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <div class="form-group">
                    <div class="col-xs-3 col-md-4"> 
                        <form action="<?php echo $aksi . paramEncrypt('module=spv&act=ganti-password-spv&id=' . paramDecrypt($superuser) . ''); ?>" method="post">
                            <div class="form-group has-feedback">
                                <input type="password" name="pass_lama" class="form-control" placeholder="Password Lama" required>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="pass_baru" class="form-control" placeholder="Password baru" required>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="retype_pass" class="form-control" placeholder="Retype password" required>
                                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                            </div>
                            <input type="submit" class="btn btn-warning" value="Update">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
        
        case "profile-update":
		?>
		<style>
		    .profile-page-card {
		        border: 0;
		        border-radius: 18px;
		        overflow: hidden;
		        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
		        background: #fff;
		        margin-bottom: 20px;
		    }
		
		    .profile-page-card .box-header {
		        border-bottom: 1px solid #f0f0f0;
		        padding: 18px 22px;
		        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
		    }
		
		    .profile-page-title {
		        margin: 0;
		        font-size: 22px;
		        font-weight: 700;
		        color: #1f2d3d;
		        display: flex;
		        align-items: center;
		        gap: 10px;
		    }
		
		    .profile-page-title i {
		        width: 42px;
		        height: 42px;
		        border-radius: 12px;
		        display: inline-flex;
		        align-items: center;
		        justify-content: center;
		        background: #eaf4ff;
		        color: #2b7de9;
		        font-size: 18px;
		    }
		
		    .profile-page-subtitle {
		        margin-top: 6px;
		        margin-left: 52px;
		        color: #7a8793;
		        font-size: 13px;
		    }
		
		    .profile-tab-wrap {
		        padding: 0;
		        background: #fff;
		    }
		
		    .profile-tab-wrap .nav-tabs {
		        padding: 0 18px;
		        border-bottom: 1px solid #f2f2f2;
		        background: #fff;
		    }
		
		    .profile-tab-wrap .nav-tabs > li > a {
		        border: 0 !important;
		        color: #6b7785;
		        font-weight: 600;
		        padding: 14px 16px;
		    }
		
		    .profile-tab-wrap .nav-tabs > li.active > a,
		    .profile-tab-wrap .nav-tabs > li.active > a:hover,
		    .profile-tab-wrap .nav-tabs > li.active > a:focus {
		        color: #2b7de9;
		        border: 0 !important;
		        border-bottom: 3px solid #2b7de9 !important;
		        background: transparent !important;
		    }
		
		    .profile-tab-content {
		        padding: 24px;
		        background: #f7f9fc;
		    }
		
		    .profile-main-card {
		        background: #fff;
		        border-radius: 18px;
		        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
		        overflow: hidden;
		    }
		
		    .profile-main-top {
		        padding: 24px;
		        border-bottom: 1px solid #f2f4f7;
		        background: linear-gradient(135deg, #ffffff 0%, #f9fbff 100%);
		    }
		
		    .profile-main-top .row-flex {
		        display: flex;
		        flex-wrap: wrap;
		        align-items: center;
		    }
		
		    .profile-photo-box {
		        text-align: center;
		    }
		
		    .profile-photo-box img {
		        width: 190px;
		        height: 190px;
		        object-fit: cover;
		        border-radius: 18px;
		        border: 4px solid #fff;
		        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
		        margin: 0 auto;
		        background: #f8f8f8;
		    }
		
		    .profile-name {
		        font-size: 26px;
		        font-weight: 700;
		        color: #1f2937;
		        margin-bottom: 6px;
		    }
		
		    .profile-meta {
		        display: flex;
		        flex-wrap: wrap;
		        gap: 8px;
		        margin-bottom: 14px;
		    }
		
		    .profile-badge {
		        display: inline-block;
		        padding: 6px 12px;
		        border-radius: 999px;
		        background: #eef5ff;
		        color: #2b7de9;
		        font-size: 12px;
		        font-weight: 600;
		    }
		
		    .profile-actions {
		        display: flex;
		        flex-wrap: wrap;
		        gap: 10px;
		        margin-top: 10px;
		    }
		
		    .profile-actions .btn {
		        border-radius: 10px;
		        padding: 10px 14px;
		        font-weight: 600;
		    }
		
		    .profile-grid {
		        padding: 24px;
		    }
		
		    .info-section-title {
		        font-size: 15px;
		        font-weight: 700;
		        color: #344054;
		        margin: 0 0 16px 0;
		        padding-bottom: 8px;
		        border-bottom: 1px solid #eef2f6;
		    }
		
		    .info-item {
		        margin-bottom: 16px;
		    }
		
		    .info-label {
		        display: block;
		        font-size: 12px;
		        letter-spacing: .3px;
		        text-transform: uppercase;
		        color: #98a2b3;
		        margin-bottom: 6px;
		        font-weight: 700;
		    }
		
		    .info-value,
		    .info-item .form-control[readonly],
		    .info-item textarea.form-control[readonly] {
		        background: #f9fafb;
		        border: 1px solid #e5e7eb;
		        border-radius: 12px;
		        color: #1f2937;
		        min-height: 44px;
		        box-shadow: none;
		    }
		
		    .info-item .form-control[readonly] {
		        padding: 10px 12px;
		    }
		
		    .info-item textarea.form-control[readonly] {
		        resize: none;
		        min-height: 96px;
		        padding: 10px 12px;
		    }
		
		    .profile-side-card {
		        background: #fff;
		        border-radius: 16px;
		        padding: 18px;
		        border: 1px solid #edf1f5;
		        margin-top: 20px;
		    }
		
		    .profile-side-card .mini-stat {
		        padding: 12px 0;
		        border-bottom: 1px dashed #e6ebf1;
		    }
		
		    .profile-side-card .mini-stat:last-child {
		        border-bottom: 0;
		    }
		
		    .mini-stat-label {
		        display: block;
		        font-size: 12px;
		        color: #98a2b3;
		        margin-bottom: 4px;
		        text-transform: uppercase;
		        font-weight: 700;
		    }
		
		    .mini-stat-value {
		        font-size: 15px;
		        color: #1f2937;
		        font-weight: 600;
		    }
		
		    .profile-modal .modal-content {
		        border-radius: 18px;
		        overflow: hidden;
		        border: 0;
		        box-shadow: 0 20px 50px rgba(0, 0, 0, .18);
		    }
		
		    .profile-modal .modal-header {
		        background: #f8fbff;
		        border-bottom: 1px solid #eef2f6;
		    }
		
		    .profile-modal .modal-title {
		        font-weight: 700;
		        color: #1f2937;
		    }
		
		    .profile-modal .form-control,
		    .profile-modal select,
		    .profile-modal textarea {
		        border-radius: 10px;
		        min-height: 42px;
		        box-shadow: none;
		        border: 1px solid #dfe6ee;
		    }
		
		    .profile-modal textarea {
		        min-height: 90px;
		        resize: vertical;
		    }
		
		    .profile-modal label {
		        margin-top: 10px;
		        margin-bottom: 6px;
		        color: #475467;
		        font-weight: 600;
		    }
		
		    .profile-section-card {
		        background: #fff;
		        border-radius: 18px;
		        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
		        overflow: hidden;
		    }
		
		    .profile-section-head {
		        padding: 20px 24px;
		        border-bottom: 1px solid #eef2f6;
		        background: linear-gradient(135deg, #ffffff 0%, #f9fbff 100%);
		    }
		
		    .profile-section-title {
		        margin: 0;
		        font-size: 20px;
		        font-weight: 700;
		        color: #1f2937;
		    }
		
		    .profile-section-desc {
		        margin-top: 6px;
		        color: #7a8793;
		        font-size: 13px;
		    }
		
		    .profile-inner-tabs {
		        padding: 0 18px;
		        border-bottom: 1px solid #f2f2f2;
		        background: #fff;
		        overflow-x: auto;
		        overflow-y: hidden;
		        white-space: nowrap;
		    }
		
		    .profile-inner-tabs > li {
		        float: none;
		        display: inline-block;
		    }
		
		    .profile-inner-tabs > li > a {
		        border: 0 !important;
		        color: #667085;
		        font-weight: 600;
		        padding: 14px 14px;
		    }
		
		    .profile-inner-tabs > li.active > a,
		    .profile-inner-tabs > li.active > a:hover,
		    .profile-inner-tabs > li.active > a:focus {
		        color: #2b7de9;
		        border: 0 !important;
		        border-bottom: 3px solid #2b7de9 !important;
		        background: transparent !important;
		    }
		
		    .profile-section-body {
		        padding: 20px 24px 24px;
		        background: #f7f9fc;
		    }
		
		    .profile-toolbar {
		        display: flex;
		        justify-content: space-between;
		        align-items: center;
		        gap: 12px;
		        margin-bottom: 18px;
		        flex-wrap: wrap;
		    }
		
		    .profile-toolbar .btn {
		        border-radius: 10px;
		        font-weight: 600;
		    }
		
		    .profile-toolbar-title {
		        font-size: 16px;
		        font-weight: 700;
		        color: #344054;
		    }
		
		    .profile-table-wrap {
		        background: #fff;
		        border: 1px solid #edf1f5;
		        border-radius: 16px;
		        overflow: hidden;
		    }
		
		    .profile-table-wrap .table {
		        margin-bottom: 0;
		    }
		
		    .profile-table-wrap .table thead th {
		        background: #f8fafc;
		        border-bottom: 1px solid #e9eef5;
		        color: #344054;
		        font-size: 13px;
		        font-weight: 700;
		        white-space: nowrap;
		    }
		
		    .profile-table-wrap .table tbody td {
		        vertical-align: middle;
		        color: #475467;
		    }
		
		    .profile-action-btn {
		        display: inline-flex;
		        align-items: center;
		        justify-content: center;
		        width: 32px;
		        height: 32px;
		        border-radius: 8px;
		        margin-right: 4px;
		        color: #fff;
		        cursor: pointer;
		    }
		
		    .profile-action-btn.warning { background: #f59e0b; }
		    .profile-action-btn.danger { background: #ef4444; }
		    .profile-action-btn.success { background: #10b981; }
		
		    @media (max-width: 767px) {
		        .profile-section-head,
		        .profile-section-body {
		            padding: 16px;
		        }
		
		        .profile-section-title {
		            font-size: 18px;
		        }
		    }
		
		    @media (max-width: 991px) {
		        .profile-photo-box {
		            margin-top: 20px;
		        }
		
		        .profile-name {
		            font-size: 22px;
		        }
		    }
		
		    @media (max-width: 767px) {
		        .profile-page-card .box-header {
		            padding: 16px;
		        }
		
		        .profile-page-subtitle {
		            margin-left: 0;
		            margin-top: 10px;
		        }
		
		        .profile-tab-content {
		            padding: 14px;
		        }
		
		        .profile-main-top,
		        .profile-grid {
		            padding: 16px;
		        }
		
		        .profile-photo-box {
		            margin-top: 18px;
		        }
		
		        .profile-photo-box img {
		            width: 140px;
		            height: 140px;
		            border-radius: 16px;
		        }
		
		        .profile-name {
		            font-size: 20px;
		            text-align: center;
		        }
		
		        .profile-meta,
		        .profile-actions {
		            justify-content: center;
		        }
		
		        .info-section-title {
		            margin-top: 10px;
		        }
		    }
		</style>
		
		<div class="box profile-page-card">
		    <div class="box-header with-border">
		        <h3 class="profile-page-title">
		            <i class="fa fa-user-circle"></i> Profil Pegawai
		        </h3>
		        <div class="profile-page-subtitle">
		            Informasi biodata pegawai, profil akun, dan data dasar kepegawaian.
		        </div>
		    </div>
		
		    <div class="nav-tabs-custom profile-tab-wrap">
		        <ul class="nav nav-tabs">
		            <li class="active"><a href="#tab_1" data-toggle="tab">Biodata</a></li>
		            <li><a href="#tab_2" data-toggle="tab">Data Pegawai</a></li>
		            <li><a href="#tab_3" data-toggle="tab">Berkas Digital</a></li>
		        </ul>
		
		        <div class="tab-content profile-tab-content">
		            <div class="tab-pane active" id="tab_1">
		                <?php
		                $biodata = fetch_array(bukaquery("SELECT tm_pegawai.nik, tm_pegawai.nip, tm_pegawai.no_rek, tm_pegawai.tempat_lahir, tm_pegawai.agama, tm_pegawai.npwp,tm_pegawai.jk,
		                                tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan, tm_pegawai.alamat, tm_pegawai.no_bpjs,tm_pegawai.no_hp,tm_pegawai.tgl_lahir,tm_pegawai.foto,
		                                tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger,
		                                tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level
		                                FROM tm_pegawai
		                                INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
		                                INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
		                                INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
		                                WHERE tm_pegawai.id_user='$id_user'"));
		
		                if ($biodata['foto'] == '-' OR $biodata['foto'] == '') {
		                    if ($biodata['jk'] == 'L') {
		                        $foto = 'img/laki.png';
		                    } else {
		                        $foto = 'img/perempuan.png';
		                    }
		                } else {
		                    $foto = "img/" . $biodata['foto'];
		                }
		                ?>
		
		                <div class="profile-main-card">
		                    <div class="profile-main-top">
		                        <div class="row row-flex">
		                            <div class="col-md-8 col-sm-7 col-xs-12">
		                                <div class="profile-name"><?php echo $nama_pegawai; ?></div>
		                                <div class="profile-meta">
		                                    <span class="profile-badge"><?php echo $biodata['nama_unit']; ?></span>
		                                    <span class="profile-badge"><?php echo $biodata['status']; ?></span>
		                                </div>
		                                <div class="profile-actions">
		                                    <button type="button" data-toggle="modal" data-target="#modal-update-pegawai-<?php echo $id_user; ?>" class="btn btn-primary">
		                                        <i class="fa fa-edit"></i> Update Profil
		                                    </button>
		                                    <button type="button" data-toggle="modal" data-target="#modal-ganti-password-<?php echo $id_user; ?>" class="btn btn-danger">
		                                        <i class="fa fa-lock"></i> Ganti Password
		                                    </button>
		                                </div>
		                            </div>
		                            <div class="col-md-4 col-sm-5 col-xs-12">
		                                <div class="profile-photo-box">
		                                    <img src="<?php echo $foto; ?>" alt="Foto Pegawai" class="img-responsive">
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		
		                    <div class="profile-grid">
		                        <div class="row">
		                            <div class="col-md-4 col-sm-6 col-xs-12">
		                                <h4 class="info-section-title">Data Utama</h4>
		                                <div class="info-item"><span class="info-label">NIK</span><input type="text" class="form-control" value="<?php echo $biodata['nik']; ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">Nama Pegawai</span><input type="text" class="form-control" value="<?php echo $nama_pegawai; ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">Alamat</span><textarea class="form-control" readonly><?php echo $biodata['alamat']; ?></textarea></div>
		                                <div class="info-item"><span class="info-label">No Rekening DKI</span><input type="text" class="form-control" value="<?php echo $biodata['no_rek']; ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">NPWP</span><input type="text" class="form-control" value="<?php echo $biodata['npwp']; ?>" readonly></div>
		                            </div>
		
		                            <div class="col-md-4 col-sm-6 col-xs-12">
		                                <h4 class="info-section-title">Informasi Personal</h4>
		                                <div class="info-item"><span class="info-label">Tempat Lahir</span><input type="text" class="form-control" value="<?php echo $biodata['tempat_lahir']; ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">Tanggal Lahir</span><input type="text" class="form-control" value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_lahir']); ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">Jenis Kelamin</span><input type="text" class="form-control" value="<?php echo kelamin($biodata['jk']); ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">Agama</span><input type="text" class="form-control" value="<?php echo $biodata['agama']; ?>" readonly></div>
		                                <div class="info-item"><span class="info-label">No HP</span><input type="text" class="form-control" value="<?php echo $biodata['no_hp']; ?>" readonly></div>
		                            </div>
		
		                            <div class="col-md-4 col-sm-12 col-xs-12">
		                                <h4 class="info-section-title">Kepegawaian</h4>
		                                <div class="profile-side-card">
		                                    <div class="mini-stat"><span class="mini-stat-label">Tanggal Masuk</span><div class="mini-stat-value"><?php echo FormatTgl('d/m/Y', $biodata['tgl_masuk']); ?></div></div>
		                                    <div class="mini-stat"><span class="mini-stat-label">Masa Kerja</span><div class="mini-stat-value"><?php echo MasaKerjaPenyebut($biodata['tgl_masuk']); ?></div></div>
		                                    <div class="mini-stat"><span class="mini-stat-label">Pendidikan</span><div class="mini-stat-value"><?php echo $biodata['pendidikan']; ?></div></div>
		                                    <div class="mini-stat"><span class="mini-stat-label">Rumpun Jabatan</span><div class="mini-stat-value"><?php echo $biodata['rumpun']; ?></div></div>
		                                    <div class="mini-stat"><span class="mini-stat-label">Status Nikah</span><div class="mini-stat-value"><?php echo $biodata['status_nikah']; ?></div></div>
		                                    <div class="mini-stat"><span class="mini-stat-label">Bagian / Unit</span><div class="mini-stat-value"><?php echo $biodata['nama_unit']; ?></div></div>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		
		                <div class="modal fade profile-modal" id="modal-update-pegawai-<?php echo $id_user; ?>" role="dialog" aria-labelledby="myModalLabel">
		                    <div class="modal-dialog" role="document">
		                        <div class="modal-content">
		                            <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-profil-pegawai&id=' . $id_user . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
		                                <div class="modal-header">
		                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		                                    <h4 class="modal-title fa fa-user-circle" id="myModalLabel"> Update Data Profil</h4>
		                                </div>
		                                <div class="modal-body">
		                                    <label>NIK</label>
		                                    <input type="text" class="form-control" name="nik" value="<?php echo $biodata['nik']; ?>" required>
		                                    <label>Nama Pegawai</label>
		                                    <input type="text" class="form-control" name="nama_pegawai" value="<?php echo $nama_pegawai; ?>" required>
		                                    <label>Alamat</label>
		                                    <textarea class="form-control" name="alamat" placeholder="Alamat" title="Alamat" required><?php echo $biodata['alamat']; ?></textarea>
		                                    <label>Tempat Lahir</label>
		                                    <input type="text" class="form-control" name="tempat_lahir" value="<?php echo $biodata['tempat_lahir']; ?>" required>
		                                    <label>Tanggal Lahir</label>
		                                    <input type="text" class="form-control" name="tgl_lahir" value="<?php echo FormatTgl('m/d/Y', $biodata['tgl_lahir']); ?>" id="tanggal_lahir1" required>
		                                    <label>No Rekening DKI</label>
		                                    <input type="text" class="form-control" name="no_rek" value="<?php echo $biodata['no_rek']; ?>" required>
		                                    <label>NPWP</label>
		                                    <input type="text" class="form-control" name="npwp" value="<?php echo $biodata['npwp']; ?>" required>
		                                    <label>No BPJS Kesehatan</label>
		                                    <input type="text" class="form-control" name="no_bpjs" value="<?php echo $biodata['no_bpjs']; ?>" required>
		                                    <label>No HP</label>
		                                    <input type="text" class="form-control" name="no_hp" value="<?php echo $biodata['no_hp']; ?>" required>
		                                    <label>Jenis Kelamin</label>
		                                    <?php echo UpdateEnumDropdown("tm_pegawai", "jk", $biodata['jk'], ""); ?>
		                                    <label>Agama</label>
		                                    <?php echo UpdateEnumDropdown("tm_pegawai", "agama", $biodata['agama'], ""); ?>
		                                    <label>Upload Foto</label>
		                                    <input type="file" class="form-control" name="file" value="<?php echo $biodata['foto']; ?>">
		                                </div>
		                                <div class="modal-footer">
		                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		                                    <button type="submit" class="btn btn-warning" autofocus>Update</button>
		                                </div>
		                            </form>
		                        </div>
		                    </div>
		                </div>
		
		                <div class="modal fade profile-modal" id="modal-ganti-password-<?php echo $id_user; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		                    <div class="modal-dialog" role="document">
		                        <div class="modal-content">
		                            <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=ganti-password&id=' . $id_user . ''); ?>" method="POST" role="form">
		                                <div class="modal-header">
		                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		                                    <h4 class="modal-title fa fa-lock" id="myModalLabel"> Ganti Password</h4>
		                                </div>
		                                <div class="modal-body">
		                                    <label>Password Lama</label>
		                                    <input type="password" class="form-control" placeholder="Password" name="password_lama" required>
		                                    <label>Password Baru</label>
		                                    <input type="password" class="form-control" placeholder="Password" name="password_baru" required>
		                                </div>
		                                <div class="modal-footer">
		                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		                                    <button type="submit" class="btn btn-warning" autofocus>Update</button>
		                                </div>
		                            </form>
		                        </div>
		                    </div>
		                </div>
		            </div>
		
		            <div class="tab-pane" id="tab_2">
		                <div class="profile-section-card">
		                    <div class="profile-section-head">
		                        <h3 class="profile-section-title">Data Pegawai</h3>
		                        <div class="profile-section-desc">Kelola riwayat pendidikan, keluarga, jabatan, diklat</div>
		                    </div>
		
		                    <div class="nav-tabs-custom" style="margin-bottom:0; box-shadow:none; border-radius:0;">
		                        <ul class="nav nav-tabs profile-inner-tabs">
		                            <li class="active"><a href="#tab_11" data-toggle="tab">Riwayat Pendidikan</a></li>
		                            <li><a href="#tab_12" data-toggle="tab">Daftar Keluarga</a></li>
		                            <li><a href="#tab_13" data-toggle="tab">Riwayat Jabatan</a></li>
		                            <li><a href="#tab_14" data-toggle="tab">Riwayat Diklat</a></li>
		                            <!--<li><a href="#tab_15" data-toggle="tab">Peningkatan Pendidikan</a></li>-->
		                        </ul>
		
		                        <div class="tab-content profile-section-body">
		                            <div class="tab-pane active" id="tab_11">
		                                <div class="profile-toolbar">
		                                    <div class="profile-toolbar-title">Riwayat Pendidikan</div>
		                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-pendidikan">
		                                        <i class="fa fa-plus"></i> Tambah Riwayat Pendidikan
		                                    </button>
		                                </div>
		
		                                <div class="profile-table-wrap">
		                                    <div class="table-responsive">
		                                        <table id="example2" class="table table-bordered table-striped">
		                                            <thead>
		                                                <tr>
		                                                    <th>Pendidikan</th>
		                                                    <th>Nama Sekolah</th>
		                                                    <th>Jurusan</th>
		                                                    <th>Periode</th>
		                                                    <th>No.Ijazah</th>
		                                                    <th>Tgl Ijazah</th>
		                                                    <th>Action</th>
		                                                </tr>
		                                            </thead>
		                                            <tbody>
		                                                <?php
		                                                $no = 0;
		                                                $sql_riwayat_pend = bukaquery("SELECT * FROM tm_riwayat_pend where id_user='$id_user' order by tm_riwayat_pend.tgl_ijazah asc");
		                                                while ($row = fetch_array($sql_riwayat_pend)) {
		                                                    $no++;
		                                                ?>
		                                                    <div class="modal fade profile-modal" id="modal-ubah-<?php echo $row['id_riwayat_pend']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		                                                        <div class="modal-dialog" role="document">
		                                                            <div class="modal-content">
		                                                                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-pendidikan&id=' . $row['id_riwayat_pend'] . ''); ?>" method="POST" role="form">
		                                                                    <div class="modal-header">
		                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		                                                                        <h4 class="modal-title" id="myModalLabel">Update Riwayat Pendidikan</h4>
		                                                                    </div>
		                                                                    <div class="modal-body">
		                                                                        <label>Pendidikan</label>
		                                                                        <?php echo UpdateEnumDropdown("tm_riwayat_pend", "pendidikan", $row['pendidikan'], ""); ?>
		                                                                        <label>Nama Sekolah</label>
		                                                                        <input type="text" class="form-control" name="nama_sekolah" value="<?php echo $row['nama_sekolah']; ?>" required>
		                                                                        <label>Jurusan</label>
		                                                                        <input type="text" class="form-control" name="jurusan" value="<?php echo $row['jurusan']; ?>" required>
		                                                                        <label>Kota</label>
		                                                                        <input type="text" class="form-control" name="kota" value="<?php echo $row['kota']; ?>" required>
		                                                                        <label>Periode</label>
		                                                                        <div class="input-group">
		                                                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
		                                                                            <input type="text" name="periode" value="<?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?>" class="form-control pull-right" data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"' data-mask required>
		                                                                        </div>
		                                                                        <label>No. Ijazah</label>
		                                                                        <input type="text" class="form-control" name="no_ijazah" value="<?php echo $row['no_ijazah']; ?>" required>
		                                                                        <label>Tanggal Ijazah</label>
		                                                                        <input type="text" class="form-control" name="tgl_ijazah" value="<?php echo FormatTgl('d-m-Y', $row['tgl_ijazah']) ?>" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
		                                                                    </div>
		                                                                    <div class="modal-footer">
		                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		                                                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
		                                                                    </div>
		                                                                </form>
		                                                            </div>
		                                                        </div>
		                                                    </div>
		
		                                                    <div class="modal fade profile-modal" id="modal-hapus-<?php echo $row['id_riwayat_pend']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		                                                        <div class="modal-dialog" role="document">
		                                                            <div class="modal-content">
		                                                                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-pendidikan&id=' . $row['id_riwayat_pend'] . ''); ?>" method="POST" role="form">
		                                                                    <div class="modal-header">
		                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		                                                                        <h4 class="modal-title" id="myModalLabel">Hapus Riwayat Pendidikan</h4>
		                                                                    </div>
		                                                                    <div class="modal-body">
		                                                                        <p>Yakin ingin menghapus data ini?</p>
		                                                                    </div>
		                                                                    <div class="modal-footer">
		                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		                                                                        <button type="submit" class="btn btn-danger">Ya</button>
		                                                                    </div>
		                                                                </form>
		                                                            </div>
		                                                        </div>
		                                                    </div>
		
		                                                    <tr>
		                                                        <td><?php echo $row['pendidikan']; ?></td>
		                                                        <td><?php echo $row['nama_sekolah']; ?></td>
		                                                        <td><?php echo $row['jurusan']; ?></td>
		                                                        <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
		                                                        <td><?php echo $row['no_ijazah']; ?></td>
		                                                        <td><?php echo FormatTgl('d-m-Y', $row['tgl_ijazah']); ?></td>
		                                                        <td>
		                                                            <span data-toggle="modal" data-target="#modal-ubah-<?php echo $row['id_riwayat_pend']; ?>" class="profile-action-btn warning" title="Ubah"><i class="fa fa-edit"></i></span>
		                                                            <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_riwayat_pend']; ?>" class="profile-action-btn danger" title="Hapus"><i class="fa fa-trash"></i></span>
		                                                        </td>
		                                                    </tr>
		                                                <?php } ?>
		                                            </tbody>
		                                        </table>
		                                    </div>
		                                </div>
		
		                                <div class="modal fade profile-modal" id="modal-add-riwayat-pendidikan">
		                                    <div class="modal-dialog">
		                                        <div class="modal-content">
		                                            <div class="modal-header">
		                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		                                                <h4 class="modal-title fa fa-university"> Tambah Riwayat Pendidikan</h4>
		                                            </div>
		                                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-pendidikan&id=' . $id_user . ''); ?>" method="post">
		                                                <div class="modal-body">
		                                                    <label>Pendidikan</label>
		                                                    <?php echo enumDropdown("tm_riwayat_pend", "pendidikan", "", "-Pilih Pendidikan-"); ?>
		                                                    <label>Nama Sekolah</label>
		                                                    <input type="text" class="form-control" name="nama_sekolah" placeholder="Pendidikan" required>
		                                                    <label>Jurusan</label>
		                                                    <input type="text" class="form-control" name="jurusan" placeholder="Jurusan" required>
		                                                    <label>Kota</label>
		                                                    <input type="text" class="form-control" name="kota" placeholder="Kota" required>
		                                                    <label>Periode</label>
		                                                    <div class="input-group">
		                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
		                                                        <input type="text" name="periode" class="form-control pull-right" id="reservation" required>
		                                                    </div>
		                                                    <label>No. Ijazah</label>
		                                                    <input type="text" class="form-control" name="no_ijazah" placeholder="No.Ijazah" required>
		                                                    <label>Tanggal Ijazah</label>
		                                                    <input type="text" class="form-control" name="tgl_ijazah" id="datepicker1" placeholder="mm/dd/yyyy" required>
		                                                </div>
		                                                <div class="modal-footer">
		                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		                                                    <input type="submit" class="btn btn-primary" value="Simpan">
		                                                </div>
		                                            </form>
		                                        </div>
		                                    </div>
		                                </div>
		                            </div>
		
		                            <div class="tab-pane" id="tab_12">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Daftar Keluarga</div>
									        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-keluarga">
									            <i class="fa fa-plus"></i> Tambah Keluarga
									        </button>
									    </div>
									
									    <div class="modal fade profile-modal" id="modal-add-keluarga">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-users"> Tambah Keluarga</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-keluarga&id=' . $id_user . ''); ?>" method="post">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>NIK</label>
									                            <input type="text" class="form-control" name="nik" placeholder="NIK" required>
									
									                            <label>Nama Keluarga</label>
									                            <input type="text" class="form-control" name="nama_keluarga" placeholder="Nama" required>
									
									                            <label>Hubungan</label>
									                            <?php echo enumDropdown("tm_keluarga", "hubungan", "", "-Pilih Hubungan-"); ?>
									
									                            <label>Tanggal lahir</label>
									                            <input type="text" class="form-control" name="tanggal_lahir" id="datepicker4" placeholder="mm/dd/yyyy" required>
									
									                            <label>Jenis Kelamin</label>
									                            <?php echo enumDropdown("tm_keluarga", "jk", "", "-Pilih Jenis Kelamin-"); ?>
									
									                            <label>Pendidikan</label>
									                            <?php echo enumDropdown("tm_keluarga", "pendidikan", "", "-Pilih Pendidikan-"); ?>
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
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example3" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>NIK</th>
									                        <th>Nama</th>
									                        <th>Hubungan</th>
									                        <th>Tanggal Lahir</th>
									                        <th>Umur</th>
									                        <th>Jenis Kelamin</th>
									                        <th>Pendidikan</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $no = 0;
									                    $sql_keluarga = bukaquery("SELECT * FROM tm_keluarga where id_user='$id_user'");
									                    while ($row = fetch_array($sql_keluarga)) {
									                        $no++;
									                    ?>
									
									                    <div class="modal fade profile-modal" id="modal-ubah-keluarga-<?php echo $row['id_fams']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                        <div class="modal-dialog" role="document">
									                            <div class="modal-content">
									                                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-keluarga&id=' . $row['id_fams'] . ''); ?>" method="POST" role="form">
									                                    <div class="modal-header">
									                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                        <h4 class="modal-title" id="myModalLabel">Update Keluarga</h4>
									                                    </div>
									                                    <div class="modal-body">
									                                        <div class="form-group">
									                                            <label>NIK</label>
									                                            <input type="text" class="form-control" value="<?php echo $row['nik']; ?>" name="nik" placeholder="NIK" required>
									
									                                            <label>Nama Keluarga</label>
									                                            <input type="text" class="form-control" value="<?php echo $row['nama_keluarga']; ?>" name="nama_keluarga" placeholder="Nama" required>
									
									                                            <label>Hubungan</label>
									                                            <?php echo UpdateEnumDropdown("tm_keluarga", "hubungan", $row['hubungan'], ""); ?>
									
									                                            <label>Tanggal lahir</label>
									                                            <input type="text" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_lahir']); ?>" name="tgl_lahir" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
									
									                                            <label>Jenis Kelamin</label>
									                                            <?php echo UpdateEnumDropdown("tm_keluarga", "jk", $row['jk'], ""); ?>
									
									                                            <label>Pendidikan</label>
									                                            <?php echo UpdateEnumDropdown("tm_keluarga", "pendidikan", $row['pendidikan'], ""); ?>
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
									
									                    <div class="modal fade profile-modal" id="modal-hapus-keluarga-<?php echo $row['id_fams']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                        <div class="modal-dialog" role="document">
									                            <div class="modal-content">
									                                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-keluarga&id=' . $row['id_fams'] . ''); ?>" method="POST" role="form">
									                                    <div class="modal-header">
									                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                        <h4 class="modal-title" id="myModalLabel">Hapus Data Keluarga</h4>
									                                    </div>
									                                    <div class="modal-body">
									                                        <label>Yakin Anda ingin menghapus data ini?</label>
									                                    </div>
									                                    <div class="modal-footer">
									                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                                        <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                                    </div>
									                                </form>
									                            </div>
									                        </div>
									                    </div>
									
									                    <tr>
									                        <td><?php echo $row['nik'] ?></td>
									                        <td><?php echo $row['nama_keluarga']; ?></td>
									                        <td><?php echo $row['hubungan']; ?></td>
									                        <td><?php echo FormatTgl('d-m-Y', $row['tgl_lahir']); ?></td>
									                        <td><?php echo MasaKerjaPenyebut($row['tgl_lahir']); ?></td>
									                        <td><?php echo kelamin($row['jk']); ?></td>
									                        <td><?php echo $row['pendidikan']; ?></td>
									                        <td>
									                            <span data-toggle="modal" data-target="#modal-ubah-keluarga-<?php echo $row['id_fams']; ?>" title="Ubah" class="profile-action-btn warning">
									                                <i class="fa fa-edit"></i>
									                            </span>
									                            <span data-toggle="modal" data-target="#modal-hapus-keluarga-<?php echo $row['id_fams']; ?>" title="Hapus" class="profile-action-btn danger">
									                                <i class="fa fa-trash"></i>
									                            </span>
									                        </td>
									                    </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									</div>
		                            <div class="tab-pane" id="tab_13">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat Jabatan</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-penempatan">
									            <i class="fa fa-plus"></i> Tambah Riwayat Jabatan
									        </button>
									    </div>
									
									    <div class="modal fade profile-modal" id="modal-add-riwayat-penempatan">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-history"> Tambah Riwayat Jabatan</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-penempatan&id=' . $id_user . ''); ?>" method="post">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No SK</label>
									                            <input type="text" class="form-control" name="no_sk" placeholder="No SK" required>
									
									                            <label>Unit/Bagian</label>
									                            <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
									                                <option selected="selected" value="">-Pilih Unit/Bagian-</option>
									                                <?php
									                                $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
									                                while ($unit = fetch_array($tm_unit)) {
									                                    echo "<option value=" . $unit['id_unit'] . ">" . $unit['nama_unit'] . "</option>";
									                                }
									                                ?>
									                            </select>
									
									                            <label>Periode</label>
									                            <div class="input-group">
									                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
									                                <input type="text" name="periode" class="form-control pull-right" id="reservation1" required>
									                            </div>
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
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example4" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No</th>
									                        <th>No.SK</th>
									                        <th>Nama Unit/Bagian</th>
									                        <th>Periode</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $no = 0;
									                    $sql_riwayat_penempatan = bukaquery("SELECT tm_riwayat_penempatan.id_riwayat_penempatan, tm_riwayat_penempatan.no_sk, tm_riwayat_penempatan.periode, tm_riwayat_penempatan.id_unit, tm_unit.nama_unit FROM tm_riwayat_penempatan inner join tm_unit on tm_unit.id_unit=tm_riwayat_penempatan.id_unit where id_user='$id_user'");
									                    while ($row = fetch_array($sql_riwayat_penempatan)) {
									                        $no++;
									                    ?>
									
									                        <div class="modal fade profile-modal" id="modal-ubah-riwayat-penempatan-<?php echo $row['id_riwayat_penempatan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                            <div class="modal-dialog" role="document">
									                                <div class="modal-content">
									                                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-penempatan&id=' . $row['id_riwayat_penempatan'] . ''); ?>" method="POST" role="form">
									                                        <div class="modal-header">
									                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                            <h4 class="modal-title" id="myModalLabel">Update Riwayat Jabatan</h4>
									                                        </div>
									                                        <div class="modal-body">
									                                            <div class="form-group">
									                                                <label>No SK</label>
									                                                <input type="text" class="form-control" value="<?php echo $row['no_sk']; ?>" name="no_sk" placeholder="No SK" required>
									
									                                                <label>Nama Unit/Bagian</label>
									                                                <select class="form-control select2" name="id_unit" data-placeholder="-Pilih Unit/Bagian-" style="width: 100%;" required>
									                                                    <option selected="selected" value="">-Pilih Unit/Bagian-</option>
									                                                    <?php
									                                                    $tm_unit = bukaquery("select tm_unit.id_unit,tm_unit.nama_unit from tm_unit");
									                                                    while ($unt = fetch_array($tm_unit)) {
									                                                        if ($row['id_unit'] == $unt['id_unit']) {
									                                                            echo "<option value=" . $unt['id_unit'] . " selected=" . $row['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
									                                                        } else {
									                                                            echo "<option value=" . $unt['id_unit'] . ">" . $unt['nama_unit'] . "</option>";
									                                                        }
									                                                    }
									                                                    ?>
									                                                </select>
									
									                                                <label>Periode</label>
									                                                <div class="input-group">
									                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
									                                                    <input type="text" name="periode" value="<?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?>" class="form-control pull-right" data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"' data-mask required>
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
									
									                        <div class="modal fade profile-modal" id="modal-hapus-riwayat-penempatan-<?php echo $row['id_riwayat_penempatan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                            <div class="modal-dialog" role="document">
									                                <div class="modal-content">
									                                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-penempatan&id=' . $row['id_riwayat_penempatan'] . ''); ?>" method="POST" role="form">
									                                        <div class="modal-header">
									                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                            <h4 class="modal-title" id="myModalLabel">Delete Data Riwayat Jabatan</h4>
									                                        </div>
									                                        <div class="modal-body">
									                                            <div class="form-group">
									                                                <label>Yakin Anda ingin menghapus data ini?</label>
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
									
									                        <tr>
									                            <td><?php echo $no; ?></td>
									                            <td><?php echo $row['no_sk']; ?></td>
									                            <td><?php echo $row['nama_unit']; ?></td>
									                            <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-riwayat-penempatan-<?php echo $row['id_riwayat_penempatan']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-riwayat-penempatan-<?php echo $row['id_riwayat_penempatan']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									</div>
		                            <div class="tab-pane" id="tab_14">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat Diklat</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-diklat">
									            <i class="fa fa-plus"></i> Tambah Riwayat Diklat
									        </button>
									    </div>
									
									    <div class="modal fade profile-modal" id="modal-add-riwayat-diklat">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-history"> Tambah Riwayat Diklat</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-diklat&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>Nama Pelatihan</label>
									                            <input type="text" class="form-control" name="nama_pelatihan" placeholder="Nama Pelatihan" required>
									
									                            <label>Instansi Pelatihan</label>
									                            <input type="text" class="form-control" name="instansi_pelatihan" placeholder="Pelaksana" required>
									
									                            <label>Lokasi Pelatihan</label>
									                            <input type="text" class="form-control" name="lokasi" placeholder="Lokasi" required>
									
									                            <label>Alamat Pelatihan</label>
									                            <textarea rows="3" class="form-control" name="alamat_pelatihan" placeholder="Alamat Pelatihan" required></textarea>
									
									                            <label>Periode Pelatihan</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text" name="periode" class="form-control pull-right" id="reservation3" required>
									                            </div>
									
									                            <label>Total Jam</label>
									                            <input type="number" class="form-control" name="total_jam" placeholder="Total Jam Pelatihan" required>
									
									                            <label>Jenis Diklat</label>
									                            <?php echo enumDropdown("tm_riwayat_diklat", "jenis_diklat", "", "-Pilih Jenis-"); ?>
									
									                            <label>No Sertifikat</label>
									                            <input type="text" class="form-control" name="no_sertifikat" placeholder="No Sertifikat" required>
									
									                            <label>Status Terakreditasi</label>
									                            <?php echo enumDropdown("tm_riwayat_diklat", "status_akreditasi", "", "-Pilih Status-"); ?>
									
									                            <label>Status Kepesertaan</label>
									                            <?php echo enumDropdown("tm_riwayat_diklat", "status_peserta", "", "-Pilih Status-"); ?>
									
									                            <label>Jumlah SKP</label>
									                            <input type="number" class="form-control" name="nilai_skp" placeholder="Nilai SKP">
									
									                            <label>UPLOAD FILE</label>
									                            <input type="file" class="form-control" name="file" required>
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
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example5" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No</th>
									                        <th>Nama Pelatihan</th>
									                        <th>Lokasi Pelatihan</th>
									                        <th>Periode</th>
									                        <th>Jenis Diklat</th>
									                        <th>No Sertifikat</th>
									                        <th>Jumlah SKP</th>
									                        <th>Jumlah JP</th>
									                        <th>Validasi Diklat</th>
									                        <th>File Sertifikat</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $no = 0;
									                    $sql_riwayat_diklat = bukaquery("SELECT * from tm_riwayat_diklat where id_user='$id_user'");
									                    while ($row = fetch_array($sql_riwayat_diklat)) {
									                        $no++;
									                    ?>
									                        <!-- modal edit -->
									                        <div class="modal fade profile-modal" id="modal-ubah-riwayat-diklat-<?php echo $row['id_riwayat_diklat']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                            <div class="modal-dialog" role="document">
									                                <div class="modal-content">
									                                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-diklat&id=' . $row['id_riwayat_diklat'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
									                                        <div class="modal-header">
									                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                            <h4 class="modal-title" id="myModalLabel">Update Riwayat Diklat <?php echo $row['id_riwayat_diklat'] ?></h4>
									                                        </div>
									                                        <div class="modal-body">
									                                            <div class="form-group">
									                                                <label>Nama Pelatihan</label>
									                                                <input type="text" class="form-control" name="nama_pelatihan" value="<?php echo $row['nama_pelatihan']; ?>" placeholder="Nama Pelatihan" required>
									
									                                                <label>Instansi Pelatihan</label>
									                                                <input type="text" class="form-control" name="instansi_pelatihan" value="<?php echo $row['instansi_pelatihan']; ?>" placeholder="Pelaksana" required>
									
									                                                <label>Lokasi Pelatihan</label>
									                                                <input type="text" class="form-control" name="lokasi" value="<?php echo $row['lokasi']; ?>" placeholder="Lokasi" required>
									
									                                                <label>Alamat Pelatihan</label>
									                                                <textarea rows="3" class="form-control" name="alamat_pelatihan" placeholder="Alamat Pelatihan" required><?php echo $row['alamat_pelatihan']; ?></textarea>
									
									                                                <label>Periode Pelatihan</label>
									                                                <div class="input-group">
									                                                    <div class="input-group-addon">
									                                                        <i class="fa fa-calendar"></i>
									                                                    </div>
									                                                    <input type="text" name="periode" value="<?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?>" class="form-control pull-right" data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"' data-mask required>
									                                                </div>
									
									                                                <label>Total Jam</label>
									                                                <input type="number" class="form-control" name="total_jam" value="<?php echo $row['total_jam']; ?>" placeholder="Total Jam Pelatihan" required>
									
									                                                <label>Jenis Diklat</label>
									                                                <?php echo UpdateEnumDropdown("tm_riwayat_diklat", "jenis_diklat", $row['jenis_diklat'], ""); ?>
									
									                                                <label>No Sertifikat</label>
									                                                <input type="text" class="form-control" name="no_sertifikat" value="<?php echo $row['no_sertifikat']; ?>" placeholder="No Sertifikat" required>
									
									                                                <label>Status Terakreditasi</label>
									                                                <?php echo UpdateEnumDropdown("tm_riwayat_diklat", "status_akreditasi", $row['status_akreditasi'], ""); ?>
									
									                                                <label>Status Kepesertaan</label>
									                                                <?php echo UpdateEnumDropdown("tm_riwayat_diklat", "status_peserta", $row['status_peserta'], ""); ?>
									
									                                                <label>Jumlah SKP</label>
									                                                <input type="number" class="form-control" name="nilai_skp" value="<?php echo $row['jumlah_skp']; ?>" placeholder="Nilai SKP">
									
									                                                <label>UPLOAD FILE</label>
									                                                <input type="file" class="form-control" name="file">
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
									
									                        <!-- modal hapus -->
									                        <div class="modal fade profile-modal" id="modal-hapus-riwayat-diklat-<?php echo $row['id_riwayat_diklat']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									                            <div class="modal-dialog" role="document">
									                                <div class="modal-content">
									                                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-diklat&id=' . $row['id_riwayat_diklat'] . ''); ?>" method="POST" role="form">
									                                        <div class="modal-header">
									                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									                                            <h4 class="modal-title" id="myModalLabel">Delete Data DIKLAT</h4>
									                                        </div>
									                                        <div class="modal-body">
									                                            <div class="form-group">
									                                                <label>Yakin Anda ingin menghapus data ini?</label>
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
									
									                        <tr>
									                            <td><?php echo $no; ?></td>
									                            <td><?php echo $row['nama_pelatihan'] . " <p>(" . $row['instansi_pelatihan'] . ")</p>"; ?></td>
									                            <td><?php echo $row['lokasi'] . " <p>(Alamat : " . $row['alamat_pelatihan'] . ")</p>"; ?></td>
									                            <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))) . " (" . $row['total_jam'] . " Jam)"; ?></td>
									                            <td><?php echo $row['jenis_diklat']; ?></td>
									                            <td><?php echo $row['no_sertifikat'] . " <p>Status Terakreditasi (" . $row['status_akreditasi'] . ")</p>"; ?></td>
									                            <td><?php echo $row['jumlah_skp'] . " <p>( " . $row['status_peserta'] . " )</p>"; ?></td>
									                            <td><?php echo $row['total_jam'] . " Jam"; ?></td>
									                            <td>
									                                <?php if ($row['validasi'] == 'Y') { ?>
									                                    <span class="btn-xs btn-success fa fa-check"> Tervalidasi</span>
									                                <?php } elseif ($row['validasi'] == 'N') { ?>
									                                    <span class="btn-xs btn-danger fa fa-ban"> Ditolak</span>
									                                    <div style="font-size:11px; margin-top:3px;"><em><?php echo htmlspecialchars($row['keterangan'] ?? '-', ENT_QUOTES); ?></em></div>
									                                <?php } else { ?>
									                                    <span class="btn-xs btn-warning fa fa-clock-o"> Belum Divalidasi</span>
									                                <?php } ?>
									                            </td>
									                            <td><a href="simpeg/diklat/<?php echo $row['file_diklat']; ?>" target="_blank"><?php echo $row['file_diklat']; ?></a></td>
									                            <td>
									                                <?php if ($row['validasi'] == 'Y') { ?>
									                                    <span class="btn-xs btn-secondary fa fa-edit disabled" style="pointer-events:none; opacity:0.6;" title="Data sudah tervalidasi"></span>
									                                    <span class="btn-xs btn-secondary fa fa-trash disabled" style="pointer-events:none; opacity:0.6;" title="Data sudah tervalidasi"></span>
									                                <?php } else { ?>
									                                    <span data-toggle="modal" data-target="#modal-ubah-riwayat-diklat-<?php echo $row['id_riwayat_diklat']; ?>" title="Ubah" class="btn-xs btn-warning fa fa-edit"></span>
									                                    <?php if ($row['config'] == 1) { ?>
									                                        <span data-toggle="modal" data-target="#modal-hapus-riwayat-diklat-<?php echo $row['id_riwayat_diklat']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
									                                    <?php } ?>
									                                <?php } ?>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									</div>
		                            <div class="tab-pane" id="tab_15">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat Peningkatan Pendidikan</div>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example6" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No</th>
									                        <th>No Izin Belajar</th>
									                        <th>Nama Akademik/Univ</th>
									                        <th>Jenis Peningkatan</th>
									                        <th>Peningkatan</th>
									                        <th>Akreditasi</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $no = 0;
									                    $sql_izin_belajar = bukaquery("SELECT * from tm_izin_belajar where id_user='$id_user'");
									                    while ($row = fetch_array($sql_izin_belajar)) {
									                        $no++;
									                    ?>
									                        <tr>
									                            <td><?php echo $no; ?></td>
									                            <td><?php echo $row['no_izin']; ?></td>
									                            <td><?php echo $row['nama_univ'] . " <p>Alamat : " . $row['alamat_univ'] . "</p>"; ?></td>
									                            <td><?php echo $row['jenis_peningkatan']; ?></td>
									                            <td><?php echo "Dari " . $row['pendidikan_sebelum'] . " ke " . $row['pendidikan_sesudah'] . " " . $row['jurusan']; ?></td>
									                            <td><?php echo $row['akreditasi']; ?></td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									</div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		
		            <div class="tab-pane" id="tab_3">
		                <div class="profile-section-card">
		                    <div class="profile-section-head">
		                        <h3 class="profile-section-title">Berkas Digital Pegawai</h3>
		                        <div class="profile-section-desc">Kelola dokumen pegawai seperti KTP, KK, ijazah, NPWP, STR, SIP, dan sertifikat lainnya.</div>
		                    </div>
		
		                    <div class="nav-tabs-custom" style="margin-bottom:0; box-shadow:none; border-radius:0;">
		                        <ul class="nav nav-tabs profile-inner-tabs">
		                            <li class="active"><a href="#tab_16" data-toggle="tab">KTP</a></li>
		                            <li><a href="#tab_17" data-toggle="tab">KK</a></li>
		                            <li><a href="#tab_18" data-toggle="tab">Ijazah</a></li>
		                            <li><a href="#tab_19" data-toggle="tab">NPWP</a></li>
		                            <li><a href="#tab_20" data-toggle="tab">STR</a></li>
		                            <li><a href="#tab_21" data-toggle="tab">SIP</a></li>
		                            <li><a href="#tab_22" data-toggle="tab">ACLS</a></li>
		                            <li><a href="#tab_23" data-toggle="tab">ATLS</a></li>
		                            <li><a href="#tab_24" data-toggle="tab">BTCLS</a></li>
		                            <li><a href="#tab_25" data-toggle="tab">APN</a></li>
		                            <li><a href="#tab_26" data-toggle="tab">Phelebotomy</a></li>
		                            <li><a href="#tab_27" data-toggle="tab">SK Jabatan</a></li>
		                            <li><a href="#tab_28" data-toggle="tab">SPK &amp; RKK</a></li>
		                        </ul>
		
		                        <div class="tab-content profile-section-body">
									<!-- KTP -->
									<div class="tab-pane active" id="tab_16">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Upload KTP</div>
									        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-ktp">
									            <i class="fa fa-upload"></i> Upload KTP
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example16" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No NIK</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sqlktp = bukaquery("SELECT * FROM tm_ktp WHERE id_user='$id_user' ORDER BY tm_ktp.no_nik DESC");
									                    while ($row = fetch_array($sqlktp)) {
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_nik']; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_ktp']; ?>" target="_blank">
									                                    <?php echo $row['file_ktp']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-ktp-<?php echo $row['no_nik']; ?>" class="profile-action-btn warning" title="Ubah">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-ktp-<?php echo $row['no_nik']; ?>" class="profile-action-btn danger" title="Hapus">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sqlktp_modal = bukaquery("SELECT * FROM tm_ktp WHERE id_user='$id_user' ORDER BY tm_ktp.no_nik DESC");
									    while ($row = fetch_array($sqlktp_modal)) {
									    ?>
									        <div class="modal fade profile-modal" id="modal-ubah-ktp-<?php echo $row['no_nik']; ?>" tabindex="-1" role="dialog">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-ktp&id=' . $row['no_nik'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
									                            <h4 class="modal-title">Update KTP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <label>No NIK</label>
									                            <input type="text" class="form-control" name="no_nik" value="<?php echo $row['no_nik']; ?>" required>
									
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									                            <button type="submit" class="btn btn-primary">Update</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									
									        <div class="modal fade profile-modal" id="modal-hapus-ktp-<?php echo $row['no_nik']; ?>" tabindex="-1" role="dialog">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-ktp&id=' . $row['no_nik'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
									                            <h4 class="modal-title">Hapus KTP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger">Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade profile-modal" id="modal-add-ktp">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
									                    <h4 class="modal-title fa fa-upload"> Upload KTP</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-ktp&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <label>No NIK</label>
									                        <input type="text" class="form-control" name="no_nik" placeholder="No NIK" required>
									
									                        <label>Upload File</label>
									                        <input type="file" class="form-control" name="file" required>
									                    </div>
									                    <div class="modal-footer">
									                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									                        <input type="submit" class="btn btn-primary" value="Simpan">
									                    </div>
									                </form>
									            </div>
									        </div>
									    </div>
									</div>
		                            <div class="tab-pane" id="tab_17">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Upload KK</div>
									        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-kk">
									            <i class="fa fa-upload"></i> Upload KK
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example17" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No KK</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_kk = bukaquery("SELECT * FROM tm_kk WHERE id_user='$id_user' ORDER BY tm_kk.no_kk DESC");
									                    while ($row = fetch_array($sql_kk)) {
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_kk']; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_kk']; ?>" target="_blank">
									                                    <?php echo $row['file_kk']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-kk-<?php echo $row['no_kk']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-kk-<?php echo $row['no_kk']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_kk_modal = bukaquery("SELECT * FROM tm_kk WHERE id_user='$id_user' ORDER BY tm_kk.no_kk DESC");
									    while ($row = fetch_array($sql_kk_modal)) {
									    ?>
									        <div class="modal fade profile-modal" id="modal-ubah-kk-<?php echo $row['no_kk']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-kk&id=' . $row['no_kk'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Update KK</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No KK</label>
									                                <input type="text" class="form-control" name="no_kk" value="<?php echo $row['no_kk']; ?>" placeholder="No KK" required>
									
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade profile-modal" id="modal-hapus-kk-<?php echo $row['no_kk']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-kk&id=' . $row['no_kk'] . ''); ?>" method="POST" role="form">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Delete Data KK</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade profile-modal" id="modal-add-kk">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-upload"> Upload KK</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-kk&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No KK</label>
									                            <input type="text" class="form-control" name="no_kk" placeholder="No KK" required>
									
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file" required>
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
									</div>
		                            <div class="tab-pane" id="tab_18">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Upload Ijazah</div>
									        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-ijazah">
									            <i class="fa fa-upload"></i> Upload Ijazah
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example18" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No Ijazah</th>
									                        <th>Nama Sekolah</th>
									                        <th>Pendidikan</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_ijazah = bukaquery("SELECT tm_ijazah.id_riwayat_pend, tm_riwayat_pend.no_ijazah, tm_riwayat_pend.nama_sekolah, tm_riwayat_pend.pendidikan, tm_ijazah.file_ijazah
									                            FROM tm_ijazah
									                            INNER JOIN tm_riwayat_pend ON tm_ijazah.id_riwayat_pend=tm_riwayat_pend.id_riwayat_pend 
									                            WHERE tm_ijazah.id_user='$id_user' ORDER BY tm_ijazah.id_riwayat_pend DESC");
									                    while ($row = fetch_array($sql_ijazah)) {
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_ijazah']; ?></td>
									                            <td><?php echo $row['nama_sekolah']; ?></td>
									                            <td><?php echo $row['pendidikan']; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_ijazah']; ?>" target="_blank">
									                                    <?php echo $row['file_ijazah']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-ijazah-<?php echo $row['id_riwayat_pend']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-ijazah-<?php echo $row['id_riwayat_pend']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_ijazah_modal = bukaquery("SELECT tm_ijazah.id_riwayat_pend, tm_riwayat_pend.no_ijazah, tm_riwayat_pend.nama_sekolah, tm_riwayat_pend.pendidikan, tm_ijazah.file_ijazah
									            FROM tm_ijazah
									            INNER JOIN tm_riwayat_pend ON tm_ijazah.id_riwayat_pend=tm_riwayat_pend.id_riwayat_pend 
									            WHERE tm_ijazah.id_user='$id_user' ORDER BY tm_ijazah.id_riwayat_pend DESC");
									    while ($row = fetch_array($sql_ijazah_modal)) {
									    ?>
									        <div class="modal fade profile-modal" id="modal-ubah-ijazah-<?php echo $row['id_riwayat_pend']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-ijazah&id=' . $row['id_riwayat_pend'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Update Upload Ijazah</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>Pilih Sekolah</label>
									                                <select class="form-control select2" name="id_riwayat_pend" data-placeholder="-Pilih Sekolah-" style="width: 100%;" required>
									                                    <option value="">-Pilih Sekolah-</option>
									                                    <?php
									                                    $tm_sekolah = bukaquery("SELECT tm_riwayat_pend.id_riwayat_pend, tm_riwayat_pend.nama_sekolah 
									                                            FROM tm_riwayat_pend WHERE tm_riwayat_pend.id_user='$id_user'");
									                                    while ($unt = fetch_array($tm_sekolah)) {
									                                        if ($row['id_riwayat_pend'] == $unt['id_riwayat_pend']) {
									                                            echo "<option value=\"" . $unt['id_riwayat_pend'] . "\" selected>" . $unt['nama_sekolah'] . "</option>";
									                                        } else {
									                                            echo "<option value=\"" . $unt['id_riwayat_pend'] . "\">" . $unt['nama_sekolah'] . "</option>";
									                                        }
									                                    }
									                                    ?>
									                                </select>
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade profile-modal" id="modal-hapus-ijazah-<?php echo $row['id_riwayat_pend']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-ijazah&id=' . $row['id_riwayat_pend'] . ''); ?>" method="POST" role="form">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Delete Data Ijazah</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade profile-modal" id="modal-add-ijazah">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university"> Upload Ijazah</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-ijazah&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>Pilih Sekolah</label>
									                            <select class="form-control select2" name="id_riwayat_pend" data-placeholder="-Pilih Sekolah-" style="width: 100%;" required>
									                                <option value="">-Pilih Sekolah-</option>
									                                <?php
									                                $tm_sekolah_add = bukaquery("SELECT tm_riwayat_pend.id_riwayat_pend, tm_riwayat_pend.nama_sekolah 
									                                        FROM tm_riwayat_pend WHERE tm_riwayat_pend.id_user='$id_user'");
									                                while ($unt = fetch_array($tm_sekolah_add)) {
									                                    echo "<option value=\"" . $unt['id_riwayat_pend'] . "\">" . $unt['nama_sekolah'] . "</option>";
									                                }
									                                ?>
									                            </select>
									
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file" required>
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
									</div>
		                            <div class="tab-pane" id="tab_19">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Upload NPWP</div>
									        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-npwp">
									            <i class="fa fa-upload"></i> Upload NPWP
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example19" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No NPWP</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_npwp = bukaquery("SELECT * FROM tm_npwp WHERE id_user='$id_user' ORDER BY tm_npwp.no_npwp DESC");
									                    while ($row = fetch_array($sql_npwp)) {
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_npwp']; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_npwp']; ?>" target="_blank">
									                                    <?php echo $row['file_npwp']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-npwp-<?php echo $row['no_npwp']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-npwp-<?php echo $row['no_npwp']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_npwp_modal = bukaquery("SELECT * FROM tm_npwp WHERE id_user='$id_user' ORDER BY tm_npwp.no_npwp DESC");
									    while ($row = fetch_array($sql_npwp_modal)) {
									    ?>
									        <div class="modal fade profile-modal" id="modal-ubah-npwp-<?php echo $row['no_npwp']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-npwp&id=' . $row['no_npwp'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Update NPWP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No NPWP</label>
									                                <input type="text" class="form-control" name="no_npwp" value="<?php echo $row['no_npwp']; ?>" placeholder="No NPWP" required>
									
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade profile-modal" id="modal-hapus-npwp-<?php echo $row['no_npwp']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-npwp&id=' . $row['no_npwp'] . ''); ?>" method="POST" role="form">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Delete Data NPWP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade profile-modal" id="modal-add-npwp">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-upload"> Upload NPWP</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-npwp&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No NPWP</label>
									                            <input type="text" class="form-control" name="no_npwp" placeholder="No NPWP" required>
									
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file" required>
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
									</div>
		                            <div class="tab-pane" id="tab_20">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat STR</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-str">
									            <i class="fa fa-plus"></i> Tambah Riwayat STR
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example15" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No STR</th>
									                        <th>Periode STR</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_str = bukaquery("SELECT * FROM tm_str WHERE id_user='$id_user' ORDER BY tm_str.id_str DESC");
									                    while ($row = fetch_array($sql_riwayat_str)) {
									                        $periode_display = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_str']; ?></td>
									                            <td><?php echo $periode_display; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_str']; ?>" target="_blank">
									                                    <?php echo $row['file_str']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-str-<?php echo $row['id_str']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-str-<?php echo $row['id_str']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_riwayat_str_modal = bukaquery("SELECT * FROM tm_str WHERE id_user='$id_user' ORDER BY tm_str.id_str DESC");
									    while ($row = fetch_array($sql_riwayat_str_modal)) {
									        $periode_display = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade profile-modal" id="modal-ubah-str-<?php echo $row['id_str']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-str&id=' . $row['id_str'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Update Riwayat STR</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No STR</label>
									                                <input type="text" class="form-control" name="no_str" value="<?php echo $row['no_str']; ?>" placeholder="No STR" required>
									
									                                <label>Periode STR</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text" name="periode" value="<?php echo $periode_display; ?>" 
									                                           class="form-control pull-right" 
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"' 
									                                           data-mask required>
									                                </div>
									
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade profile-modal" id="modal-hapus-str-<?php echo $row['id_str']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-str&id=' . $row['id_str'] . ''); ?>" method="POST" role="form">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="myModalLabel">Delete Data STR</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade profile-modal" id="modal-add-riwayat-str">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university"> Tambah Riwayat STR</h4>
									                </div>
									                <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-str&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No STR</label>
									                            <input type="text" class="form-control" name="no_str" placeholder="No STR" required>
									
									                            <label>Periode STR</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text" name="periode" class="form-control pull-right" 
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"' 
									                                       data-mask required>
									                            </div>
									
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file" required>
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
									</div>
		                            <div class="tab-pane" id="tab_21">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat SIP</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-sip">
									            <i class="fa fa-plus"></i> Tambah Riwayat SIP
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example7" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No SIP</th>
									                        <th>Periode SIP</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_sip = bukaquery("SELECT * FROM tm_sip WHERE id_user='$id_user' ORDER BY tm_sip.id_sip DESC");
									                    while ($row = fetch_array($sql_riwayat_sip)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_sip']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_sip']; ?>" target="_blank">
									                                    <?php echo $row['file_sip']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-sip-<?php echo $row['id_sip']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-sip-<?php echo $row['id_sip']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_sip = bukaquery("SELECT * FROM tm_sip WHERE id_user='$id_user' ORDER BY tm_sip.id_sip DESC");
									    while ($row = fetch_array($sql_modal_sip)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-sip-<?php echo $row['id_sip']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahSipLabel<?php echo $row['id_sip']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-sip&id=' . $row['id_sip'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahSipLabel<?php echo $row['id_sip']; ?>">Update Riwayat SIP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No SIP</label>
									                                <input type="text" class="form-control" name="no_sip" value="<?php echo $row['no_sip']; ?>" placeholder="No SIP" required>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Periode SIP</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-sip-<?php echo $row['id_sip']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusSipLabel<?php echo $row['id_sip']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-sip&id=' . $row['id_sip'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusSipLabel<?php echo $row['id_sip']; ?>">Delete Data SIP</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-sip" tabindex="-1" role="dialog" aria-labelledby="modalAddSipLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddSipLabel"> Tambah Riwayat SIP</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-sip&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No SIP</label>
									                            <input type="text" class="form-control" name="no_sip" placeholder="No SIP" required>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Periode SIP</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
		                            <div class="tab-pane" id="tab_22">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat ACLS</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-acls">
									            <i class="fa fa-plus"></i> Tambah Riwayat ACLS
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example8" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No ACLS</th>
									                        <th>Periode ACLS</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_acls = bukaquery("SELECT * FROM tm_acls WHERE id_user='$id_user' ORDER BY tm_acls.id_acls DESC");
									                    while ($row = fetch_array($sql_riwayat_acls)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_acls']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_acls']; ?>" target="_blank">
									                                    <?php echo $row['file_acls']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-acls-<?php echo $row['id_acls']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-acls-<?php echo $row['id_acls']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_acls = bukaquery("SELECT * FROM tm_acls WHERE id_user='$id_user' ORDER BY tm_acls.id_acls DESC");
									    while ($row = fetch_array($sql_modal_acls)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-acls-<?php echo $row['id_acls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahAclsLabel<?php echo $row['id_acls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-acls&id=' . $row['id_acls'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahAclsLabel<?php echo $row['id_acls']; ?>">Update Riwayat ACLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No ACLS</label>
									                                <input type="text" class="form-control" name="no_acls" value="<?php echo $row['no_acls']; ?>" placeholder="No ACLS" required>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Periode ACLS</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-acls-<?php echo $row['id_acls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusAclsLabel<?php echo $row['id_acls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-acls&id=' . $row['id_acls'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusAclsLabel<?php echo $row['id_acls']; ?>">Delete Data ACLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-acls" tabindex="-1" role="dialog" aria-labelledby="modalAddAclsLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddAclsLabel"> Tambah Riwayat ACLS</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-acls&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No ACLS</label>
									                            <input type="text" class="form-control" name="no_acls" placeholder="No ACLS" required>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Periode ACLS</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
		                            <div class="tab-pane" id="tab_23">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat ATLS</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-atls">
									            <i class="fa fa-plus"></i> Tambah Riwayat ATLS
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example9" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No ATLS</th>
									                        <th>Periode ATLS</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_atls = bukaquery("SELECT * FROM tm_atls WHERE id_user='$id_user' ORDER BY tm_atls.id_atls DESC");
									                    while ($row = fetch_array($sql_riwayat_atls)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_atls']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_atls']; ?>" target="_blank">
									                                    <?php echo $row['file_atls']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-atls-<?php echo $row['id_atls']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-atls-<?php echo $row['id_atls']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_atls = bukaquery("SELECT * FROM tm_atls WHERE id_user='$id_user' ORDER BY tm_atls.id_atls DESC");
									    while ($row = fetch_array($sql_modal_atls)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-atls-<?php echo $row['id_atls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahAtlsLabel<?php echo $row['id_atls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-atls&id=' . $row['id_atls'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahAtlsLabel<?php echo $row['id_atls']; ?>">Update Riwayat ATLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No ATLS</label>
									                                <input type="text" class="form-control" name="no_atls" value="<?php echo $row['no_atls']; ?>" placeholder="No ATLS" required>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Periode ATLS</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-atls-<?php echo $row['id_atls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusAtlsLabel<?php echo $row['id_atls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-atls&id=' . $row['id_atls'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusAtlsLabel<?php echo $row['id_atls']; ?>">Delete Data ATLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-atls" tabindex="-1" role="dialog" aria-labelledby="modalAddAtlsLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddAtlsLabel"> Tambah Riwayat ATLS</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-atls&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No ATLS</label>
									                            <input type="text" class="form-control" name="no_atls" placeholder="No ATLS" required>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Periode ATLS</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
		                            <div class="tab-pane" id="tab_24">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat BTCLS</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-btcls">
									            <i class="fa fa-plus"></i> Tambah Riwayat BTCLS
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example10" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No BTCLS</th>
									                        <th>Periode BTCLS</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_btcls = bukaquery("SELECT * FROM tm_btcls WHERE id_user='$id_user' ORDER BY tm_btcls.id_btcls DESC");
									                    while ($row = fetch_array($sql_riwayat_btcls)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_btcls']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_btcls']; ?>" target="_blank">
									                                    <?php echo $row['file_btcls']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-btcls-<?php echo $row['id_btcls']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-btcls-<?php echo $row['id_btcls']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_btcls = bukaquery("SELECT * FROM tm_btcls WHERE id_user='$id_user' ORDER BY tm_btcls.id_btcls DESC");
									    while ($row = fetch_array($sql_modal_btcls)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-btcls-<?php echo $row['id_btcls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahBtclsLabel<?php echo $row['id_btcls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-btcls&id=' . $row['id_btcls'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahBtclsLabel<?php echo $row['id_btcls']; ?>">Update Riwayat BTCLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                abel>No BTCLS</label>
									                                <input type="text" class="form-control" name="no_btcls" value="<?php echo $row['no_btcls']; ?>" placeholder="No BTCLS" required>
									                            </div>
									
									                            <div class="form-group">
									                                abel>Periode BTCLS</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                abel>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-btcls-<?php echo $row['id_btcls']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusBtclsLabel<?php echo $row['id_btcls']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-btcls&id=' . $row['id_btcls'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusBtclsLabel<?php echo $row['id_btcls']; ?>">Delete Data BTCLS</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-btcls" tabindex="-1" role="dialog" aria-labelledby="modalAddBtclsLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddBtclsLabel"> Tambah Riwayat BTCLS</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-btcls&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            abel>No BTCLS</label>
									                            <input type="text" class="form-control" name="no_btcls" placeholder="No BTCLS" required>
									                        </div>
									
									                        <div class="form-group">
									                            abel>Periode BTCLS</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            abel>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
		                            <div class="tab-pane" id="tab_25">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat APN</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-apn">
									            <i class="fa fa-plus"></i> Tambah Riwayat APN
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example12" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No APN</th>
									                        <th>Periode APN</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_apn = bukaquery("SELECT * FROM tm_apn WHERE id_user='$id_user' ORDER BY tm_apn.id_apn DESC");
									                    while ($row = fetch_array($sql_riwayat_apn)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_apn']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_apn']; ?>" target="_blank">
									                                    <?php echo $row['file_apn']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-apn-<?php echo $row['id_apn']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-apn-<?php echo $row['id_apn']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_apn = bukaquery("SELECT * FROM tm_apn WHERE id_user='$id_user' ORDER BY tm_apn.id_apn DESC");
									    while ($row = fetch_array($sql_modal_apn)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-apn-<?php echo $row['id_apn']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahApnLabel<?php echo $row['id_apn']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-apn&id=' . $row['id_apn'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahApnLabel<?php echo $row['id_apn']; ?>">Update Riwayat APN</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No APN</label>
									                                <input type="text" class="form-control" name="no_apn" value="<?php echo $row['no_apn']; ?>" placeholder="No APN" required>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Periode APN</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-apn-<?php echo $row['id_apn']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusApnLabel<?php echo $row['id_apn']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-apn&id=' . $row['id_apn'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusApnLabel<?php echo $row['id_apn']; ?>">Delete Data APN</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-apn" tabindex="-1" role="dialog" aria-labelledby="modalAddApnLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddApnLabel"> Tambah Riwayat APN</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-apn&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No APN</label>
									                            <input type="text" class="form-control" name="no_apn" placeholder="No APN" required>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Periode APN</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
		                            <div class="tab-pane" id="tab_26">
									    <div class="profile-toolbar">
									        <div class="profile-toolbar-title">Riwayat Phelebethomy</div>
									        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-riwayat-phelebethomy">
									            <i class="fa fa-plus"></i> Tambah Riwayat Phelebethomy
									        </button>
									    </div>
									
									    <div class="profile-table-wrap">
									        <div class="table-responsive">
									            <table id="example11" class="table table-bordered table-striped">
									                <thead>
									                    <tr>
									                        <th>No Phelebethomy</th>
									                        <th>Periode Phelebethomy</th>
									                        <th>File</th>
									                        <th>Action</th>
									                    </tr>
									                </thead>
									                <tbody>
									                    <?php
									                    $sql_riwayat_phelebethomy = bukaquery("SELECT * FROM tm_phelebethomy WHERE id_user='$id_user' ORDER BY tm_phelebethomy.id_phelebethomy DESC");
									                    while ($row = fetch_array($sql_riwayat_phelebethomy)) {
									                        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									                    ?>
									                        <tr>
									                            <td><?php echo $row['no_phelebethomy']; ?></td>
									                            <td><?php echo $periode_tampil; ?></td>
									                            <td>
									                                <a href="simpeg/<?php echo $row['file_phelebethomy']; ?>" target="_blank">
									                                    <?php echo $row['file_phelebethomy']; ?>
									                                </a>
									                            </td>
									                            <td>
									                                <span data-toggle="modal" data-target="#modal-ubah-phelebethomy-<?php echo $row['id_phelebethomy']; ?>" title="Ubah" class="profile-action-btn warning">
									                                    <i class="fa fa-edit"></i>
									                                </span>
									                                <span data-toggle="modal" data-target="#modal-hapus-phelebethomy-<?php echo $row['id_phelebethomy']; ?>" title="Hapus" class="profile-action-btn danger">
									                                    <i class="fa fa-trash"></i>
									                                </span>
									                            </td>
									                        </tr>
									                    <?php } ?>
									                </tbody>
									            </table>
									        </div>
									    </div>
									
									    <?php
									    $sql_modal_phelebethomy = bukaquery("SELECT * FROM tm_phelebethomy WHERE id_user='$id_user' ORDER BY tm_phelebethomy.id_phelebethomy DESC");
									    while ($row = fetch_array($sql_modal_phelebethomy)) {
									        $periode_tampil = date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10)));
									    ?>
									        <div class="modal fade" id="modal-ubah-phelebethomy-<?php echo $row['id_phelebethomy']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUbahPhelebethomyLabel<?php echo $row['id_phelebethomy']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-riwayat-phelebethomy&id=' . $row['id_phelebethomy'] . ''); ?>" method="POST" enctype="multipart/form-data">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalUbahPhelebethomyLabel<?php echo $row['id_phelebethomy']; ?>">Update Riwayat PHELEBETHOMY</h4>
									                        </div>
									                        <div class="modal-body">
									                            <div class="form-group">
									                                <label>No Phelebethomy</label>
									                                <input type="text" class="form-control" name="no_phelebethomy" value="<?php echo $row['no_phelebethomy']; ?>" placeholder="No PHELEBETHOMY" required>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Periode Phelebethomy</label>
									                                <div class="input-group">
									                                    <div class="input-group-addon">
									                                        <i class="fa fa-calendar"></i>
									                                    </div>
									                                    <input type="text"
									                                           name="periode"
									                                           value="<?php echo $periode_tampil; ?>"
									                                           class="form-control pull-right"
									                                           data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                           data-mask
									                                           required>
									                                </div>
									                            </div>
									
									                            <div class="form-group">
									                                <label>Upload File</label>
									                                <input type="file" class="form-control" name="file">
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
									
									        <div class="modal fade" id="modal-hapus-phelebethomy-<?php echo $row['id_phelebethomy']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusPhelebethomyLabel<?php echo $row['id_phelebethomy']; ?>">
									            <div class="modal-dialog" role="document">
									                <div class="modal-content">
									                    <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-riwayat-phelebethomy&id=' . $row['id_phelebethomy'] . ''); ?>" method="POST">
									                        <div class="modal-header">
									                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                                <span aria-hidden="true">&times;</span>
									                            </button>
									                            <h4 class="modal-title" id="modalHapusPhelebethomyLabel<?php echo $row['id_phelebethomy']; ?>">Delete Data PHELEBETHOMY</h4>
									                        </div>
									                        <div class="modal-body">
									                            <p>Yakin Anda ingin menghapus data ini?</p>
									                        </div>
									                        <div class="modal-footer">
									                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
									                            <button type="submit" class="btn btn-danger" autofocus>Ya</button>
									                        </div>
									                    </form>
									                </div>
									            </div>
									        </div>
									    <?php } ?>
									
									    <div class="modal fade" id="modal-add-riwayat-phelebethomy" tabindex="-1" role="dialog" aria-labelledby="modalAddPhelebethomyLabel">
									        <div class="modal-dialog">
									            <div class="modal-content">
									                <div class="modal-header">
									                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
									                        <span aria-hidden="true">&times;</span>
									                    </button>
									                    <h4 class="modal-title fa fa-university" id="modalAddPhelebethomyLabel"> Tambah Riwayat Phelebethomy</h4>
									                </div>
									                <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-riwayat-phelebethomy&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
									                    <div class="modal-body">
									                        <div class="form-group">
									                            <label>No Phelebethomy</label>
									                            <input type="text" class="form-control" name="no_phelebethomy" placeholder="No PHELEBETHOMY" required>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Periode Phelebethomy</label>
									                            <div class="input-group">
									                                <div class="input-group-addon">
									                                    <i class="fa fa-calendar"></i>
									                                </div>
									                                <input type="text"
									                                       name="periode"
									                                       class="form-control pull-right"
									                                       data-inputmask='"mask": "99-99-9999 - 99-99-9999","placeholder":"dd-mm-yyyy - dd-mm-yyyy"'
									                                       data-mask
									                                       required>
									                            </div>
									                        </div>
									
									                        <div class="form-group">
									                            <label>Upload File</label>
									                            <input type="file" class="form-control" name="file">
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
									</div>
									<!--Untuk SK Jabatan-->
									<div class="tab-pane" id="tab_27">
                                        <div class="profile-toolbar">
                                            <div class="profile-toolbar-title">Upload SK Jabatan</div>
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-sk-jabatan">
                                                <i class="fa fa-upload"></i> Upload SK Jabatan
                                            </button>
                                        </div>
                                    
                                        <div class="profile-table-wrap">
                                            <div class="table-responsive">
                                                <table id="example27" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No SK</th>
                                                            <th>Nama Unit/Bagian</th>
                                                            <th>Periode</th>
                                                            <th>File</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sql_sk_jabatan = bukaquery("SELECT 
                                                                tm_sk_jabatan.id_riwayat_penempatan,
                                                                tm_riwayat_penempatan.no_sk,
                                                                tm_riwayat_penempatan.periode,
                                                                tm_unit.nama_unit,
                                                                tm_sk_jabatan.file_skjabatan
                                                            FROM tm_sk_jabatan
                                                            INNER JOIN tm_riwayat_penempatan 
                                                                ON tm_sk_jabatan.id_riwayat_penempatan = tm_riwayat_penempatan.id_riwayat_penempatan
                                                            INNER JOIN tm_unit 
                                                                ON tm_riwayat_penempatan.id_unit = tm_unit.id_unit
                                                            WHERE tm_sk_jabatan.id_user='$id_user'
                                                            ORDER BY tm_sk_jabatan.id_riwayat_penempatan DESC");
                                                        while ($row = fetch_array($sql_sk_jabatan)) {
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $row['no_sk']; ?></td>
                                                                <td><?php echo $row['nama_unit']; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . ' s/d ' . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td>
                                                                    <a href="simpeg/<?php echo $row['file_skjabatan']; ?>" target="_blank">
                                                                        <?php echo $row['file_skjabatan']; ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span data-toggle="modal" data-target="#modal-ubah-sk-jabatan-<?php echo $row['id_riwayat_penempatan']; ?>" title="Ubah" class="profile-action-btn warning">
                                                                        <i class="fa fa-edit"></i>
                                                                    </span>
                                                                    <span data-toggle="modal" data-target="#modal-hapus-sk-jabatan-<?php echo $row['id_riwayat_penempatan']; ?>" title="Hapus" class="profile-action-btn danger">
                                                                        <i class="fa fa-trash"></i>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    
                                        <?php
                                        $sql_sk_jabatan_modal = bukaquery("SELECT 
                                                tm_sk_jabatan.id_riwayat_penempatan,
                                                tm_riwayat_penempatan.no_sk,
                                                tm_riwayat_penempatan.periode,
                                                tm_unit.nama_unit,
                                                tm_sk_jabatan.file_skjabatan
                                            FROM tm_sk_jabatan
                                            INNER JOIN tm_riwayat_penempatan 
                                                ON tm_sk_jabatan.id_riwayat_penempatan = tm_riwayat_penempatan.id_riwayat_penempatan
                                            INNER JOIN tm_unit 
                                                ON tm_riwayat_penempatan.id_unit = tm_unit.id_unit
                                            WHERE tm_sk_jabatan.id_user='$id_user'
                                            ORDER BY tm_sk_jabatan.id_riwayat_penempatan DESC");
                                        while ($row = fetch_array($sql_sk_jabatan_modal)) {
                                        ?>
                                            <div class="modal fade profile-modal" id="modal-ubah-sk-jabatan-<?php echo $row['id_riwayat_penempatan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-sk-jabatan&id=' . $row['id_riwayat_penempatan'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">Update Upload SK Jabatan</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Pilih Riwayat Jabatan</label>
                                                                    <select class="form-control select2" name="id_riwayat_penempatan" data-placeholder="-Pilih Riwayat Jabatan-" style="width: 100%;" required>
                                                                        <option value="">-Pilih Riwayat Jabatan-</option>
                                                                        <?php
                                                                        $tm_riwayat_penempatan = bukaquery("SELECT 
                                                                                tm_riwayat_penempatan.id_riwayat_penempatan,
                                                                                tm_riwayat_penempatan.no_sk,
                                                                                tm_unit.nama_unit
                                                                            FROM tm_riwayat_penempatan
                                                                            INNER JOIN tm_unit 
                                                                                ON tm_riwayat_penempatan.id_unit = tm_unit.id_unit
                                                                            WHERE tm_riwayat_penempatan.id_user='$id_user'
                                                                            ORDER BY tm_riwayat_penempatan.id_riwayat_penempatan DESC");
                                                                        while ($unt = fetch_array($tm_riwayat_penempatan)) {
                                                                            if ($row['id_riwayat_penempatan'] == $unt['id_riwayat_penempatan']) {
                                                                                echo "<option value=\"" . $unt['id_riwayat_penempatan'] . "\" selected>" . $unt['no_sk'] . " - " . $unt['nama_unit'] . "</option>";
                                                                            } else {
                                                                                echo "<option value=\"" . $unt['id_riwayat_penempatan'] . "\">" . $unt['no_sk'] . " - " . $unt['nama_unit'] . "</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                    
                                                                    <label>Upload File</label>
                                                                    <input type="file" class="form-control" name="file">
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
                                    
                                            <div class="modal fade profile-modal" id="modal-hapus-sk-jabatan-<?php echo $row['id_riwayat_penempatan']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-sk-jabatan&id=' . $row['id_riwayat_penempatan'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">Delete Data SK Jabatan</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Yakin Anda ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                                <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    
                                        <div class="modal fade profile-modal" id="modal-add-sk-jabatan">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title fa fa-upload"> Upload SK Jabatan</h4>
                                                    </div>
                                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-sk-jabatan&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Pilih Riwayat Jabatan</label>
                                                                <select class="form-control select2" name="id_riwayat_penempatan" data-placeholder="-Pilih Riwayat Jabatan-" style="width: 100%;" required>
                                                                    <option value="">-Pilih Riwayat Jabatan-</option>
                                                                    <?php
                                                                    $tm_riwayat_penempatan_add = bukaquery("SELECT 
                                                                            tm_riwayat_penempatan.id_riwayat_penempatan,
                                                                            tm_riwayat_penempatan.no_sk,
                                                                            tm_unit.nama_unit
                                                                        FROM tm_riwayat_penempatan
                                                                        INNER JOIN tm_unit 
                                                                            ON tm_riwayat_penempatan.id_unit = tm_unit.id_unit
                                                                        WHERE tm_riwayat_penempatan.id_user='$id_user'
                                                                        ORDER BY tm_riwayat_penempatan.id_riwayat_penempatan DESC");
                                                                    while ($unt = fetch_array($tm_riwayat_penempatan_add)) {
                                                                        echo "<option value=\"" . $unt['id_riwayat_penempatan'] . "\">" . $unt['no_sk'] . " - " . $unt['nama_unit'] . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                    
                                                                <label>Upload File</label>
                                                                <input type="file" class="form-control" name="file" required>
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
                                    </div>
									<!--//-->
									<div class="tab-pane" id="tab_28">
                                        <div class="profile-toolbar">
                                            <div class="profile-toolbar-title">Upload SPK &amp; RKK</div>
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-add-spkrkk">
                                                <i class="fa fa-upload"></i> Upload SPK / RKK
                                            </button>
                                        </div>
                                    
                                        <div class="profile-table-wrap">
                                            <div class="table-responsive">
                                                <table id="example28" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Tipe</th>
                                                            <th>No SPK / RKK</th>
                                                            <th>Tanggal Dikeluarkan</th>
                                                            <th>File</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sql_spkrkk = bukaquery("SELECT * FROM tm_spk_rkk 
                                                            WHERE id_user='$id_user' 
                                                            ORDER BY id_spkrkk DESC");
                                                        while ($row = fetch_array($sql_spkrkk)) {
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $row['tipe']; ?></td>
                                                                <td><?php echo $row['no_spkrkk']; ?></td>
                                                                <td><?php echo $row['tgl_dikeluarkan'] != '' ? date('d-m-Y', strtotime($row['tgl_dikeluarkan'])) : '-'; ?></td>
                                                                <td>
                                                                    <a href="simpeg/<?php echo $row['file_spkrkk']; ?>" target="_blank">
                                                                        <?php echo $row['file_spkrkk']; ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span data-toggle="modal" data-target="#modal-ubah-spkrkk-<?php echo $row['id_spkrkk']; ?>" title="Ubah" class="profile-action-btn warning">
                                                                        <i class="fa fa-edit"></i>
                                                                    </span>
                                                                    <span data-toggle="modal" data-target="#modal-hapus-spkrkk-<?php echo $row['id_spkrkk']; ?>" title="Hapus" class="profile-action-btn danger">
                                                                        <i class="fa fa-trash"></i>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    
                                        <?php
                                        $sql_spkrkk_modal = bukaquery("SELECT * FROM tm_spk_rkk 
                                            WHERE id_user='$id_user' 
                                            ORDER BY id_spkrkk DESC");
                                        while ($row = fetch_array($sql_spkrkk_modal)) {
                                        ?>
                                            <div class="modal fade profile-modal" id="modal-ubah-spkrkk-<?php echo $row['id_spkrkk']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=update-spkrkk&id=' . $row['id_spkrkk'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">Update Upload SPK / RKK</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Tipe</label>
                                                                    <select class="form-control" name="tipe" required>
                                                                        <option value="">- Pilih Tipe -</option>
                                                                        <option value="SPK" <?php echo $row['tipe'] == 'SPK' ? 'selected' : ''; ?>>SPK</option>
                                                                        <option value="RKK" <?php echo $row['tipe'] == 'RKK' ? 'selected' : ''; ?>>RKK</option>
                                                                    </select>
                                    
                                                                    <label>No SPK / RKK</label>
                                                                    <input type="text" class="form-control" name="no_spkrkk" value="<?php echo $row['no_spkrkk']; ?>" placeholder="No SPK / RKK" required>
                                    
                                                                    <label>Tanggal Dikeluarkan</label>
                                                                    <input type="text" class="form-control" value="<?php echo FormatTgl('d-m-Y', $row['tgl_dikeluarkan']); ?>" name="tgl_dikeluarkan" data-inputmask='"mask": "99-99-9999","placeholder":"dd-mm-yyyy"' data-mask required>
                                    
                                                                    <label>Upload File</label>
                                                                    <input type="file" class="form-control" name="file">
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
                                    
                                            <div class="modal fade profile-modal" id="modal-hapus-spkrkk-<?php echo $row['id_spkrkk']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="<?php echo $aksi . paramEncrypt('module=simpeg&act=delete-spkrkk&id=' . $row['id_spkrkk'] . ''); ?>" method="POST" role="form">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">Delete Data SPK / RKK</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Yakin Anda ingin menghapus data ini?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                                <button type="submit" class="btn btn-danger" autofocus>Ya</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    
                                        <div class="modal fade profile-modal" id="modal-add-spkrkk">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        <h4 class="modal-title fa fa-upload"> Upload SPK / RKK</h4>
                                                    </div>
                                                    <form role="form" action="<?php echo $aksi . paramEncrypt('module=simpeg&act=add-spkrkk&id=' . $id_user . ''); ?>" method="post" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Tipe</label>
                                                                <select class="form-control" name="tipe" required>
                                                                    <option value="">- Pilih Tipe -</option>
                                                                    <option value="SPK">SPK</option>
                                                                    <option value="RKK">RKK</option>
                                                                </select>
                                    
                                                                <label>No SPK / RKK</label>
                                                                <input type="text" class="form-control" name="no_spkrkk" placeholder="No SPK / RKK" required>
                                    
                                                                <label>Tanggal Dikeluarkan</label>
                                                                <input type="text" class="form-control" name="tanggal_dikeluarkan" id="datepicker4" placeholder="mm/dd/yyyy" required>
                                    
                                                                <label>Upload File</label>
                                                                <input type="file" class="form-control" name="file" required>
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
                                    </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		
		<script>
		$(function () {
		    if ($.fn.select2) {
		        $('.select2').select2({ width: '100%' });
		    }
		
		    if ($.fn.inputmask) {
		        $('[data-mask]').inputmask();
		    }
		
		    if ($.fn.datepicker) {
		        $('#tanggal_lahir1').datepicker({
		            autoclose: true,
		            format: 'mm/dd/yyyy'
		        });
		
		        $('#datepicker1, #datepicker4').datepicker({
		            autoclose: true,
		            format: 'mm/dd/yyyy'
		        });
		    }
		
		    if ($.fn.daterangepicker) {
		        $('#reservation, #reservation1, #reservation_penempatan, #reservation_diklat').daterangepicker({
		            locale: {
		                format: 'DD-MM-YYYY'
		            }
		        });
		    }
		
		    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
		        if ($.fn.DataTable) {
		            $.each($.fn.dataTable.tables(true), function () {
		                $(this).DataTable().columns.adjust();
		            });
		        }
		    });
		
		    $('.modal').on('shown.bs.modal', function () {
		        $(this).find('input:text:visible:first').focus();
		    });
		});
		</script>
		<?php
		break;

		case "profile-pegawai":
        ?>
        <div class="box">

            <div class="box-header with-border">  
                <h3 class="box-title fa fa-user-circle">  DATA PEGAWAI </h3> 
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div> 

            </div>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">BIODATA</a></li>
                    <li class=""> <a href="?<?php echo paramEncrypt('module=master-data&act=list-master-data-pegawai-non-pns'); ?>" ><span class="fa fa-undo"> Kembali</span></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="box-body">  
                            <?php $biodata = fetch_array(bukaquery("SELECT tm_pegawai.nik, tm_pegawai.nip, tm_pegawai.no_rek, tm_pegawai.tempat_lahir, tm_pegawai.agama, tm_pegawai.npwp,tm_pegawai.jk, 
                                            tm_pegawai.nama_pegawai, tm_pegawai.tgl_masuk, tm_pegawai.pendidikan, tm_pegawai.alamat, tm_pegawai.no_bpjs, tm_pegawai.no_hp,tm_pegawai.tgl_lahir,tm_pegawai.foto,
                                            tm_unit.id_unit, tm_unit.nama_unit, tm_pegawai.status_nikah, tm_pegawai.sub_bagian,tm_pegawai.pajak, tm_pegawai.rumpun,tm_pegawai.log_finger, 
                                            tm_pegawai.bpjs_ks, tm_pegawai.bpjs_jkk,tm_pegawai.status, tm_pegawai.bpjs_ijht, tm_pegawai.bpjs_jp, tm_level.nama_level, tm_level.id_level 
                                            FROM
                                            tm_pegawai
                                            INNER JOIN tm_unit ON tm_pegawai.id_unit = tm_unit.id_unit
                                            INNER JOIN tm_user ON tm_user.id_user = tm_pegawai.id_user
                                            INNER JOIN tm_level ON tm_user.id_level = tm_level.id_level
                                            where tm_pegawai.id_user='$id'")); ?>                           
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3 col-md-4"> 
                                        <label>NIK</label>
                                        <input type="text" class="form-control" value="<?php echo $biodata['nik']; ?>" readonly>
                                        <label>NAMA PEGAWAI</label>
                                        <input type="text" class="form-control" value="<?php echo $biodata['nama_pegawai']; ?>" readonly>
                                        <label>ALAMAT</label>
                                        <textarea class="form-control" placeholder="Alamat" title="Alamat" readonly><?php echo $biodata['alamat']; ?></textarea>
                                        <label>NO REKENING DKI</label>
                                        <input type="text" class="form-control " value="<?php echo $biodata['no_rek']; ?>" readonly> 
                                        <label>NPWP</label>
                                        <input type="text" class="form-control" value="<?php echo $biodata['npwp']; ?>" readonly>
                                        <label>GAPOK</label>
                                        <input type="text" class="form-control" value="<?php echo formatDuit(GajiPokok($biodata['pendidikan'], $biodata['tgl_masuk'])); ?>" readonly>
                                        <label>GAJI BRUTO</label>
                                        <input type="text" class="form-control" value="<?php echo formatDuit(GajiBruto(GajiPokok($biodata['pendidikan'], $biodata['tgl_masuk']), NilaiStatusKawin($biodata['status_nikah']))); ?>" readonly>
                                        <br><br> 
                                    </div>
                                    <div class="col-xs-8 col-md-4"> 
                                        <div class="col-xs-6">
                                            <label>TEMPAT LAHIR</label>
                                            <input type="text" class="form-control " value="<?php echo $biodata['tempat_lahir']; ?>" readonly> 
                                            <label>JENIS KELAMIN</label>
                                            <input type="text" class="form-control" value="<?php echo kelamin($biodata['jk']); ?>" readonly>
                                            <label>TANGGAL MASUK</label>
                                            <input type="text" class="form-control" value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_masuk']); ?>" readonly>
                                            <br>
                                            <label>PENDIDIDIKAN</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['pendidikan']; ?>" readonly>
                                            <label>RUMPUN JABATAN</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['rumpun']; ?>" readonly>
                                            <label>STATUS NIKAH</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['status_nikah']; ?>" readonly>
                                            <label>BAGIAN/UNIT</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['nama_unit']; ?>" readonly>
                                        </div>
                                        <div class="col-xs-4">
                                            <label>TANGGAL LAHIR </label>
                                            <input type="text" class="form-control " value="<?php echo FormatTgl('d/m/Y', $biodata['tgl_lahir']); ?>" readonly> 
                                            <label>AGAMA</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['agama']; ?>" readonly>
                                            <label>MASA KERJA</label>
                                            <input type="text" class="form-control" value="<?php echo MasaKerjaPenyebut($biodata['tgl_masuk']); ?>" readonly>
                                            <label>NO HP</label>
                                            <input type="text" class="form-control" value="<?php echo $biodata['no_hp']; ?>" readonly>
                                            <br>
                                        </div>
                                        <div class="col-xs-2">
                                            <?php
                                            if ($biodata['foto'] == '-' OR $biodata['foto'] == '') {
                                                if ($biodata['jk'] == 'L') {
                                                    $foto = 'img/laki.png';
                                                } else {
                                                    $foto = 'img/perempuan.png';
                                                }
                                            } else {
                                                $foto = "img/" . $biodata['foto'];
                                            }
                                            ?>
                                            <img src="<?php echo $foto; ?>" style="align:center" class="img-rounded" width="450px" height="450px" alt="" > 
                                        </div>
                                        <div class="col-xs-4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <di
                        </div>
                    </div>                    
                </div>
            </div>

            <!-- SIMPEG-->
            <div class="margin row">
                <div class="x_panel">
                    <!-- Riwayat Pendidikan -->
                    <div class="panel-group" id="dropdown">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title fa  fa-hospital-o">
                                    <a data-toggle="collapse" data-parent="#dropdown" href="#collapse1">
                                        SIMPEG</a>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#tab_11" data-toggle="tab"> RIWAYAT PENDIDIKAN</a></li>
                                            <li><a href="#tab_12" data-toggle="tab">DAFTAR KELUARGA</a></li>
                                            <li><a href="#tab_13" data-toggle="tab">RIWAYAT JABATAN</a></li>
                                            <li><a href="#tab_14" data-toggle="tab">RIWAYAT DIKLAT</a></li>
                                            <!--<li><a href="#tab_15" data-toggle="tab">RIWAYAT PENINGKATAN PENDIDIKAN</a></li>-->
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_11">
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example2" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Pendidikan</th>
                                                                <th>Nama Sekolah</th>
                                                                <th>Jurusan</th>
                                                                <th>Periode</th>
                                                                <th>No.Ijazah</th>
                                                                <th>Tgl Ijazah</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_pend = bukaquery("SELECT * FROM tm_riwayat_pend where id_user='$id' order by tm_riwayat_pend.tgl_ijazah asc");
                                                            while ($row = fetch_array($sql_riwayat_pend)) {
                                                                $no++;
                                                                ?>                                                              
                                                           
                                                            <tr>
                                                                <td><?php echo $row['pendidikan'] ?></td>
                                                                <td><?php echo $row['nama_sekolah']; ?></td>
                                                                <td><?php echo $row['jurusan']; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><?php echo $row['no_ijazah']; ?> </td>
                                                                <td><?php echo FormatTgl('d-m-Y', $row['tgl_ijazah']); ?></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_12">
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example3" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>NIK</th>
                                                                <th>Nama</th>
                                                                <th>Hubungan</th>
                                                                <th>Tanggal Lahir</th>
                                                                <th>Umur</th>
                                                                <th>Jenis Kelamin</th>
                                                                <th>Pendidikan</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_keluarga = bukaquery("SELECT * FROM tm_keluarga where id_user='$id'");
                                                            while ($row = fetch_array($sql_keluarga)) {
                                                                $no++;
                                                                ?>
                                                              
                                                            <tr>
                                                                <td><?php echo $row['nik'] ?></td>
                                                                <td><?php echo $row['nama_keluarga']; ?></td>
                                                                <td><?php echo $row['hubungan']; ?></td>
                                                                <td><?php echo FormatTgl('d-m-Y', $row['tgl_lahir']); ?></td>
                                                                <td><?php echo MasaKerjaPenyebut($row['tgl_lahir']); ?></td>                                            
                                                                <td><?php echo kelamin($row['jk']); ?></td>
                                                                <td><?php echo $row['pendidikan']; ?> </td>                                            
                                                               
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_13">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example4" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>No.SK</th>
                                                                <th>Nama Unit/Bagian</th>
                                                                <th>Periode</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_penempatan = bukaquery("SELECT tm_riwayat_penempatan.id_riwayat_penempatan, tm_riwayat_penempatan.no_sk, tm_riwayat_penempatan.periode, tm_riwayat_penempatan.id_unit, tm_unit.nama_unit "
                                                                    . "FROM tm_riwayat_penempatan "
                                                                    . "inner join tm_unit on tm_unit.id_unit=tm_riwayat_penempatan.id_unit "
                                                                    . "where id_user='$id'");
                                                            while ($row = fetch_array($sql_riwayat_penempatan)) {
                                                                $no++;
                                                                ?>
                                                               
                                                            <tr>
                                                                <td><?php echo $no; ?></td>
                                                                <td><?php echo $row['no_sk']; ?></td>
                                                                <td><?php echo $row['nama_unit']; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>                                          
                                                               
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>  
                                            </div>
                                            <div class="tab-pane" id="tab_14">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example5" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama Pelatihan</th>
                                                                <th>Lokasi Pelatihan</th>
                                                                <th>Periode</th>
                                                                <th>Jenis Diklat</th>
                                                                <th>No Sertifikat</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_diklat = bukaquery("SELECT * from tm_riwayat_diklat where id_user='$id'");
                                                            while ($row = fetch_array($sql_riwayat_diklat)) {
                                                                $no++;
                                                                ?>
                                                              
                                                            <tr>
                                                                <td><?php echo $no; ?></td>
                                                                <td><?php echo $row['nama_pelatihan'] . " <p>(" . $row['instansi_pelatihan'] . ")</p>"; ?></td>
                                                                <td><?php echo $row['lokasi'] . " <p>(Alamat : " . $row['alamat_pelatihan'] . ")</p>"; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))) . " (" . $row['total_jam'] . " Jam)"; ?></td>
                                                                <td><?php echo $row['jenis_diklat']; ?></td>
                                                                <td><?php echo $row['no_sertifikat'] . " <p>Status Terakreditasi (" . $row['status_akreditasi'] . ")</p>"; ?></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>  
                                            </div>
                                            <div class="tab-pane" id="tab_15">                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example6" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>No Izin Belajar</th>
                                                                <th>Nama Akademik/Univ</th>
                                                                <th>Jenis Peningkatan</th>
                                                                <th>Peningkatan</th>
                                                                <th>Akreditasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_izin_belajar = bukaquery("SELECT * from tm_izin_belajar where id_user='$id'");
                                                            while ($row = fetch_array($sql_izin_belajar)) {
                                                                $no++;
                                                                ?>                                                                
                                                                <tr>
                                                                    <td><?php echo $no; ?></td>
                                                                    <td><?php echo $row['no_izin']; ?></td>
                                                                    <td><?php echo $row['nama_univ'] . " <p> Alamat : " . $row['alamat_univ'] . "</p>"; ?></td>
                                                                    <td><?php echo $row['jenis_peningkatan']; ?></td>
                                                                    <td><?php echo "Dari " . $row['pendidikan_sebelum'] . " Ke " . $row['pendidikan_sesudah'] . " " . $row['jurusan']; ?></td>
                                                                    <td><?php echo $row['akreditasi']; ?></td>
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
                        </div>
                    </div>
                </div>
                <!-- upload file-->
                <div class="x_panel">
                    <!-- Riwayat Pendidikan -->
                    <div class="panel-group" id="dropdown">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title fa  fa-upload">
                                </h4>
                            </div>
                            <div id="collapse2" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#tab_22" data-toggle="tab">KTP</a></li>
                                            <li><a href="#tab_23" data-toggle="tab">KK</a></li>
                                            <li><a href="#tab_24" data-toggle="tab">IJAZAH</a></li>
                                            <li><a href="#tab_26" data-toggle="tab">NPWP</a></li>
                                            <li><a href="#tab_25" data-toggle="tab">STR</a></li>
                                            <li><a href="#tab_16" data-toggle="tab">SIP</a></li>
                                            <li><a href="#tab_17" data-toggle="tab">ACLS</a></li>
                                            <li><a href="#tab_18" data-toggle="tab">ATLS</a></li>
                                            <li><a href="#tab_19" data-toggle="tab">BTCLS</a></li>
                                            <li><a href="#tab_20" data-toggle="tab">APN</a></li>
                                            <li><a href="#tab_21" data-toggle="tab">PHELEBOTOMY</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane" id="tab_16">
                                               
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example7" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No SIP</th>
                                                                <th>Periode SIP</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_sip = bukaquery("SELECT * FROM tm_sip where id_user='$id' order by tm_sip.id_sip desc");
                                                            while ($row = fetch_array($sql_riwayat_sip)) {
                                                                $no++;
                                                                ?>                                                               
                                                           
                                                            <tr>
                                                                <td><?php echo $row['no_sip'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_sip']; ?>" target="_blank"><?php echo $row['file_sip']; ?></a></td>
                                                              
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_17">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example8" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No ACLS</th>
                                                                <th>Periode ACLS</th>
                                                                <th>File</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_acls = bukaquery("SELECT * FROM tm_acls where id_user='$id' order by tm_acls.id_acls desc");
                                                            while ($row = fetch_array($sql_riwayat_acls)) {
                                                                $no++;
                                                                ?>
                                                                
                                                            <tr>
                                                                <td><?php echo $row['no_acls'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_acls']; ?>" target="_blank"><?php echo $row['file_acls']; ?></a></td>
                                                               
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_18">
                                               
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example9" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No ATLS</th>
                                                                <th>Periode ATLS</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_atls = bukaquery("SELECT * FROM tm_atls where id_user='$id' order by tm_atls.id_atls desc");
                                                            while ($row = fetch_array($sql_riwayat_atls)) {
                                                                $no++;
                                                                ?>
                                                                
                                                            <tr>
                                                                <td><?php echo $row['no_atls'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_atls']; ?>" target="_blank"><?php echo $row['file_atls']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_19">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example10" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No BTCLS</th>
                                                                <th>Periode BTCLS</th>
                                                                <th>File</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_btcls = bukaquery("SELECT * FROM tm_btcls where id_user='$id' order by tm_btcls.id_btcls desc");
                                                            while ($row = fetch_array($sql_riwayat_btcls)) {
                                                                $no++;
                                                                ?>
                                                              
                                                            <tr>
                                                                <td><?php echo $row['no_btcls'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_btcls']; ?>" target="_blank"><?php echo $row['file_btcls']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_20"> 
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example12" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No APN</th>
                                                                <th>Periode APN</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_apn = bukaquery("SELECT * FROM tm_apn where id_user='$id' order by tm_apn.id_apn desc");
                                                            while ($row = fetch_array($sql_riwayat_apn)) {
                                                                $no++;
                                                                ?>
                                                                
                                                            <tr>
                                                                <td><?php echo $row['no_apn'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_apn']; ?>" target="_blank"><?php echo $row['file_apn']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_21">   
                                               
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example11" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No Phelebethomy</th>
                                                                <th>Periode Phelebethomy</th>
                                                                <th>File</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_phelebethomy = bukaquery("SELECT * FROM tm_phelebethomy where id_user='$id' order by tm_phelebethomy.id_phelebethomy desc");
                                                            while ($row = fetch_array($sql_riwayat_phelebethomy)) {
                                                                $no++;
                                                                ?>
                                                             
                                                            <tr>
                                                                <td><?php echo $row['no_phelebethomy'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_phelebethomy']; ?>" target="_blank"><?php echo $row['file_phelebethomy']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane active" id="tab_22">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example13" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No NIK</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_ktp = bukaquery("SELECT * FROM tm_ktp where id_user='$id' order by tm_ktp.no_nik desc");
                                                            while ($row = fetch_array($sql_ktp)) {
                                                                $no++;
                                                                ?>
                                                               
                                                            <tr>
                                                                <td><?php echo $row['no_nik'] ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_ktp']; ?>" target="_blank"><?php echo $row['file_ktp']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_23">
                                                
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example14" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No KK</th>
                                                                <th>File</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_kk = bukaquery("SELECT * FROM tm_kk where id_user='$id' order by tm_kk.no_kk desc");
                                                            while ($row = fetch_array($sql_kk)) {
                                                                $no++;
                                                                ?>
                                                              
                                                            <tr>
                                                                <td><?php echo $row['no_kk'] ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_kk']; ?>" target="_blank"><?php echo $row['file_kk']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="tab_24">   
                                               
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example16" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No Ijazah</th>
                                                                <th>Nama Sekolah</th>
                                                                <th>Pendidikan</th>
                                                                <th>File</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_ijazah = bukaquery("SELECT tm_ijazah.id_riwayat_pend, tm_riwayat_pend.no_ijazah, tm_riwayat_pend.nama_sekolah, tm_riwayat_pend.pendidikan, tm_ijazah.file_ijazah
                                                                        FROM tm_ijazah
                                                                        INNER JOIN tm_riwayat_pend ON tm_ijazah.id_riwayat_pend=tm_riwayat_pend.id_riwayat_pend where tm_ijazah.id_user='$id'");
                                                            while ($row = fetch_array($sql_ijazah)) {
                                                                $no++;
                                                                ?>
                                                           
                                                            <tr>
                                                                <td><?php echo $row['no_ijazah'] ?></td>
                                                                <td><?php echo $row['nama_sekolah'] ?></td>
                                                                <td><?php echo $row['pendidikan'] ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_ijazah']; ?>" target="_blank"><?php echo $row['file_ijazah']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                                <div class="tab-pane" id="tab_26">
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example17" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No NPWP</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_npwp = bukaquery("SELECT * FROM tm_npwp where id_user='$id' order by tm_npwp.no_npwp desc");
                                                            while ($row = fetch_array($sql_npwp)) {
                                                                $no++;
                                                                ?>
                                                               
                                                            <tr>
                                                                <td><?php echo $row['no_npwp'] ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_npwp']; ?>" target="_blank"><?php echo $row['file_npwp']; ?></a></td>
                                                                
                                                            </tr>                                  
                                                            <?php
                                                        }
                                                        ?>    
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane" id="tab_25">  
                                                <!-- /.box-header -->
                                                <div class="table-responsive">
                                                    <table id="example15" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>No STR</th>
                                                                <th>Periode STR</th>
                                                                <th>File</th>
                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $no = 0;
                                                            $sql_riwayat_str = bukaquery("SELECT * FROM tm_str where id_user='$id' order by tm_str.id_str desc");
                                                            while ($row = fetch_array($sql_riwayat_str)) {
                                                                $no++;
                                                                ?>
                                                             
                                                            <tr>
                                                                <td><?php echo $row['no_str'] ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime(substr($row['periode'], 0, 10))) . " s/d " . date('d-m-Y', strtotime(substr($row['periode'], 13, 10))); ?></td>
                                                                <td><a href="simpeg/<?php echo $row['file_str']; ?>" target="_blank"><?php echo $row['file_str']; ?></a></td>
                                                                
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
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <?php
        break;
        
}
?>


