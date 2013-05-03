<?php
namespace Ticket;

return array(
    'controllers' => array(
        'invokables' => array(
            'Ticket\Controller\Ticket' => 'Ticket\Controller\TicketController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'ticket' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ticket[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-z0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ticket\Controller\Ticket',
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
            'ticket' => __DIR__ . '/../view',
        ),
    ),
);