<?php
return array(
    'doctrine' => array(
        'driver' => array(
             'zfcuser_driver' =>array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ .'/../../module/User/src/User/Entity')
            ),

            'odm_default' =>array(
                'drivers' => array(
                    'ZfcUser\Entity'  =>  'zfcuser_driver',
                    'User\Entity'  =>  'zfcuser_driver',
                )
            ),
        ),
        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array(
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                ),
            ),
        ),
    ),
);
