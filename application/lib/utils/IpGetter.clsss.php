<?php

class IpGetter {

    public static function getApiReturn($ip) {
        $api_return = file_get_contents("http://www.youdao.com/smartresult-xml/search.s?type=ip&q=" . $ip);
        return $api_return;
    }

    public static function process($ip) {
        $aip_return = self::getApiReturn($ip);
        $xml_to_array = new xmlToArrayParser($aip_return);
        $return_array = $xml_to_array->array;
        return $return_array;
    }

    public static function getLocation($ip) {
        $return_array = self::process($ip);
        return $return_array['smartresult']['product']['location'];
    }

}