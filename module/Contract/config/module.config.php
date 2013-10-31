<?php
namespace Contract;

return array(
    'controllers' => array(
        'invokables' => array(
            'Contract\Controller\Contract' => 'Contract\Controller\ContractController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'contract' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/contracts[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Contract\Controller\Contract',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Contract\Controller\Contract', 'roles' => array('inner', 'admin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'contract', 'roles' => array('inner', 'admin')),
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
            'contract' => __DIR__ . '/../view',
        ),
    ),
);