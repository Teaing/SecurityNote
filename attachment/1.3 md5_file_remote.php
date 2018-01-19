<?php
function md5_file_remote($file_url, $port = 80)
{
    $parse_url_array = parse_url(trim($file_url));
    if (!isset($parse_url_array['scheme']) && !isset($parse_url_array['host'])) {
        return false;
    }
    $parse_scheme = $parse_url_array['scheme'];    //	scheme
    $parse_host = $parse_url_array['host'];    // not SSL
    $parse_path = isset($parse_url_array['path']) ? $parse_url_array['path'] : '/';    //	path
    if (!in_array(strtolower($parse_scheme), array('http', 'https'))) {
        return false;
    }
    if ('https' == strtolower($parse_scheme)) {
        if (80 == $port) {
            $port = 443;
            $parse_host = "ssl://" . $parse_url_array['host'];    //SSL
        }
    }
    $fp = fsockopen($parse_host, $port, $errno, $errstr, 30);
    if (!$fp) {
        return false;
    } else {
        $file_content = '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $out = "GET {$parse_path} HTTP/1.1\r\n";
        $out .= "Host: {$parse_url_array['host']}\r\n";
        $out .= "User-Agent: $user_agent\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "\r\n";
        fwrite($fp, $out);
        while (!feof($fp)) {
            $file_content .= fgets($fp, 10240);
        }
        fclose($fp);
        $file_content = substr($file_content, strpos($file_content, "\r\n\r\n") + 4);
        return md5($file_content);
    }
}

$url_file = 'https://www.baidu.com/img/bd_logo1.png';
echo md5_file_remote($url_file);
