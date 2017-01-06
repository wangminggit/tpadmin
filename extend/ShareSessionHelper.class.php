<?php

/**
 * Session跨平台共享帮助
 *
 * @author Bob
 */
class ShareSessionHelper {

    // 每个平台独立的token
    private static $session_clean_flag_token = 'beA6JouUzG7P';

    /**
     * 获取目标 session.cookie_domain
     * 
     * @return string
     */
    public static function getSessionCookieDomain() {
        $cookie_domain = '';
        $cookieDefaults = session_get_cookie_params();
        
        // 不改变admin app的cookie domain, 因为没有必要改成上级共享，而且也更加安全
        if (UtilsSymfony::isFromAdmin()) {
            $cookie_domain = $cookieDefaults['domain'];
        } else if(isset ($_SERVER["SERVER_NAME"])){
            // 父域名 =  '.gtjaqh.com'
            // 如果当前域名是父域名的子域名，则把session作用域改成父域名, 从而实现和不同平台共享session
            
            if(preg_match('@^(.)+\.jh\.gtjaqh\.com$@', $_SERVER["SERVER_NAME"])){
                $cookie_domain = '.jh.gtjaqh.com';
            }
            
            if(preg_match('@^(.)+\.jh\.gtjaqh\.local$@', $_SERVER["SERVER_NAME"])){
                $cookie_domain = '.jh.gtjaqh.local';
            }
            
            if(preg_match('@^(.)+\.jh\.gtjaqh\.justwebwork\.com$@', $_SERVER["SERVER_NAME"])){
                $cookie_domain = '.jh.gtjaqh.justwebwork.com';
            }
            
        }
        return $cookie_domain;
    }

    /**
     * 尝试Session清洗
     * 
     */
    public static function sessionCleanerTry() {
        if (!self::isSessionClean()) {
            self::sessionCleanerDo();
        }
    }

    /**
     * Session清洗
     * 
     */
    private static function sessionCleanerDo() {
        $keep_keys = array(
            'valid_admin_user', // 后台用户
            'valid_user', // 前台用户，对应字段user.id (int)
                // add more session here if any
        );

        // start
        foreach ($_SESSION as $k => $v) {
            // symfony: 以symfony开头的任何key (symfony框架控制)
            if (in_array($k, $keep_keys) || substr($k, 0, 7) == 'symfony') {
                // do nothing
            } else {
                // reset
                unset($_SESSION[$k]);
            }
        }

        // end
        $_SESSION['clean_flag'] = self::$session_clean_flag_token;
    }

    /**
     * 是否有清洗过
     * 
     * @return bool
     */
    private static function isSessionClean() {
        return isset($_SESSION['clean_flag']) && $_SESSION['clean_flag'] == self::$session_clean_flag_token;
    }

}
