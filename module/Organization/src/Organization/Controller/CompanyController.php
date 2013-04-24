<?php
namespace Organization\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Organization\Entity\Company;
use Organization\Entity\Organization;
use Organization\Form\CompanyCreate;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

class CompanyController extends AbstractActionController
{
    public function indexAction()
    {

    }

    public function addAction()
    {
        $this->loginControl();
        $form = new CompanyCreate();
        return new ViewModel(array(
            'form' => $form
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
}