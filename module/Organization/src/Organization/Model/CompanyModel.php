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

class CompanyModel implements ServiceLocatorAwareInterface
{
    public function __construct()
    {

    }

    protected $serviceLocator;

    public function returnCompanies($org_id,$number='30', $page='1') {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->find($org_id);
        $com=array();
        foreach ($qb as $cur) {
            $arr=array('uuid'=>$cur->getUUID(),'description'=>$cur->getDescription(), 'type'=>$cur->getType(), 'name'=>$cur->getName());
            array_push($org,$arr);
        }
        //die(var_dump($org));
        return $com;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function createCompany($post,$org_id) {
        $uuid_gen=new UuidGenerator();
        if(!$uuid_gen->isValid($org_id)) return false;
        if(!empty($post->csrf)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $com_item=$post->company;
            $com = new Company($org_id);

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

}