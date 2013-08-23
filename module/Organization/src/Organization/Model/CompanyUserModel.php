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


class CompanyUserModel implements ServiceLocatorAwareInterface
{
    public function __construct()
    {

    }

    protected $serviceLocator;
    protected $organizationModel;

    public function addUserToCompany($post, $org_id,$param)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (is_object($post)) {

            $post=get_object_vars($post);

            $user_id = $this->findUserByEmail($post['company_user']['email']);
        } else {
            $user_id = $post;

        }
        if ($user_id) {
            $comUser = new CompanyUser($org_id, $user_id,$param);
        } else {
            return false;
        }
        $objectManager->persist($comUser);
        $objectManager->flush();
        return true;
    }

    public function findUserByEmail($email)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user_id = $objectManager->getRepository('User\Entity\User')->findOneBy(array('email' => $email));
        if (empty($user_id)) {
            return false;
        } else {
            return $user_id->getId();
        }
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getAllUsersByComId($orgId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com=$objectManager->getRepository('Organization\Entity\Company')->findBy(array('ownerOrgId' => new \MongoId($orgId)));
        if(empty($com)) {
            return null;
        }
        $orgModel = $this->getOrganizationModel();
        $orgName=$orgModel->getOrganization($orgId);

        $result=array();
        foreach($com as $c) {
            $users = $objectManager->getRepository('Organization\Entity\CompanyUser')->findBy(array('companyId' => new \MongoId($c->id)));
            foreach($users as $userT) {
                $user=$objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userT->userId)));
                if(!empty($user)) {
                    $us=array('id'=>$user->getId(), 'username'=> $user->getUsername(), 'displayName'=>$user->getDisplayName(),'email'=>$user->getEmail(),'orgName'=> $orgName['name'], 'comName'=>$c->name);
                    array_push($result,$us);
                }
            }
        }
        return $result;
    }

    public function getUsersByOrgId($orgId,$param){
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if($orgId!='all') {
            if($param=='admin') {
                $usersId = $objectManager->getRepository('Organization\Entity\CompanyUser')->findBy(array('orgId' => new \MongoId($orgId)));
            } else {
                $usersId = null;
            }

        } else {
            $usersId =$objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->getQuery()->execute();
        }
        $result=array();
        foreach($usersId as $userId) {
            if($orgId!='all') {
                $usId=$userId->userId;
            } else {
                $usId=$userId->getId();
            }
            $user=$objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($usId)));
            if(!empty($user)) {
                $us=array('id'=>$user->getId(), 'id'=>$user->getId(), 'username'=> $user->getUsername(), 'displayName'=>$user->getDisplayName(),'email'=>$user->getEmail());

                array_push($result,$us);
            }
        }
        return $result;
    }

    public function deleteUserFromOrg($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('userId' => new \MongoId($userId)));
        if (!$user) {
            throw DocumentNotFoundException::documentNotFound('Organization\Entity\CompanyUser', $userId);
        }
        $objectManager->remove($user);
        $objectManager->flush();
    }

    public function deleteUserFull($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId)));
        if (!$user) {
            throw DocumentNotFoundException::documentNotFound('User\Entity\User', $userId);
        }
        $objectManager->remove($user);
        $objectManager->flush();
    }

    public function getOrgIdByUserId($userId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userObject = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(
            array('userId' => new \MongoId($userId))
        );
        return $userObject->orgId;
    }

    public function addRole($userId,$post) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userObject = $objectManager->getRepository('User\Entity\User')->findOneBy(
            array('id' => new \MongoId($userId))
        );
        $roles=$post->roles;
        array_unshift($roles,'user');
        $userObject->setRoles($roles);
        $objectManager->persist($userObject);
        $objectManager->flush();
    }

    public function getRoles($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userObject = $objectManager->getRepository('User\Entity\User')->findOneBy(
            array('id' => new \MongoId($userId))
        );
        return $userObject->getRoles();
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }
}