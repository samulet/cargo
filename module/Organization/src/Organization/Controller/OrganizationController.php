<?php
namespace Organization\Controller {

    use Organization\Entity\Company;
    use Organization\Form\OrganizationCreate;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Organization\Entity\Organization;

    use Doctrine\MongoDB\Connection;
    use Doctrine\ODM\MongoDB\Configuration;
    use Doctrine\ODM\MongoDB\DocumentManager;
    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

    use Doctrine\ODM\MongoDB\Id\UuidGenerator;

    class OrganizationController extends AbstractActionController
    {
        public function indexAction()
        {
            error_reporting(E_ALL | E_STRICT) ;
            ini_set('display_errors', 'On');

            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $org = $objectManager->getRepository('Organization\Entity\Organization')->findBy(array('activated'=>1));

       //     die(var_dump($org));
            return new ViewModel(array(
                'org' =>$org
            ));
        }

        public function addAction()
        {
            $form = new OrganizationCreate();
            return new ViewModel(array(
                'form' => $form
            ));
        }

        public function editAction()
        {
        }

        public function deleteAction()
        {
        }

        public function createOrganizationAction(){
            $var=$this->getRequest()->getPost();
            if(!empty($var->csrf)) {
                $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
                $org_item=$var->organization;
                $org = new Organization();
                $org->setDescription($org_item['description']);
                $org->setOrgName($org_item['orgName']);
                $org->setOrgType($org_item['orgType']);
                $org->setActivated(1);
                $objectManager->persist($org);
                $objectManager->flush();
                return new ViewModel(array(
                    'result' => 'успешно!'
                ));
            }
            else die();
        }
    }
}
