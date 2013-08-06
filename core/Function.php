<?php
if(!defined('xDEC')) exit;
/**
 * @param $key
 * @return mixed
 */
function get($key)
{
    return Registry::get($key);
}

/**
 * @param $key
 * @param $value
 * @return int
 */
function set($key, $value)
{
    return Registry::set($key, $value);
}

/**
 * @param $key
 */
function remove($key)
{
    return Registry::remove($key);
}

/**
 * @return bool
 */
function is_ssl() {
    if ( isset($_SERVER['HTTPS']) ) {
        if ( 'on' == strtolower($_SERVER['HTTPS']) )
            return true;
        if ( '1' == $_SERVER['HTTPS'] )
            return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}

/**
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function error_handler($errno, $errstr, $errfile, $errline){
    if (!(error_reporting() & $errno)) {
        return;
    }
    switch($errno){
        case E_ERROR:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
        case E_RECOVERABLE_ERROR:
            get(LOGGER)->error("#$errno $errstr in `$errfile` at line no $errline");
            break;
        default:
            get(LOGGER)->log("#$errno $errstr in `$errfile` at line no $errline");
            break;
    }

}

/**
 * @param $directory
 * @return int
 */
function directory_size($directory) {
    $directorySize=0;
    if ($dh = @opendir($directory)) {
        while (($filename = readdir ($dh))) {
            if ($filename != "." && $filename != "..")
            {
                if (is_file($directory."/".$filename))
                    $directorySize += filesize($directory."/".$filename);
                if (is_dir($directory."/".$filename))
                    $directorySize += directory_size($directory."/".$filename);
            }
        }
    }
    @closedir($dh);
    return $directorySize;
}

function quot($str){
    return '`'.$str.'`';
}