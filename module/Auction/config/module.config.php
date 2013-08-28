<?php
namespace Auction;

return array(
    'controllers' => array(
        'invokables' => array(
            'Auction\Controller\Auction' => 'Auction\Controller\AuctionController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'auction' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/auctions[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Auction\Controller\Auction',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Auction\Controller\Auction','roles' => array('inner','admin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'auction', 'roles' => array('inner')),
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
            'auction' => __DIR__ . '/../view',
        ),
    ),
);