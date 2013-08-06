<?php
if(!defined('xDEC')) exit;
class Security {
    //TODO clean is for sql filtering
    //TODO add more security to framework and remove sql cleaning from here
    public function clean($str){
        return addslashes(strip_tags(htmlspecialchars($str)));
    }

    public function mysql($str){
        return strip_tags(htmlspecialchars($str));
    }
}