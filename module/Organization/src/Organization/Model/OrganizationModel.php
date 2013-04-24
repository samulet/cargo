<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Organization\Model;

use Organization\Entity\Organization;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class OrganizationModel implements ServiceLocatorAwareInterface
{
    public function __construct()
    {
       // $this->serviceLocator=$this->getServiceLocator();
        die(var_dump($this->serviceLocator));
        $this->objectManager=$this->serviceLocator->get('doctrine.documentmanager.odm_default');
    }
    protected $objectManager;
    protected $serviceLocator;

    public function returnOrganizations($number='30', $page='1') {
        $qb = $this->objectManager->objectManager->createQueryBuilder('Organization\Entity\Organization')
            ->eagerCursor(true);
        $query = $qb->getQuery();
        $cursor = $query->execute();
        $org=array();
        foreach ($cursor as $cur) {
            $arr=array('uuid'=>$cur->getUUID(),'description'=>$cur->getDescription(), 'orgType'=>$cur->getOrgType(), 'orgName'=>$cur->getOrgName());
            array_push($org,$arr);
        }
        return $org;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}