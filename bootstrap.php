<?php
if(!defined('xDEC')) exit;
$core = array('Auth', 'Cache', 'Cookie', 'Database', 'Extensions', 'Logger', 'Mail', 'Pages', 'Router', 'Security');
$namespace = '';

require_once(CORE.'Registry.class.php');
require_once(CORE.'Function.php');
require_once(CORE . 'Page.php');
require_once(CORE.'Admin.php');
require_once(CORE.'Extension.php');

define('BASE_URL', (is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST']);
set('home_url', BASE_URL.DIR.'/');
error_reporting(E_ALL);
set_error_handler('error_handler', E_ALL);

foreach($core as $class){
    define(strtoupper($class),$class);
    require_once(CORE.$class.".class.php");
    $_class = $namespace.$class;
    set($class, new $_class());
}

get('Extensions')->load();
