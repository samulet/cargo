<?php
namespace Organization;

use Organization\Entity\Organization;
use Organization\Model\OrganizationModel;
use Organization\Model\CompanyModel;
use Organization\Model\CompanyUserModel;
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
        error_reporting(E_ALL | E_STRICT) ;
        ini_set('display_errors', 'On');

        return array(
            'factories' => array(
                'Organization\Model\OrganizationModel' =>  function($sm) {
                    $org = new OrganizationModel();
                    return $org;
                },
                'Organization\Model\CompanyModel' =>  function($sm) {
                    $com = new CompanyModel();
                    return $com;
                },
                'Organization\Model\CompanyUserModel' =>  function($sm) {
                    $comus = new CompanyUserModel();
                    return $comus;
                },
            ),
        );
    }

}