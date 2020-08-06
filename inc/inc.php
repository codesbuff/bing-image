<?php
function getNeedBetween($kw,$mark1,$mark2){
$st =stripos($kw,$mark1);
$ed =stripos($kw,$mark2);
if(($st==false||$ed==false)||$st>=$ed) return substr($kw,0,stripos($kw,$mark2));
$kw=substr($kw,($st+1+2),($ed-$st-1-2));
return $kw;
}

function getInfo($date){
header("Content-Type: text/html; charset=utf-8");
$curl = curl_init();
$text=file_get_contents('http://cn.bing.com/cnhp/life?currentDate='.$date);
preg_match_all('/<a class="hplaDMLink" target="_blank" href="(.*)" h="(.*)">(.*)<\/a>/U',$text,$url);
preg_match_all('/<span class="hplaAttr">(.*)<\/span>/U',$text,$attr);
preg_match_all('/<div class="hplatt">(.*)<\/div>/U',$text,$title);
preg_match_all('/<div class="hplats">(.*)<\/div>/U',$text,$subtitle);
preg_match_all('/<div id="hplaSnippet">(.*)<\/div>/U',$text,$con);
preg_match_all('/<div class="hplaPvd">(.*)<\/div>/U',$text,$author);
$turl = @htmlspecialchars_decode($url[1][0]);//URL字符html转换
$turl = substr(strrchr($turl,'?'), 1);//截取URL参数
parse_str($turl,$arry_url);//URL参数转换成数组
$info['url'] = $arry_url;
$info['attr'] = $attr[1][0];
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
$img = $path.'/'. $filename; //原图
$simg = $spath.'/'. $filename; //缩略图
if (!file_exists($simg))    //如果缩略图不存在，则说明今天还没有进行缓存
{
    if(!file_exists($path)) //如果目录不存在
    {
        mkdir($path, 0777); //创建缓存目录
    }
    if (!file_exists($img)) $img = grabImage($imgurl, $img); //读取并保存图片
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
  $tstrWidth = '320';//'1800';
  $tstrHeight = '180';//'120000';
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


function getDayImg($idx){
//bing数据次序从-1开始,idx最多获取到前16天.idx=-1&n=8 和 idx=7&n=8 分两次可获取全部
if($idx<7) $idx = -1;
else $idx=7;
$url = 'http://www.bing.com/HPImageArchive.aspx?format=js&cc=cn&pid=hp&og=1&idx='.$idx.'&n=8';
     $curl = curl_init(); // 启动一个CURL会话
      //以下三行代码解决https图片访问受限问题
     $dir = pathinfo($url);//以数组的形式返回图片路径的信息
     $host = $dir['dirname'];//图片路径
     $ref = $host.'/';
      if(strstr($ref, 'bing'))  $ref = 'http://cn.bing.com/';
      curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址    
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
      curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
      if($ref){
        curl_setopt($curl, CURLOPT_REFERER, $ref);//带来的Referer
      }else{
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
      }
      curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
      curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
      curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
      $output = curl_exec($curl);
      if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);
      }
      curl_close($curl);
      if($output === FALSE ){
        return false;
      }else{
      $content = json_decode($output);
      $content_arr = objtoarr($content);
        for($i=0;$i<7;$i++){
          $tarry = $content_arr['images'][$i];
          $name = $tarry['enddate'];
          $tarry['info'] = getInfo($name) ;
          $tjson = json_encode($tarry);
          if(!file_exists('json/'.$name.'.json')){
            savejson('json/'.$name.'.json',$tjson);
          }
        }
      }
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