<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/25/13
 * Time: 4:48 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Organization\Entity\Company;
use Organization\Model\CompanyModel;
use Organization\Model\OrganizationModel;
use Organization\Entity\CompanyUser;
use Organization\Form\CompanyUserCreate;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\ModuleManager\ModuleManager;

class CompanyUserController extends AbstractActionController
{
    protected $companyUserModel;
    protected $organizationModel;

    public function indexAction()
    {

    }

    public function addAction()
    {
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $form = new CompanyUserCreate();
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        return new ViewModel(array(
            'form' => $form,
            'org_id' => $org_uuid,
            'param' =>$param
        ));
    }

    public function addUserAction()
    {
        $post = $this->getRequest()->getPost();
        $this->loginControl();
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        $uuid_gen = new UuidGenerator();
        if (!$uuid_gen->isValid($org_uuid)) {
            $result = "Ошибка";
        } else {
            $orgModel = $this->getOrganizationModel();
            if($param=='admin') {
                $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            } else {
                $org_id = $orgModel->getComIdByUUID($org_uuid);
            }

            $comUserModel = $this->getCompanyUserModel();
            if ($comUserModel->addUserToCompany($post, $org_id,$param)) {
                $result = "Успешо";
            } else {
                $result = "Ошибка";
            }
        }
        return new ViewModel(array(
            'result' => $result
        ));
    }

    public function listAction() {
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        if($org_uuid!="all") {
            $orgModel = $this->getOrganizationModel();
            $orgId = $orgModel->getOrgIdByUUID($org_uuid);

        } else {
            $orgId='all';
            $this->layout('layout/admin');
        }
        $comUserModel = $this->getCompanyUserModel();
        $users=$comUserModel->getUsersByOrgId($orgId);
        return new ViewModel(array(
            'users' => $users,
            'org_uuid'=>$org_uuid
        ));
    }

    public function deleteAction() {
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $param = $this->getEvent()->getRouteMatch()->getParam('param');
        $comUserModel = $this->getCompanyUserModel();
        if($param=='full') {
            $comUserModel->deleteUserFull($userId);
            return $this->redirect()->toUrl('/account/user/all/list');
        } else {
            $comUserModel->deleteUserFromOrg($userId);
            return $this->redirect()->toUrl('/account');
        }
    }

    private function loginControl()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return true;
        }
        else {
            return $this->redirect()->toUrl('/user/login');
        }
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function roleAction() {
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $adminParam = $this->getEvent()->getRouteMatch()->getParam('param');
        $builder = new AnnotationBuilder();
        if($adminParam=='admin') {
            $this->layout('layout/admin');
        }
        $form = $builder->createForm('User\Entity\User');
        $comUserModel = $this->getCompanyUserModel();
        $roles=$comUserModel->getRoles($userId);
        return new ViewModel(array(
            'id' =>$userId,
            'form' =>$form,
            'roles'=>$roles
        ));
    }

    public function roleEditAction() {
        $comUserModel = $this->getCompanyUserModel();
        $userId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $post=$this->getRequest()->getPost();
        $comUserModel->addRole($userId,$post);
        return $this->redirect()->toUrl('/account');
    }

}