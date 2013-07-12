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
use Ticket\Form\TicketForm;
use AddList\Form\AddListForm;
class TicketController extends AbstractActionController
{

    protected $companyUserModel;
    protected $ticketModel;
    protected $cargoModel;
    protected $addListModel;
    protected $interactionModel;


    public function indexAction()
    {
        $res = $this->getTicketModel();
        return new ViewModel(array(
            'res' => $res->returnAllTicket()
        ));
    }

    public function myAction()
    {
        $res = $this->getTicketModel();
        $ticket=$res->returnMyTicket($this->zfcUserAuthentication()->getIdentity()->getId());
        return new ViewModel(array(
            'res' => $ticket
        ));
    }

    public function addAction()
    {
        $post=$this->getRequest()->getPost();

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');

        $formWay= $builder->createForm('Ticket\Entity\TicketWay');

        $formVehicle = $builder->createForm('Resource\Entity\Vehicle');

        $form_array=array();

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);
        $formVehicleData=$addListModel->returnDataArray(array(),'vehicle',$orgListId);

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData,$form_array);
        $formVehicle=$fillFrom->fillFrom($formVehicle,$formVehicleData);

        if(!empty($post->submit)) {
            $error=0;
            $formWay->setData($post);
            if(!$formWay->isValid()) {
                $error++;
            }
            $form->setData($post);
            if(!$form->isValid()) {
                $error++;
            }
            die(var_dump($formWay->getMessages(),$form->getMessages() ));
            if(empty($error)) {
                $id = $this->getEvent()->getRouteMatch()->getParam('id');
                $comUserModel = $this->getCompanyUserModel();
                $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
                $org_id = $comUserModel->getOrgIdByUserId($user_id);
                $res = $this->getTicketModel();
                $res->addTicket($this->getRequest()->getPost(), $user_id, $org_id, $id);
                return $this->redirect()->toUrl('/tickets/my');
            }
        }



        return new ViewModel(array(
            'form' => $form,
            'formWay' =>$formWay,
            'formVehicle'=>$formVehicle

        ));
    }

    public function editAction()
    {
        $resModel = $this->getTicketModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listTicket($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');

        $formWay= $builder->createForm('Ticket\Entity\TicketWay');


        $form_array=array();

        $addListModel = $this->getAddListModel();

        $formData=$addListModel->returnDataArray($form_array,'ticketWay');

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData,$form_array);


        $way=$resModel->returnAllWays($res['id']);





        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'formWay'=>$formWay,
            'way'=>$way,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $resModel = $this->getTicketModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listTicket($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');

        $formWay= $builder->createForm('Ticket\Entity\TicketWay');


        $form_array=array();

        $addListModel = $this->getAddListModel();

        $formData=$addListModel->getAllDataArray('ticketWay');

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData);


        $way=$resModel->returnAllWays($res['id']);





        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'formWay'=>$formWay,
            'way'=>$way,
            'id' => $id
        ));

    }

    public function deleteAction()
    {
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getTicketModel();
        $resModel->deleteTicket($uuid);
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
        $resModel = $this->getTicketModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listTicket($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,

        ));
    }
    public function searchAction() {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');

        $formWay= $builder->createForm('Ticket\Entity\TicketWay');

        $form_array=array();

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData);


        return new ViewModel(array(
            'form' => $form,
            'formWay' =>$formWay

        ));
    }
    public function getResultsAction() {
        $res = $this->getTicketModel();
        $ticket=$res->returnSearchTicket($this->getRequest()->getPost());
        return new ViewModel(array(
            'res' => $ticket
        ));
    }
    public function getCargoModel()
    {
        if (!$this->cargoModel) {
            $sm = $this->getServiceLocator();
            $this->cargoModel = $sm->get('Ticket\Model\CargoModel');
        }
        return $this->cargoModel;
    }
    public function getAddListModel()
    {
        if (!$this->addListModel) {
            $sm = $this->getServiceLocator();
            $this->addListModel = $sm->get('AddList\Model\AddListModel');
        }
        return $this->addListModel;
    }

    public function getInteractionModel()
    {
        if (!$this->interactionModel) {
            $sm = $this->getServiceLocator();
            $this->interactionModel = $sm->get('Interaction\Model\InteractionModel');
        }
        return $this->interactionModel;
    }


}
