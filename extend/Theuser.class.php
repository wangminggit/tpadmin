<?php

class Theuser {

    /**
     * Get cucrent admin_user id/ user name
     *
     * @return string
     */
    public static function getCurrentAdminUserEmail() {
        $user = self::getCurrentUser();
        return $user->getEmail();
    }

    /**
     * Get current admin_user int id
     * 
     * @return int or null 
     */
    public static function getCurrentAdminUserIntId() {
        $result = null;
        $user = self::getCurrentAdminUser();
        if ($user) {
            $result = $user->getId();
        }
        return $result;
    }

    /**
     * Get current admin_user object
     *
     * @return object
     */
    public static function getCurrentAdminUser() {
        static $result;

        if (!isset($result)) {
            $user_id = isset($_SESSION['valid_admin_user']) ? $_SESSION['valid_admin_user'] : '';
            $c = new Criteria();
            $c->add(AdminUserPeer::ID, $user_id);
            $result = AdminUserPeer::doSelectOne($c);
        }
        return $result;
    }

    /**
     * Get current admin_user.admin_user_group_id
     *
     * @return int or null 
     */
    public static function getCurrentAdminUserGroupId() {
        $result = null;
        $admin_user = self::getCurrentAdminUser();
        if (is_object($admin_user)) {
            $result = $admin_user->getAdminUserGroupId();
        }
        return $result;
    }

    public static function isSuperAdminUserByCurrentAdminUser() {
        $result = false;
        $admin_user = self::getCurrentAdminUser();
        if (is_object($admin_user) && $admin_user->isSuperAdminUser()) {
            $result = true;
        }
        return $result;
    }

    /**
     * Get current user int id
     * 
     * @return int or null 
     */
    public static function getCurrentUserIntId() {
        $result = null;
        $user = self::getCurrentUser();
        if ($user) {
            $result = $user->getId();
        }
        return $result;
    }

    /**
     * Get current user object
     *
     * @return object
     */
    public static function getCurrentUser() {
        static $result;

        if (!isset($result)) {
            $user_id = isset($_SESSION['valid_user']) ? $_SESSION['valid_user'] : '';
            $c = new Criteria();
            $c->add(UserPeer::ID, $user_id);
            $result = UserPeer::doSelectOne($c);
        }
        return $result;
    }

    /**
     * Get current user email
     * 
     * @return type
     */
    public static function getCurrentUserEmail() {
        $result = "";

        $user = self::getCurrentUser();
        if (is_object($user)) {
            $result = $user->getEmail();
        }

        return $result;
    }

}

?>