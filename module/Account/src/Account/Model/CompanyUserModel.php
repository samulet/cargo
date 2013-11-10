<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/25/13
 * Time: 5:43 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Account\Model;

use Account\Entity\CompanyUser;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;

class CompanyUserModel implements ServiceLocatorAwareInterface
{
    public function __construct()
    {

    }

    protected $serviceLocator;
    protected $accountModel;
    protected $companyModel;

    public function addUserToCompany($post, $accId, $param)
    {
        $roles = array();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (is_object($post)) {

            $post = get_object_vars($post);

            $user_id = $this->findUserByEmail($post['company_user']['email']);
        } else {
            $user_id = $post;

        }
        if (!$user_id) {
            return false;
        }
        if ($param == 'admin') {
            $orgTest = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('orgId' => new \MongoId($accId), 'userId' => new \MongoId($user_id))
            );
            if (!empty($orgTest)) {
                return false;
            }
        } else {
            $comTest = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('companyId' => new \MongoId($accId), 'userId' => new \MongoId($user_id))
            );
            if (!empty($comTest)) {
                return false;
            }
        }

        if ($user_id) {
            if ($param == 'admin') {
                $roles = array('accAdmin');
            } else {
                if (!empty($post['roles'])) {
                    $roles = $post['roles'];
                }
            }
            $comUser = new CompanyUser($accId, $user_id, $param, $roles);
        } else {
            return false;
        }
        $objectManager->persist($comUser);
        $objectManager->flush();
        return true;
    }

    public function updateUserRoles($roles, $userId, $rolesToDelete = array())
    {
        if ((!empty($rolesToDelete)) || (!empty($roles))) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId)));
            $oldRoles = $user->getRoles();
            if (!empty($rolesToDelete)) {
                foreach ($rolesToDelete as $roleToDelete) {
                    unset($oldRoles[array_search($roleToDelete, $oldRoles)]);
                }
            }


            foreach ($oldRoles as &$oldRole) {
                if ($oldRole == 'user') {
                    unset($oldRole);
                }
                if ($oldRole == 'inner') {
                    unset($oldRole);
                }
            }
            $oldRoles = array_merge($oldRoles, $roles);
            array_unshift($oldRoles, 'inner');

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

    public function findUserAndSetRole($type, $userId, $itemId)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if ($type == 'currentAcc') {
            $acc = $orgTest = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('orgId' => new \MongoId($itemId), 'userId' => new \MongoId($userId))
            );
            if (!empty($acc)) {
                $roles = $acc->roles;
            } else {
                $roles = array();
            }
            $this->updateUserRoles($roles, $userId, array("accAdmin", "forwarder", "carrier", "customer"));
        } elseif ($type == 'currentCom') {

            $com = $orgTest = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('companyId' => new \MongoId($itemId), 'userId' => new \MongoId($userId))
            );
            if (!empty($com)) {
                $this->updateUserRoles($com->roles, $userId, array("forwarder", "carrier", "customer"));
            }
        }
    }

    public function addOrgAndCompanyToUser($post, $userId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        // $post=;
        unset($post['submit']);
        if (empty($post)) {
            return null;
        }
        if (!empty($post['currentAcc'])) {
            $objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->findAndUpdate()
                ->field('id')->equals(new \MongoId($userId))
                ->field('currentAcc')->set(new \MongoId($post['currentAcc']))
                ->field('currentCom')->set(null)
                ->getQuery()
                ->execute();
            $this->findUserAndSetRole('currentAcc', $userId, $post['currentAcc']);
        }
        if (!empty($post['currentCom'])) {
            $comModel = $this->getCompanyModel();
            $com = $comModel->getCompany($post['currentCom']);
            $objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->findAndUpdate()
                ->field('id')->equals(new \MongoId($userId))
                ->field('currentCom')->set(new \MongoId($post['currentCom']))
                ->field('currentAcc')->set(new \MongoId($com['ownerAccId']))
                ->getQuery()
                ->execute();
            $this->findUserAndSetRole('currentAcc', $userId, $com['ownerAccId']);
            $this->findUserAndSetRole('currentCom', $userId, $post['currentCom']);
        }
    }

    public function getOrgWenUserConsist($userId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $orgTmp = $objectManager->getRepository('Account\Entity\CompanyUser')->findBy(
            array('userId' => new \MongoId($userId))
        );
        $accModel = $this->getAccountModel();
        $comModel = $this->getCompanyModel();
        $resultArray = array();

        foreach ($orgTmp as $or) {
            if (!empty($or->companyId)) {
                $comTmp = $comModel->getCompany($or->companyId);
                $orgLocal = $accModel->getAccount($comTmp['ownerAccId']);
                $resultArray = $resultArray + array($orgLocal['id'] => $orgLocal['name']);
            } elseif (!empty($or->orgId)) {
                $orgLocal = $accModel->getAccount($or->orgId);

                $resultArray = $resultArray + array($orgLocal['id'] => $orgLocal['name']);
            }
        }

        return array_unique($resultArray);
    }

    public function getComWenUserConsist($orgId, $userId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comUserTmp = $objectManager->getRepository('Account\Entity\CompanyUser')->findBy(
            array('userId' => new \MongoId($userId), 'orgId' => null)
        );
        $comModel = $this->getCompanyModel();
        if (empty($comUserTmp)) {
            return array();
        }
        $resultArray = array();

        foreach ($comUserTmp as $comUser) {
            $comTmp = $comModel->getCompany($comUser->companyId);
            if ($comTmp['ownerAccId'] == $orgId) {
                $resultArray = $resultArray + array($comTmp['id'] => $comTmp['name']);
            }
        }

        return $resultArray;
    }

    public function addCompanyInOrgWhenConsist($acc, $userId)
    {
        if (!empty($acc)) {
            $resultArray = array();
            $comModel = $this->getCompanyModel();
            foreach ($acc as $key => $value) {
                $comArray = $this->getComWenUserConsist($key, $userId);
                array_push(
                    $resultArray,
                    array(
                        'acc' => array($key => $value),
                        'com' => $comArray
                    )
                );
            }
            return $resultArray;
        } else {
            return array();
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

    public function getUsersByComId($orgId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $company = $comModel->getCompany($orgId);

        $accModel = $this->getAccountModel();
        $orgName = $accModel->getAccount($company['ownerAccId']);
        $result = array();
        $users = $objectManager->getRepository('Account\Entity\CompanyUser')->findBy(
            array('companyId' => new \MongoId($orgId))
        );
        foreach ($users as $userT) {
            $user = $objectManager->getRepository('User\Entity\User')->findOneBy(
                array('id' => new \MongoId($userT->userId))
            );
            if (!empty($user)) {
                $us = array(
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'displayName' => $user->getDisplayName(),
                    'email' => $user->getEmail(),
                    'orgName' => $orgName['name'],
                    'comName' => $company['name']
                );
                array_push($result, $us);
            }
        }

        return $result;
    }

    public function getUser($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($id)));
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'displayName' => $user->getDisplayName(),
            'email' => $user->getEmail()
        );
    }

    public function getAllUsersByOrgId($orgId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com = $objectManager->getRepository('Account\Entity\Company')->findBy(
            array('ownerAccId' => new \MongoId($orgId))
        );
        if (empty($com)) {
            return null;
        }
        $accModel = $this->getAccountModel();
        $orgName = $accModel->getAccount($orgId);

        $result = array();
        foreach ($com as $c) {
            $users = $objectManager->getRepository('Account\Entity\CompanyUser')->findBy(
                array('companyId' => new \MongoId($c->id))
            );
            foreach ($users as $userT) {
                $user = $objectManager->getRepository('User\Entity\User')->findOneBy(
                    array('id' => new \MongoId($userT->userId))
                );
                if (!empty($user)) {
                    $us = array(
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'displayName' => $user->getDisplayName(),
                        'email' => $user->getEmail(),
                        'orgName' => $orgName['name'],
                        'comName' => $c->name
                    );
                    array_push($result, $us);
                }
            }
        }
        return $result;
    }

    public function getUsersByOrgId($orgId, $param)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if ($orgId != 'all') {
            if ($param == 'admin') {
                $usersId = $objectManager->getRepository('Account\Entity\CompanyUser')->findBy(
                    array('orgId' => new \MongoId($orgId))
                );
            } else {
                $usersId = null;
            }

        } else {
            $usersId = $objectManager->getRepository('User\Entity\User')->createQueryBuilder()
                ->getQuery()->execute();
        }
        $result = array();
        foreach ($usersId as $userId) {
            if ($orgId != 'all') {
                $usId = $userId->userId;
            } else {
                $usId = $userId->getId();
            }
            $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($usId)));
            if (!empty($user)) {
                $us = array(
                    'id' => $user->getId(),
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'displayName' => $user->getDisplayName(),
                    'email' => $user->getEmail()
                );

                array_push($result, $us);
            }
        }
        return $result;
    }

    public function deleteUserFromOrg($userId, $itemId, $param)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if ($param == 'admin') {
            $accModel = $this->getAccountModel();
            $orgId = $accModel->getOrgIdByUUID($itemId);
            $user = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('orgId' => new \MongoId($orgId), 'userId' => new \MongoId($userId))
            );
            $userT = $objectManager->getRepository('User\Entity\User')->findOneBy(
                array('id' => new \MongoId($userId), 'currentAcc' => new \MongoId($orgId))
            );
            if (!empty($userT)) {
                $this->updateUserRoles(array(), $userId, array("accAdmin"));

                if (empty($userT->currentCom)) {
                    $userT->currentAcc = null;
                }

                $objectManager->persist($userT);
                $objectManager->flush();
            }
        } elseif (($param == 'user') || ($param == 'current')) {
            $comModel = $this->getCompanyModel();
            $comId = $comModel->getCompanyIdByUUID($itemId);
            $user = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
                array('companyId' => new \MongoId($comId), 'userId' => new \MongoId($userId))
            );
            $userT = $objectManager->getRepository('User\Entity\User')->findOneBy(
                array('id' => new \MongoId($userId), 'currentCom' => new \MongoId($comId))
            );
            if (!empty($userT)) {
                $this->updateUserRoles(array(), $userId, array("forwarder", "carrier", "customer"));
                $userT->currentCom = null;
                if (!is_int(array_search('accAdmin', $userT->getRoles()))) {
                    $userT->currentAcc = null;
                }
                $objectManager->persist($userT);
                $objectManager->flush();
            }
        }
        if (!$user) {
            throw DocumentNotFoundException::documentNotFound('Account\Entity\CompanyUser', $userId);
        }
        $objectManager->remove($user);
        $objectManager->flush();
    }

    public function deleteUserFull($userId)
    {
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
        $userObject = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
            array('userId' => new \MongoId($userId))
        );
        return $userObject->orgId;
    }

    public function updateUserData($userId, $post)
    {
        if (!empty($post)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $user = $objectManager->getRepository('User\Entity\User')->findOneBy(array('id' => new \MongoId($userId)));
            if (!empty($user)) {
                if (empty($post['displayName'])) {
                    $post['displayName'] = '';
                }
                if (empty($post['email'])) {
                    $post['email'] = '';
                }
                if (empty($post['username'])) {
                    $post['username'] = '';
                }
                $user->setDisplayName($post['displayName']);
                $user->setEmail($post['email']);
                $user->setUsername($post['username']);
                if (!empty($post['password'])) {
                    $bcrypt = new Bcrypt;
                    $auth = $this->serviceLocator->get('zfcuser_auth_service');
                    $bcrypt->setCost($auth->getOptions()->getPasswordCost());
                    $user->setPassword($bcrypt->create($user->getPassword()));
                    $user->setPassword($post['password']);
                }
                $objectManager->persist($user);
                $objectManager->flush();
            }
        }


    }

    public function addRole($userId, $roles, $comUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $comId = $comModel->getCompanyIdByUUID($comUuid);

        $objectManager->getRepository('Account\Entity\CompanyUser')->createQueryBuilder()
            ->findAndUpdate()
            ->field('companyId')->equals(new \MongoId($comId))
            ->field('userId')->equals(new \MongoId($userId))
            ->field('roles')->set($roles)
            ->getQuery()
            ->execute();
        $user = $objectManager->getRepository('User\Entity\User')->findOneBy(
            array('id' => new \MongoId($userId), 'currentCom' => new \MongoId($comId))
        );
        if (!empty($user)) {
            $this->updateUserRoles($roles, $userId, array("forwarder", "carrier", "customer"));
        }
    }

    public function updateCompanyUserRoles($comId, $userId)
    {

    }

    public function getRoles($userId, $comUuid)
    {
        if (empty($comUuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $comId = $comModel->getCompanyIdByUUID($comUuid);
        $rolesObject = $objectManager->getRepository('Account\Entity\CompanyUser')->findOneBy(
            array('userId' => new \MongoId($userId), 'companyId' => new \MongoId($comId))
        );
        return $rolesObject->roles;
    }

    public function getAccountModel()
    {
        if (!$this->accountModel) {
            $sm = $this->getServiceLocator();
            $this->accountModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->accountModel;
    }

    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Account\Model\CompanyModel');
        }
        return $this->companyModel;
    }

}