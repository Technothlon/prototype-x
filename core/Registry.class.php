<?php
if(!defined('xDEC')) exit;
//TODO add access limit to registry
/**
 * Class Registry
 * @package xDec
 * @class Registry:  Singleton handler for managing one copy of each object
 */
class Registry
{
    /**
     * @var array: object registry
     */
    private static $_objects = array();
    /**
     * @var array: settings registry[key => value pairs]
     */
    private static $_settings = array();

    /**
     *  hidden function
     */
    private function __construct()
    {
    }

    /**
     * @param $key: reference to object required
     * @return mixed: object or string from registry
     */
    public static function get($key)
    {
        if (array_key_exists($key, self::$_objects)) return self::$_objects[$key];
        if (array_key_exists($key, self::$_settings)) return self::$_settings[$key];
        return null;
    }

    /**
     * @param $key: reference to object to be added to registry
     * @param $value: data object or setting string
     * @throws \Exception: key already exists in registry
     * @info    Use namespace convention for adding key to registry EX. xDec:Example refers Example class in xDec package
     * @return int 0 for success and 1 for failed[already exists]
     */
    public static function set($key, $value)
    {
        if (is_object($value)) {
            if (!array_key_exists($key, self::$_objects)) {
                self::$_objects[$key] = $value;
            } else {
                trigger_error("Key `$key` already exists in Object Registry.", E_USER_NOTICE);
                return 1;
            }
        } else {
            if (!array_key_exists($key, self::$_settings)) {
                self::$_settings[$key] = $value;
            } else {
                trigger_error("Key `$key` already exists in Setting Registry.", E_USER_NOTICE);
                return 1;
            }
        }
        return 0;
    }

    public static function remove($key){
        if (array_key_exists($key, self::$_objects)) unset(self::$_objects[$key]);
        if (array_key_exists($key, self::$_settings)) unset(self::$_settings[$key]);
    }
    private function __clone()
    {
    }

    static public function _print(){
        print_r(self::$_settings);
        echo "<br /><br />";
        print_r(self::$_objects);
    }
}