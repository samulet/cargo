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
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;

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
                //die(var_dump($result));
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
    public function getExcelAction() {
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        date_default_timezone_set('Europe/London');

        /** PHPExcel_IOFactory */
       // require_once '../Classes/PHPExcel/IOFactory.php';



        echo date('H:i:s') , " Load from Excel5 template" , EOL;
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("/var/www/php3/excel/Examples/templates/30template.xls");




        echo date('H:i:s') , " Add new data to the template" , EOL;
        $data = array(array('title'		=> 'Excel for dummies',
            'price'		=> 17.99,
            'quantity'	=> 2
        ),
            array('title'		=> 'PHP for dummies',
                'price'		=> 15.99,
                'quantity'	=> 1
            ),
            array('title'		=> 'Inside OOP',
                'price'		=> 12.95,
                'quantity'	=> 1
            )
        );

        $objPHPExcel->getActiveSheet()->setCellValue('D1', PHPExcel_Shared_Date::PHPToExcel(time()));

        $baseRow = 5;
        foreach($data as $r => $dataRow) {
            $row = $baseRow + $r;
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
                ->setCellValue('B'.$row, $dataRow['title'])
                ->setCellValue('C'.$row, $dataRow['price'])
                ->setCellValue('D'.$row, $dataRow['quantity'])
                ->setCellValue('E'.$row, '=C'.$row.'*D'.$row);
        }
        $objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);


        echo date('H:i:s') , " Write to Excel5 format" , EOL;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('/var/www/php3/excel/Examples/templates/30templateNew.xls');
        echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', '/var/www/php3/excel/Examples/templates/30templateNew.xls') , EOL;


// Echo memory peak usage
        echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
        echo date('H:i:s') , " Done writing file" , EOL;
        echo 'File has been created in ' , getcwd() , EOL;
        die(var_dump('Files have been created in ' , getcwd() , EOL)) ;
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
