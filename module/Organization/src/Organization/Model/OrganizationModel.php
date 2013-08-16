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

    public function getOrgIdByUUID($org_uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $org_uuid));
        return $qb->getId();
    }


    public function returnOrganizations($user_id, $number = '30', $page = '1')
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user_id = new \MongoId($user_id);
        $orgOfUser = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(
            array('userId' => new \MongoId($user_id))
        );
        $orgs = array();
        if (empty($orgOfUser)) {
            return null;
        }
        $org_id = $orgOfUser->getOrgId();
        $org_obj = $objectManager->getRepository('Organization\Entity\Organization')->getMyAvailableOrganization($org_id);

          //  find(new \MongoId($org_id));
        if (empty($org_obj)) {
            return null;
        }
        foreach($org_obj as $org_ob) {
            $org = get_object_vars($org_ob);
            break;
        }


        unset($org['created']);
        unset($org['updated']);
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($org_id);

        array_push($orgs, array('org' => $org, 'com' => $com));
        return $orgs;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function createOrganization($post, $user_id, $org_id)
    {
        if (!empty($post->csrf)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $org_item = $post->organization;
            if (!empty($org_id)) $org = $objectManager->getRepository('Organization\Entity\Organization')->findOneBy(
                array('id' => new \MongoId($org_id))
            );
            else {
                $org = new Organization($user_id);
            }
            $org->setDescription($org_item['description']);
            $org->setName($org_item['name']);
            $org->setType($org_item['type']);
            $org->lastItemNumber=0;
            $org->setActivated(1);
            $org_uuid = $org->getUUID();
            $objectManager->persist($org);
            $objectManager->flush();


            $org_id = $this->getOrgIdByUUID($org_uuid);

            $comUserModel = $this->getCompanyUserModel();
            $comUserModel->addUserToOrg($user_id, $org_id);

            return true;
        } else return false;
    }
    public function increaseLastItemNumber($orgId,$lastItemNumber) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Organization\Entity\Organization')->createQueryBuilder()

            ->findAndUpdate()
            ->field('id')->equals(new \MongoId($orgId))
            ->field('lastItemNumber')->set($lastItemNumber)
            ->getQuery()
            ->execute();
    }
    public function getOrganization($id)
    {
        if(empty($id)) {
          return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $org = $objectManager->getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $id));
        if (empty($org)) {
            $org = $objectManager->getRepository('Organization\Entity\Organization')->find($id);
        }
        if (empty($org)) {
            return null;
        }

        $user = $objectManager->find('User\Entity\User', $org->getOwnerId());

        if (empty($user)) {
            return null;
        }
        return array(
            'uuid' => $org->getUUID(),
            'description' => $org->getDescription(),
            'type' => $org->getType(),
            'name' => $org->getName(),
            'orgOwner' => $user->getDisplayName(),
            'lastItemNumber' =>$org->lastItemNumber
        );
    }

    public function addIntNumber() {

    }

    public function getCompanyModel()
    {

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

    public function deleteOrganization($org_id)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $qb = $objectManager->getRepository('Organization\Entity\Organization')->find(new \MongoId($org_id));
        if (!$qb) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $org_id);
        }
        $objectManager->remove($qb);
        $objectManager->flush();

        $qb2 = $objectManager->createQueryBuilder('Organization\Entity\CompanyUser');
        $qb2->remove()->field('orgId')->equals(new \MongoId($org_id))->getQuery()
            ->execute();

        $qb3 = $objectManager->getRepository('Organization\Entity\Company')->findBy(array('ownerOrgId' => new \MongoId($org_id)));
        if (!$qb3) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $org_id);
        }
        $objectManager->remove($qb3);
        $objectManager->flush();

        $qb4 = $objectManager->getRepository('Resource\Entity\Resource')->findBy(array('ownerOrgId' => new \MongoId($org_id)));
        if (!$qb4) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $org_id);
        }
        $objectManager->remove($qb4);
        $objectManager->flush();

        $qb5 = $objectManager->getRepository('Ticket\Entity\Ticket')->findBy(array('ownerOrgId' => new \MongoId($org_id)));
        if (!$qb5) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $org_id);
        }
        $objectManager->remove($qb5);
        $objectManager->flush();

    }

    public function getOrgByUserId($userId) {

    }
    public function addBootstrap3Class(&$form) {

        foreach ($form->get('organization') as $el) {
            $attr=$el->getAttributes();
            if(!empty($attr['type'])) {
                if(($attr['type']!='checkbox')&&($attr['type']!='multi_checkbox')) {
                    $el->setAttributes(array( 'class' => 'form-control' ));
                }
            }

        }
    }

}