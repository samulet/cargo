<?php
return array(
    'modules' => array(
        'Whoops',
        'Yassa\Rollbar',
        'ZendDeveloperTools',
        'Application',
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineMongoODM',
        'User',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php'),
        // 'config_cache_enabled' => true,
        // 'config_cache_key' => 'common',
        // 'module_map_cache_enabled' => true,
        // 'module_map_cache_key' => 'common',
        // 'cache_dir' => './data/cache',
    ),

);
