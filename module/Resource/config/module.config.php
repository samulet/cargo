<?php
namespace Resource;

return array(
    'controllers' => array(
        'invokables' => array(
            'Resource\Controller\Resource' => 'Resource\Controller\ResourceController',
            'Resource\Controller\Vehicle' => 'Resource\Controller\VehicleController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'resource' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/resources[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Resource\Controller\Resource',
                        'action' => 'index',
                    ),
                ),
            ),
            'vehicle' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/vehicles[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Resource\Controller\Vehicle',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'resource', 'roles' => array('user')),
                array('route' => 'vehicle', 'roles' => array('user')),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'odm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'resource' => __DIR__ . '/../view',
        ),
    ),
);