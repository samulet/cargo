<?php
namespace Account;

use Account\Entity\Account;
use Account\Model\AccountModel;
use Account\Model\CompanyModel;
use Account\Model\CompanyUserModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
                'Account\Model\AccountModel' => function ($sm) {
                    $org = new AccountModel();
                    return $org;
                },
                'Account\Model\CompanyModel' => function ($sm) {
                    $com = new CompanyModel();
                    return $com;
                },
                'Account\Model\CompanyUserModel' => function ($sm) {
                    $comus = new CompanyUserModel();
                    return $comus;
                },
            ),
        );
    }

}