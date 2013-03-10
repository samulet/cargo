<?php
return array(
    'doctrine' => array(

        'configuration' => array(
            'odm_default' => array(
                'metadata_cache'     => 'array',
                'driver'             => 'odm_default',
                'generate_proxies'   => true,
                'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
                'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',
                'generate_hydrators' => true,
                'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
                'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
                'default_db'         => 'cargo',
                'filters'            => array(),
                'logger'             => 'DoctrineMongoODMModule\Logging\DebugStack',
            )
        ),

        'documentmanager' => array(
            'odm_default' => array(
                'connection'    => 'odm_default',
                'configuration' => 'odm_default',
                'eventmanager' => 'odm_default'
            )
        ),

        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array()
            )
        ),
    ),
);