<?php
//Directories
define('BASE', dirname(__FILE__).'/');
define('CORE', BASE.'core/');
define('CONTENT', BASE.'content/');
define('EXT', BASE.'extensions/');
define('THEME', BASE.'theme/');

//URL
define('DIR', '/xDec');

//DATABASE
define('DATABASE_DRIVER', 'MySQL');
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','xEffect');
define('DB_NAME','man');
define('DB_PORT',3306);
define('DB_ENABLED', true);

//DEV mode
define('LOG_DB_QUERY', true);

//VIEW
define('HOME_CLASS','home');
define('SITE_NAME', 'Manas');