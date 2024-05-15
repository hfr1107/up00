<?php
$is_browser = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false;
 
if ($is_browser) {
    $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.html";
    header("Location: " . $defaultUrl); // 否则进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
} else {
// 假设URL1是从浏览器输入的，我们可以通过$_SERVER['REQUEST_URI']获取
$url1 = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// 解析URL1以获取查询参数
$url1_parts = parse_url($url1);
$query_params = [];
parse_str($url1_parts['query'], $query_params);

// 假设URL2是已知的，并且我们想要将URL1的参数传递给它
	$url2_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api/box/";
//$url2_base = 'https://hfr1107.top/api/box/';
$url2 = $url2_base . '?' . http_build_query($query_params);

// 使用cURL或file_get_contents获取URL2的数据
$ch = curl_init($url2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);

// 输出从URL2获取的数据
echo $data;
}
?>