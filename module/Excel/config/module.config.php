<?php
namespace Excel;

return array(
    'controllers' => array(
        'invokables' => array(
            'Excel\Controller\Excel' => 'Excel\Controller\ExcelController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'excel' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/excels[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Excel\Controller\Excel',
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
            'excel' => __DIR__ . '/../view',
        ),
    ),
);
