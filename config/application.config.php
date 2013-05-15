<?php
return array(
    'modules' => array(
        'Yassa\Rollbar',
        'ZendDeveloperTools',
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineMongoODM',
        'User',
        'BjyAuthorize',
        'ZfcAdmin',
        'Application',
        'Organization',
        'Resource',
        'Ticket',
        'Auction'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php'),
    ),

);
