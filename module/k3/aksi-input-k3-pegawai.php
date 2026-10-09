<?php
require_once('../../conf/conf.php');
require_once('../../libs/aes-encrypt/function.php');
$URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$url = decode($URI);
$page = isset($url['module']) ? $url['module'] : null; 
$action = isset($url['act']) ? $url['act'] : null;
$id = isset($url['id']) ? $url['id'] : null;


include('../../conf.php');
function antiinjection($data){
      $filter_sql = stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES)));
      return $filter_sql;
      }
//menerima nilai dari kiriman form input-barang 
        $id_user = antiinjection($_POST['id_user']);
        $tgl_kegiatan = antiinjection($_POST['tgl_kegiatan']);
        $bb = antiinjection($_POST['bb']);
        $tb = antiinjection($_POST['tb']);
        $id_kegiatan = antiinjection($_POST['id_kegiatan']);

        $imt = $tb*$tb;
        $himt = $bb / $imt;

        if ($himt <= 17.0) {
          $h_imt = "Sangat Kurus";
        } else if ($himt >= 17.0 && $himt <= 18.5) {
             $h_imt = "Kurus";
        } else if ($himt >= 18.5 && $himt <= 24.9) {
             $h_imt = "Normal";
        } else if ($himt >= 25.0 && $himt <= 29.9) {
             $h_imt = "Gemuk";
        } else if ($himt >= 30.0 && $himt <= 34.9) {
             $h_imt = "Obesitas Level 1";
        } else if ($himt >= 35.0 && $himt <= 39.9) {
             $h_imt = "Obesitas Level 2";
        } else if ($himt >= 40.0) {
             $h_imt = "Obesitas Level 3";
        }


$sql="insert into tt_imt (id_kegiatan,id_user,tgl_kegiatan,bb,tb,imt,h_imt) values
    ('$id_kegiatan','$id_user','$tgl_kegiatan','$bb','$tb','$himt','$h_imt')";

//Mengeksekusi/menjalankan query diatas 
  $hasil=mysqli_query($kon,$sql);

//Kondisi apakah berhasil atau tidak
  if ($hasil) {
  header('location:../../page-view?' . paramEncrypt('module=k3&act=list-data-k3'));
  }
else {
  echo "Gagal insert data";
  exit;
}  

?>