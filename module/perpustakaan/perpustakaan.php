<?php
$aksi = "module/perpustakaan/aksi-perpustakaan.php?";
$id = isset($url['id']) ? $url['id'] : null;
switch ((isset($url['act']) ? $url['act'] : '')) {
    default :
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-university">  Perpustakaan </h3>               
            </div>
            <div class="box box-success">
                <div class="box-body">
                    Coming Soon.. Programernya istirahat dlu.. hehe
                </div>
            </div>
        </div>
        <?php
        break;

        case "ebook":
        $awal = FormatTgl('Y-m-d', TanggalAwalBulan_());
        $akhir = FormatTgl('Y-m-d', TanggalAkhirBulanDepan());
        ?>
        
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-book">  eBook RSUD MATRAMAN </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">     
                <?php if ($hak_akses['upload_perpustakaan'] == '1' OR $superuser != '') { ?>
                    <button type="button" class="btn btn-info fa fa-upload" data-toggle="modal" data-target="#modal-add-ebook">
                        Upload File eBook
                    </button>
                <?php } ?>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-ebook">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-upload"> Upload File eBook</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=add-ebook'); ?>" method="post" enctype="multipart/form-data"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>No eBook</label>
                                        <input type="text" class="form-control" name="no_ebook" placeholder="No eBook" required>
                                        <label class="control-label">Kategori eBook</label>
                                        <?php echo enumDropdown("tm_ebook", "kategori", "", "Kategori eBook"); ?>
                                        <label>Judul</label>
                                        <input type="text" class="form-control" name="judul" placeholder="Judul" required>
                                        <label>Penulis</label>
                                        <input type="text" class="form-control" name="penulis" placeholder="Penulis" required>
                                        <label>Penerbit</label>
                                        <input type="text" class="form-control" name="penerbit" placeholder="Penerbit" required>
                                        <label>Tahun Terbit</label>
                                        <input type="text" class="form-control" name="tahun" placeholder="Tahun Terbit" required>
                                        <label>Kota Terbit</label>
                                        <input type="text" class="form-control" name="kota" placeholder="Kota Terbit" required>
                                        <label>Tentang</label>
                                        <input type="text" class="form-control" name="tentang" placeholder="Tentang" required>
                                        <label>Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3px" cols="3px"  ></textarea>
                                        <label>File</label>
                                        <input type="file" class="form-control" name="file"  required>
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
                                <th>NO</th>
                                <th>KATEGORI</th>
                                <th>JUDUL</th>
                                <th>PENULIS</th>
                                <th>PENERBIT</th>
                                <th>TAHUN TERBIT</th>
                                <th>KOTA TERBIT</th>
                                <th>TENTANG</th>
                                <th>DOKUMEN</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $ebook = bukaquery("select * from tm_ebook");
                            while ($row = fetch_array($ebook)) {
                                $no++;
                                ?>

                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_ebook']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=delete-ebook&id=' . $row['id_ebook'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data eBook</h4>
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
                                <td><?php echo $row['no_ebook']; ?></td>
                                <td><?php echo $row['kategori']; ?></td>
                                <td><?php echo $row['judul']; ?></td>
                                <td><?php echo $row['penulis']; ?></td>
                                <td><?php echo $row['penerbit']; ?></td>
                                <td><?php echo $row['tahun']; ?></td>
                                <td><?php echo $row['kota']; ?></td>
                                <td><?php echo $row['tentang'] ?></td>
                                <td><a href="files/ebook/<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td>
                                <td>
                                    <?php if ($superuser != '') { ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_ebook']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        <?php
        break;
    

    case "spo":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-book">  REFRENSI SPO PERPUSTAKAAN RSUD MATRAMAN </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">     
                <?php if ($hak_akses['upload_perpustakaan'] == '1' OR $superuser != '') { ?>
                    <button type="button" class="btn btn-info fa fa-upload" data-toggle="modal" data-target="#modal-add-sop">
                        Upload File SPO
                    </button>
                <?php } ?>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-sop">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-upload"> Upload File SPO</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=add-spo'); ?>" method="post" enctype="multipart/form-data"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>No SOP</label>
                                        <input type="text" class="form-control" name="no_spo" placeholder="No SPO" required>
                                        <label>Tentang</label>
                                        <input type="text" class="form-control" name="tentang" placeholder="Tentang" required>
                                        <label>Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3px" cols="3px"  ></textarea>
                                        <label>File</label>
                                        <input type="file" class="form-control" name="file"  required>
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
                                <th>NO SPO</th>
                                <th>TENTANG</th>
                                <th>DOKUMEN</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sop = bukaquery("select * from tm_spo");
                            while ($row = fetch_array($sop)) {
                                $no++;
                                ?>

                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_spo']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=delete-spo&id=' . $row['id_spo'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data SPO</h4>
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
                                <td><?php echo $row['no_spo']; ?></td>
                                <td><?php echo $row['tentang'] ?></td>
                                <td><a href="files/spo/<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td>
                                <td>
                                    <?php if ($superuser != '') { ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_spo']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        <?php
        break;

    case "kebijakan":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-book">  REFRENSI SK/KEBIJAKAN PERPUSTAKAAN RSUD MATRAMAN </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">    
                <?php if ($hak_akses['upload_perpustakaan'] == '1' OR $superuser != '') { ?>
                    <button type="button" class="btn btn-info fa fa-upload" data-toggle="modal" data-target="#modal-add-kebijakan">
                        Upolad File SK/KEBIJAKAN
                    </button>
                <?php } ?>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-kebijakan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-upload"> Upload File SK/Kebijakan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=add-sk'); ?>" method="post" enctype="multipart/form-data"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>No SK</label>
                                        <input type="text" class="form-control" name="no_sk" placeholder="No SK" required>
                                        <label>Tanggal SK</label>
                                        <input type="text" class="form-control" name="tanggal_sk" placeholder="m/d/Y" id='datepicker' required readonly>
                                        <label>Tentang</label>
                                        <input type="text" class="form-control" name="tentang" placeholder="Tentang" required >
                                        <label>File</label>
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
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NO SK/KEBIJAKAN</th>
                                <th>TAHUN</th>
                                <th>TENTANG</th>
                                <th>DOKUMEN</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sk = bukaquery("select * from tm_sk");
                            while ($row = fetch_array($sk)) {
                                $no++;
                                ?>

                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_sk']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=delete-sk&id=' . $row['id_sk'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data SK/KEBIJAKAN</h4>
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
                                <td><?php echo $row['no_sk']; ?></td>
                                <td><?php echo $row['tanggal_sk']; ?></td>
                                <td><?php echo $row['tentang'] ?></td>
                                <td><a href="files/sk/<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td><td>
                                    <?php if ($superuser != '') { ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_sk']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        <?php
        break;

    case "peraturan":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-book">  REFRENSI PERATURAN PERPUSTAKAAN RSUD MATRAMAN </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                <?php if ($hak_akses['upload_perpustakaan'] == '1' OR $superuser != '') { ?>
                    <button type="button" class="btn btn-info fa fa-upload" data-toggle="modal" data-target="#modal-add-peraturan">
                        Upoad File Peraturan
                    </button>
                <?php } ?>
                <br>
                <!--Modal Add SKP -->
                <div class="modal fade" id="modal-add-peraturan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title fa fa-upload"> Upload File Peraturan</h4>
                            </div>
                            <form role="form" action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=add-peraturan'); ?>" method="post" enctype="multipart/form-data"> 
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>No Peraturan</label>
                                        <input type="text" class="form-control" name="no_peraturan" placeholder="No Peraturan" required>
                                        <label>Tentang</label>
                                        <input type="text" class="form-control" name="tentang" placeholder="Tentang" required>
                                        <label class="control-label">Group</label>
                                        <?php echo enumDropdown("tm_peraturan", "jenis", "", "-Pilih Jenis-"); ?>
                                        <label>File</label>
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
                <!-- Tutup Modal Skp -->
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>NO PERATURAN</th>
                                <th>TENTANG</th>
                                <th>JENIS</th>
                                <th>DOKUMEN</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $peraturan = bukaquery("select * from tm_peraturan");
                            while ($row = fetch_array($peraturan)) {
                                $no++;
                                ?>

                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_peraturan']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=delete-peraturan&id=' . $row['id_peraturan'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data Peraturan</h4>
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
                                <td><?php echo $row['no_peraturan']; ?></td>
                                <td><?php echo $row['tentang'] ?></td>
                                <td><?php echo $row['jenis'] ?></td>
                                <td><a href="files/peraturan/<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td><td>
                                    <?php if ($superuser != '') { ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_peraturan']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        <?php
        break;

        case "sismadak":
        ?>
        <div class="box">
            <div class="box-header with-border">  
                <h3 class="box-title fa fa-book">FILE SISMADAK RSUD MATRAMAN 2019</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>                
            </div>
            <div class="box-body">  
                
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>JENIS</th>
                                <th>TENTANG</th>
                                <th>FILE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $sismadak = bukaquery("select * from tm_sismadak");
                            while ($row = fetch_array($sismadak)) {
                                $no++;
                                ?>

                                <!-- Edit Modal SKP -->
                            <div class="modal fade" id="modal-hapus-<?php echo $row['id_sismadak']; ?>" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo $aksi . paramEncrypt('module=perpustakaan&act=delete-sismadak&id=' . $row['id_sismadak'] . ''); ?>" method="POST" role="form">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Delete Data sismadak</h4>
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
                                <td><?php echo $row['jenis'] ?></td>
                                <td><?php echo $row['tentang'] ?></td>
                                <td><a href="files/sismadak/<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td><td>
                                    <?php if ($superuser != '') { ?>
                                        <span data-toggle="modal" data-target="#modal-hapus-<?php echo $row['id_sismadak']; ?>" title="Hapus" class="btn-xs btn-danger fa fa-trash"></span>
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
        <?php
        break;
}
?>
        
