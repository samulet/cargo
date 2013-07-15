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
                        'type' =>'[a-zA-Z][a-zA-Z0-9_-]*',
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
                        'type' =>'[a-zA-Z][a-zA-Z0-9_-]*',
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
                array('controller' => 'Resource\Controller\Resource','action'=> array('index','list','delete', 'addResource', 'copy'),'roles' => array('user','admin')),
                array('controller' => 'Resource\Controller\Resource','action'=> array('my','add','edit'),'roles' => array('carrier','admin')),
                array('controller' => 'Resource\Controller\Resource','action'=> array('search'),'roles' => array('customer','admin')),

                array('controller' => 'Resource\Controller\Vehicle', 'action' => array('index'), 'roles' => array('admin')),
                array('controller' => 'Resource\Controller\Vehicle', 'action' => array('my','add', 'edit', 'list', 'delete', 'addVehicle', 'copy'), 'roles' => array('carrier','admin')),
            ),
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