<?php

class U {

    const DEFAULT_CITY_IP = "183.129.215.98";       #杭州

    public static function C() {
        return sfContext::getInstance()->getUser()->getCulture();
    }

    public static function L() {
        return sfContext::getInstance()->getUser()->getCulture() == 'en' ? 'zh-cn' : 'en';
    }

    public static function buildCultureUrl($culture) {
        $result = '';
        $current_url = $_SERVER['REQUEST_URI'];
        $uri = $_SERVER['REQUEST_URI'];
        preg_match("/\/.*\//U", $current_url, $match);
        $result = str_replace($match, "/$culture/", $uri);
        return $result;
    }

    public static function getRealUri($url) {
        $result = '';
        $match_result = preg_match("/\/.*\//U", $url, $match);
        if ($match_result) {
            $result = str_replace($match, '/', $url);
        }
        return $result;
    }

    public static function getServerUrl() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"];
        return $pageURL;
    }

    /**
     * 将字符串进行格式化
     * 如 "menu_item" 转为 "MenuItem"
     */
    public static function FormatString($string) {
        $result = '';
        $str_temps = explode("_", $string);
        foreach ($str_temps as $str_temp) {
            $result .= ucfirst($str_temp);
        }
        return $result;
    }

    /**
     * 获取 IP 地理位置
     * 淘宝IP接口
     * @Return: array
     */
    public static function getAddress($ip) {
        $result = " - ";
        
        // 如果ip地址为127.0.0.1 或于服务器属于同局域网段时，则使用默认IP来解析访问地址
        if($ip == '127.0.0.1' || substr($ip,0,7) == "192.168"){
            $ip = U::DEFAULT_CITY_IP;
        }
        
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $content = @file_get_contents($url);
        if ($content) {
            $_result = json_decode($content);
            if ((string) $_result->code == '1') {
                return false;
            }
            $result = $_result->data->region . ' - ' . $_result->data->city;
        }

        return $result;
    }

}

?>