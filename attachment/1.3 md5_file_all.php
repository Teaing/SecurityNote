<?php

function md5_file_all($file_path, $port = 80)
{
    $file_path = ltrim($file_path);
    if (strpos($file_path, 'http://') !== false || strpos($file_path, 'https://') !== false) {
        $parse_url_array = parse_url($file_path);
        if (!isset($parse_url_array['scheme']) && !isset($parse_url_array['host'])) {
            return false;
        }
        $parse_scheme = strtolower($parse_url_array['scheme']);    //	scheme
        $parse_host = $parse_url_array['host'];    // not SSL
        $parse_path = isset($parse_url_array['path']) ? trim($parse_url_array['path']) : '/';    //	path
        if (!in_array($parse_scheme, array('http', 'https'))) {
            return false;
        }
        if ('https' == $parse_scheme) {
            if (80 == intval($port)) {
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
            $file_content = substr($file_content, strpos($file_content, "\r\n\r\n") + 4);   // get file content
            return md5($file_content);  // return md5 hash
        }
    } else {    // local file
        if (!is_file($file_path)) {
            return false;   // file not exists
        }
        return md5_file($file_path);    // return md5 hash
    }
}

$url = 'https://www.baidu.com/img/bd_logo1.png';
var_dump(md5_file_all($url));
