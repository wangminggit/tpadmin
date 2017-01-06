<?php

class MobileHelper {

    /**
     * 检测用户当前浏览器
     * @return boolean 是否ie浏览器
     */
    public static function isIeBrowser() {
        $userbrowser = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $userbrowser)) {
            $usingie = true;
        } else {
            $usingie = false;
        }
        return $usingie;
    }

    public static function isWeixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    public static function isMobile() {
        return !self::isPC();
    }

    public static function isIpad() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mobile_agents = array("ipad");
        $is_ipad = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_ipad = true;
                break;
            }
        }
        return $is_ipad;
    }

    public static function isIphone() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mobile_agents = array("iphone");
        $is_iphone = false;
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_iphone = true;
                break;
            }
        }
        return $is_iphone;
    }

    public static function isPC() {
        $is_pc = true;

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_agents = array("240x320", "acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel", "amoi",
            "android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio",
            "au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu",
            "cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric", "etouch", "fly ",
            "fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi",
            "htc", "huawei", "hutchison", "inno", "ipad", "ipaq", "iphone", "ipod", "jbrowser", "kddi",
            "kgt", "kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", "lge9", "longcos", "maemo",
            "mercator", "meridian", "micromax", "midp", "mini", "mitsu", "mmm", "mmp", "mobi", "mot-",
            "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", "nintendo", "nitro", "nokia",
            "nook", "novarra", "obigo", "palm", "panasonic", "pantech", "philips", "phone", "pg-",
            "playstation", "pocket", "pt-", "qc-", "qtek", "rover", "sagem", "sama", "samu", "sanyo",
            "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", "sharp", "siemens", "sie-", "softbank",
            "sony", "spice", "sprint", "spv", "symbian", "tablet", "talkabout", "tcl-", "teleca", "telit",
            "tianyu", "tim-", "toshiba", "tsm", "up.browser", "utec", "utstar", "verykool", "virgin",
            "vk-", "voda", "voxtel", "vx", "wap", "wellco", "wig browser", "wii", "windows ce",
            "wireless", "xda", "xde", "zte");
        foreach ($mobile_agents as $device) {
            if (stristr($user_agent, $device)) {
                $is_pc = false;
                break;
            }
        }

        return $is_pc;
    }

}
