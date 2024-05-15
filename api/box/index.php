<?php 
header("Content-Type: text/plain;charset=utf-8"); // Ensure content is utf-8 and text
$defaults = [
	'p' => 'epg',
	'v' => '0',
	'ch' => '',
	'data' => '',
	'jm' => '',
	'vip' => ''
];
$urls = [
 "live"=> [
    "0"=> "https://hfr1107.github.io/up/tv/tv.txt",
    "1"=> "https://hfr1107.github.io/up/tv/tv1.txt",
    "2"=> "https://hfr1107.github.io/up/tv/tv2.txt"
  ],
  "epg"=> [
    "0"=> "https://diyp.112114.xyz",
    "1"=> "https://epg.112114.eu.org",
    "2"=> "https://epg.112114.eu.org"
  ],
  "tv"=> [
    "1"=> "http://xn--sss604efuw.top/tv/",
    "2"=> "http://tvbox.xn--4kq62z5rby2qupq9ub.xyz/",
    "3"=> "https://jihulab.com/okcaptain/kko/-/raw/main/ok.txt",
    "4"=> "http://xn--z7x900a.live/",
    "5"=> "http://xn--ihqu10cn4c.xn--v4q818bf34b.top/",
    "18"=> "https://gaotianliuyun.github.io/gao/9918.json"
  ],
  "pry" => [
    "0" => "./jm.php?jm=", 
    "1" => "https://000.hfr1107.top/api/box/jm.php?jm=", 	
    "2" => "https://hfr1107.serv00.net/api/box/jm.php?jm=", 	
    "3" => "https://hfr1107.000webhostapp.com/api/box/jm.php?jm=", 	
    "4" => "https://hfr1107.top/api/box/jm.php?jm=",	
    "5" => "http://lige.unaux.com/?url=",		 
 ],
  "dc"=> [
    "0"=> "https://hfr1107.github.io/up/tv/dc.json",
    "1"=> "https://hfr1107.github.io/up/tv/dc1.json"
  ]
];
$params = array_merge($defaults, $_GET);

// Removed extract to improve security and avoid variable pollution
$p = $params['p'];
$v = $params['v'];
$ch = $params['ch'];
$data = $params['data'];
$jm = $params['jm'];
$vip = $params['vip'];


function fetchContent($url, $p, &$attemptedUrls = []) {
    if (in_array($url, $attemptedUrls)) {
        return false; // Prevent infinite loops by checking already attempted URLs
    }

    $attemptedUrls[] = $url; // Add current URL to the list of attempted URLs
    $content = @file_get_contents($url);
    if ($content === FALSE || ($p === "tv" && strpos($content, "sites") === false)) {
        if ($p === "tv") {
            // Assuming $urls is now passed as a parameter or defined globally
            global $urls; // If $urls is a global variable
            // or add $urls as a function parameter and remove this line
            foreach ($urls['pry'] as $key => $pryurl) {
                $newUrl = $pryurl . urlencode($url);
                if (!in_array($newUrl, $attemptedUrls)) { // Check if the new URL has already been attempted
                    $cord = @file_get_contents($newUrl);
                    if ($cord && strpos($cord, "sites") !== false) {
                        return $cord;
                    }
                }
            }
            // Recursively try the alternative URL if not found
            foreach ($urls['pry'] as $key => $pryurl) {
                $newUrl = $pryurl . urlencode($url);
                $recursiveContent = fetchContent($newUrl, $p, $attemptedUrls);
                if ($recursiveContent !== false) {
                    return $recursiveContent; // Return the content if found recursively
                }
            }
        }
        return false; // Return false if content not found or $p is not "tv"
    }
    return $content; // Return the content if it was successfully fetched from the original URL
}
function redirectWithIncrementedV($p, $v) {
	$query = http_build_query(['p' => $p, 'v' => $v + 1]); // Use http_build_query for cleaner URL building
	$newUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?{$query}";
	header("Location: " . $newUrl);
	exit();
}
function convertDomainToPunycode($url) {
    $parsedUrl = parse_url($url);
    $scheme = $parsedUrl['scheme'];
    $host = $parsedUrl['host'];
    $path = $parsedUrl['path'] ?? '';
    $query = $parsedUrl['query'] ?? '';
    $fragment = $parsedUrl['fragment'] ?? '';

    // 转换域名为Punycode
    $punycodeHost = idn_to_ascii($host);

    // 重新组合URL
    $punycodeUrl = $scheme . '://' . $punycodeHost;
    if (!empty($path)) {
        $punycodeUrl .= $path;
    }
    if (!empty($query)) {
        $punycodeUrl .= '?' . $query;
    }
    if (!empty($fragment)) {
        $punycodeUrl .= '#' . $fragment;
    }

    return $punycodeUrl;
}

if (isset($urls[$p]) && isset($urls[$p][$v])) {
	// 处理$jm变量，因为它不能在JSON中表示
	if ($jm !== '') {
		$urls['tv']['0'] = $_GET['jm'];
		$p = 'tv'; // 根据原始脚本中的逻辑，如果$_GET['jm']不为空，则将$p设置为'tv'
	}
	if (!empty($_GET['vip'])) {
		$p = 'tv';
		$v = $vip;
	}
	$baseUrl = $urls[$p][$v];
	$baseUrl = convertDomainToPunycode($baseUrl);
	$urlParams = $p === 'epg' ? "?ch={$ch}&data={$data}" : ''; // Fixed variable name mismatch
	$url = $baseUrl . $urlParams;
	$content = fetchContent($url, $p);

	if ($content !== false || ($p === "epg" && strpos($content, "channel_name") !== false)) { // Fixed strpos usage
		$drurl = dirname($url) . '/';
		$content = str_replace('./', $drurl, $content);		
		$vip_mode = !empty($_GET['vip']);
if ($vip_mode) {
    // 定义要替换的模式和替换内容
    $patterns = [
        '/:\s*"\s*[^"]*(?:token[^.]*)+\.(?:txt|json)/',
        '/"\s*lives\s*"\s*:[^\[]*?\[(?:[\s\S]*?\[[\s\S]*?\]|[\s\S]*?)\]/'
    ];
    $replacements = [
        ':"https://hfr1107.github.io/up/tv/token.txt',
        '"lives": [{"name": "直播","url": "https://hfr1107.github.io/up/tv/tv.txt","epg": "https://hfr1107.top/api/box/?ch={name}&date={date}"}]'
    ];

    // 使用循环进行多次替换操作
    foreach ($patterns as $index => $pattern) {
        $content = preg_replace($pattern, $replacements[$index], $content);
    }
}
echo $content;
	} else {
		redirectWithIncrementedV($p, $v);
	}
} else {
	echo '未知参数';
}
?>
