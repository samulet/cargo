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

    }

    protected $serviceLocator;

    public function returnOrganizations($number='30', $page='1') {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Organization\Entity\Organization')->eagerCursor(true);
        $query = $qb->getQuery();
        $cursor = $query->execute();
        $org=array();
        foreach ($cursor as $cur) {
            $arr=array('uuid'=>$cur->getUUID(),'description'=>$cur->getDescription(), 'orgType'=>$cur->getOrgType(), 'orgName'=>$cur->getOrgName());
            array_push($org,$arr);
        }
        //die(var_dump($org));
        return $org;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function createOrganization($post,$user_id) {
        if(!empty($post->csrf)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $org_item=$post->organization;
            $org = new Organization($user_id);
            $org->setDescription($org_item['description']);
            $org->setOrgName($org_item['orgName']);
            $org->setOrgType($org_item['orgType']);
            $org->setActivated(1);
            $objectManager->persist($org);
            $objectManager->flush();
            return true;
        } else return false;
    }
}