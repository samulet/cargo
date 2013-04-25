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

class CompanyController extends AbstractActionController
{
    protected $companyModel;
    protected $organizationModel;

    public function indexAction()
    {

    }

    public function addAction()
    {
        $this->loginControl();
        $form = new CompanyCreate();
        $org_id=$this->getEvent()->getRouteMatch()->getParam('org_id');
        return new ViewModel(array(
            'form' => $form,
            'org_id' => $org_id
        ));


    }

    public function createCompanyAction(){
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $post=$this->getRequest()->getPost();
        $comModel=$this->getCompanyModel();
        $org_uuid=$this->getEvent()->getRouteMatch()->getParam('org_id');
        $uuid_gen=new UuidGenerator();
        if(!$uuid_gen->isValid($org_uuid)) $result="Ошибка";
        else {
            $orgModel=$this->getOrganizationModel();
            $org_id=$orgModel->getOrgIdByUUID($org_uuid);
            if($comModel->createCompany($post, $org_id)) $result="Успешо";
            else $result="Ошибка";
        }
        return new ViewModel(array(
            'result' =>$result
        ));
    }

    public function editAction()
    {
        $org_id=$this->getEvent()->getRouteMatch()->getParam('org_id');
        die(var_dump($org_id));
    }

    public function deleteAction()
    {
    }
    private function loginControl() {
        if ($this->zfcUserAuthentication()->hasIdentity()) return true;
        else return $this->redirect()->toUrl('/user/login');
    }
    public function getCompanyModel()
    {
        error_reporting(E_ALL | E_STRICT) ;
        ini_set('display_errors', 'On');
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }
    public function getOrganizationModel()
    {
        error_reporting(E_ALL | E_STRICT) ;
        ini_set('display_errors', 'On');
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }
}