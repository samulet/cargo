<?php
namespace Organization;

return array(
    'controllers' => array(
        'invokables' => array(
            'Organization\Controller\Organization' => 'Organization\Controller\OrganizationController',
            'Organization\Controller\Company' => 'Organization\Controller\CompanyController',
            'Organization\Controller\CompanyUser' => 'Organization\Controller\CompanyUserController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'organization' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/organization[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Organization\Controller\Organization',
                        'action' => 'index',
                    ),
                ),
            ),
            'company' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/organization[/:org_id]/company[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                        'org_id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Organization\Controller\Company',
                        'action' => 'index',
                    ),
                ),
            ),
            'company_user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/organization/user[/:org_id][/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'org_id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Organization\Controller\CompanyUser',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'organization', 'roles' => array('user')),
                array('route' => 'company', 'roles' => array('user')),
                array('route' => 'company_user', 'roles' => array('user')),
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
            'organization' => __DIR__ . '/../view',
        ),
    ),
);