<?php

error_reporting(-1);
ini_set('display_errors', 1);


// ����Ӧ�õľ���·��
define('HOME_PATH', dirname(__FILE__));

require_once HOME_PATH . '/aip-php-sdk-4.16.1/AipOcr.php';


function curl_https($url, $data = array(), $header = array(), $type = '', $timeout = 60)
{
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_SSLVERSION, 1.2); // ����SSL֤��汾
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ����֤����
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // ��֤���м��SSL�����㷨�Ƿ����
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
    // ����Ƿ���BOM
    $charset[1] = substr($response, 0, 1);
    $charset[2] = substr($response, 1, 1);
    $charset[3] = substr($response, 2, 1);
    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        $response = substr($response, 3);
    }
    return $response;
}


// ��� APPID AK SK
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

// ����ͨ������ʶ��, ͼƬ����Ϊ����ͼƬ
$re = $client->basicGeneral($image);
echo '<pre>';
print_r($re);
echo '</pre>';

// ����п�ѡ����
/* $options = array();
$options["language_type"] = "ENG";
$options["detect_direction"] = "false";
$options["detect_language"] = "false";
// $options["probability"] = "true";

// ����������ͨ������ʶ��, ͼƬ����Ϊ����ͼƬ
$re = $client->basicGeneral($image, $options); */



/* $url = "https://10.5.6.99/security/generateYzm.png";
// ����ͨ������ʶ��, ͼƬ����ΪԶ��urlͼƬ
$re = $client->basicGeneralUrl($url);

print_r($re); */

// ����п�ѡ����
/* $options = array();
$options["language_type"] = "CHN_ENG";
// �߾���
$options["detect_direction"] = "true";
$options["detect_language"] = "true";
$options["probability"] = "true";

// ����������ͨ������ʶ��, ͼƬ����ΪԶ��urlͼƬ
$client->basicGeneralUrl($url, $options);

// ����ͨ������ʶ�𣨸߾��Ȱ棩
$client->basicAccurate($image); */

// ����п�ѡ����
$options = array();
$options["detect_direction"] = "false";
$options["probability"] = "true";

// ����������ͨ������ʶ�𣨸߾��Ȱ棩
$re = $client->basicAccurate($image, $options);
echo '<pre>';
print_r($re);
echo '</pre>';


/* $url = "https://10.5.6.99/security/generateYzm";

// ��������ͼƬ����ʶ��, ͼƬ����ΪԶ��urlͼƬ
$re = $client->webImageUrl($url);
print_r($re);

// ����п�ѡ����
$options = array();
$options["detect_direction"] = "true";
$options["detect_language"] = "true";

// ��������������ͼƬ����ʶ��, ͼƬ����ΪԶ��urlͼƬ
$re = $client->webImageUrl($url, $options);
print_r($re); */

