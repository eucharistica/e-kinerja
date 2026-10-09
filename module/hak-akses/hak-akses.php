<?php
$aksi = "module/hak-akses/aksi-hak-akses.php?";
$id = isset($url['id']) ? $url['id'] : null;
$dpa = isset($url['dpa']) ? $url['dpa'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        echo"default";
        header('location:error404');
        break;

    case "list-hak-akses":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-list">  LIST DATA HAK USER/LEVEL</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <button type="button" class="btn btn-info fa fa-plus" data-toggle="modal" data-target="#modal-add-hak-akses">
                    Buat Hak Akses
                </button>    
                <!--Modal Add-->
                <div class="modal fade" id="modal-add-hak-akses" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=simpan-hak-akses'); ?>" role="form" method="post">
                            <div class="modal-content"> 
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Form Add Hak Akses</h4>
                                </div>
                                <div class="modal-body">
                                    <div  class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Nama Level</label>
                                            <input type="text" name="nama_level" class="form-control" placeholder="Masukan Nama Level" required>
                                        </div>  
                                    </div>
                                    <div  class="col-md-8">
                                        <div class="form-group"> 
                                            <label class="control-label">Pilih Hak Akses/Level</label><br>
                                            <input type="checkbox" name="menu_kepegawaian" Value="1" class="flat"> Menu Kepegawaian &nbsp;<br>
                                            <input type="checkbox" name="menu_diklat" Value="1" class="flat"> Menu Diklat &nbsp;<br>
                                            <input type="checkbox" name="menu_keuangan" Value="1" class="flat"> Menu Keuangan &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Form</label><br>
                                            <input type="checkbox" name="menu_surtug" Value="1" class="flat"> Menu Surat Tugas &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Validasi</label><br>
                                            <input type="checkbox" name="menu_val_pj" Value="1" class="flat"> Menu Validasi PJ &nbsp;<br>
                                            <input type="checkbox" name="menu_val_kasatpel" Value="1" class="flat"> Menu Validasi Kasatpel &nbsp; <br>
                                            <input type="checkbox" name="menu_val_kasie" Value="1" class="flat"> Menu Validasi Kasie &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Laporan</label><br>
                                            <input type="checkbox" name="menu_laporan" Value="1" class="flat"> Menu Laporan &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Perpustakaan</label><br>
                                            <input type="checkbox" name="upload_perpustakaan" Value="1" class="flat"> Izinkan Upload File Perpustakaan &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Helpdesk</label><br>
                                            <input type="checkbox" name="menu_helpdesk" Value="1" class="flat"> Tiket Helpdesk &nbsp;<br>
                                        </div>
                                        <div class="form-group"> 
                                            <label class="control-label">Administrator</label><br>
                                            <input type="checkbox" name="configurasi" Value="1" class="flat"> Configurasi &nbsp;
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label class="control-label"><br></label>
                                    </div> 
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
                                    <button type="submit" class="btn btn-primary fa fa-save"> Save changes</button>                            
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--Tutup Modal Add-->
                <?php
                $sql = bukaquery("select * from tm_level");
                ?>
                <div class="box-body table-responsive">
                    <table id="example" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Level</th>
                                <th>Menu Kepegawaian</th>
                                <th>Menu Surat Tugas</th>
                                <th>Menu Diklat</th>
                                <th>Menu Keuangan</th>
                                <th>Menu Validasi Pj</th>
                                <th>Menu Validasi Kasatpel</th>
                                <th>Menu Validasi Kasie</th>
                                <th>Menu Laporan</th>
                                <th>Tiket Helpdesk</th>
                                <th>Upload Perpustakaan</th>
                                <th>Menu Configurasi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            while ($row = fetch_array($sql)) {
                                $no++;
                                ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $row['nama_level']; ?></td>
                                    <td><?php if ($row['menu_kepegawaian'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_surtug'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_diklat'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_keuangan'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_val_pj'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_val_kasatpel'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_val_kasie'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_laporan'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['upload_perpustakaan'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['menu_helpdesk'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>
                                    <td><?php if ($row['configurasi'] == '1') {
                                    ?> <i class="btn-success fa fa-check-square-o"></i> <?php } ?></td>                                   
                                    <td>
                                        <button type="button" class="btn-xs btn-warning fa fa-edit" data-toggle="modal" title="Update" data-target="#modal-edit-<?php echo $row['id_level']; ?>"> </button>
                                        <button type="button" class="btn-xs btn-danger fa fa-trash" data-toggle="modal" title="Delete" data-target="#modal-hapus-<?php echo $row['id_level']; ?>"> </button>
                                    </td>
                                </tr>   
                                <!--Modal Edit-->                                
                            <div class="modal fade" id="modal-edit-<?php echo $row['id_level']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog">  
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-hak-akses&id=' . $row['id_level'] . ''); ?>" role="form" method="post">
                                        <div class="modal-content"> 
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel">Form Edit Hak Akses/Level</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div  class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Nama Level</label>
                                                        <input type="text" name="nama_level" class="form-control" value="<?php echo $row['nama_level']; ?>" required>
                                                    </div>  
                                                </div>
                                                <div  class="col-md-8">
                                                    <div class="form-group"> 
                                                        <label class="control-label">Pilih Hak Akses/Level</label><br>
                                                        <input type="checkbox" name="menu_kepegawaian" Value="1" class="" <?php
                                                        if ($row['menu_kepegawaian'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Kepegawaian &nbsp;<br>
                                                        <input type="checkbox" name="menu_surtug" Value="1" <?php
                                                        if ($row['menu_surtug'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>>Menu Surat Tugas &nbsp;<br>
                                                        <input type="checkbox" name="menu_diklat" Value="1" <?php
                                                        if ($row['menu_diklat'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Diklat &nbsp;<br>
                                                        <input type="checkbox" name="menu_keuangan" Value="1" <?php
                                                        if ($row['menu_keuangan'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Keuangan &nbsp;<br>
                                                    </div>
                                                    <div class="form-group"> 
                                                        <label class="control-label">Validasi</label><br>
                                                        <input type="checkbox" name="menu_val_pj" Value="1" <?php
                                                        if ($row['menu_val_pj'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Validasi PJ &nbsp;<br>
                                                        <input type="checkbox" name="menu_val_kasatpel" Value="1" <?php
                                                        if ($row['menu_val_kasatpel'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Validasi Kasatpel &nbsp; <br>
                                                        <input type="checkbox" name="menu_val_kasie" Value="1" <?php
                                                        if ($row['menu_val_kasie'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Validasi Kasie &nbsp;<br>
                                                    </div>
                                                    <div class="form-group"> 
                                                        <label class="control-label">Laporan</label><br>
                                                        <input type="checkbox" name="menu_laporan" Value="1" <?php
                                                        if ($row['menu_laporan'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Menu Laporan &nbsp;<br>
                                                    </div>
                                                    <div class="form-group"> 
                                                        <label class="control-label">Perpustakaan</label><br>
                                                        <input type="checkbox" name="upload_perpustakaan" Value="1" <?php
                                                        if ($row['upload_perpustakaan'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Izinkan Upload File &nbsp;
                                                    </div>
                                                     <div class="form-group"> 
                                                        <label class="control-label">Helpdesk</label><br>
                                                        <input type="checkbox" name="menu_helpdesk" Value="1" <?php
                                                        if ($row['menu_helpdesk'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Tiket Helpdesk &nbsp;
                                                    </div>
                                                    <div class="form-group"> 
                                                        <label class="control-label">Administrator</label><br>
                                                        <input type="checkbox" name="configurasi" Value="1" <?php
                                                        if ($row['configurasi'] == '1') {
                                                            echo "checked";
                                                        }
                                                        ?>> Configurasi &nbsp;
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label"><br></label>
                                                </div> 
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
                                                <button type="submit" class="btn btn-warning fa fa-save"> Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!--Tutup Modal Edit-->
                            <!--Modal Hapus-->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_level']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=hapus-hak-akses&id=' . $row['id_level'] . ''); ?>" role="form" method="post">
                                        <div class="modal-content"> 
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="myModalLabel"><span class="lg glyphicon glyphicon-trash"></span> Approval Delete</h4>
                                            </div>
                                            <div class="modal-body">                                                    
                                                <p>Yakin Anda Ingin Penghapus Data Ini !!.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> No</button>
                                                <button type="submit" class="btn btn-danger fa fa-trash" autofocus=""> Yes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!--Tutup Modal Hapus-->
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

    case "setup":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-cog">  UPDATE DATA DASAR/SETUP</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">                  
                <?php $setup = fetch_array(bukaquery("select * from setup")); ?>
                <div id="collapse1" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1" data-toggle="tab"> <span class="fa fa-hospital-o"> DATA DASAR</span></a></li>
                                <li><a href="#tab_2" data-toggle="tab"><span class="fa fa-clock-o"> SETTING ABSENSI</span></a></li>
                                <li><a href="#tab_3" data-toggle="tab"><span class="fa fa-gear"> SETTING WAKTU</span></a></li> 
                                <li><a href="#tab_4" data-toggle="tab"><span class="fa fa-newspaper-o"> PENGUMUMAN</span></a></li>
                                <li><a href="#tab_5" data-toggle="tab"><span class="fa fa-newspaper-o"> RUNNING TEXT</span></a></li>   
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-data-dasar&id=' . $setup['id'] . ''); ?>" method="POST" role="form" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-3 col-md-4"> 
                                                    <label>KODE SKPD</label>
                                                    <input type="text" class="form-control" name="kode_skpd" value="<?php echo paramDecrypt($setup['kode_skpd']); ?>" required>
                                                    <label>NAMA INSTANSI</label>
                                                    <input type="text" class="form-control" name="nama_instansi" value="<?php echo $setup['nama_instansi']; ?>" required>
                                                    <label>ALAMAT</label>
                                                    <textarea class="form-control" name="alamat" placeholder="Alamat" title="Alamat" required><?php echo $setup['alamat_kop']; ?></textarea>
                                                    <label>NAMA DIREKTUR</label>
                                                    <input type="text" class="form-control" name="direktur" value="<?php echo $setup['direktur']; ?>" required>
                                                    <label>NIP DIREKTUR</label>
                                                    <input type="text" class="form-control" name="nip_direktur" value="<?php echo $setup['nip_direktur']; ?>" required>
                                                </div>
                                                <div class="col-xs-8 col-md-4"> 
                                                    <div class="col-xs-6">
                                                        <label>TLP</label>
                                                        <input type="text" class="form-control" name="tlp" value="<?php echo $setup['tlp']; ?>" required>
                                                        <label>FAX</label>
                                                        <input type="text" class="form-control" name="fax" value="<?php echo $setup['fax']; ?>" required>
                                                        <label>EMAIL</label>
                                                        <input type="email" class="form-control" name="email" value="<?php echo $setup['email']; ?>" required>
                                                        <br>
                                                        <label>WEBSITE</label>
                                                        <input type="text" class="form-control" name="website" value="<?php echo $setup['website']; ?>" required>
                                                        <label>KODE POS</label>
                                                        <input type="text" class="form-control" name="kode_pos" value="<?php echo $setup['kode_pos']; ?>" required>
                                                    </div>                                                            
                                                    <div class="col-xs-4">
                                                        <label>UPLOAD LOGO</label>
                                                        <input type="file"  class="form-control" name="file" >
                                                        <?php
                                                        if ($setup['logo'] == '') {
                                                            $foto = 'img/jayaraya.png';
                                                        } else {
                                                            $foto = "img/" . $setup['logo'];
                                                        }
                                                        ?>
                                                        <img src="<?php echo $foto; ?>" style="align:center" class="img-rounded" width="250" height="250" alt="" > 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary left" autofocus>Update</button>                                     
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_2">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-setting-keterlambatan-absensi&id=' . $setup['id'] . ''); ?>" method="POST" role="form">
                                        <div class="form-group">
                                            <div class="row"> 
                                                <div class="col-xs-3">
                                                    <label>DISPENSASI KETERLAMBATAN</label>                                                    
                                                    <input type="number" class="form-control" name="dispensasi_absensi" value="<?php echo $setup['dispensasi_absensi']; ?>" max="100" required>                                                      
                                                    <br>
                                                    <button type="submit" class="btn btn-warning" autofocus>Update</button>
                                                </div>                                                  
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_3">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-setting-waktu-kinerja&id=' . $setup['id'] . ''); ?>" method="POST" role="form">
                                        <div class="form-group">
                                            <div class="row"> 
                                                <div class="col-xs-2">
                                                    <label>TANGGAL PENARIKAN KINERJA</label>                                                    
                                                    <input type="number" class="form-control" name="tutup_kinerja" value="<?php echo $setup['tutup_kinerja']; ?>" max="31" required>                                                      
                                                    <br>
                                                    <button type="submit" class="btn btn-success" autofocus>Update</button>
                                                </div>
                                                <div class="col-xs-2">
                                                    <label>TANGGAL BATAS VALIDASI PJ</label>
                                                    <input type="number" class="form-control" name="validasi_pj" value="<?php echo $setup['validasi_pj']; ?>" max="31" required>                                                    
                                                </div>
                                                <div class="col-xs-2">
                                                    <label>TANGGAL BATAS VALIDASI KASIE</label>
                                                    <input type="number" class="form-control" name="validasi_kasie" value="<?php echo $setup['validasi_kasie']; ?>" max="31" required>
                                                </div>
                                                <div class="col-xs-3">
                                                    <label>TANGGAL BUKA MENU JADWAL PEGAWAI</label>
                                                    <input type="number" class="form-control" name="validasi_jadwal" value="<?php echo $setup['validasi_jadwal']; ?>" max="31" placeholder="Isi 0 untuk untuk membuka sebulan full" required>
                                                </div>                                                    
                                            </div>
                                        </div>
                                    </form>
                                </div> 
                                <div class="tab-pane" id="tab_4">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-pengumuman&id=' . $setup['id'] . ''); ?>" method="POST" role="form">
                                        <div class="form-group">
                                            <div class="row"> 
                                                <div class="col-xs-12">
                                                    <label>PENGUMUMAN</label>  
                                                    <br>
                                                    <textarea id="new" class="form-control" name="pengumuman" rows="30px" cols="120px">
                                                        <?php echo $setup['pengumuman'];
                                                        ;
                                                        ?>
                                                    </textarea>
                                                    <br>
                                                    <button type="submit" class="btn btn-warning" autofocus>Update</button>
                                                </div>                                                  
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_5">
                                    <form action="<?php echo $aksi . paramEncrypt('module=configurasi&act=update-runningtext&id=' . $setup['id'] . ''); ?>" method="POST" role="form">
                                        <div class="form-group">
                                            <div class="row"> 
                                                <div class="col-xs-12">
                                                    <label>RUNNING TEXT</label>  
                                                    <br>
                                                    <textarea id="new1" class="form-control" name="runningtext" rows="30px" cols="120px">
                                                        <?php echo $setup['running_text'];
                                                        ;
                                                        ?>
                                                    </textarea>
                                                    <br>
                                                    <button type="submit" class="btn btn-warning" autofocus>Update</button>
                                                </div>                                                  
                                            </div>
                                        </div>
                                    </form>
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

