<?php
$is_browser = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false;
 
if ($is_browser) {
    $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.html";
    header("Location: " . $defaultUrl); // 否则进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
} else {
	$apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api/box/?p=live&v=0";
    $content = @file_get_contents($apiUrl);
$pattern = '/央视\|IPV6(.*?)(央视\|IPV4)/s'; // 添加s标志以支持跨行匹配，使用(.*?)进行非贪婪匹配，并添加捕获组

// 使用preg_replace_callback函数进行替换
$content = preg_replace_callback($pattern, function($matches) {
    // 返回要保留的部分，即"央视|IPV4"
    return $matches[2]; // 使用$matches[2]来引用第二个捕获组
}, $content);

    if ($content === false) {
        // 处理错误，例如记录日志或显示错误消息
        echo "无法获取内容";
    } else {
        echo $content;
    }
}
?>
