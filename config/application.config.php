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
        'BjyAuthorize',
        'User',
        'ZfcAdmin',
        'Application',
        'User',
        'Organization',
        'Resource',
        'Ticket',
        'Auction',
        'AddList',
        'Interaction',
        'Account',
        'Notification'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
    ),

);
