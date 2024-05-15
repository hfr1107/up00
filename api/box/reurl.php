<?php
//Created by hfr1107.
//User: hfr1107
//qq:51113874
//Date: 2024/04/21
//不支持域名解析的php服务器可调用本接口，获取真实url
//调用方法：本代码存放地址/reurl.php?reurl=
//@file_get_contents('本代码存放地址/reurl.php?reurl=' . $jm); //$jm为需要解析的域名
$defaults = [
 'reurl' => '',
];

function getFileDataFromUrl($url, &$finalUrl) {
 $ch = curl_init($url);
 if (!$ch) {
 return false;
 }
 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 注意在生产环境中应启用
 curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/4.12.0');
 
 $data = curl_exec($ch);
 if ($data === false) {
 curl_close($ch);
 return false;
 }
 
 $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // 获取最终请求的URL
 $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
 curl_close($ch);
 
 if ($httpCode != 200) {
 return false;
 }
 
 return $data;
}

// 示例用法
$originalUrl =  $_GET['reurl'] ?? $defaults['reurl']; // 使用null合并运算符提供默认值
$finalUrl = '';
$data = getFileDataFromUrl($originalUrl, $finalUrl);
if ($data !== false) {
 echo $finalUrl;
} else {
 echo "//无法获取数据\n";
}
?>