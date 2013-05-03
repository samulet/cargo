<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/1/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Ticket\Controller;

    use Entity\Recources;
    use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
    use Zend\Form\Annotation\AnnotationBuilder;
    use Zend\Form\Element\Checkbox;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;

    class TicketController extends AbstractActionController
    {
        protected $ticketModel;

        public function indexAction()
        {
            $res=$this->getTicketModel();
            return new ViewModel(array(
                'res' =>  $res->returnAllTicket()
            ));
        }

        public function myAction()
        {
            $res=$this->getTicketModel();
            return new ViewModel(array(
                'res' => $res->returnMyTicket($this->zfcUserAuthentication()->getIdentity()->getId())
            ));
        }

        public function addAction()
        {
            $builder = new AnnotationBuilder();
            $form    = $builder->createForm('Ticket\Entity\Ticket');
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
            $res=$this->getTicketModel();
            //dir(var_dump($res->listTicket($id)));
            return new ViewModel(array(
                'res' => $res->listTicket($id)
            ));

        }

        public function deleteAction()
        {

         }

        public function addTicketAction() {
            $res=$this->getTicketModel();
            $res->addTicket($this->getRequest()->getPost(),$this->zfcUserAuthentication()->getIdentity()->getId());
            return $this->redirect()->toUrl('/tickets/my');
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
