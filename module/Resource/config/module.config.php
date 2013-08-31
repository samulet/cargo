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
                    'route' => '/resources[/:action][/:id][/:type]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                        'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                    'route' => '/vehicles[/:action][/:id][/:type]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                        'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
            'BjyAuthorize\Guard\Controller' => array(
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('index'),
                    'roles' => array('admin','forwarder', 'customer')
                ),
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('add'),
                    'roles' => array('forwarder','orgAdmin', 'customer', 'carrier', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('myAcc'),
                    'roles' => array('forwarder','orgAdmin', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('search', 'getResults'),
                    'roles' => array('forwarder', 'customer', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('my'),
                    'roles' => array('forwarder', 'carrier', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Resource',
                    'action' => array('delete'),
                    'roles' => array('forwarder', 'carrier', 'admin','orgAdmin')
                ),
                array(
                    'controller' => 'Resource\Controller\Vehicle',
                    'action' => array('add'),
                    'roles' => array('forwarder','orgAdmin', 'customer', 'carrier', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Vehicle',
                    'action' => array('my', 'error'),
                    'roles' => array('forwarder', 'admin', 'carrier')
                ),
                array(
                    'controller' => 'Resource\Controller\Vehicle',
                    'action' => array('delete'),
                    'roles' => array('forwarder', 'admin', 'carrier','orgAdmin')
                ),
                array(
                    'controller' => 'Resource\Controller\Vehicle',
                    'action' => array('index'),
                    'roles' => array('forwarder', 'customer', 'admin')
                ),
                array(
                    'controller' => 'Resource\Controller\Vehicle',
                    'action' => array('myAcc'),
                    'roles' => array('forwarder','orgAdmin', 'admin')
                ),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'resource', 'roles' => array('inner')),
                array('route' => 'vehicle', 'roles' => array('inner')),
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