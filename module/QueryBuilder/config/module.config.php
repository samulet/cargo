<?php
namespace QueryBuilder;

return array(
    'controllers' => array(
        'invokables' => array(
            'QueryBuilder\Controller\QueryBuilder' => 'QueryBuilder\Controller\QueryBuilderController',
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'QueryBuilder\Controller\QueryBuilder', 'roles' => array('inner', 'admin')),
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
            'queryBuilder' => __DIR__ . '/../view',
        ),
    ),
);