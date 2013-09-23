<?php
namespace Organization\Controller {

    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
    use Zend\Form\Annotation\AnnotationBuilder;
    use Organization\Form\OrganizationCreate;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;

    use AddList\Form\AddListForm;

    class OrganizationController extends AbstractActionController
    {
        protected $organizationModel;
        protected $ticketModel;
        protected $resourceModel;
        protected $addListModel;
        protected $companyUserModel;

        public function indexAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $orgModel = $this->getOrganizationModel();
            $org = $orgModel->returnOrganizations($this->zfcUserAuthentication()->getIdentity()->getcurrentOrg());

            $tickModel = $this->getTicketModel();
            $resModel = $this->getResourceModel();
            $res = $resModel->returnAllResource();
            $tick = $tickModel->returnAllTicket();

            return new ViewModel(array(
                'org' => $org,
                'tick' => $tick,
                'res' => $res
            ));

        }

        public function addAccountAction()
        {
            return $this->redirect()->toUrl('/account/add');
        }
        public function setAccAndComAction() {
            $id=$this->getEvent()->getRouteMatch()->getParam('id');
            $param=$this->getEvent()->getRouteMatch()->getParam('param');
            if($param=='acc') {
                $post['currentOrg']=$id;
            } elseif($param=='com') {
                $post['currentCom']=$id;
            }
            $post['submit']='submit';
            $comUserModel = $this->getCompanyUserModel();
            $comUserModel->addOrgAndCompanyToUser($post, $this->zfcUserAuthentication()->getIdentity()->getId());
            return $this->redirect()->toUrl('/user');
        }
        public function choiceOrgAndCompanyAction()
        {

            $post = $this->getRequest()->getPost();
            $comUserModel = $this->getCompanyUserModel();
            $orgModel = $this->getOrganizationModel();

            $result = null;
            if ($this->getRequest()->isPost()) {
                $comUserModel->addOrgAndCompanyToUser(get_object_vars($post), $this->zfcUserAuthentication()->getIdentity()->getId());
                $result = 'Успешно, продлжить выбор Аккаунта и Компании';
            }
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('User\Entity\User');
            $orgModel->addBootstrap3Class($form);

            $org = $comUserModel->getOrgWenUserConsist($this->zfcUserAuthentication()->getIdentity()->getId());
            $fillFrom = new AddListForm();
            $form = $fillFrom->fillOrg($form, $org);
            $currentOrg = $this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();
            if (!empty($currentOrg)) {
                $form->get('currentOrg')->setValue($currentOrg);
                $com = $comUserModel->getComWenUserConsist(
                    $currentOrg,
                    $this->zfcUserAuthentication()->getIdentity()->getId()
                );

                if (!empty($com)) {
                    $form = $fillFrom->fillCom($form, $com);
                }

            }
            $currentCom = $this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
            if (!empty($currentCom)) {
                $form->get('currentCom')->setValue($currentCom);
            }
            return new ViewModel(array(
                'form' => $form,
                'result' => $result
            ));
        }

        private function loginControl()
        {
            if ($this->zfcUserAuthentication()->hasIdentity()) {
                return true;
            } else {
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

        public function addAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $form = new OrganizationCreate();
            $orgModel = $this->getOrganizationModel();
            $orgModel->addBootstrap3Class($form);
            return new ViewModel(array(
                'form' => $form
            ));
        }

        public function editAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $form = new OrganizationCreate();
            $orgModel = $this->getOrganizationModel();

            $org = $orgModel->returnOrganizations($this->zfcUserAuthentication()->getIdentity()->getId());
            $org_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
            return new ViewModel(array(
                'form' => $form,
                'org' => $org[0]['org'],
                'uuid' => $org_uuid
            ));
        }

        public function addContractAgentToAccountAction() {

        }

        public function listAction()
        {
            $this->loginControl();
            $org_id = $this->getEvent()->getRouteMatch()->getParam('id');
            $orgModel = $this->getOrganizationModel();
            $org = $orgModel->getOrganization($org_id);
            if (!$org) {
                $org = false;
            }
            return new ViewModel(array(
                'org' => $org
            ));
        }

        public function deleteAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $orgModel = $this->getOrganizationModel();
            $org_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
            $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            $orgModel->deleteOrganization($org_id);
            return $this->redirect()->toUrl('/account');
        }

        public function addIntNumberAction()
        {
            $orgModel = $this->getOrganizationModel();
            $orgModel->addIntNumber();
            return $this->redirect()->toUrl('/account');
        }

        public function createOrganizationAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $post = $this->getRequest()->getPost();
            $orgModel = $this->getOrganizationModel();
            $org_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
            if (!empty($org_uuid)) {
                $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            } else {
                $org_id = null;
            }
            $userId=$this->zfcUserAuthentication()->getIdentity()->getId();

            $orgModel->createOrganization($post, $userId,$org_id);


            return $this->redirect()->toUrl('/account');

        }

        public function getResourceModel()
        {
            if (!$this->resourceModel) {
                $sm = $this->getServiceLocator();
                $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
            }
            return $this->resourceModel;
        }

        public function getTicketModel()
        {
            if (!$this->ticketModel) {
                $sm = $this->getServiceLocator();
                $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
            }
            return $this->ticketModel;
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
}
