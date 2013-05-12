<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
 error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
