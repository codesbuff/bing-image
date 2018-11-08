<html>
  <head>
    <title>Bing每日图片</title>
  </head>
  <body>
<?php
require('inc/inc.php');
header("Content-type: text/html; charset=utf-8");
$tname = $_GET['img'];
$timg = '../images/'.$tname.'.jpg';
$json_file = fopen('json/'.$tname.'.json','r');
$tfile = json_decode(fgets($json_file), true);
if(empty($timg) || $timg == '') $con = "<p style='display:block;width:96%;margin:0 auto;max-width:640px;'>无图片</p>";
else $con = "<img src='".$timg."' style='display:block;width:96%;margin:0 auto;max-width:1920px;' ><p>".$tfile['enddate']."</p><p>".$tfile['copyright']."</p>";
echo $con;
echo "<p>".$tfile['info']['title']."</p>";
echo "<p>".$tfile['info']['subtitle']."</p>";
echo "<p>".$tfile['info']['con']."</p>";
echo "<p>".$tfile['info']['author']."</p>";
echo "<a href='http://www.wdja.net/'>bing壁纸</a>";
?>
</body>
  </html>