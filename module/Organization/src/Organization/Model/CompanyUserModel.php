<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/25/13
 * Time: 5:43 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Model;

use Organization\Entity\CompanyUser;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class CompanyUserModel implements ServiceLocatorAwareInterface
{
    public function __construct()
    {

    }

    protected $serviceLocator;

    public function addUserToOrg($post, $org_id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if(empty($post['email'])) $user_id=$post;
        else $user_id=$this->findUserByEmail($post['email']);
        if($user_id) $comUser= new CompanyUser($org_id,$user_id);
        else return false;
        $objectManager->persist($comUser);
        $objectManager->flush();
        return true;
    }

    public function findUserByEmail($email) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user_id=$objectManager->
            getRepository('Organization\Entity\Organization')->findOneBy(array('email' => $email));
        if(empty($user_id)) return false;
        else return $user_id->getId();
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function getOrgIdByUserId($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userObject=$objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('userId' => new \MongoId($userId)));
        return $userObject->userId;
    }

}