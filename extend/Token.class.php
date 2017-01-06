<?php

/**
 * class for prevent csrf attack
 * 
 * @uses 
 * 
 * $t = new Token();
 * echo $t->getCSRFToken('loginpage');
 * var_dump($t->verifyCSRFToken());
 *
 */
class Token{
    private $csrf_secret = 'f3ade978af63e43ebe3c6fee163be417e7c3ee87';
    
    public function getCSRFToken($name){
        return md5($this->csrf_secret.session_id().$name);
    }
    
    public function verifyCSRFToken($token,$name){
        return $token == $this->getCSRFToken($name);
    }    

}

