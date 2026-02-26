<?php
// database
$_DB = array(
	array(
		'SERVER'   => 'localhost',
		'USERNAME' => 'root',
		'PASSWORD' => '1',
		'DATABASE' => 'kopdar'
		)
	);
date_default_timezone_set('Asia/Jakarta');
//system...
define('_URI', '/kopdargh/');
define('_URL', 'http://'.@$_SERVER['HTTP_HOST']._URI);
define('_ROOT', dirname(__FILE__).'/');
// define('_MST', '/var/www/html/master/');
define('_SALT', '91306f423f837d323b93ddceebfe97a0');
define('_SEO', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

