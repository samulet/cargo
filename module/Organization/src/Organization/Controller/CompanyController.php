<?php
namespace Organization\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Organization\Entity\Company;
use Organization\Model\CompanyModel;
use Organization\Model\OrganizationModel;
use Organization\Entity\Organization;
use Organization\Form\CompanyCreate;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Organization\Form\CompanyForm;

class CompanyController extends AbstractActionController
{
    protected $companyModel;
    protected $organizationModel;
    protected $addListModel;

    public function indexAction()
    {
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $uuid_gen = new UuidGenerator();
        if (!$uuid_gen->isValid($org_uuid)) {
            $com = "Ошибка";
        } else {
            $comModel = $this->getCompanyModel();
            $orgModel = $this->getOrganizationModel();
            $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            $com = $comModel->returnCompanies($org_id);
        }
        return new ViewModel(array(
            'org' => $com,
            'org_id' => $org_uuid

        ));
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Organization\Entity\Company');
        $addListModel = $this->getAddListModel();
        $form_array=array();
        $formData=$addListModel->returnDataArray($form_array,'company');

        $fillFrom=new CompanyForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);


        $org_id = $this->getEvent()->getRouteMatch()->getParam('org_id');
        return new ViewModel(array(
            'form' => $form,
            'org_id' => $org_id
        ));


    }

    public function createCompanyAction()
    {
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $post = $this->getRequest()->getPost();
        $comModel = $this->getCompanyModel();
        $org_uuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $uuid_gen = new UuidGenerator();
        if (!$uuid_gen->isValid($org_uuid)) {
            $result = "Ошибка";
        } else {
            $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
            if (!empty($com_uuid)) {
                $com_id = $comModel->getCompanyIdByUUID($com_uuid);
            } else {
                $com_id = null;
            }
            $orgModel = $this->getOrganizationModel();
            $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            if ($comModel->createCompany($post, $org_id, $com_id)) {
                $result = "Успешо";
            } else {
                $result = "Ошибка";
            }
        }
        return new ViewModel(array(
            'result' => $result
        ));
    }

    public function editAction()
    {
        $this->loginControl();
        $form = new CompanyCreate();
        $org_id = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');


        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompany($comModel->getCompanyIdByUUID($com_uuid));
        return new ViewModel(array(
            'com' => $com,
            'form' => $form,
            'org_id' => $org_id,
            'com_id' => $com_uuid
        ));
    }

    public function deleteAction()
    {
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $comModel = $this->getCompanyModel();
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $com_id = $comModel->getCompanyIdByUUID($com_uuid);
        $comModel->deleteCompany($com_id);
        return $this->redirect()->toUrl('/organization');
    }

    private function loginControl()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return true;
        } else {
            return $this->redirect()->toUrl('/user/login');
        }
    }

    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

    public function listAction()
    {
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompany($comModel->getCompanyIdByUUID($com_uuid));

        return new ViewModel(array(
            'com' => $com
        ));
    }
    public function getAddListModel()
    {
        if (!$this->addListModel) {
            $sm = $this->getServiceLocator();
            $this->addListModel = $sm->get('AddList\Model\AddListModel');
        }
        return $this->addListModel;
    }
}