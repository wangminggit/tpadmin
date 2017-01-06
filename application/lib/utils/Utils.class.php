<?php

class Utils {

    public static function wfGenerateToken($salt = '') {
        $salt = serialize($salt);

        return md5(mt_rand(0, 0x7fffffff) . $salt);
    }

    /**
     * 处理jquery datapicker 传递过来的date
     * @param type string
     * @return array 
     */
    public static function getDatePickerArr($date) {
        $result = array();
        if (!trim($date)) {
            $result['year'] = "0000";
            $result['month'] = "00";
            $result['date'] = "00";
            return $result;
        }
        $date_str = $date;
        $date_arr = explode('/', $date_str);
        $result['year'] = $date_arr[2];
        $result['month'] = $date_arr[0];
        $result['date'] = $date_arr[1];
        return $result;
    }

    public static function getDatetimePickerArr($datetime) {
        $result = array();
        if (!trim($datetime)) {
            $result['year'] = "0000";
            $result['month'] = "00";
            $result['date'] = "00";
            $result['hour'] = "00";
            $result['minute'] = "00";
            $result['second'] = "00";
            return $result;
        }
        $datetime_arr = explode(' ', $datetime);
        $date_str = $datetime_arr[0];
        $time_str = $datetime_arr[1];
        $date_arr = explode('/', $date_str);
        $time_arr = explode(':', $time_str);
        $result['year'] = $date_arr[2];
        $result['month'] = $date_arr[0];
        $result['date'] = $date_arr[1];
        $result['hour'] = $time_arr[0];
        $result['minute'] = $time_arr[1];
        $result['second'] = $time_arr[2];
        return $result;
    }

    public static function datetimePickerArrToMysqlDateTime($date_time) {
        if (!is_array($date_time)) {
            return false;
        }
        return $date_time['year'] . '-' . $date_time['month'] . '-' . $date_time['date'] . ' ' . $date_time['hour'] . ':' . $date_time['minute'] . ':' . $date_time['second'];
    }

    public static function substr_utf8($string, $start, $length) {
        $chars = $string;
        $m = 0;
        $n = 0;
        //echo $string[0].$string[1].$string[2];  
        $i = 0;
        do {
            if (@preg_match("/[0-9a-zA-Z]/", $chars[$i])) {//纯英文  
                $m++;
            } else {
                $n++;
            }//非英文字节,  
            $k = $n / 3 + $m / 2;
            $l = $n / 3 + $m;
            $i++;
        } while ($k < $length);
        $str1 = mb_substr($string, $start, $l, 'utf-8');
        return $str1;
    }

    /**
     * 处理数组，获取数组value为string之后为数组key arr
     */
    public static function getArrStrNextArrLv($arr, $key) {
        $result = array();
        if (is_array($arr[$key])) {
            return;
        }

        for ($i = $key + 1; $i < count($arr); $i++) {
            if (is_array($arr[$i])) {
                $result[] = "line_" . $i;
            } else {
                break;
            }
        }
        return $result;
    }

    public static function rebuildUrlForAdmin($url) {
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

    public static function numToExcelAlpha($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::numToExcelAlpha($num2) . $letter;
        } else {
            return $letter;
        }
    }

    public static function formatNumeric($num) {
        $result = '';

        $str = str_replace('+', '', $num);
        $result = str_replace('-', '', $str);

        return $result;
    }

    public static function statisticCharNumber($string) {
        $position = array();
        $sTotal = 0;
        for ($i = 0; $i < mb_strlen($string, "UTF-8"); $i++) {
            $str = mb_substr($string, $i, 1, "UTF-8");
            $position[] = $str;
            if (preg_match("/^[\x7f-\xff]+$/", $str)) {
                $sTotal = $sTotal + 2;
            } else {
                $sTotal = $sTotal + 1;
            }
        }
        return $sTotal;
    }

    /**
     * 处理中英文字符串截取
     * @param string string
     * @param length int
     * @return string 
     */
    public static function substr_word($string, $length) {
        $strLength = 0;
        $buildLenght = 0;

        $str_length = strlen($string);
        for ($i = 0; $i < $str_length;) {
            if ($strLength >= $length) {
                break;
            }
            if (ord(substr($string, $i, 1)) > 127) {
                $i += 3;
                $buildLenght += 3;
                $strLength += 1;
            } else {
                $i += 1;
                $buildLenght += 1;
                $strLength += 1;
            }
        }

        if (strlen($string) > $buildLenght) {
            $etc = '…';
        } else {
            $etc = '';
        }

        return substr($string, 0, $buildLenght) . $etc;
    }

    public static function substr_word_length($string) {
        $strLength = 0;

        $str_length = strlen($string);
        for ($i = 0; $i < $str_length;) {
            if (ord(substr($string, $i, 1)) > 127) {
                $i += 3;
                $strLength += 1;
            } else {
                $i += 1;
                $strLength += 1;
            }
        }

        return $strLength;
    }

    // 过滤文件名中的非法字符
    public static function buildFileName($file_name) {
        $result = $file_name;

        $result = str_replace("\\", "_", $result);
        $result = str_replace("/", "_", $result);
        $result = str_replace('"', "_", $result);
        $result = str_replace(":", "_", $result);
        $result = str_replace("*", "_", $result);
        $result = str_replace("?", "_", $result);
        $result = str_replace("<", "_", $result);
        $result = str_replace(">", "_", $result);
        $result = str_replace("|", "_", $result);

        return $result;
    }

    public static function tableToPropelFomat($f) {
        $parts = explode('_', $f);
        foreach ($parts as &$part) {
            $part = ucfirst(strtolower(trim($part)));
        }
        return implode('', $parts);
    }

    public static function fieldToPropelFomat($f) {
        $parts = explode('_', $f);
        foreach ($parts as &$part) {
            $part = ucfirst(strtolower(trim($part)));
        }
        return implode('', $parts);
    }

    /*
     * 高亮状态字段
     */

    public static function hightlightTextByStatusId($str, $status_id = null) {
        $result = $str;
        if ($status_id == 1) {
            $color = "red";
        } else if ($status_id == 2) {
            $color = "green";
        }
        $result = "<span style='color:" . $color . "'>" . $str . "</span>";
        return $result;
    }

    public static function parseArr($arr = array(), $per_row = 4) {
        $results = array();
        $j = 0;
        $i = 0;
        foreach ($arr as $_arr) {
            if (($j + 1) == $per_row + 1) {
                $i++;
                $j = 0;
            }
            $results[$i][$j] = $_arr;
            $j++;
        }
        return $results;
    }

    /**
     * if function is call able from given object
     * similar to is_callable, but take care of scope issue
     * 
     * @param object $object
     * @param string $function
     * @return bool
     */
    public static function isObjectFunction($object, $function) {
        return in_array($function, get_class_methods($object));
    }

    /**
     *  替换string里最后一个匹配的
     */
    public static function strReplaceLastOccurence($search, $replace, $subject) {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * 返回当前月初时间戳 
     * @return type
     */
    public static function getCurrentMonthStart() {
        return mktime(0, 0, 0, date("m"), 1, date("Y"));
    }

    /**
     * 返回当前月末时间戳 
     * @return type
     */
    public static function getCurrentMonthEnd() {
        return mktime(23, 59, 59, date("m"), date("t"), date("Y"));
    }

    /**
     * 返回当前天初的时间戳
     * @return type
     */
    public static function getCurrentDayStart() {
        return strtotime(date('Y-m-d'));
    }

    /**
     * 返回当前天末的时间戳
     * @return type
     */
    public static function getCurrentDayEnd() {
        return strtotime(date('Y-m-d', strtotime('+1 day')));
    }

    /**
     * 返回当前小时初的时间戳
     * @return type
     */
    public static function getCurrentHourStart() {
        return strtotime(date('Y-m-d H:00:00'));
    }

    /**
     * 返回当前小时末的时间戳
     * @return type
     */
    public static function getCurrentHourEnd() {
        return strtotime(date('Y-m-d H:59:59'));
    }

    /**
     * 检查IP地址是否合规
     * @param type $ip
     * @return boolean
     */
    public static function checkIp($ip) {
        $result = true;

        $arr = explode('.', $ip);
        if (count($arr) != 4) {
            $result = false;
        } else {
            for ($i = 0; $i < 4; $i++) {
                if (($arr[$i] < '0') || ($arr[$i] > '255')) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    public static function checkPhone($phone) {
        $result = false;

        if ($phone != "" && is_numeric($phone) && strlen($phone) == 11) {
            $result = true;
        }

        return $result;
    }

}
