<?php
namespace Resource;

use Zend\Db\ResultSet\ResultSet;
use Resource\Model\ResourceModel;
use Resource\Model\VehicleModel;

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
                'Resource\Model\ResourceModel' => function ($sm) {
                    $res = new ResourceModel();
                    return $res;
                },
                'Resource\Model\VehicleModel' => function ($sm) {
                    $veh = new VehicleModel();
                    return $veh;
                },
            ),
        );
    }

}