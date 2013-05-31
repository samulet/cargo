<?php
namespace Ticket;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Ticket\Model\TicketModel;
use Ticket\Model\CargoModel;
class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),

        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {

        return array(
            'factories' => array(
                'Ticket\Model\TicketModel' => function ($sm) {
                    $res = new TicketModel();
                    return $res;
                },
                'Ticket\Model\CargoModel' => function ($sm) {
                    $veh = new CargoModel();
                    return $veh;
                },
            ),
        );
    }

}