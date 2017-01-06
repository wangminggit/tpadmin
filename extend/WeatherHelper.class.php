<?php

class WeatherHelper {

    const DEFAULT_CITY_IP = "183.129.215.98";       #杭州

    /**
     * 获取用户真实 IP
     */

    public static function getIP() {
        static $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }

        if ($realip == "127.0.0.1" || preg_match("/192.168/", $realip)) {
            $realip = self::DEFAULT_CITY_IP;
        }

        return $realip;
    }

    /**
     * 获取 IP  地理位置
     * 淘宝IP接口
     * @Return: array
     */
    public static function getCity($ip) {
        $result = "";

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $content = @file_get_contents($url);
        if ($content) {
            $data = json_decode($content);
            if ((string) $data->code == '1') {
                return false;
            }
            $result = $data->data->city;
        }

        return $result;
    }

    public static function getWeather() {
        $result = array();

        $city = self::getCurrentCity();
        $content = @file_get_contents("http://api.map.baidu.com/telematics/v3/weather?location=" . $city . "&output=json&ak=3526c3282a61adf3baf585318be957bc");
        if ($content) {
            $weather_datas = json_decode($content);
            if ($weather_datas->status == "success") {
                $result = $weather_datas->results[0]->weather_data;
            }
        }

        return $result;
    }

    public static function getCurrentCity() {
        $result = " - ";

        $ip = self::getIP();
        $city = self::getCity($ip);
        if ($city != "" && strlen($city) > 0) {
            $city = str_split($city, strlen($city) - 3);
            $result = $city[0];
        }

        return $result;
    }

    public static function getCurrentDayWeather() {
        $result = " - ";

        $weather_datas = self::getWeather();
        if (count($weather_datas) > 0) {
            $result = $weather_datas[0]->weather;
        }

        return $result;
    }

}
