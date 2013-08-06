<?php
if(!defined('xDEC')) exit;
/**
 *
 */
define('LOG_FILE', BASE.'xDec.log');
/**
 *
 */
define('ERROR_LOG_FILE', BASE.'xDecErrors.log');
/**
 * Class Logger
 */
class Logger {
    /**
     * @param $file
     * @param $log
     */
    public function custom_log($file, $log){
           $fp = fopen($file,'a');
           fwrite($fp,$log."\n");
           fclose($fp);
    }

    /**
     * @param $log
     */
    public function log($log){
        $this->custom_log(LOG_FILE,$log);
    }

    /**
     * @param $log
     */
    public function error($log){
        $this->custom_log(ERROR_LOG_FILE, $log);
    }
}