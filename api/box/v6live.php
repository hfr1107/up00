<?php
$is_browser = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false;
if ($is_browser) {
    $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.html";
    header("Location: " . $defaultUrl); // 否则进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
} else {
	$apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api/box/?p=live&v=0";
    $content = @file_get_contents($apiUrl);
    if ($content === false) {
        // 处理错误，例如记录日志或显示错误消息
        echo "无法获取内容";
    } else {
        echo $content;
    }
}
?>
