<?php

header("Content-Type: text/plain;charset=utf-8"); // 确保内容是 utf-8 文本

<<<<<<< HEAD
function fetchUrl($url) {
=======
function fetchUrl($url, $retries = 2, $retryUrls = []) {
>>>>>>> bdf410164b5d5b6778d2b06403a5fb2112871de5
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // 注释说明生产环境应启用SSL验证
<<<<<<< HEAD
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 暂时禁用SSL验证，生产环境请设置为true并配置CA证书
    curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/4.12.0');
    $response = curl_exec($ch);
    
=======
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 暂时禁用SSL验证，生产环境请设置为true并配置CA证书
    curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/4.12.0');
    $response = curl_exec($ch);
    
    if (curl_errno($ch) && $retries > 0) {
        foreach ($retryUrls as $retryUrl) {
            $newUrl = @file_get_contents($retryUrl . '?reurl=' . urlencode($url));
            if ($newUrl) {
                return fetchUrl($newUrl, $retries - 1, []); // 递归调用，重试次数减1，并确保不再使用其他备用URL
            } else {
                throw new Exception("请完善有效地代理地址" . $retryUrl);
            }
        }
        throw new Exception("域名解析出错啦！");
    }
    
>>>>>>> bdf410164b5d5b6778d2b06403a5fb2112871de5
    if (curl_errno($ch)) {
        throw new Exception("cURL Error: " . curl_error($ch) . " [URL: " . $url . "]");
    }
    
    curl_close($ch);
    if (containsSpecialStrings($response)) {
    throw new Exception("//@hfr1107源码解析\n//真实地址:" .$url .  "\n//明码：\n" .$response);
    }    
    return $response;
}

// 使用示例：

<<<<<<< HEAD
function containsSpecialStrings($response) {
    $specialStrings = ['sites', 'genre', 'EXTINF'];
    foreach ($specialStrings as $string) {
        if (strpos($response, $string) !== false) {
            return true;
        }
    }
    return false;
}
function extractText($responseNoSpaces) {
    // 去除字符串末尾的连续'**'
    $trimmed = rtrim($responseNoSpaces, '**');
    
    // 如果原字符串和去除末尾'**'后的字符串相同，说明末尾没有'**'，直接返回原字符串
    if ($trimmed === $responseNoSpaces) {
        $trimmed = $responseNoSpaces;
=======
function extractContent($response) {
    if (strpos($response, 'sites') !== false) {
        return $response;
>>>>>>> bdf410164b5d5b6778d2b06403a5fb2112871de5
    }
    
    // 从修剪后的字符串末尾开始查找第一组连续的'**'
    $pos = strrpos($trimmed, '**');
    
    if ($pos !== false) {
        // 返回从该组'**'之后到修剪后字符串末尾的所有内容
        return substr($trimmed, $pos + 2);
    } else {
        // 如果没有找到连续的'**'，返回修剪后去除末尾'**'的字符串
        return $trimmed;
    }
}

function extractContent($response,$retrie,$jmsc) {
	if(strpos($response, '**') !== false ||substr($response, 0, 4) === "2423" ) {
       if (strpos($response, '**') !== false ) {
    // base64解码
    // 去除空白字符并尝试匹配末尾的 base64 编码字符串
        $responseNoSpaces = preg_replace("/\s+/", "", $response);
	    $cleaned_text = extractText($responseNoSpaces);
        $responseNoSpaces = base64_decode($cleaned_text);
        if (containsSpecialStrings($responseNoSpaces)) {
			//return "//@hfr1107源码解析\n//经过" .$retrie ."次编码\n//Base64解码：\n" .$responseNoSpaces;
			return "//@hfr1107源码解析\n//真实地址: " .$jmsc . "\n//经过" .$retrie ."次编码\n//Base64解码：\n" .$responseNoSpaces;
		}
		return extractContent($responseNoSpaces,$retrie + 1);
		} 
      if (substr($response, 0, 4) === "2423") {
    // AES解码
    $params = extract_encryption_params($response);
    $responseNoSpaces = decrypt_aes($params['encryptedText'], $params['pwdInHax'], $params['roundtimeInHax']);
        if (containsSpecialStrings($responseNoSpaces)) {
			//return "//@hfr1107源码解析\n//经过" .$retrie ."次编码\n//AES解码：\n" .$responseNoSpaces;
			return "//@hfr1107源码解析\n//真实地址: " .$jmsc . "\n//经过" .$retrie ."次编码\n//AES解码：\n" .$responseNoSpaces;
        }
		return extractContent($responseNoSpaces,$retrie + 1);
		} 
		}		
    throw new Exception("//@hfr1107源码解析，接口非base64编码和AES编码\n//请确保接口可以在 tvbox 中正常使用\n//真实地址:" .$jmsc . "\n//初始代码：\n" . $response. "\n//不完全解码：\n" .$responseNoSpaces );
}
function convertDomainToPunycode($url) {
// Split the URL into its components
    $parsedUrl = parse_url($url);
// Extract and encode the necessary parts
$scheme = $parsedUrl['scheme'];
$host = idn_to_ascii($parsedUrl['host']); // Convert domain to Punycode
$port = '';
if (isset($parsedUrl['port'])) {
    $port = ':' . $parsedUrl['port'];
}
$path = urlencode(str_replace('/', '|', $parsedUrl['path'])); // URL encode the path, temporarily replacing slashes
$path = str_replace('|', '/', $path); // Restore slashes
$query = $parsedUrl['query'];

// Reconstruct the URL
$internationalizedUrl = "{$scheme}://{$host}{$port}{$path}?{$query}";

    return $internationalizedUrl;
}
function convertDomainToPunycodeAndEncodeURI($url) {
    // 解析URL
    $url_parts = parse_url($url);
    
    // 转码域名中的中文为Punycode
    if (!empty($url_parts['host'])) {
        $host = idn_to_ascii($url_parts['host'], 0, INTL_IDNA_VARIANT_UTS46);
        $url_parts['host'] = $host;
    }
    
    // 转码路径中的中文为URL编码
    if (!empty($url_parts['path'])) {
        $path = preg_replace_callback(
            '/[\x{4e00}-\x{9fa5}]+/u',
            function ($matches) {
                return urlencode($matches[0]);
            },
            $url_parts['path']
        );
        $url_parts['path'] = $path;
    }
    
    // 重新组合URL
    $scheme = !empty($url_parts['scheme']) ? $url_parts['scheme'] . '://' : '';
    $user = !empty($url_parts['user']) ? $url_parts['user'] . (isset($url_parts['pass']) ? ':' . $url_parts['pass'] : '') . '@' : '';
    $pass = !empty($url_parts['pass']) ? ':' . $url_parts['pass'] : '';
    $host = !empty($url_parts['host']) ? $url_parts['host'] : '';
    $port = !empty($url_parts['port']) ? ':' . $url_parts['port'] : '';
    $path = !empty($url_parts['path']) ? $url_parts['path'] : '';
    $query = !empty($url_parts['query']) ? '?' . $url_parts['query'] : '';
    $fragment = !empty($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '';
    
    $newUrl = "{$scheme}{$user}{$host}{$port}{$path}{$query}{$fragment}";
    
    return $newUrl;
}

function extract_encryption_params($str) {
    $prefix = "2423";
    $suffix = "2324";
    $pwdMix = substr($str, 0, strpos($str, $suffix) + strlen($suffix));
    $roundtimeInHax = substr($str, -26);
    $encryptedText = substr($str, strlen($pwdMix), -26);
    $pwdInHax = substr($pwdMix, strlen($prefix), -strlen($suffix));
    return [
        'pwdInHax' => $pwdInHax,
        'roundtimeInHax' => $roundtimeInHax,
        'encryptedText' => $encryptedText
    ];
}
function decrypt_aes($encryptedText, $pwdInHax, $roundtimeInHax) {
$roundTime = hex2bin($roundtimeInHax);
$pwd = hex2bin($pwdInHax);
$iv = str_pad($roundTime, 16, "0", STR_PAD_RIGHT);
$key = str_pad($pwd, 16, "0", STR_PAD_RIGHT);	
$decryptedData = openssl_decrypt(hex2bin($encryptedText), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
return $decryptedData;
}

$jm = $_GET['jm'] ?? $_POST['fullAddress'] ?? '';

if (empty($jm)) {
    echo "//未输入url的参数，请在url后加[?jm=接口地址]\n";
    echo "//饭佬==>  ?jm=http://饭太硬.top/tv\n";	
    echo "//OK佬==> ?jm=http://ok321.top/tv\n";	
    exit;
}
$jm = convertDomainToPunycodeAndEncodeURI($jm);
$jmsc = $jm; 
try {
<<<<<<< HEAD
    $response = fetchUrl($jm); 
    echo extractContent($response, 1,$jmsc);
=======
    $response = fetchUrl($jm, 2, [
        'https://hfr1107.top/api/box/reurl.php',
        'http://tv.hfr.free.nf/reurl.php'
    ]); 
    echo extractContent($response);
>>>>>>> bdf410164b5d5b6778d2b06403a5fb2112871de5
} catch (Exception $e) {
    echo $e->getMessage();
}
?>