<?php
require('inc/inc.php');
header("Content-type: text/html; charset=utf-8");
$pre='前一日';
$next='后一日';
$tnow = date('Ymd');
$tname = $_GET['img'];
$tdate = date($tname);
$prename = date('Ymd',strtotime($tdate.'-1 day'));
$nextname = date('Ymd',strtotime($tdate.'+1 day'));
if(!file_exists('images/simg/'.$prename.'.jpg')){
 $pre='没有了';
 $prename = $tnow;
}
if($nextname > $tnow) {
  $next='没有了';
  $nextname = $tnow;
}
$timg = '../images/'.$tname.'.jpg';
$json_file = fopen('json/'.$tname.'.json','r');
$tfile = json_decode(fgets($json_file), true);
if(empty($timg) || $timg == '') $con = "<p style='display:block;width:96%;margin:0 auto;max-width:640px;'>无图片</p>";
else $con = "<img src='".$timg."' style='display:block;width:96%;margin:0 auto;max-width:1920px;' ><p>".$tfile['enddate']."</p><p>".$tfile['copyright']."</p>";
    ?>
<html>
  <head>
    <title><?php echo $tfile['info']['title']; ?> - Bing壁纸下载</title>
  </head>
  <body>
    <?php
echo "<div style='width:100%;margin:25px auto;text-align:center;'><h1>".$tfile['info']['title']."</h1></div>";
echo "<p style='width:100%;margin:25px auto;text-align:center;'><a style='display:inline-block;text-decoration: none;padding:2px 5px;border:1px solid #00f;' href='http://bing.menglei.info/img.php?img=".$prename."'>".$pre."</a>&nbsp;&nbsp;<a style='display:inline-block;text-decoration: none;padding:2px 5px;border:1px solid #00f;' href='http://bing.menglei.info/img.php?img=".$tnow."'>今天</a>&nbsp;&nbsp;<a style='display:inline-block;text-decoration: none;padding:2px 5px;border:1px solid #00f;' href='http://bing.menglei.info/img.php?img=".$nextname."'>".$next."</a></p>";
echo $con;
echo "<p>".$tfile['info']['title']."</p>";
echo "<p>".$tfile['info']['subtitle']."</p>";
echo "<p>".$tfile['info']['con']."</p>";
echo "<p>".$tfile['info']['author']."</p>";
echo "<p style='width:100%;margin:0 auto;text-align:center;'><a style='display:block;text-decoration: none;' href='http://bing.menglei.info/'>bing壁纸</a></p>";
?>
</body>
  </html>