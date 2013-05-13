<?php
namespace Auction;


use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Auction\Model\AuctionModel;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

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
                'Auction\Model\AuctionModel' => function ($sm) {
                    $auc = new AuctionModel();
                    return $auc;
                },
            ),
        );
    }

}