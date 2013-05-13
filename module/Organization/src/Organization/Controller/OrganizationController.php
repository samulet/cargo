<?php
namespace Organization\Controller {

    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
    use Organization\Form\OrganizationCreate;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;

    class OrganizationController extends AbstractActionController
    {
        protected $organizationModel;
        protected $ticketModel;
        protected $resourceModel;

        public function indexAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $orgModel = $this->getOrganizationModel();
            $org = $orgModel->returnOrganizations($this->zfcUserAuthentication()->getIdentity()->getId());

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
            return $this->redirect()->toUrl('/organization');
        }

        public function createOrganizationAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $post = $this->getRequest()->getPost();
            $orgModel = $this->getOrganizationModel();
            $org_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
            if (!empty($org_uuid)) {
                $org_id = $orgModel->getOrgIdByUUID($org_uuid);
            }
            else {
                $org_id = null;
            }
            if ($orgModel->createOrganization(
                $post,
                $this->zfcUserAuthentication()->getIdentity()->getId(),
                $org_id
            )
            ) {
                $result = "Успешо";
            } else {
                $result = "Ошибка";
            }

            return new ViewModel(array(
                'result' => $result
            ));

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

    }
}
