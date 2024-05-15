<?php
//Created by hfr1107.
//User: hfr1107
//qq:51113874
//Date: 2024/04/21
//不支持域名解析的php服务器可采用本代码，调用支持域名解析的https://hfr1107.top/api/box/reurl.php文件获取真实url
//使用方法为：/dcjc.php?url1=https://hfr1107.github.io/up/tv/dc.json&url2=https://hfr1107.github.io/up/tv/dc1.json
header('Content-Type: application/json; charset=utf-8');
$urls = [];
// 处理 GET 请求中的 URL 参数
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
if ($request_method === 'GET' && count($_GET) > 0) {
    foreach ($_GET as $key => $value) {
        if (preg_match('/^url\d+$/', $key)) {
            $urls[] = $value;
        }
    }
} 
// 处理 POST 请求中的 textbox_urls 参数
else if (isset($_POST['textbox_urls'])) {
    $textboxUrls = explode("\n", $_POST['textbox_urls']);
    foreach ($textboxUrls as $url) {
        $url = trim($url);
        if (!empty($url)) {
            $urls[] = $url;
        }
    }
}

$mergedUrls = [];
$results = ['success' => [], 'failed' => []];
$checkedUrls = [];

// 定义 checkUrl 函数，用于检查 URL 并更新结果数组
function checkUrl($url, $name, &$results, &$checkedUrls, &$mergedUrls, $retries = 2, $retryUrls = []) {
    if (in_array($url, $checkedUrls)) return;
    $checkedUrls[] = $url;
    $mergedUrls[] = ['name' => $name, 'url' => $url];
    
    // 初始化 cURL 并设置选项
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'okhttp/4.12.0'); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 注意：在生产环境中应启用SSL验证
    
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        if ($retries > 0 && !empty($retryUrls)) {
            foreach ($retryUrls as $retryUrl) {
                $newUrl = file_get_contents($retryUrl . '?reurl=' . urlencode($url));
                if ($newUrl) {
                    // 递归调用，传递原始的$retryUrls数组，避免不必要的数组创建
                    return checkUrl($newUrl, $name, $results, $checkedUrls, $mergedUrls, $retries - 1, $retryUrls);
                }
            }
            // 如果所有备用URL都尝试过且失败，则记录错误或进行其他处理
            // 例如：可以将URL添加到$results['failed']中，并标记为“无法访问”
        }
        // 如果没有备用URL或重试次数已用尽，则处理错误（例如记录日志或返回错误信息）
    } else {
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    }
    curl_close($curl);
    
    // 根据 HTTP 响应码和响应内容更新结果数组
    if ($httpCode == 200 && (stripos($response, 'sites') !== false || stripos($response, '**') !== false)) {
        $results['success'][] = ['name' => $name, 'url' => $url];
    } else {
        $results['failed'][] = ['name' => $name, 'url' => $url];
    }
}
function removeSpacesAndNewlines($str) {
    // 使用正则表达式匹配{}中的内容，并使用preg_replace_callback进行替换处理
    return preg_replace_callback(
        '/\{(.*?)\}/s', 
        function($matches) {
            // 去除匹配到的{}内部字符串中的空格和换行符
            return '{' . str_replace(array(' ', "\n", "\r"), '', $matches[1]) . '}';
        },
        $str
    );
}
//echo "//开始处理\n";
// 遍历 URL 数组，对每个 URL 进行处理
foreach ($urls as $url) {
//    echo "//多仓接口获取: $url\n";
    $json = @file_get_contents($url); 
    if ($json) {
        $data = json_decode($json, true);
        if (isset($data['urls'])) {
            foreach ($data['urls'] as $item) {
                checkUrl($item['url'], $item['name'], $results, $checkedUrls, $mergedUrls,2, [
        'https://hfr1107.top/api/box/reurl.php',
        'http://tv.hfr.free.nf/reurl.php'
    ]); 
            }
        }
    }
}
//echo "//处理完成，输出结果\n";

// 去除重复的成功 URL，并更新结果数组
$uniqueSuccessUrls = [];
$uniqueUrlSet = [];
foreach ($results['success'] as $result) {
    if (!in_array($result['url'], $uniqueUrlSet)) {
        $uniqueUrlSet[] = $result['url'];
        $uniqueSuccessUrls[] = $result;
    }
}
$results['success'] = $uniqueSuccessUrls;

// 将结果数组转换为 JSON 格式并输出
$json = json_encode(['urls' => array_merge($results['success'], array_map(function($failed) {
    return ['//' .'name' => $failed['name'], 'url' => $failed['url']];
}, $results['failed']))], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$json = removeSpacesAndNewlines($json);
$json = str_replace('\/', '/', $json);
$json = str_replace('{"//name', '//{"name', $json);
echo $json;
?>
