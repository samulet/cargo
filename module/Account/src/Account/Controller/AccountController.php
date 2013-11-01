<?php
namespace Account\Controller {

    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
    use Zend\Form\Annotation\AnnotationBuilder;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use AddList\Form\AddListForm;

    class AccountController extends AbstractActionController
    {
        protected $accountModel;
        protected $ticketModel;
        protected $resourceModel;
        protected $addListModel;
        protected $companyUserModel;

        public function indexAction()
        {
            $accModel = $this->getAccountModel();
            $acc = $accModel->returnAccounts($this->zfcUserAuthentication()->getIdentity()->getCurrentAcc());
            $tickModel = $this->getTicketModel();
            $resModel = $this->getResourceModel();
            $res = $resModel->returnAllResource();
            $tick = $tickModel->returnAllTicket();

            return new ViewModel(array(
                'org' => $acc,
                'tick' => $tick,
                'res' => $res
            ));

        }

        public function addAccountAction()
        {
            return $this->redirect()->toUrl('/account/add');
        }

        public function setAccAndComAction()
        {
            $id = $this->getEvent()->getRouteMatch()->getParam('id');
            $param = $this->getEvent()->getRouteMatch()->getParam('param');
            if ($param == 'acc') {
                $post['currentAcc'] = $id;
            } elseif ($param == 'com') {
                $post['currentCom'] = $id;
            }
            $post['submit'] = 'submit';
            $comUserModel = $this->getCompanyUserModel();
            $comUserModel->addOrgAndCompanyToUser($post, $this->zfcUserAuthentication()->getIdentity()->getId());
            return $this->redirect()->toUrl('/user');
        }

        public function choiceOrgAndCompanyAction()
        {

            $post = $this->getRequest()->getPost();
            $comUserModel = $this->getCompanyUserModel();
            $accModel = $this->getAccountModel();

            $result = null;
            if ($this->getRequest()->isPost()) {
                $comUserModel->addOrgAndCompanyToUser(
                    get_object_vars($post),
                    $this->zfcUserAuthentication()->getIdentity()->getId()
                );
                $result = 'Успешно, продлжить выбор Аккаунта и Компании';
            }
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('User\Entity\User');
            $accModel->addBootstrap3Class($form);

            $acc = $comUserModel->getOrgWenUserConsist($this->zfcUserAuthentication()->getIdentity()->getId());
            $fillFrom = new AddListForm();
            $form = $fillFrom->fillOrg($form, $acc);
            $currentOrg = $this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();
            if (!empty($currentOrg)) {
                $form->get('currentAcc')->setValue($currentOrg);
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

        public function getAccountModel()
        {
            if (!$this->accountModel) {
                $sm = $this->getServiceLocator();
                $this->accountModel = $sm->get('Account\Model\AccountModel');
            }
            return $this->accountModel;
        }

        public function addAction()
        {
            $post = $this->getRequest();

            if ($post->isPost()) {
                $accModel = $this->getAccountModel();
                $accUuid = $this->getEvent()->getRouteMatch()->getParam('id');
                if (!empty($accUuid)) {
                    $accId = $accModel->getOrgIdByUUID($accUuid);
                } else {
                    $accId = null;
                }
                $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
                $accModel->createAccount($post->getPost(), $userId, $accId);
                return $this->redirect()->toUrl('/account');
            } else {
                $builder = new AnnotationBuilder();
                $form = $builder->createForm('Account\Entity\Account');
                $accModel = $this->getAccountModel();
                $accModel->addBootstrap3Class($form);
                return new ViewModel(array(
                    'form' => $form
                ));
            }
        }

        public function editAction()
        {
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('Account\Entity\Account');
            $accModel = $this->getAccountModel();

            $acc = $accModel->returnAccounts($this->zfcUserAuthentication()->getIdentity()->getId());
            $accUuid = $this->getEvent()->getRouteMatch()->getParam('id');
            return new ViewModel(array(
                'form' => $form,
                'org' => $acc[0]['org'],
                'uuid' => $accUuid
            ));
        }
        public function listAction()
        {
            $accId = $this->getEvent()->getRouteMatch()->getParam('id');
            $accModel = $this->getAccountModel();
            $acc = $accModel->getAccount($accId);
            if (!$acc) {
                $acc = false;
            }
            return new ViewModel(array(
                'org' => $acc
            ));
        }

        public function deleteAction()
        {
            $accModel = $this->getAccountModel();
            $accUuid = $this->getEvent()->getRouteMatch()->getParam('id');
            $accId = $accModel->getOrgIdByUUID($accUuid);
            $accModel->deleteAccount($accId);
            return $this->redirect()->toUrl('/account');
        }

        public function addIntNumberAction()
        {
            $accModel = $this->getAccountModel();
            $accModel->addIntNumber();
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
                $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
            }
            return $this->companyUserModel;
        }
    }
}
