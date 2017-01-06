<?php
namespace application\lib\utils;
class UtilsNet
{

    public static function rebuildUrl($url)
    {
        $url_p = explode('/', $url);
        array_shift($url_p); #throw junk
        $module = array_shift($url_p);
        $action = array_shift($url_p);
        $tmp = array();
        $j = count($url_p) / 2;
        for ($i = 0; $i < $j; $i++) {
            $k = array_shift($url_p);
            $v = array_shift($url_p);
            $tmp[$k] = $v;
        }
        $url = "/$module/$action";
        foreach ($tmp as $k => $v) {
            $url .= "/$k/$v";
        }
        return $url;
    }

    public static function rebuildUrlForFullUrl($url)
    {
        $url_p = explode('/', $url);
        array_shift($url_p); #throw junk
        $prefix = array_shift($url_p);
        $module = array_shift($url_p);
        $action = array_shift($url_p);
        $tmp = array();
        $j = count($url_p) / 2;
        for ($i = 0; $i < $j; $i++) {
            $k = array_shift($url_p);
            $v = array_shift($url_p);
            $tmp[$k] = $v;
        }
        $url = "/$prefix/$module/$action";
        foreach ($tmp as $k => $v) {
            $url .= "/$k/$v";
        }
        return $url;
    }

    /**
     * Fetches an alternate IP address of the current visitor, attempting to detect proxies etc.
     *
     * @return    string
     */
    public static function fetch_alt_ip()
    {
        $alt_ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            // try to avoid using an internal IP address, its probably a proxy
            $ranges = array(
                '10.0.0.0/8' => array(ip2long('10.0.0.0'), ip2long('10.255.255.255')),
                '127.0.0.0/8' => array(ip2long('127.0.0.0'), ip2long('127.255.255.255')),
                '169.254.0.0/16' => array(ip2long('169.254.0.0'), ip2long('169.254.255.255')),
                '172.16.0.0/12' => array(ip2long('172.16.0.0'), ip2long('172.31.255.255')),
                '192.168.0.0/16' => array(ip2long('192.168.0.0'), ip2long('192.168.255.255')),
            );
            foreach ($matches[0] AS $ip) {
                $ip_long = ip2long($ip);
                if ($ip_long === false) {
                    continue;
                }

                $private_ip = false;
                foreach ($ranges AS $range) {
                    if ($ip_long >= $range[0] AND $ip_long <= $range[1]) {
                        $private_ip = true;
                        break;
                    }
                }

                if (!$private_ip) {
                    $alt_ip = $ip;
                    break;
                }
            }
        } else if (isset($_SERVER['HTTP_FROM'])) {
            $alt_ip = $_SERVER['HTTP_FROM'];
        }

        return $alt_ip;
    }

    /**
     * Out put email address in Ascii format, anti spam scanner
     *
     */
    public static function antiSpamAsciiStr($in)
    {
        $out = '';
        for ($i = 0; $i < strlen($in); $i++) {
            $out .= '&#' . ord($in[$i]) . ';';
        }
        return $out;
    }

    public static function getUrlBase()
    {
        $url_base = "http";
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $url_base .= "s";
        }
        $url_base .= "://" . $_SERVER["SERVER_NAME"];
        if (isset($_SERVER["SERVER_PORT"]) && !empty($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $url_base .= ":" . $_SERVER["SERVER_PORT"];
        }

        return $url_base;
    }

    public static function isFromMobile()
    {
        $result = false;
        //Detect special conditions devices
        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $webOS = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");

        if ($iPod || $iPhone) {
            $result = true;
        } else if ($iPad) {
            $result = true;
        } else if ($Android) {
            $result = true;
        }
        return $result;
    }

}
