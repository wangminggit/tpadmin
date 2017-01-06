<?php

class UtilsSymfony {
    
    const IS_FROM_TASK_KEY = '_app_is_from_task';
    
    public static function isFromAdmin() {
        return sfContext::getInstance()->getConfiguration()->getApplication() == 'backend';
    }

    public static function isFromFrontend() {
        return sfContext::getInstance()->getConfiguration()->getApplication() == 'frontend';
    }

    public static function isFromUser() {
        return sfContext::getInstance()->getConfiguration()->getApplication() == 'user';
    }


    /**
     * if is from task
     *  
     * @return bool
     */
    public static function isFromTask() {
        // if is from cmd script
        try{
            $sf_instance = sfContext::getInstance();
        } catch (Exception $ex) {
            $sf_instance = null;
        }
        $module = $sf_instance ? $sf_instance->getModuleName() : null;
        if (empty($module)) {
            return true;
        }else{
            return false;
        }  
    }

}