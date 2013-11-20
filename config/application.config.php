<?php
return array(
    'modules' => array(
        'Yassa\Rollbar',
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'ScnSocialAuth',
        'ScnSocialAuthDoctrineMongoODM',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineMongoODM',
        'Application',
        'User',
        'AuthToken',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
    ),

);
