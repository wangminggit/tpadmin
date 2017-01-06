<?php

class mySessionStroage extends sfPDOSessionStorage {

    public function initialize($options = null) {
        if(isset ($_SERVER["SERVER_NAME"])) {
            ini_set('session.cookie_domain', ShareSessionHelper::getSessionCookieDomain());
        }
        parent::initialize($options);
    }

}
