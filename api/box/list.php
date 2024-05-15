<?php
$is_browser = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false;

if ($is_browser) {
    $defaultUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/index.html";
    header("Location: " . $defaultUrl); // 进行页面跳转
    exit(); // 确保重定向并停止进一步的代码执行
} else {
	//$tslisturl= 'https://d.kstore.space/download/3403/up/appmarket/index.php';// 修改
	//$search_string = '路尧';
	//$content = @file_get_contents($tslisturl);
	//if ($content !== false && strpos($content, $search_string) !== false) {
	// Replace all occurrences of the base URL with the merged URL prefix
	//	echo $content;
	//} else {
    $urls = [
        'https://hub.gitmirror.com/',  
        'https://slink.ltd/',
        'https://gh.con.sh/',
        'https://ghproxy.cc/',
        'https://gb.hfr1107.workers.dev/',  
    ];

    $base_url = 'https://raw.githubusercontent.com/hfr1107/app/main/appmarket/list.php';
    $search_string = '路尧';
    $fastest_url = ''; // 添加一个变量来记录最快的URL
    $fastest_time = PHP_INT_MAX; // 初始化最快时间为最大整数

    foreach ($urls as $url) {
        $start_time = microtime(true); // 记录开始时间
        $merged_url = $url . trim($base_url, '/'); // Trim to avoid double slashes in URL
        $content = @file_get_contents($merged_url);
        $end_time = microtime(true); // 记录结束时间

        $time_taken = $end_time - $start_time; // 计算获取数据所花费的时间

        if ($content !== false && strpos($content, $search_string) !== false && $time_taken < $fastest_time) {
            // 如果当前URL有效，且包含搜索字符串，并且时间更短，则更新最快URL和最快时间
            $fastest_url = $merged_url;
            $fastest_time = $time_taken;
        }
    }

    if ($fastest_url !== '') {
        // 如果找到了最快的URL，则输出对应的内容
        $content = @file_get_contents($fastest_url);
        // Replace all occurrences of the base URL with the merged URL prefix
        $modified_content = str_replace(
            'https://raw.githubusercontent.com',
            $fastest_url,
            $content
        );
        echo "$modified_content\n\n";
    } else {
        // 如果没有找到有效的URL，则不输出任何内容
        echo "No valid URL found.\n\n";
    }
//}
//}
}
?>