<?php
if(!defined('xDEC')) exit;
/**
 * Class Cookie
 */
class Cookie {
    /**
     * @return bool
     */
    public function login(){
        var_dump($_COOKIE);
        if(isset($_COOKIE['session_id']))
        {
            get('Database')->select(get('Model')->getTable('session'), '*',get('Model')->getField('session','session_id')."='".get('Security')->mysql($_COOKIE['session_id'])."'");
            if(get('Database')->result()->num_rows == 1){
                $this->setCookie('session_id', session_id());
                return true;
            } else $this->removeCookie($_COOKIE['session_id']);
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value){
        setcookie($key, $value, time()+7*24*60*60, DIR.'/', BASE_URL, true);
    }

    /**
     * @param $key
     */
    public function removeCookie($key){
        setcookie($key, null, time()-1, null, null, true);
    }
}