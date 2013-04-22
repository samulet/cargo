<?php
namespace Organization\Controller;

use Organization\Entity\Company;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Organization\Entity\Organization;

class OrganizationController extends AbstractActionController
{
    public function indexAction()
    {
       error_reporting(E_ALL | E_STRICT) ;
       ini_set('display_errors', 'On');
       $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $org = new Company();
        $org->setDescription('Test!');

        $objectManager->persist($org);
        $objectManager->flush();

        die(var_dump($org->getId()));
    }

    public function addAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }
}
