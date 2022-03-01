<?php

error_reporting(-1);
ini_set('display_errors', 1);


// 定义应用的绝对路径
define('HOME_PATH', dirname(__FILE__));

require_once HOME_PATH . '/aip-php-sdk-4.16.1/AipOcr.php';


function curl_https($url, $data = array(), $header = array(), $type = '', $timeout = 60)
{
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_SSLVERSION, 1.2); // 设置SSL证书版本
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, true);
    if ($type == 'file') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
        curl_close($ch);
        return false;
    }
    if ($error = curl_error($ch)) {
        die($error);
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode == '503') {
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    // 检查是否含有BOM
    $charset[1] = substr($response, 0, 1);
    $charset[2] = substr($response, 1, 1);
    $charset[3] = substr($response, 2, 1);
    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        $response = substr($response, 3);
    }
    return $response;
}


// 你的 APPID AK SK
const APP_ID = '25679648';
const API_KEY = 'LEtca82UU6Tzyhg6rFYbggPs';
const SECRET_KEY = 'o0eNxI7Dnc93T0HV3R2LVfCU4ZaVAspV';
$client = new AipOcr(APP_ID, API_KEY, SECRET_KEY);

$file = HOME_PATH.'/generateYzm.png';
$image = curl_https('https://10.5.6.99/security/generateYzm');
// $image = curl_https('https://10.5.10.53/security/generateYzm');
file_put_contents($file, $image);
$image = file_get_contents(HOME_PATH.'/generateYzm.png');

echo '<img src="./generateYzm.png?'.time().'" alt="">';

// 调用通用文字识别, 图片参数为本地图片
$re = $client->basicGeneral($image);
echo '<pre>';
print_r($re);
echo '</pre>';

// 如果有可选参数
/* $options = array();
$options["language_type"] = "ENG";
$options["detect_direction"] = "false";
$options["detect_language"] = "false";
// $options["probability"] = "true";

// 带参数调用通用文字识别, 图片参数为本地图片
$re = $client->basicGeneral($image, $options); */



/* $url = "https://10.5.6.99/security/generateYzm.png";
// 调用通用文字识别, 图片参数为远程url图片
$re = $client->basicGeneralUrl($url);

print_r($re); */

// 如果有可选参数
/* $options = array();
$options["language_type"] = "CHN_ENG";
// 高精度
$options["detect_direction"] = "true";
$options["detect_language"] = "true";
$options["probability"] = "true";

// 带参数调用通用文字识别, 图片参数为远程url图片
$client->basicGeneralUrl($url, $options);

// 调用通用文字识别（高精度版）
$client->basicAccurate($image); */

// 如果有可选参数
$options = array();
$options["detect_direction"] = "false";
$options["probability"] = "true";

// 带参数调用通用文字识别（高精度版）
$re = $client->basicAccurate($image, $options);
echo '<pre>';
print_r($re);
echo '</pre>';


/* $url = "https://10.5.6.99/security/generateYzm";

// 调用网络图片文字识别, 图片参数为远程url图片
$re = $client->webImageUrl($url);
print_r($re);

// 如果有可选参数
$options = array();
$options["detect_direction"] = "true";
$options["detect_language"] = "true";

// 带参数调用网络图片文字识别, 图片参数为远程url图片
$re = $client->webImageUrl($url, $options);
print_r($re); */

