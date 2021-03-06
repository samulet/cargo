<?php
namespace AddList;

return array(
    'controllers' => array(
        'invokables' => array(
            'AddList\Controller\AddList' => 'AddList\Controller\AddListController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'addList' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/addList[/:action][/:id][/:parent][/:global]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'parent' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'global' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'AddList\Controller\AddList',
                        'action' => 'index',
                    ),
                ),
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
            'addList' => __DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        'AddList' => 'layout/list',
    ),
);
