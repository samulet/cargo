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
    protected $companyModel;

    public function addUserToCompany($post, $org_id,$param)
    {
        $roles=array();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (is_object($post)) {

            $post=get_object_vars($post);

            $user_id = $this->findUserByEmail($post['company_user']['email']);
        } else {
            $user_id = $post;

        }
        if($param=='admin') {
            $orgTest = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('orgId' => new \MongoId($org_id), 'userId' => new \MongoId($user_id)));
            if(!empty($orgTest)) {
                return false;
            }
        } else {
            $comTest =$objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('companyId' => new \MongoId($org_id), 'userId' => new \MongoId($user_id)));
            if(!empty($comTest)) {
                return false;
            }
        }

        if ($user_id) {
            if($param=='admin') {
                $roles=array('orgAdmin');
            } else {
                if(!empty($post['roles'])) {
                    $roles=$post['roles'];
                }
            }
            $comUser = new CompanyUser($org_id, $user_id,$param,$roles);
        } else {
            return false;
        }
        $objectManager->persist($comUser);
        $objectManager->flush();
        return true;
    }
    public function updateUserRoles($roles,$userId,$rolesToDelete=array()) {
        if( (!empty($rolesToDelete)) || (!empty($roles)) ) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $user=$objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId)));
            $oldRoles=$user->getRoles();
            if(!empty($rolesToDelete)) {
                foreach($rolesToDelete as $roleToDelete) {
                    unset( $oldRoles[array_search($roleToDelete, $oldRoles )] );
                }
            }


                foreach($oldRoles as &$oldRole) {
                    if($oldRole=='user') {
                        unset($oldRole);
                    }
                    if($oldRole=='inner') {
                        unset($oldRole);
                    }
                }
                $oldRoles=array_merge($oldRoles,$roles);
                array_unshift($oldRoles,'inner');

                $user->setRoles(array_unique($oldRoles));

                $objectManager->persist($user);
                $objectManager->flush();

        }
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
    public  function findUserAndSetRole($type, $userId ,$itemId) {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if($type=='currentOrg') {
            $org=$orgTest = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('orgId' => new \MongoId($itemId), 'userId' => new \MongoId($userId)));
            if(!empty($org)) {
                $roles=$org->roles;
            } else {
                $roles=array();
            }
            $this->updateUserRoles($roles,$userId,array("orgAdmin","forwarder", "carrier", "customer" ));
        } elseif($type=='currentCom') {

            $com=$orgTest = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('companyId' => new \MongoId($itemId), 'userId' => new \MongoId($userId)));
            if(!empty($com)) {
             $this->updateUserRoles($com->roles,$userId, array("forwarder", "carrier", "customer" ));
            }
        }
    }
    public function addOrgAndCompanyToUser($post,$userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $post=get_object_vars($post);
        unset($post['submit']);
        if(empty($post)) {
            return null;
        }
        if(!empty($post['currentOrg'])) {
            $objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->findAndUpdate()
                ->field('id')->equals(new \MongoId($userId))
                ->field('currentOrg')->set(new \MongoId($post['currentOrg']))
                ->field('currentCom')->set(null)
                ->getQuery()
                ->execute();
            $this->findUserAndSetRole('currentOrg', $userId,$post['currentOrg']);
        }
        if(!empty($post['currentCom'])) {
            $objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->findAndUpdate()
                ->field('id')->equals(new \MongoId($userId))
                ->field('currentCom')->set(new \MongoId($post['currentCom']))
                ->getQuery()
                ->execute();
            $this->findUserAndSetRole('currentCom', $userId,$post['currentCom']);
        }
    }

    public function getOrgWenUserConsist($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $orgTmp =$objectManager->getRepository('Organization\Entity\CompanyUser')->findBy(array('userId' => new \MongoId($userId)));
        $orgModel = $this->getOrganizationModel();
        $comModel = $this->getCompanyModel();
        $resultArray=array();

        foreach($orgTmp as $or) {
            if(!empty($or->companyId)) {
                $comTmp=$comModel->getCompany($or->companyId);
                $orgLocal=$orgModel->getOrganization($comTmp['ownerOrgId']);
                $resultArray=$resultArray+array($orgLocal['id'] => $orgLocal['name']);
            } elseif(!empty($or->orgId)) {
                $orgLocal=$orgModel->getOrganization($or->orgId);

                $resultArray=$resultArray+array($orgLocal['id'] => $orgLocal['name']);
            }
        }

        return array_unique($resultArray);
    }

    public function getComWenUserConsist($orgId, $userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comUserTmp =$objectManager->getRepository('Organization\Entity\CompanyUser')->findBy(array('userId' => new \MongoId($userId),'orgId' => null));
        $comModel = $this->getCompanyModel();
        if(empty($comUserTmp)) {
            return null;
        }
        $resultArray=array();

        foreach($comUserTmp as $comUser) {
            $comTmp=$comModel->getCompany($comUser->companyId);
            if($comTmp['ownerOrgId']==$orgId) {
                $resultArray=$resultArray+array($comTmp['id'] => $comTmp['name']);
            }
        }

        return $resultArray;
    }
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getUsersByComId($orgId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $company=$comModel->getCompany($orgId);

        $orgModel = $this->getOrganizationModel();
        $orgName=$orgModel->getOrganization($company['ownerOrgId']);
        $result=array();
        $users = $objectManager->getRepository('Organization\Entity\CompanyUser')->findBy(array('companyId' => new \MongoId($orgId)));
        foreach($users as $userT) {
            $user=$objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userT->userId)));
            if(!empty($user)) {
                $us=array('id'=>$user->getId(), 'username'=> $user->getUsername(), 'displayName'=>$user->getDisplayName(),'email'=>$user->getEmail(),'orgName'=> $orgName['name'], 'comName'=>$company['name']);
                array_push($result,$us);
            }
        }

        return $result;
    }
    public function getAllUsersByOrgId($orgId) {
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

    public function deleteUserFromOrg($userId, $itemId,$param) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if($param=='admin') {
            $orgModel = $this->getOrganizationModel();
            $orgId=$orgModel->getOrgIdByUUID($itemId);
            $user = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('orgId'=> new \MongoId($orgId),'userId' => new \MongoId($userId)));
            $userT = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId), 'currentOrg' =>new \MongoId($orgId)));
            if(!empty($userT)) {
                $this->updateUserRoles(array(),$userId,array("orgAdmin" ));

                if(empty($userT->currentCom)) {
                    $userT->currentOrg=null;
                }

                $objectManager->persist($userT);
                $objectManager->flush();
            }
        } elseif(($param=='user') || ($param=='current')) {
            $comModel = $this->getCompanyModel();
            $comId=$comModel->getCompanyIdByUUID($itemId);
            $user = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(array('companyId'=> new \MongoId($comId),'userId' => new \MongoId($userId)));
            $userT = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId), 'currentCom' =>new \MongoId($comId)));
            if(!empty($userT)) {
                $this->updateUserRoles(array(),$userId,array("forwarder", "carrier", "customer" ));
                $userT->currentCom=null;
                if(!is_int(array_search('orgAdmin',$userT->getRoles()))) {
                    $userT->currentOrg=null;
                }
                    $objectManager->persist($userT);
                    $objectManager->flush();
            }
        }
        if(!$user) {
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

    public function addRole($userId,$post,$comUuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $roles=$post->roles;
        $comModel = $this->getCompanyModel();
        $comId=$comModel->getCompanyIdByUUID($comUuid);

        $objectManager->getRepository('Organization\Entity\CompanyUser')->createQueryBuilder()
            ->findAndUpdate()
            ->field('companyId')->equals(new \MongoId($comId))
            ->field('userId')->equals(new \MongoId($userId))
            ->field('roles')->set($roles)
            ->getQuery()
            ->execute();
        $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId), 'currentCom' =>new \MongoId($comId)));
        if(!empty($user)) {
            $this->updateUserRoles($roles,$userId,array("forwarder", "carrier", "customer" ));
        }
    }

    public function updateCompanyUserRoles($comId,$userId) {

    }

    public function getRoles($userId,$comUuid) {
        if(empty($comUuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $comId=$comModel->getCompanyIdByUUID($comUuid);
        $rolesObject = $objectManager->getRepository('Organization\Entity\CompanyUser')->findOneBy(
            array('userId' => new \MongoId($userId),'companyId' =>new \MongoId($comId))
        );
        return $rolesObject->roles;
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }
    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }

}