<?php

define('REQUEST_MICROTIME', microtime(true)); // for ZendDeveloperTools

chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
