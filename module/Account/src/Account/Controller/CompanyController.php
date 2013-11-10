<?php
namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use AddList\Form\AddListForm;

class CompanyController extends AbstractActionController
{
    protected $companyModel;
    protected $accountModel;
    protected $addListModel;
    protected $companyUserModel;

    public function indexAction()
    {
        $accUuid = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comModel = $this->getCompanyModel();
        $accModel = $this->getAccountModel();
        $accId = $accModel->getOrgIdByUUID($accUuid);
        $com = $comModel->returnCompanies($accId,array('activated' =>'1'));

        return new ViewModel(array(
            'org' => $com,
            'org_id' => $accUuid
        ));
    }

    public function contractAgentListAction()
    {
        $comId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comModel = $this->getCompanyModel();
        $agents = $comModel->getContractAgentsFromCompany($comId);
        $accModel = $this->getAccountModel();
        $company = $comModel->getCompany($comId);
        $acc = $accModel->getAccount($company['ownerAccId']);
        return new ViewModel(array(
            'com' => $company,
            'agents' => $agents,
            'accId' => $acc['uuid']
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
            $form = $builder->createForm('Account\Entity\ContractAgents');
            $fillFrom = new AddListForm();
            $fillFrom->fillComNew($form, $companies, 'contactAgentId');
            $comModel->addBootstrap3Class($form);

            return new ViewModel(array(
                'form' => $form,
                'comId' => $comId
            ));
        }
    }

    public function addCompany($accId, $userListId, $comId, $param, $post)
    {
        $comModel = $this->getCompanyModel();
        if (empty($post)) {
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('Account\Entity\Company');
            $addListModel = $this->getAddListModel();
            $formArray = array();

            $comListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
            $accListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

            $formData = $addListModel->returnDataArray($formArray, 'company', $accListId, $comListId);

            $fillFrom = new AddListForm();
            $form = $fillFrom->fillFrom(
                $form,
                $formData,
                array('address', 'bankAccount', 'documents', 'applicants', 'authorizedPerson', 'founder', 'contact')
            );

            $comModel = $this->getCompanyModel();
            $comModel->addBootstrap3Class($form);
            if (($param == 'list') || ($param == 'edit')) {
                $comData = $comModel->getCompany($comId);
                $form->setData($comData);
                if ($param == 'list') {

                    foreach ($form as $el) {
                        $el->setAttributes(array('disabled' => 'disabled'));
                        foreach ($el as $col) {
                            foreach ($col as $e) {
                                $e->setAttributes(array('disabled' => 'disabled'));
                            }

                        }

                    }

                }
            }

            return array(
                'form' => $form,
                'org_id' => $accId,
                'comId' => $comId,
                'param' => $param
            );
        } else {


            if (!empty($param)) {
                if ($param != 'contractAgent') {
                    $comEditId = $comModel->getCompanyIdByUUID($comId);
                } else {
                    $comEditId = $param;
                }
            } else {
                $comEditId = null;
            }

            $accModel = $this->getAccountModel();
            $accId = $accModel->getOrgIdByUUID($accId);
            $newComId = $comModel->createCompany($post, $accId, $comEditId);

            if ($param == 'contractAgent') {
                $data['contactAgentId'] = $newComId;
                $comModel->addContractAgentToCompany($data, $comId);
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
        $post = get_object_vars($this->getRequest()->getPost());
        return new ViewModel(
            $this->addCompany($accId, $userListId, $comContractUuid, $param, $post)
        );


    }

    public function editAction()
    {


        $this->loginControl();

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Account\Entity\Company');

        $accId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $com_uuid = $this->getEvent()->getRouteMatch()->getParam('id');


        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompany($comModel->getCompanyIdByUUID($com_uuid));

        $addListModel = $this->getAddListModel();
        $formArray = array();

        $comListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId = $this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

        $formData = $addListModel->returnDataArray($formArray, 'company', $accListId, $comListId);

        $fillFrom = new CompanyForm();
        $form = $fillFrom->fillFrom($form, $formData, $formArray);

        return new ViewModel(array(
            'com' => $com,
            'form' => $form,
            'org_id' => $accId,
            'com_id' => $com_uuid
        ));
    }

    public function adminContractAgentUnityAction() {
        $comId = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comUnityId = $this->getEvent()->getRouteMatch()->getParam('id');
        $comModel = $this->getCompanyModel();
        if(!empty($comUnityId)) {
            $comModel->unityContractAgent($comId,$comUnityId);
            $comModel->deleteCompany($comId);
            return $this->redirect()->toUrl('/account/all/company/adminContractAgents');
        }

        $company = $comModel->getCompany($comId);
        $com = $comModel->returnCompanies(null,array('activated'=>'1','dirty' =>null));
        return new ViewModel(array(
            'com' => $com,
            'company' =>$company
        ));
    }

    public function adminContractAgentsAction() {
        $param = $this->getEvent()->getRouteMatch()->getParam('org_id');
        $comId = $this->getEvent()->getRouteMatch()->getParam('id');

        $comModel = $this->getCompanyModel();
        if($param=='approve') {
            $comModel->update(array('id'=>new \MongoId($comId)),array('dirty' =>null));
        } elseif($param=='block') {
            $comModel->update(array('id'=>new \MongoId($comId)),array('activated' =>null));
        }

        $com = $comModel->returnCompanies(null,array('dirty'=>'1'));
        return new ViewModel(array(
            'com' => $com
        ));

    }

    public function deleteAction()
    {
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
            $this->companyModel = $sm->get('Account\Model\CompanyModel');
        }
        return $this->companyModel;
    }

    public function getAccountModel()
    {
        if (!$this->accountModel) {
            $sm = $this->getServiceLocator();
            $this->accountModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->accountModel;
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
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }
}