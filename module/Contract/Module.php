<?php
namespace Contract;

use Zend\Db\ResultSet\ResultSet;
use Contract\Model\ContractModel;

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
                'Contract\Model\ContractModel' => function ($sm) {
                    $auc = new ContractModel();
                    return $auc;
                },
            ),
        );
    }

}