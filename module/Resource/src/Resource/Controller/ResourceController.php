<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/1/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resource\Controller;

    use Entity\Recources;
    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
    use Zend\Form\Annotation\AnnotationBuilder;
    use Zend\Form\Element\Checkbox;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;

    class ResourceController extends AbstractActionController
    {
        protected $resourceModel;

        public function indexAction()
        {
            $res=$this->getResourceModel();
            return new ViewModel(array(
                'res' =>  $res->returnAllResource()
            ));
        }

        public function myAction()
        {
            $res=$this->getResourceModel();
            return new ViewModel(array(
                'res' => $res->returnMyResource($this->zfcUserAuthentication()->getIdentity()->getId())
            ));
        }

        public function addAction()
        {
            $builder = new AnnotationBuilder();
            $form    = $builder->createForm('Resource\Entity\Resource');
            return new ViewModel(array(
                'form' => $form
            ));
        }

        public function editAction()
        {

        }

        public function listAction()
        {
            $id = $this->getEvent()->getRouteMatch()->getParam('id');
            $res=$this->getResourceModel();
            //dir(var_dump($res->listResource($id)));
            return new ViewModel(array(
                'res' => $res->listResource($id)
            ));

        }

        public function deleteAction()
        {

         }

        public function addResourceAction() {
            $res=$this->getResourceModel();
            $res->addResource($this->getRequest()->getPost(),$this->zfcUserAuthentication()->getIdentity()->getId());
            return $this->redirect()->toUrl('/resources/my');
        }

        public function getResourceModel()
        {
            if (!$this->resourceModel) {
                $sm = $this->getServiceLocator();
                $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
            }
            return $this->resourceModel;
        }
}
