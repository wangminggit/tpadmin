<?php

class PermissionHelper {

    /**
     * 只能操作有关自己的信息
     * @param type $user_id
     * @return boolean
     */
    public static function canAccessOnlyCurrentUser($user_id) {
        $result = Theuser::getCurrentUserIntId() == $user_id ? TRUE : FALSE;
        return $result;
    }

    /**
     * 产品是否上线
     * @param type $product_id
     * @return boolean
     */
    public static function isValidProductByProductId($product_id) {
        $result = FALSE;
        $product = ProductPeer::retrieveByPK($product_id);
        if ($product) {
            $result = $product->getStatus() == ProductPeer::STATUS_ONLINE ? TRUE : FALSE;
        }
        return $result;
    }

    /**
     * 用户是否可以订阅
     * 检查点：1.用户级别>=产品级别；2.产品上线；3.如果有部门限制，需要检查部门；
     * @param type $product_id
     * @param type $user
     * @return boolean
     */
    public static function isUserCanSubscriptionProduct($product_id, $user = NULL) {
        if (!$user) {
            $user = Theuser::getCurrentUser();
        }
        $result = FALSE;
        $product = ProductPeer::retrieveByPK($product_id);
        if ($product && $product->getProductTypeId() != ProductTypePeer::TYPE_SOFTWARE) {
            if ($product->checkIsOnline()) {
                if ($user->getUserLevelId() >= $product->getUserLevelId()) {
                    if ($product->getIsLimitedVisibility()) {
                        //部分人(department)可以订阅产品
                        $department_ids = ProductUserVisibilityByDepartmentPeer::getDepartmentIdsByProductId($product_id);
                        if ($user->getDepartmentId() && in_array($user->getDepartmentId(), $department_ids)) {
                            $result = TRUE;
                        }
                    } else {
                        //所有人可以订阅产品
                        $result = TRUE;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 判断用户是否可以查看或者使用产品
     * 1.是否订阅；2.有效期；
     * @param type $product_id
     * @return boolean
     */
    public static function isUserCanViewProduct($product_id, $user = NULL) {
        if (!$user) {
            $user = Theuser::getCurrentUser();
        }
        $result = FALSE;
        $product = ProductPeer::retrieveByPK($product_id);
        if ($product) {
            if ($product->checkIsOnline()) {
                //级别
                if ($user->getUserLevelId() >= $product->getUserLevelId()) {
                    //部门
                    $department_ids = ProductUserVisibilityByDepartmentPeer::getDepartmentIdsByProductId($product_id);
                    if (!$product->getIsLimitedVisibility() || ($product->getIsLimitedVisibility() && $user->getDepartmentId() && in_array($user->getDepartmentId(), $department_ids))) {
                        //订阅
                        $product_user_subscrption = ProductUserSubscriptionPeer::retrieveByUserIdProductId($user->getId(), $product_id);
                        if ($product_user_subscrption) {
                            if (!$product_user_subscrption->getExpiredAt()) {
                                $result = TRUE;
                            } else {
                                if ($product_user_subscrption->getExpiredAt() < time()) {
                                    $result = FALSE;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 判断用户是否可以查看报告产品
     * @return boolean
     */
    public static function isUserCanViewProductInstanceReport($product_instance_report_id, $user = NULL) {
        if (!$user) {
            $user = Theuser::getCurrentUser();
        }
        //用户的订阅关键字与产品的订阅关键字比较
        $user_keyword_ids = UserKeywordSubscriptionPeer::getKeywordIdsByUserId($user->getId());
        $product_instance_report_keyword_ids = ProductInstanceReportKeywordXrefPeer::getKeywordIdsByProductInstanceReportId($product_instance_report_id);
        return array_intersect($user_keyword_ids, $product_instance_report_keyword_ids) ? TRUE : FALSE;
    }

}
