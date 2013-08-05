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
use PHPExcel_RichText;
use PHPExcel_Style_Alignment;
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

        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $ticketModel = $this->getTicketModel();
        $ticket = $ticketModel->listTicket($id);
        $ticketWay=$ticketModel->returnAllWays($ticket['id']);
        $orgModel = $this->getOrganizationModel();
        $org = $orgModel->getOrganization($ticket['ownerOrgId']);
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);


        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("public/xls/templateTicket.xls");

        $counter=1;
        $offset=9;
        $step=14;
//die(var_dump(get_object_vars($ticket['created'])));
        $mainParams=1;
        $objPHPExcel->getActiveSheet()
            ->setCellValue('D'.($mainParams), $ticket['uuid'])
            ->setCellValue('F'.($mainParams), get_object_vars($ticket['created'])['date']);
        $mainParams=$mainParams+2;

        $objPHPExcel->getActiveSheet()
            ->setCellValue('D'.($mainParams), $org['name'])
            ->setCellValue('D'.(++$mainParams), '')
            ->setCellValue('D'.(++$mainParams), '')
            ->setCellValue('D'.(++$mainParams), '')
            ->setCellValue('D'.(++$mainParams), $ticket['money'].' '.$ticket['currency']);
        foreach($ticketWay as $way) {
            if($counter!=1) {
                $start=$offset+($counter-1)*$step;

                //  $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('A'.($offset).':G'.($offset)), 'A'.$start.':G'.$start);
                //$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('A'.($offset+1).':G'.($offset+$step)), 'A'.($start+1).':G'.($start+$step-1));

                $objPHPExcel->getActiveSheet()->getStyle('D'.($start+1).':G'.($start+$step-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':G'.$start)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$start.':G'.$start);
                $copyCounter=$offset+1;
                for($i=$start+1;$i<$start+$step;$i++) {
                    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
                    $objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':G'.$i);
                   // $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $objPHPExcel->getActiveSheet()->getCell('A'.$copyCounter)->getValue());
                    //  $objPHPExcel->getActiveSheet()->getStyle('D10')->getFont()->setBold(true);
                    //$objPHPExcel->getActiveSheet()->getStyle('D10:D1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $copyCounter++;
                }

                //$objBold= $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                //$objPHPExcel->getActiveSheet()->setStyle('A'.$start,$objBold);
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A'.($start), 'Загрузка '.$counter);
            } else {
                $start=$offset;
            }
            $objPHPExcel->getActiveSheet()
                ->setCellValue('D'.(++$start), $way['cargoOwner'])
                ->setCellValue('D'.(++$start), '')
                ->setCellValue('D'.(++$start), $way['dateStart'].' / '.$way['timeStart'])
                ->setCellValue('D'.(++$start), $way['areaLoad'])
                ->setCellValue('D'.(++$start), $way['dateEnd'].' / '.$way['timeEnd'])
                ->setCellValue('D'.(++$start), $way['areaUnload'])
                ->setCellValue('D'.(++$start), '')
                ->setCellValue('D'.(++$start), '')
                ->setCellValue('D'.(++$start), $way['weight'])
                ->setCellValue('D'.(++$start), $way['pallet'])
                ->setCellValue('D'.(++$start), $way['type'])
                ->setCellValue('D'.(++$start), $way['temperature'])
            ->setCellValue('D'.(++$start), $way['note']);
            $counter++;

        }
      //  $objPHPExcel->getActiveSheet()->getStyle('D10')->getFont()->setBold(true);
       // $objPHPExcel->getActiveSheet()->getStyle('D10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean() ;
        $objWriter->save('php://output');
       // $objWriter->save('public/xls/ticket.xls');
        ob_end_flush();
    }

    public function getExcel2Action() {
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        date_default_timezone_set('Europe/London');

        /** PHPExcel_IOFactory */
     //   require_once '../Classes/PHPExcel/IOFactory.php';



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
        $objPHPExcel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        echo date('H:i:s') , " Write to Excel5 format" , EOL;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('/var/www/php3/excel/Examples/30template2.xls');
        echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


// Echo memory peak usage
        echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
        echo date('H:i:s') , " Done writing file" , EOL;
        echo 'File has been created in ' , getcwd() , EOL;
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
