<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Model;

use Organization\Entity\Company;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class CompanyModel implements ServiceLocatorAwareInterface
{


    public function __construct()
    {

    }

    protected $serviceLocator;

    public function returnCompanies($org_id, $number = '30', $page = '1')
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Organization\Entity\Company')->field('ownerOrgId')->equals(
            new \MongoId($org_id)
        )->eagerCursor(true);
        $query = $qb->getQuery();
        $cursor = $query->execute();

        $com = array();
        foreach ($cursor as $cur) {
            $arr = get_object_vars($cur);
            unset($arr['created']);
            unset($arr['updated']);
            array_push($com, $arr);
        }
        return $com;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function createCompany($post, $org_id, $com_id)
    {
        if (!empty($post->csrf)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $com_item = $post->company;
            if (!empty($com_id)) $com = $objectManager->getRepository('Organization\Entity\Company')->find($com_id);
            else {
                $com = new Company($org_id);
            }


            $com->setDescription($com_item['description']);
            $com->setName($com_item['name']);
            $com->setType($com_item['type']);
            $com->setRequisites($com_item['requisites']);

            $com->setAddressFact($com_item['addressFact']);
            $com->setGeneralManager($com_item['generalManager']);
            $com->setTelephone($com_item['telephone']);
            $com->setEmail($com_item['email']);

            $com->setActivated(1);

            $objectManager->persist($com);
            $objectManager->flush();
            return true;
        } else return false;
    }

    public function getCompany($id)
    {
        $uuid_gen = new UuidGenerator();
        if (!$uuid_gen->isValid($id)) return false;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $org = $objectManager->
            getRepository('Organization\Entity\Organization')->findOneBy(array('uuid' => $id));
        if (empty($org)) return false;
        $user = $objectManager->find('User\Entity\User', $org->getOwnerId());
        if (empty($user)) return false;
        return array(
            'uuid' => $org->getUUID(),
            'description' => $org->getDescription(),
            'type' => $org->getType(),
            'name' => $org->getName(),
            'orgOwner' => $user->getDisplayName()
        );
    }

    public function getCompanyIdByUUID($com_uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com_id = $objectManager->getRepository('Organization\Entity\Company')->findOneBy(array('uuid' => $com_uuid));
        return $com_id->id;
    }

    public function returnCompany($com_id)
    {
        ini_set('display_errors', 'Off');
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com_obj = $objectManager->getRepository('Organization\Entity\Company')->find($com_id);

        $com = get_object_vars($com_obj);
        unset($com['created']);
        unset($com['updated']);
        return $com;
    }

    public function deleteCompany($com_id)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $qb = $objectManager->createQueryBuilder('Organization\Entity\Company');
        $qb->remove()->field('id')->equals(new \MongoId($com_id))->getQuery()
            ->execute();
    }
}