<?php
require('inc/inc.php');
header("Content-type: text/html; charset=utf-8");
$pre='前一日';
$next='后一日';
$tnow = date('Ymd');
$tname = @$_GET['img'] ? @$_GET['img'] : date('Ymd');
$tdate = date($tname);
$prename = date('Ymd',strtotime($tdate.'-1 day'));
$nextname = date('Ymd',strtotime($tdate.'+1 day'));
if(!file_exists('images/simg/'.$tnow.'.jpg')) {
  $tnow = date('Ymd',strtotime($tnow.'-1 day'));
}
if(!file_exists('images/simg/'.$prename.'.jpg')){
 $pre='没有了';
 $prename = $tnow;
}
if($nextname > $tnow) {
  $next='没有了';
  $nextname = $tnow;
}
$timg = 'images/'.$tname.'.jpg';
if(!file_exists('json/'.$tname.'.json'))  exit('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><script>alert("没有数据啦！");window.location.href="./";</script>');
$json_file = fopen('json/'.$tname.'.json','r');
$tfile = json_decode(fgets($json_file), true);
fclose($json_file);
$tadd = trim(getNeedBetween($tfile['copyright'],'，','('));
$title = !empty($tfile['info']['title']) ? $tfile['info']['title'] : $tadd;
if(empty($timg) || $timg == '') $copy = "<p style='display:block;width:96%;margin:0 auto;max-width:640px;'>无图片</p>";
else $copy = "<img src='".$timg."' style='display:block;width:96%;margin:0 auto;max-width:1920px;' alt='".$tfile['copyright']."'><p>".$tfile['copyright']."</p><p>".$tfile['info']['con']."</p>";
$temp = file_get_contents('template/detail.html');
$str = str_replace('{$copy}',$copy,$temp);
$str = str_replace('{$title}',$title,$str);
$str = str_replace('{$con}',$tfile['info']['con'],$str);
$str = str_replace('{$prename}',$prename,$str);
$str = str_replace('{$pre}',$pre,$str);
$str = str_replace('{$tnow}',$tnow,$str);
$str = str_replace('{$nextname}',$nextname,$str);
$str = str_replace('{$next}',$next,$str);
echo $str;
    ?>