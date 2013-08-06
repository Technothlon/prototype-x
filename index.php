<?php
session_start();
define('xDEC', true);
require_once(dirname(__FILE__).'/config.inc.php');
require_once(BASE.'bootstrap.php');
get('Pages')->get();
$end = time();