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
use Ticket\Entity\FiltersArrayStatic;

class TicketController extends AbstractActionController
{

    protected $companyUserModel;
    protected $ticketModel;
    protected $cargoModel;
    protected $addListModel;
    protected $interactionModel;

    protected $organizationModel;
    public function indexAction()
    {
        $res = $this->getTicketModel();
        return new ViewModel(array(
            'res' => $res->returnTickets(array('activated'=>'1'))
        ));
    }

    public function myAction()
    {
        $res = $this->getTicketModel();
        $ticket=$res->returnTickets(array('ownerId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())));
        $filterArray=$this->addFunction(null,null,'search');
        $fillFrom=new AddListForm();

        $filterArray['form']=$fillFrom->fillMultiFields($filterArray['form'],$filterArray['formsArray'][0]['formWay'],$filterArray['formsArray'][0]['formsDocArray'][0]);
        return new ViewModel(
            array('res' => $ticket)+$filterArray);
    }

    public function myAccAction()
    {
        $res = $this->getTicketModel();
        $ticket=$res->returnMyAccTicket($this->zfcUserAuthentication()->getIdentity()->getCurrentOrg());
        return new ViewModel(array(
            'res' => $ticket
        ));
    }

    public function addFunction($post,$type,$id) {
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


        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);
        $formVehicleData=$addListModel->returnDataArray(array(),'vehicle',$orgListId);

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData,$form_array);

        $formWay=$fillFrom->fillFromVehicleSpecial($formWay,$formData,array('typeLoad'));
        $form=$fillFrom->fillFromVehicleSpecial($form,$formVehicleData,array('type'));


        $formCargoOwnerData=$ticketModel->getCargoOwnerData($orgListId);

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
                    if(!empty($resF['doc'])) {
                        $formsDocArray=array();
                        foreach($resF['doc'] as $doc) {
                            $newFormDoc= clone $docWay;
                            $doc['submit']='submit';
                            $newFormDoc->setData($doc);
                            if(!$newFormDoc->isValid()) {
                                $error++;
                            }
                            array_push($formsDocArray,$newFormDoc);
                        }
                    }
                    array_push($formsArray,array('formWay' =>$newForm,'formsDocArray'=>$formsDocArray));
                }

                $form->setData($post);
                if(!$form->isValid()) {
                    $error++;

                }

                if(empty($error)) {
                    $ticketModel->addTicket($this->getRequest()->getPost(), $comListId, $orgListId, $id);
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
                    $documentWays=$ticketModel->getDocumentWay($resF['id']);
                    if(!empty($documentWays)) {
                        $formsDocArray=array();
                        foreach($documentWays as $doc) {
                            $newFormDoc= clone $docWay;
                            $doc['submit']='submit';
                            $newFormDoc->setData($doc);

                            array_push($formsDocArray,$newFormDoc);
                        }
                    }
                    array_push($formsArray,array('formWay' =>$newForm,'formsDocArray'=>$formsDocArray));
                }

                if($type=='edit') {
                    $typeForm['action']='edit';
                    $typeForm['id']=$id;
                }
                elseif($type=='copy') {
                    $typeForm['action']='copy';
                    $typeForm['id']=$id;
                } elseif($type=='list') {
                    foreach ($formsArray as $formElement) {
                        $formWay=$formElement['formWay'];
                        foreach($formElement['formsDocArray'] as $docWay) {
                            foreach ($docWay as $wayEl) {
                                $wayEl->setAttributes(array( 'disabled' => 'disabled' ));
                            }
                        }
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
                if(!empty($post)) {
                    $postData=get_object_vars($post);
                    $formsArray[0]['formWay']->setData($postData);
                    $formsArray[0]['formsDocArray'][0]->setData($postData);
                    $form->setData($postData);
                }


                foreach ($formsArray as $formElement) {

                    $formWay=$formElement['formWay'];
                    foreach($formElement['formsDocArray'] as $docWay) {
                        foreach ($docWay as $wayEl) {
                            $wayEl->setAttributes(array('required'  => '' ));
                        }
                    }
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
        $ticketModel->addBootstrap3Class($form,$formsArray);
        return array(
            'form' => $form,
            'formsArray' =>$formsArray,
            'typeForm' => $typeForm
        );
    }

    public function addAction()
    {
        $post=$this->getRequest()->getPost();
        $type = $this->getEvent()->getRouteMatch()->getParam('type');
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        return new ViewModel($this->addFunction($post,$type,$id));
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

        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);

        $fillFrom=new AddListForm();
        $formWay=$fillFrom->fillFrom($formWay,$formData);


        return new ViewModel(array(
            'form' => $form,
            'formWay' =>$formWay

        ));
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

    public function getResultsAction() {
        $res = $this->getTicketModel();
        $post=$this->getRequest()->getPost();

        if(!empty($post->multiField)) {
            $multiField=$post->multiField;
            $multiFieldData=$multiField;
            unset($post->multiField);
            $multiField=$res->multiFieldProc($multiField);
        } else {
            $multiFieldData=array();
            $multiField=array();
        }

        $ticket=$res->returnSearchTicket($post);


        $filterData=FiltersArrayStatic::$list;
        foreach($filterData as $key => &$value) {
            if(!empty($post->$key)) {
                $value=$post->$key;
            }
        }


        $filterArray=$this->addFunction($post,null,'search');
        $fillFrom=new AddListForm();

        $filterArray['form']=$fillFrom->fillMultiFields($filterArray['form'],$filterArray['formsArray'][0]['formWay'],$filterArray['formsArray'][0]['formsDocArray'][0]);

        return new ViewModel(array(
            'res' => $ticket,
            'multiField'=>$multiField,
            'filterData' =>$filterData,
            'multiFieldData'=>$multiFieldData
        )+$filterArray);
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
