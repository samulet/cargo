<?php

define('REQUEST_MICROTIME', microtime(true)); // for ZendDeveloperTools

chdir(dirname(__DIR__));
error_reporting(E_ALL | E_STRICT) ;
ini_set('display_errors', 'On');
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
