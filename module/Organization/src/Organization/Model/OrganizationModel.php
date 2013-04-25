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
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;

class OrganizationModel implements ServiceLocatorAwareInterface
{
    protected $companyModel;
    public function __construct()
    {

    }

    protected $serviceLocator;

    public function getOrgIdByUUID($org_uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $org_uuid));
        return $qb->getId();
    }


    public function returnOrganizations($number='30', $page='1') {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Organization\Entity\Organization')->eagerCursor(true);
        $query = $qb->getQuery();
        $cursor = $query->execute();
        $orgs=array();
        foreach ($cursor as $cur) {
            $org=array('uuid'=>$cur->getUUID(),'description'=>$cur->getDescription(), 'type'=>$cur->getType(),
                'name'=>$cur->getName());
            $comModel=$this->getCompanyModel();
            $com=$comModel->returnCompanies($cur->getId());
            array_push($orgs,array('org'=>$org,'com'=>$com));
        }
       // die(var_dump($orgs));
        return $orgs;
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
            $org->setName($org_item['name']);
            $org->setType($org_item['type']);
            $org->setActivated(1);
            $objectManager->persist($org);
            $objectManager->flush();
            return true;
        } else return false;
    }
    public function getOrganization($id) {
        $uuid_gen=new UuidGenerator();
        if(!$uuid_gen->isValid($id)) return false;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $org=$objectManager->getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $id));
        if(empty($org)) return false;
        $user=$objectManager->find('User\Entity\User', $org->getOwnerId());
        //die(var_dump($user->getDisplayName()));
        if(empty($user)) return false;
        return array('uuid'=>$org->getUUID(),'description'=>$org->getDescription(), 'type'=>$org->getType(), 'name'=>$org->getName(), 'orgOwner'=>$user->getDisplayName());
    }
    public function getCompanyModel()
    {
        error_reporting(E_ALL | E_STRICT) ;
        ini_set('display_errors', 'On');
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }
}