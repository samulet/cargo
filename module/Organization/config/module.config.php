<?php
namespace Organization;

return array(
    'controllers' => array(
        'invokables' => array(
            'Organization\Controller\Organization' => 'Organization\Controller\OrganizationController',
            'Organization\Controller\Company' => 'Organization\Controller\CompanyController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'organization' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/organization[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Organization\Controller\Organization',
                        'action'     => 'index',
                    ),
                ),
            ),
            'company' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/organization[/:id]/company[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Organization\Controller\Company',
                        'action'     => 'index',
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
            'album' => __DIR__ . '/../view',
        ),
    ),
);