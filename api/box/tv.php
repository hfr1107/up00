<?php
$is_browser = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false;
 
if ($is_browser) {
    $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.html";
    header("Location: " . $defaultUrl); // 否则进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
} else {
    $apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api/box/?vip=1";
    header("Location: " . $apiUrl); // 否则进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
}
?>