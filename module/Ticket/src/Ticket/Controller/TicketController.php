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

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormSelect;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TicketController extends AbstractActionController
{
    protected $ticketModel;
    protected $companyUserModel;

    public function indexAction()
    {

        $tick = $this->getTicketModel();
        return new ViewModel(array(
            'res' => $tick->returnAllTicket()
        ));
    }

    public function myAction()
    {
        $res = $this->getTicketModel();
        $tick = $res->returnMyTicket($this->zfcUserAuthentication()->getIdentity()->getId());
        return new ViewModel(array(
            'tick' => $tick
        ));
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function editAction()
    {
        $tickModel = $this->getTicketModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $tick = $tickModel->listTicket($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');
        return new ViewModel(array(
            'form' => $form,
            'res' => $tick,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $this->getTicketModel();
        return new ViewModel(array(
            'res' => $res->listTicket($id)
        ));

    }

    public function deleteAction()
    {
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getTicketModel();
        $resModel->deleteTicket($uuid);
        return $this->redirect()->toUrl('/tickets/my');
    }

    public function addTicketAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $comUserModel = $this->getCompanyUserModel();
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $org_id = $comUserModel->getOrgIdByUserId($user_id);

        $res = $this->getTicketModel();
        $res->addTicket($this->getRequest()->getPost(), $user_id, $org_id, $id);
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

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function copyAction() {
        $uuid=$this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getTicketModel();
        $resModel->copyTicket($uuid);
        return $this->redirect()->toUrl('/tickets/my');
    }
}
