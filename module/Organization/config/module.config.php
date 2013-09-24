<?php
namespace Organization;

return array(
    'controllers' => array(
        'invokables' => array(
            'Organization\Controller\Organization' => 'Organization\Controller\OrganizationController',
            'Organization\Controller\Company' => 'Organization\Controller\CompanyController',
            'Organization\Controller\CompanyUser' => 'Organization\Controller\CompanyUserController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'account' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/account[/:action][/:id][/:param]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                        'param' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                    'route' => '/account[/:org_id]/company[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
                    'route' => '/account/user[/:org_id][/:action][/:param][/:comId]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'org_id' => '[a-z0-9]*',
                        'param' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'comId' => '[a-z0-9]*',
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
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Organization\Controller\Organization','action'=>array('index','add', 'edit','list','delete','addIntNumber','createOrganization'), 'roles' => array('admin','orgAdmin')),
                array('controller' => 'Organization\Controller\Organization','action'=>array('addAccount','add','createOrganization','choiceOrgAndCompany','setAccAndCom'), 'roles' => array('user','inner')),
                array('controller' => 'Organization\Controller\Company','roles' => array('admin','orgAdmin')),
                array('controller' => 'Organization\Controller\CompanyUser','roles' => array('admin','orgAdmin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'account', 'roles' => array('admin','orgAdmin')),
                array('route' => 'company', 'roles' => array('admin','orgAdmin')),
                array('route' => 'company_user', 'roles' => array('admin','orgAdmin')),
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
            'account' => __DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        //'Organization\Controller\CompanyUser' => 'layout/admin',
    ),
);