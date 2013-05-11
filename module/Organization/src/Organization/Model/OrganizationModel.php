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
    protected $companyUserModel;
    public function __construct()
    {

    }

    protected $serviceLocator;

    public function getOrgIdByUUID($org_uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $org_uuid));
        return $qb->getId();
    }


    public function returnOrganizations($user_id,$number='30', $page='1') {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user_id=new \MongoId($user_id);
        $orgOfUser=$objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('userId' => $user_id));
        $orgs=array();
       // foreach ($orgOfUser as $cur) {

        if(empty($orgOfUser)) return null;
        $org_id=$orgOfUser->getOrgId();
            $org_obj=$objectManager->getRepository('Organization\Entity\Organization')->find($org_id);
        //die(var_dump($org_obj));
        if(empty($org_obj)) return null;
            $org=get_object_vars($org_obj);
           // $org=array('uuid'=>$org_obj->getUUID(),'description'=>$org_obj->getDescription(), 'type'=>$org_obj->getType(),
               // 'name'=>$org_obj->getName());
            unset($org['created']);
            unset($org['updated']);
            $comModel=$this->getCompanyModel();
            $com=$comModel->returnCompanies($org_id);

            array_push($orgs,array('org'=>$org,'com'=>$com));
      //  }
        //die(var_dump($orgs));
        return $orgs;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function createOrganization($post,$user_id,$org_id) {
        if(!empty($post->csrf)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $org_item=$post->organization;
            if(!empty($org_id)) $org=$objectManager->getRepository('Organization\Entity\Organization')->find($org_id);
            else $org = new Organization($user_id);
            $org->setDescription($org_item['description']);
            $org->setName($org_item['name']);
            $org->setType($org_item['type']);
            $org->setActivated(1);
            $org_uuid=$org->getUUID();
            $objectManager->persist($org);
            $objectManager->flush();


            $org_id=$this->getOrgIdByUUID($org_uuid);

            $comUserModel=$this->getCompanyUserModel();
            $comUserModel->addUserToOrg($user_id, $org_id);

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
    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function deleteOrganization($org_id) {
      //  die(var_dump($org_id));
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
       // $objectManager->getRepository('Organization\Entity\Organization')->find($org_id)->remove();
        $qb = $objectManager->createQueryBuilder('Organization\Entity\Organization');
        $qb->remove()->field('id')->equals(new \MongoId($org_id)) ->getQuery()
            ->execute();
        $qb2 = $objectManager->createQueryBuilder('Organization\Entity\CompanyUser');
        $qb2->remove()->field('orgId')->equals(new \MongoId($org_id)) ->getQuery()
            ->execute();
        $qb3 = $objectManager->createQueryBuilder('Organization\Entity\Company');
        $qb3->remove()->field('ownerOrgId')->equals(new \MongoId($org_id)) ->getQuery()
            ->execute();
    }
}