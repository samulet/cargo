<?php
return array(
    'modules' => array(
        'Yassa\Rollbar',
        'ZendDeveloperTools',
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'ScnSocialAuth',
        'ScnSocialAuthDoctrineMongoODM',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineMongoODM',
        'BjyAuthorize',
        'User',
        'ZfcAdmin',
        'EdpModuleLayouts',
        'Application',
        'User',
        'Account',
        'Resource',
        'Ticket',

        'AddList',
        'Interaction',
        'Notification',
        'Excel',
        'QueryBuilder'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
    ),

);
