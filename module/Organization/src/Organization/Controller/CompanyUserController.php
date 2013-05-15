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
        return new ViewModel(array(
            'form' => $form,
            'org_id' => $org_uuid
        ));
    }

    public function addUserAction()
    {
        $post = $this->getRequest()->getPost();
        $this->loginControl();
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $uuid_gen = new UuidGenerator();
        if (!$uuid_gen->isValid($org_uuid)) {
            $result = "Ошибка";
        } else {
            $orgModel = $this->getOrganizationModel();
            $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            $comUserModel = $this->getCompanyUserModel();
            if ($comUserModel->addUserToOrg($post, $org_id)) {
                $result = "Успешо";
            } else {
                $result = "Ошибка";
            }
        }
        return new ViewModel(array(
            'result' => $result
        ));
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

}