<?php
namespace Interaction;

return array(
    'controllers' => array(
        'invokables' => array(
            'Interaction\Controller\Interaction' => 'Interaction\Controller\InteractionController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'interaction' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/interactions[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Interaction\Controller\Interaction',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Interaction\Controller\Interaction','roles' => array('inner','admin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'interaction', 'roles' => array('inner','admin')),
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
            'interaction' => __DIR__ . '/../view',
        ),
    ),
);