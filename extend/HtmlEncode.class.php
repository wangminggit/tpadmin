<?php

class HtmlEncode {

    static $_convertToHtmlEntitiesSrcEncoding = 'UTF-8';

    /**
     * 将非ASCII字符串转换成HTML实体 
     * 
     * @example HtmlEncode::encode("我信了"); //输出:&#25105;&#20449;&#20102; 
     * @param string $s 要进行编码的字符串 
     * @return string 返回HTML实体引用 
     */
    public static function encode($s, $srcEncoding = 'UTF-8') {
        self::$_convertToHtmlEntitiesSrcEncoding = $srcEncoding;
        return preg_replace_callback('|[^\x00-\x7F]+|', array(__CLASS__, '_convertToHtmlEntities'), $s);
    }

    public static function _convertToHtmlEntities($data) {
        if (is_array($data)) {
            $chars = str_split(iconv(self::$_convertToHtmlEntitiesSrcEncoding, "UCS-2BE", $data[0]), 2);
            $chars = array_map(array(__CLASS__, __FUNCTION__), $chars);
            return join("", $chars);
        } else {
            $code = hexdec(sprintf("%02s%02s;", dechex(ord($data {0})), dechex(ord($data {1}))));
            return sprintf("&#%s;", $code);
        }
    }

}

?>
