<html>
  <head>
    <title>Bing每日图片</title>
  </head>
  <body>
<?php
header("Content-type: text/html; charset=utf-8");
/*
https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&cc=cn
&ensearch=0
n，必要参数。这是输出信息的数量。比如n=1，即为1条，以此类推，至多输出8条。
format，非必要。返回结果的格式，不存在或者等于xml时，输出为xml格式，等于js时，输出json格式
idx，非必要。不存在或者等于0时，输出当天的图片，-1为已经预备用于明天显示的信息，1则为昨天的图片，以此类推，idx最多获取到前16天的图片信息
cc,地域参数,中国cn
&mkt=zh-CN
*/
require('inc/inc.php');
$url_pre = 'https://cn.bing.com';
$idx = $_GET['idx'];
if(empty($idx)|| $idx == '' || $idx >=8 ||$idx <= -1 ) $idx=0;
$n = $_GET['n'];
if(empty($n)|| $n == '' || $n >8 ||$n < 1 ) $n=8;
$pre = $idx + 1;
$next = $idx - 1;
if($next == -1) $next = 7;
$images = getimg($idx,$n);
echo "<div style='width:100%;margin:0 auto;text-align:center;'><a href='/?idx=".$pre."&n=".$n."'>前一日</a>&nbsp;&nbsp;<a href='/?idx=".$next."&n=".$n."'>后一日</a>";
?>
<ul style="list-style:none;margin:0;padding:0;">
<?php
for($i=0;$i<$n;$i++){
  $tname =$images['images'][$i]['enddate'];
  $images['images'][$i]['info'] = getInfo($tname) ;
  $tjson = json_encode($images['images'][$i]);
  if (!file_exists('json/'.$tname.'.json')) {
    fopen('json/'.$tname.'.json','w');
    file_put_contents('json/'.$tname.'.json',$tjson);
  }
  $json_file = fopen('json/'.$tname.'.json','r');
  $tfile = json_decode(fgets($json_file), true);
  $timg = $url_pre.$tfile['url'];
  $tname = $tfile['enddate'];
  if (!file_exists('/simg/simg/'.$tname.'.jpg')) $nimg = save($timg,$tname);//如果不存在,则保存
  else $nimg = '/simg/'.$tname.'.jpg';
  $hdimg = '/images/'.$tname.'.jpg';
  $con = "<li style='display: inline-block;margin:5px;'><a href='img.php?img=".$tname."' target='_blank'><img src='".$nimg."' style='width:96%;margin:0 2%;max-width:640px;' ></a><p>".$tfile['enddate']."</p><p>".$tfile['copyright']."</p></li>";
  echo $con;
}
?>
</ul>
<?php

echo "<br/><a href='/?idx=".$pre."&n=".$n."'>前一日</a>&nbsp;&nbsp;<a href='/?idx=".$next."&n=".$n."'>后一日</a></div>";


 

?>
</body>
  </html>