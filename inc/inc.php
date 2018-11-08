<?php
/**
 * php抓取bing每日图片并保存到服务器
 * 作者：陈鑫威blog (iav6.cn)
 * 日期：2017/12/11
 */


function getInfo($date){
header("Content-Type: text/html; charset=utf-8");
$curl = curl_init();
$text=file_get_contents('http://cn.bing.com/cnhp/life?currentDate='.$date);
preg_match_all('/<div class="hplatt">(.*)<\/div>/U',$text,$title);
preg_match_all('/<div class="hplats">(.*)<\/div>/U',$text,$subtitle);
preg_match_all('/<div id="hplaSnippet">(.*)<\/div>/U',$text,$con);
preg_match_all('/<div class="hplaPvd">(.*)<\/div>/U',$text,$author);
$info['title'] = $title[1][0];
$info['subtitle'] = $subtitle[1][0];
$info['con'] = $con[1][0];
$info['author'] = $author[1][0];
return $info;
}

function save($imgurl,$filename){
$path = 'images'; 
$spath = $path.'/simg'; 
$filename = $filename . '.jpg';  
if (!file_exists($spath.'/'. $filename))    //如果文件不存在，则说明今天还没有进行缓存
{
    if(!file_exists($path)) //如果目录不存在
    {
        mkdir($path, 0777); //创建缓存目录
    }
    $img = grabImage($imgurl, $path.'/'.$filename); //读取并保存图片
    $simg = $spath.'/'. $filename; //缩略图
    create_thumbnail($img, $simg, $strScale = 0);//生成缩略图
}
  return $spath.'/'. $filename;
}
/**
 * 远程抓取图片并保存
 * @param $url 图片url
 * @param $filename 保存名称和路径
 */
function grabImage($url, $filename = "")
{
    if($url == "") return false; //如果$url地址为空，直接退出
    if ($filename == "") //如果没有指定新的文件名
    {
        $ext = strrchr($url, ".");  //得到$url的图片格式
        $filename = date("Ymd") . $ext;  //用天月面时分秒来命名新的文件名
    }
    ob_start();         //打开输出
    readfile($url);     //输出图片文件
    $img = ob_get_contents();   //得到浏览器输出
    ob_end_clean();             //清除输出并关闭
    $size = strlen($img);       //得到图片大小
    $fp2 = @fopen($filename, "a");
    fwrite($fp2, $img);         //向当前目录写入图片文件，并重新命名
    fclose($fp2);
    return $filename;           //返回新的文件名
}


function create_thumbnail($strURL1, $strURL2, $tstrScale = 0)
{
  $tstrURL1 = $strURL1;
  $tstrURL2 = $strURL2;
  $tstrWidth = '620';//'1800';
  $tstrHeight = '350';//'120000';
  if (!empty($tstrURL1) && !empty($tstrURL2) && $tstrWidth != 0 && $tstrHeight != 0)
  {
    $tImageType = substr($tstrURL1,-3);
    if ($tImageType == 'jpg' || $tImageType == 'jpeg') $timg = ImageCreateFromJpeg($tstrURL1);
    elseif ($tImageType == 'gif') $timg = ImageCreateFromGif($tstrURL1);
    elseif ($tImageType == 'png') $timg = ImageCreateFromPng($tstrURL1);
    if ($timg && function_exists('imagecopyresampled'))
    {
      $tImageSize = getImageSize($tstrURL1);
      $tImageWidth = $tImageSize[0];
      $tImageHeight = $tImageSize[1];
      if ($tstrWidth == -1) $tstrWidth = $tImageWidth;
      if ($tstrHeight == -1) $tstrHeight = $tImageHeight;
      if ($tstrScale == 1)
      {
        if ($tImageWidth <= $tstrWidth && $tImageHeight <= $tstrHeight)
        {
          $tstrWidth = $tImageWidth;
          $tstrHeight = $tImageHeight;
        }
        else
        {
          $tScNum1 = $tImageWidth / $tstrWidth;
          $tScNum2 = $tImageHeight / $tstrHeight;
          if ($tImageWidth <= $tstrWidth) $tstrWidth = $tImageWidth / $tScNum2;
          elseif ($tImageHeight <= $tstrHeight) $tstrHeight = $tImageHeight / $tScNum1;
          else
          {
            if ($tScNum1 >= $tScNum2) $tstrHeight = $tImageHeight / $tScNum1;
            else $tstrWidth = $tImageWidth / $tScNum2;
          }
        }
      }
      $timgs = imagecreatetruecolor($tstrWidth, $tstrHeight);
      imagecopyresampled($timgs, $timg, 0, 0, 0, 0, $tstrWidth, $tstrHeight, $tImageWidth, $tImageHeight);
      if ($tImageType == 'jpg' || $tImageType == 'jpeg') imagejpeg ($timgs, $strURL2, 60);//60为压缩质量.默认75,数值0-100imagejpeg() 独有参数
      elseif ($tImageType == 'gif') imagegif ($timgs, $strURL2);
      elseif ($tImageType == 'png') imagepng ($timgs, $strURL2);
      imagedestroy($timg);
      //return 1;
    }
  }
}

   
function savejson($url,$str){
fopen($url,'w');
file_put_contents($url,$str);
}
    
    
function getimg($idx,$n){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,'http://www.bing.com/HPImageArchive.aspx?format=js&cc=cn&idx='.$idx.'&n='.$n);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
$output = curl_exec($ch);
curl_close($ch);
$content = json_decode($output);
$content_arr = objtoarr($content);
return $content_arr;
}

function objtoarr($obj){
    $ret = array();
    foreach($obj as $key =>$value){
        if(gettype($value) == 'array' || gettype($value) == 'object'){
        $ret[$key] = objtoarr($value);
        }else{
        $ret[$key] = $value;
        }
    }
    return $ret;
}
