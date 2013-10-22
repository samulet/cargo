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
use AddList\Form\AddListForm;

class CompanyController extends AbstractActionController
{
    protected $companyModel;
    protected $organizationModel;
    protected $addListModel;
    protected $companyUserModel;

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
            $accId = $orgModel->getOrgIdByUUID($org_uuid);
            $com = $comModel->returnCompanies($accId);
        }
        return new ViewModel(array(
            'org' => $com,
            'org_id' => $org_uuid

        ));
    }

    public function choiceOrgAndComAction()
    {

    }

    public function contractAgentListAction()
    {
        $comId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comModel = $this->getCompanyModel();
        $agents = $comModel->getContractAgentsFromCompany($comId);
        $orgModel = $this->getOrganizationModel();
        $company = $comModel->getCompany($comId);
        $acc=$orgModel->getOrganization($company['ownerOrgId']);
        return new ViewModel(array(
            'com' => $company,
            'agents' => $agents,
            'accId'=>$acc['uuid']
        ));
    }

    public function errorAction()
    {
        $comId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        return new ViewModel(array(
            'comId' => $comId
        ));
    }

    public function addContractAgentToCompanyAction()
    {
        $post = get_object_vars($this->getRequest()->getPost());
        $comId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comModel = $this->getCompanyModel();
        if (!empty($post)) {
            $res = $comModel->addContractAgentToCompany($post, $comId);
            if ($res) {
                return $this->redirect()->toUrl('/account/' . $comId . '/company/contractAgentList');
            } else {
                return $this->redirect()->toUrl('/account/' . $comId . '/company/error');
            }
        } else {

            $companies = $comModel->getAllCompanies();
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('Organization\Entity\ContractAgents');
            $fillFrom = new AddListForm();
            $fillFrom->fillComNew($form, $companies, 'contactAgentId');
            $comModel->addBootstrap3Class($form);

            return new ViewModel(array(
                'form' => $form,
                'comId' => $comId
            ));
        }
    }

    public function addCompany($accId, $userListId, $comContractUuid,$param,$post)
    {

        if(empty($post)) {
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('Organization\Entity\Company');
            $addListModel = $this->getAddListModel();
            $form_array = array();
            $orgUserModel = $this->getCompanyUserModel();
            $orgListId = $orgUserModel->getOrgIdByUserId($userListId);


            $formData = $addListModel->returnDataArray($form_array, 'company', $orgListId);

            $fillFrom = new AddListForm();
            $form = $fillFrom->fillFrom($form, $formData, array('address','bankAccount'));

            $comModel = $this->getCompanyModel();
            $comModel->addBootstrap3Class($form);
            if($param=='list') {

            } if($param=='edit') {

            }
            return array(
                'form' => $form,
                'org_id' => $accId,
                'comContractUuid' =>$comContractUuid,
                'param' => $param
            );
        } else {

            $comModel = $this->getCompanyModel();
            if (!empty($param)) {
                if($param!='contractAgent') {
                    $comEditId = $comModel->getCompanyIdByUUID($param);
                } else {
                    $comEditId=$param;
                }
            } else {
                $comEditId = null;
            }

            $orgModel = $this->getOrganizationModel();
            $accId = $orgModel->getOrgIdByUUID($accId);
            $newComId=$comModel->createCompany($post, $accId, $comEditId);

            if($param=='contractAgent') {
                $data['contactAgentId']=$newComId;
                $comModel->addContractAgentToCompany($data,$comContractUuid);
            }

            return $this->redirect()->toUrl('/account');
        }

    }

    public function addAction()
    {
        $accId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $userListId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $param = $this->getEvent()->getRouteMatch()->getParam('id');
        $comContractUuid = $this->getEvent()->getRouteMatch()->getParam('comId');
        $post=get_object_vars($this->getRequest()->getPost());
        return new ViewModel(
            $this->addCompany($accId, $userListId, $comContractUuid,$param,$post)
        );


    }

    public function editAction()
    {


        $this->loginControl();

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Organization\Entity\Company');

        $accId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');


        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompany($comModel->getCompanyIdByUUID($com_uuid));

        $addListModel = $this->getAddListModel();
        $form_array = array();
        $orgUserModel = $this->getCompanyUserModel();
        $userListId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId = $orgUserModel->getOrgIdByUserId($userListId);
        $formData = $addListModel->returnDataArray($form_array, 'company', $orgListId);

        $fillFrom = new CompanyForm();
        $form = $fillFrom->fillFrom($form, $formData, $form_array);

        return new ViewModel(array(
            'com' => $com,
            'form' => $form,
            'org_id' => $accId,
            'com_id' => $com_uuid
        ));
    }

    public function deleteAction()
    {
        $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        $comModel = $this->getCompanyModel();
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $comEditId = $comModel->getCompanyIdByUUID($com_uuid);
        $comModel->deleteCompany($comEditId);
        return $this->redirect()->toUrl('/account');
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

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
}