<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Account\Model;

use Account\Entity\Account;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;

class AccountModel implements ServiceLocatorAwareInterface
{
    protected $companyModel;
    protected $companyUserModel;

    public function __construct()
    {

    }

    protected $serviceLocator;

    public function getOrgIdByUUID($accUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->getRepository('Account\Entity\Account')->findOneBy(array('uuid' => $accUuid));
        return $qb->getId();
    }

    public function getComIdByUUID($accUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->getRepository('Account\Entity\Company')->findOneBy(array('uuid' => $accUuid));
        return $qb->getId();
    }

    public function returnAccounts($orgId, $number = '30', $page = '1')
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $org_obj = $objectManager->getRepository('Account\Entity\Account')->getMyAvailableAccount($orgId);
        if (empty($org_obj)) {
            return null;
        }
        foreach ($org_obj as $org_ob) {
            $acc = get_object_vars($org_ob);
            break;
        }
        if (empty($acc)) {
            return null;
        }
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgId);
        $orgs = array();
        array_push($orgs, array('org' => $acc, 'com' => $com));
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

    public function createAccount($post, $user_id, $accId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $propArray = get_object_vars($post);

        if (!empty($accId)) {
            $acc = $objectManager->getRepository('Account\Entity\Account')->findOneBy(
                array('id' => new \MongoId($accId))
            );
        }
        else {
            $acc = new Account($user_id);
        }
        $acc->setName($propArray['name']);
        $acc->lastItemNumber = 0;
        $acc->setActivated(1);
        $accUuid = $acc->getUUID();
        $objectManager->persist($acc);
        $objectManager->flush();


        $accId = $this->getOrgIdByUUID($accUuid);

        $comUserModel = $this->getCompanyUserModel();
        $comUserModel->addUserToCompany($user_id, $accId, 'admin');

        $comUserModel->addOrgAndCompanyToUser(array('currentAcc' => $accId), $user_id);
        return true;
    }

    public function increaseLastItemNumber($orgId, $lastItemNumber)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Account\Entity\Account')->createQueryBuilder()

            ->findAndUpdate()
            ->field('id')->equals(new \MongoId($orgId))
            ->field('lastItemNumber')->set($lastItemNumber)
            ->getQuery()
            ->execute();
    }

    public function getAccount($id)
    {
        if (empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $acc = $objectManager->getRepository('Account\Entity\Account')->findOneBy(array('uuid' => $id));
        if (empty($acc)) {
            $acc = $objectManager->getRepository('Account\Entity\Account')->findOneBy(array('id' => new \MongoId($id)));
        }
        if (empty($acc)) {
            return null;
        }

        $user = $objectManager->find('User\Entity\User', $acc->getOwnerId());

        if (empty($user)) {
            return null;
        }
        return get_object_vars($acc);
    }

    public function addIntNumber()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $orgs = $objectManager->getRepository('Account\Entity\Account')->createQueryBuilder()
            ->field('lastItemNumber')->equals(null)
            ->getQuery()
            ->execute()->toArray();
        $orgs['lastOrg']['id'] = null;
        foreach ($orgs as $acc) {

            if (!empty($acc->id)) {
                $id = new \MongoId($acc->id);

            } else {
                $id = $acc['id'];
            }
            $lastItemNumber = 1;
            if (!empty($id)) {
                $tickets = $objectManager->getRepository('Ticket\Entity\Ticket')->createQueryBuilder()
                    ->field('ownerAccId')->equals($id)
                    ->field('numberInt')->equals(null)
                    ->getQuery()
                    ->execute();
            } else {
                $tickets = $objectManager->getRepository('Ticket\Entity\Ticket')->createQueryBuilder()
                    ->field('numberInt')->equals(null)
                    ->getQuery()
                    ->execute();
            }
            foreach ($tickets as $ticket) {

                $objectManager->getRepository('Ticket\Entity\Ticket')->createQueryBuilder()
                    ->findAndUpdate()
                    ->field('id')->equals(new \MongoId($ticket->id))
                    ->field('numberInt')->set($lastItemNumber)
                    ->getQuery()
                    ->execute();
                $lastItemNumber++;
            }
            if (!empty($acc->id)) {
                //$this->increaseLastItemNumber($id,$lastItemNumber);
            }

        }
    }

    public function getCompanyModel()
    {

        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Account\Model\CompanyModel');
        }
        return $this->companyModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function deleteAccount($accId)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $qb = $objectManager->getRepository('Account\Entity\Account')->find(new \MongoId($accId));
        if (!$qb) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $accId);
        }
        $objectManager->remove($qb);
        $objectManager->flush();

        $qb2 = $objectManager->createQueryBuilder('Account\Entity\CompanyUser');
        $qb2->remove()->field('orgId')->equals(new \MongoId($accId))->getQuery()
            ->execute();

        $qb3 = $objectManager->getRepository('Account\Entity\Company')->findBy(
            array('ownerAccId' => new \MongoId($accId))
        );
        if (!$qb3) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $accId);
        }
        $objectManager->remove($qb3);
        $objectManager->flush();

        $qb4 = $objectManager->getRepository('Resource\Entity\Resource')->findBy(
            array('ownerAccId' => new \MongoId($accId))
        );
        if (!$qb4) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $accId);
        }
        $objectManager->remove($qb4);
        $objectManager->flush();

        $qb5 = $objectManager->getRepository('Ticket\Entity\Ticket')->findBy(
            array('ownerAccId' => new \MongoId($accId))
        );
        if (!$qb5) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $accId);
        }
        $objectManager->remove($qb5);
        $objectManager->flush();

    }

    public function getOrgByUserId($userId)
    {

    }

    public function addBootstrap3Class(&$form)
    {

        foreach ($form as $el) {
            $attr = $el->getAttributes();
            if (!empty($attr['type'])) {
                if (($attr['type'] != 'checkbox') && ($attr['type'] != 'multi_checkbox')) {
                    $el->setAttributes(array('class' => 'form-control'));
                }
            }

        }
    }

}