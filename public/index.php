<?php
    error_reporting(0);
    ini_set('display_errors','on');
  // error_reporting(E_ALL);

chdir(dirname(__DIR__));
//error_reporting(E_ALL);
//ini_set('display_errors','on');
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

if($_SERVER['HTTP_HOST'] =='platform.farmlink.cn'){

define('ADMIN_USERNAME','fl'); 	// Admin Username
define('ADMIN_PASSWORD','kaimenba');  	// Admin Password
define('DATE_FORMAT','Y/m/d H:i:s');

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
    Header("WWW-Authenticate: Basic realm=\" Login\"");
    Header("HTTP/1.0 401 Unauthorized");

    echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
    exit;
}
}
    define('ADMIN_USERNAME','api'); 	// Admin Username
    define('ADMIN_PASSWORD','zhimakaimen');  	// Admin Password
    define('DATE_FORMAT','Y/m/d H:i:s');

// Setup autoloading
require '../vendor/autoload.php';
ini_set("max_execution_time", "1800");
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
