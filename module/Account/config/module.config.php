<?php
namespace Account;

return array(
    'controllers' => array(
        'invokables' => array(
            'Account\Controller\Account' => 'Account\Controller\AccountController',
            'Account\Controller\Company' => 'Account\Controller\CompanyController',
            'Account\Controller\CompanyUser' => 'Account\Controller\CompanyUserController'
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
                        'controller' => 'Account\Controller\Account',
                        'action' => 'index',
                    ),
                ),
            ),
            'company' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/account[/:org_id]/company[/:action][/:id][/:comId]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',

                        'org_id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'comId' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Account\Controller\Company',
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
                        'controller' => 'Account\Controller\CompanyUser',
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
                    'controller' => 'Account\Controller\Account',
                    'action' => array('index', 'add', 'edit', 'list', 'delete', 'addIntNumber', 'createAccount'),
                    'roles' => array('admin', 'accAdmin')
                ),
                array(
                    'controller' => 'Account\Controller\Account',
                    'action' => array('addAccount', 'add', 'createAccount', 'choiceOrgAndCompany', 'setAccAndCom'),
                    'roles' => array('user', 'inner')
                ),
                array('controller' => 'Account\Controller\Company', 'roles' => array('admin', 'accAdmin')),
                array('controller' => 'Account\Controller\CompanyUser', 'roles' => array('admin', 'accAdmin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'account', 'roles' => array('admin', 'accAdmin')),
                array('route' => 'company', 'roles' => array('admin', 'accAdmin')),
                array('route' => 'company_user', 'roles' => array('admin', 'accAdmin')),
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
    ),
);