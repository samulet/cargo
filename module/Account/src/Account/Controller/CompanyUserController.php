<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/25/13
 * Time: 4:48 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Account\Form\CompanyUserCreate;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;

class CompanyUserController extends AbstractActionController
{
    protected $companyUserModel;
    protected $accountModel;
    protected $companyModel;

    public function addAction()
    {
        $form = new CompanyUserCreate();
        $accUuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        $builder = new AnnotationBuilder();
        $formRoles = $builder->createForm('User\Entity\User');
        return new ViewModel(array(
            'form' => $form,
            'org_id' => $accUuid,
            'param' => $param,
            'formRoles' => $formRoles
        ));
    }

    public function addUserAction()
    {
        $post = $this->getRequest()->getPost();
        $this->loginControl();
        $accUuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        $uuid_gen = new UuidGenerator();
        $form = null;

        if (!$uuid_gen->isValid($accUuid)) {
            $result = "Ошибка";
        } else {
            $accModel = $this->getAccountModel();
            if ($param == 'admin') {
                $accId = $accModel->getOrgIdByUUID($accUuid);
            } else {

                $accId = $accModel->getComIdByUUID($accUuid);
            }

            $comUserModel = $this->getCompanyUserModel();
            if ($comUserModel->addUserToCompany($post, $accId, $param)) {
                $result = "Успешо";
            } else {
                $result = "Ошибка, скорее всего юзер уже добавлен или не существует";
            }
        }
        return new ViewModel(array(
            'result' => $result
        ));
    }

    public function listAction()
    {
        $accUuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        if ($accUuid != "all") {
            $accModel = $this->getAccountModel();
            if ($param == 'current') {
                $orgId = $accModel->getComIdByUUID($accUuid);
            } else {
                $orgId = $accModel->getOrgIdByUUID($accUuid);
            }
        } else {
            $orgId = 'all';
            $this->layout('layout/admin');
        }
        $comUserModel = $this->getCompanyUserModel();
        $comModel = $this->getCompanyModel();
        $accModel = $this->getAccountModel();
        $name = '';
        if (($param == 'user') && ($accUuid != "all")) {
            $users = $comUserModel->getAllUsersByOrgId($orgId);
            $acc = $accModel->getAccount($accUuid);
            $name = $acc['name'];
        } elseif (($param == 'admin') && ($accUuid != "all")) {
            $users = $comUserModel->getUsersByOrgId($orgId, $param);
        } elseif (($param == 'full') && ($orgId == 'all')) {
            $users = $comUserModel->getUsersByOrgId($orgId, $param);
        } elseif (($param == 'current') && ($accUuid != "all")) {
            $users = $comUserModel->getUsersByComId($orgId);
            $com = $comModel->getCompany($accUuid);
            $name = $com['property'] . ' ' . $com['name'];
        }
        return new ViewModel(array(
            'users' => $users,
            'org_uuid' => $accUuid,
            'param' => $param,
            'name' => $name
        ));
    }

    public function deleteAction()
    {
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        $itemId = $this->getEvent()->getRouteMatch()->getParam('comId');
        $comUserModel = $this->getCompanyUserModel();
        if ($param == 'full') {
            $comUserModel->deleteUserFull($userId);
            return $this->redirect()->toUrl('/account/user/all/list');
        } else {
            $comUserModel->deleteUserFromOrg($userId, $itemId, $param);
            return $this->redirect()->toUrl('/account');
        }
    }

    private function loginControl()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return true;
        } else {
            return $this->redirect()->toUrl('/user/login');
        }
    }

    public function getAccountModel()
    {
        if (!$this->accountModel) {
            $sm = $this->getServiceLocator();
            $this->accountModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->accountModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function roleAction()
    {
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $adminParam = $this->getEvent()->getRouteMatch()->getParam('param');
        $comId = $this->getEvent()->getRouteMatch()->getParam('comId');

        $builder = new AnnotationBuilder();
        if ($adminParam == 'admin') {
            $this->layout('layout/admin');
        }
        $form = $builder->createForm('User\Entity\User');
        foreach ($form as $el) {
            $attr = $el->getAttributes();
            if (!empty($attr['type'])) {
                if (($attr['type'] != 'checkbox') && ($attr['type'] != 'multi_checkbox')) {
                    $el->setAttributes(array('class' => 'form-control'));
                }
            }
        }
        $comUserModel = $this->getCompanyUserModel();
        $roles = $comUserModel->getRoles($userId, $comId);
        $data = $comUserModel->getUser($userId);
        return new ViewModel(array(
            'id' => $userId,
            'form' => $form,
            'roles' => $roles,
            'comId' => $comId,
            'data' => $data
        ));
    }

    public function roleEditAction()
    {
        $comUserModel = $this->getCompanyUserModel();
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comId = $this->getEvent()->getRouteMatch()->getParam('comId');
        $post = $this->getRequest()->getPost();
        $post = get_object_vars($post);
        if (empty($post['roles'])) {
            $roles = array();
        } else {
            $roles = $post['roles'];
            unset($post['roles']);
        }
        $comUserModel->addRole($userId, $roles, $comId);
        $comUserModel->updateUserData($userId, $post);
        return $this->redirect()->toUrl('/account/user/' . $userId . '/role/current/' . $comId);
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