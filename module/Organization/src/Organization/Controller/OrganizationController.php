<?php
namespace Organization\Controller {

    use Organization\Entity\Company;
    use Organization\Form\OrganizationCreate;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Organization\Entity\Organization;
    use Organization\Model\OrganizationModel;

    use Doctrine\MongoDB\Connection;
    use Doctrine\ODM\MongoDB\Configuration;
    use Doctrine\ODM\MongoDB\DocumentManager;
    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

    use Doctrine\ODM\MongoDB\Id\UuidGenerator;

    class OrganizationController extends AbstractActionController
    {
        protected $organizationModel;

        public function indexAction()
        {
           // $this->getS
            error_reporting(E_ALL | E_STRICT) ;
            ini_set('display_errors', 'On');
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $orgModel=$this->getOrganizationModel();
            $org=$orgModel->returnOrganizations($this->zfcUserAuthentication()->getIdentity()->getId());



            return new ViewModel(array(
                'org' =>$org
            ));

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
            $org_id=$this->getEvent()->getRouteMatch()->getParam('id');
        }

        public function listAction() {
            $this->loginControl();
            $org_id=$this->getEvent()->getRouteMatch()->getParam('id');
            $orgModel=$this->getOrganizationModel();
            $org=$orgModel->getOrganization($org_id);
            if(!$org) $org=false;
            return new ViewModel(array(
                'org' =>$org
            ));
        }

        public function deleteAction()
        {
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
        }

        public function createOrganizationAction(){
            $this->loginControl(); //проверяем, авторизован ли юзер, если нет перенаправляем на страницу авторизации
            $post=$this->getRequest()->getPost();
            $orgModel=$this->getOrganizationModel();
            if($orgModel->createOrganization($post, $this->zfcUserAuthentication()->getIdentity()->getId())) $result="Успешо";
            else $result="Ошибка";

            return new ViewModel(array(
                    'result' =>$result
                ));

        }
        private function loginControl() {
            if ($this->zfcUserAuthentication()->hasIdentity()) return true;
            else return $this->redirect()->toUrl('/user/login');
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
}
