<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'bjyauthorize' => array(
        // default role for unauthenticated users
        'default_role'          => 'guest',

        // default role for authenticated users (if using the
        // 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider' identity provider)
        'authenticated_role'    => 'user',

        // identity provider service name
        'identity_provider'     => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        // Role providers to be used to load all available roles into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'role_providers' => array(
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user' => array(
                    'children' => array(
                        'stuff' => array(
                            'children' => array(
                                'forwarder',
                                'carrier',
                                'customer',
                                'owner' => array(
                                    'children' => array(
                                        'admin' => array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),

        // Resource providers to be used to load all available resources into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'resource_providers'    => array(),

        // Rule providers to be used to load all available rules into Zend\Permissions\Acl\Acl
        // Keys are the provider service names, values are the options to be passed to the provider
        'rule_providers'        => array(),

        // Guard listeners to be attached to the application event manager
        'guards'                => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
                // You can also specify an array of actions or an array of controllers (or both)
                // allow "guest" and "admin" to access actions "list" and "manage" on these "index",
                // "static" and "console" controllers
                array(
                    'controller' => array('index', 'static', 'console'),
                    'action' => array('list', 'manage'),
                    'roles' => array('guest', 'admin')
                ),

                array('controller' => 'zfcuser', 'roles' => array()),
               // array('controller' => 'zfcadmin', 'roles' => array('admin')),
                // Below is the default index action used by the ZendSkeletonApplication
                 array('controller' => 'Application\Controller\Index', 'roles' => array('guest', 'user')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'zfcuser', 'roles' => array('user')),
                array('route' => 'zfcuser/logout', 'roles' => array('user')),
                array('route' => 'zfcuser/login', 'roles' => array('guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest')),
                // Below is the default index action used by the ZendSkeletonApplication
                array('route' => 'home', 'roles' => array('guest', 'user')),
                array('route' => 'dashboard', 'roles' => array('user')),
                array('route' => 'account', 'roles' => array('user')),
            ),
        ),

        // strategy service name for the strategy listener to be used when permission-related errors are detected
        'unauthorized_strategy' => 'BjyAuthorize\View\RedirectionStrategy',

        // Template name for the unauthorized strategy
        'template'              => 'error/403',
    ),


    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'bjy_authorize_role_collector' => 'BjyAuthorize\\Collector\\RoleCollector',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'bjy_authorize_role_collector' => 'zend-developer-tools/toolbar/bjy-authorize-role',
            ),
        ),
    ),
);
