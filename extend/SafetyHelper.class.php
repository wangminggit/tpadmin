<?php

class SafetyHelper {

    /**
     * 防止恶意脚步写入数据
     * 
     * @param type $ip
     * @param type $description
     */
    public static function checkSource($model_peer, $ip, $description = '异常访问') {
        // 首先检查IP是否在黑名单中
        $blacklist = BlacklistPeer::retrieveByIP($ip);
        if (is_object($blacklist)) {
            die("Error.");
        }

        // 检查该IP是否连续写入数据，秒为单位
        eval('$model_objs = ' . $model_peer . '::getTwoNewestItemsByIp(' . $ip . ');');
        
        if (isset($model_objs) && count($model_objs) == 2 && (time() - $model_objs[0]->getCreatedAt() < 60)) {
            $error_arr = array(0, 1);  // 如果在这个时间间隔内，则认为该访问来自机器
            $result = $model_objs[0]->getCreatedAt() - $model_objs[1]->getCreatedAt();
            if (in_array($result, $error_arr)) {
                // 将该IP加入到黑名单
                $b = new Blacklist();
                $b->setIp($ip);
                $b->setReason($description);
                $b->save();
                die("Error.");
            }
        }
    }

}
