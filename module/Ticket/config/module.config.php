<?php
namespace Ticket;

return array(
    'controllers' => array(
        'invokables' => array(
            'Ticket\Controller\Ticket' => 'Ticket\Controller\TicketController',
            'Ticket\Controller\Cargo' => 'Ticket\Controller\CargoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'ticket' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/tickets[/:action][/:id][/:type]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                        'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ticket\Controller\Ticket',
                        'action' => 'index',
                    ),
                ),
            ),
            'cargo' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cargos[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ticket\Controller\Cargo',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Ticket\Controller\Ticket','action'=> array('index','search','list','delete', 'addTicket', 'getResults','copy'),'roles' => array('user','admin')),
                array('controller' => 'Ticket\Controller\Ticket','action'=> array('search', 'getResults'),'roles' => array('carrier','admin')),
                array('controller' => 'Ticket\Controller\Ticket','action'=> array('my','add','edit'),'roles' => array('customer','admin')),

                array('controller' => 'Ticket\Controller\Cargo', 'action' => array('index'), 'roles' => array('user','admin')),
                array('controller' => 'Ticket\Controller\Cargo', 'action' => array('my','add', 'edit', 'list', 'delete', 'addCargo', 'copy'), 'roles' => array('customer','admin')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route'=> 'ticket','roles' => array('user')),
                array('route' => 'cargo', 'roles' => array('user')),
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
            'ticket' => __DIR__ . '/../view',
        ),
    ),
);