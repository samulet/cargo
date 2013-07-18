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
        $type = $this->getEvent()->getRouteMatch()->getParam('type');
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if($id=='search') {
            $type=$id;
        }
        $ticketModel = $this->getTicketModel();
        $typeForm=array();
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Ticket');
        $formWay= $builder->createForm('Ticket\Entity\TicketWay');
        $docWay= $builder->createForm('Ticket\Entity\DocumentWay');

        $form_array=array();

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);
        $formVehicleData=$addListModel->returnDataArray(array(),'vehicle',$orgListId);

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData,$form_array);
        //$formVehicle=$fillFrom->fillFrom($formVehicle,$formVehicleData);
        $formWay=$fillFrom->fillFromVehicleSpecial($formWay,$formData,array('typeLoad'));
        $formWay=$fillFrom->fillFromVehicleSpecial($formWay,$formVehicleData,array('type'));


        $formCargoOwnerData=$ticketModel->getCargoOwnerData($userListId);

        $formWay=$fillFrom->fillCargoOwner($formWay,$formCargoOwnerData);
        $docWay=$fillFrom->fillFromVehicleSpecial($docWay,$formData,array('docType'));

        $formsDocArray=array($docWay);
        $formsArray=array(
            array(
                'formWay' =>$formWay,
                'formsDocArray'=>$formsDocArray

            )
        );
        if(empty($type)) {
            if(!empty($post->submit)) {

                $result=$ticketModel->unSplitArray(get_object_vars($post));
                $error=0;
                $formsArray=array();

                foreach($result as $resF) {
                    $newForm= clone $formWay;
                    $resF['submit']='submit';
                    $newForm->setData($resF);
                    if(!$newForm->isValid()) {
                        $error++;
                    }
                    array_push($formsArray,$newForm);
                }

                $form->setData($post);
                if(!$form->isValid()) {
                    $error++;
                }

                if(empty($error)) {
                    $comUserModel = $this->getCompanyUserModel();
                    $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
                    $org_id = $comUserModel->getOrgIdByUserId($user_id);

                    $ticketModel->addTicket($this->getRequest()->getPost(), $user_id, $org_id, $id);
                    return $this->redirect()->toUrl('/tickets/my');
                }
            }
        } else {
            $ticket = $ticketModel->listTicket($id);
            $ticketWay=$ticketModel->returnAllWays($ticket['id']);
            if( ($type=='copy')||($type=='edit')||($type=='list') ) {
                $form->setData($ticket);

                $formsArray=array();
                foreach($ticketWay as $resF) {
                    $newForm= clone $formWay;
                    $newForm->setData($resF);
                    array_push($formsArray,$newForm);
                }
                if($type=='edit') {
                    $typeForm['action']='edit';
                    $typeForm['id']=$id;
                }
                elseif($type=='copy') {
                    $typeForm['action']='copy';
                    $typeForm['id']=$id;
                } elseif($type=='list') {
                    foreach ($formsArray as $formWay) {
                        foreach ($formWay as $wayEl) {
                            $wayEl->setAttributes(array( 'disabled' => 'disabled' ));
                        }
                    }

                    foreach ($form as $el) {
                        $el->setAttributes(array( 'disabled' => 'disabled' ));
                    }
                    $typeForm['action']='list';
                    $typeForm['id']=$id;
                }
            } elseif($type=='search') {
                foreach ($formsArray as $formWay) {
                    foreach ($formWay as $wayEl) {
                        $wayEl->setAttributes(array('required'  => '' ));
                    }
                }
                foreach ($form as $el) {
                    $el->setAttributes(array('required'  => '' ));
                }
                $typeForm['action']='search';
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'formsArray' =>$formsArray,
            'typeForm' => $typeForm
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
