<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Excel\Model;


use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_RichText;
use PHPExcel_Style_Alignment;

class ExcelModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;
    protected $resourceModel;
    protected $ticketModel;
    protected $vehicleModel;
    protected $organizationModel;

    public function getExcel($id) {
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
        $offset=11;
        $step=13;
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
            ->setCellValue('D'.(++$mainParams), $ticket['type'])
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
                $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':G'.$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

    public function generateTemplate($id) {
        $ticketModel = $this->getTicketModel();
        $ticket = $ticketModel->listTicket($id);
        $ticketWay=$ticketModel->returnAllWays($ticket['id']);
        $orgModel = $this->getOrganizationModel();
        $org = $orgModel->getOrganization($ticket['ownerOrgId']);
        $ticket['owner']=$org['name'];
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);


        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("public/xls/templateTrue2.xls");


        $coord=$this->getCoordinates($objPHPExcel,$ticket,$ticketWay);

        $mode='down';

        $objWriter=$this->fillCoordinates($objPHPExcel,$ticket,$ticketWay,$coord, $mode) ;

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

    public function getCoordinates($objPHPExcel,$ticket,$ticketWay) {
        $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        $lastColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $lastColumn++;
        $resultArray=array(
            "ticket" => array(),
            "ticketWay" => array(),
            "special" => array(),
            "title" => array(),
            "offset" => array()
        );
        for ($column = 'A'; $column != $lastColumn; $column++) {
            $offsetMax=0;
            $offsetMin=100000000000000000000000000000000000000;
            for ($row = 1; $row <= $lastRow; $row++) {
                $cell= $objPHPExcel->getActiveSheet()->getCell($column.$row);
                if(!empty($cell)) {
                    $cellArr=explode('_',$cell);
                    if(!empty($cellArr[1])) {
                        $cellArrMany=explode('/',$cellArr[0]);
                        if(($cellArr[1]=='main')||($cellArr[1]=='way')||($cellArr[1]=='special')) {
                            $trueArray=array();
                            foreach($cellArrMany as $cellArrOne) {
                                $cell=$cellArrOne;
                                $trueArray=$trueArray+array($cell=> array("column"=>$column,"row"=>$row));
                            }
                            if($cellArr[1]=='main') {
                                if(isset($ticket[$cell])) {
                                    $resultArray["ticket"]=$resultArray["ticket"]+ $trueArray;
                                }
                            } elseif($cellArr[1]=='way') {
                                if($offsetMax<$row) {
                                    $offsetMax=$row;
                                }
                                if($offsetMin>$row) {
                                    $offsetMin=$row;
                                }
                                $resultArray["offset"]["down"]=$offsetMax-$offsetMin;
                                if($cellArr[0]=='title')  {
                                    array_push($resultArray["title"], $trueArray['title']);
                                } else {
                                    if(isset($ticketWay[0][$cell])) {
                                        $resultArray["ticketWay"]=$resultArray["ticketWay"]+ $trueArray;
                                    }
                                }
                            } elseif($cellArr[1]=='special') {
                                if($offsetMax<$row) {
                                    $offsetMax=$row;
                                }
                                if($offsetMin>$row) {
                                    $offsetMin=$row;
                                }
                                $resultArray["offset"]["down"]=$offsetMax-$offsetMin;
                                $resultArray["special"]=$resultArray["special"]+ $trueArray;
                            }
                        }
                    }
                }
            }

        }

        return $resultArray;
    }

    public function fillCoordinates($objPHPExcel,$ticket,$ticketWay,$coord, $mode) {
        $objPHPExcel=$this->clearFields($objPHPExcel,$coord);
        if($mode=='right') {
            $objPHPExcel= $this->fillCoordinatesRight($objPHPExcel,$ticketWay,$coord);
        } elseif($mode=='down') {
            $objPHPExcel= $this->fillCoordinatesDown($objPHPExcel,$ticketWay, $coord);
        } elseif($mode=='worksheet') {
            $objPHPExcel=$this->fillCoordinatesWorksheet($objPHPExcel,$ticketWay,$coord);
        }

        foreach($coord['ticket'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'].$value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'].$value['row']).' '.$ticket[$key]);
        }
        return $objPHPExcel;
    }

    public function clearFields($objPHPExcel,$coord) {
        foreach($coord['ticket'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'].$value['row'], '');
        }
        foreach($coord['ticketWay'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'].$value['row'], '');
        }
        foreach($coord['special'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'].$value['row'], '');
        }
        foreach($coord['title'] as $key => $value) {
            $cell= $objPHPExcel->getActiveSheet()->getCell($value['column'].$value['row']);
            $cellArr=explode('_',$cell);
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'].$value['row'], $cellArr[2]);
        }
        return $objPHPExcel;
    }

    public function fillCoordinatesRight($objPHPExcel,$ticketWay, $coord) {

        $coordWay=$coord['ticketWay'];
        $loadCountName="Загрузка №";
        $loadCount=1;

        foreach($ticketWay as $tick) {
            foreach($coordWay as $key => &$value) {
                if(isset($tick[$key])) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($value['column'].$value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'].$value['row']).' '.$tick[$key]);
                    $objPHPExcel->getActiveSheet()->getStyle($value['column'].$value['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $value['column']++;
                }
            }
            $objPHPExcel->getActiveSheet()
                ->setCellValue($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'], $loadCountName.$loadCount);
            $loadCount++;
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'])->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getColumnDimension($coord['special']['loadNumber']['column'])
                ->setAutoSize(true);
            $coord['special']['loadNumber']['column']++;

        }
        return $objPHPExcel;
    }

    public function fillCoordinatesDown($objPHPExcel,$ticketWay, $coord) {
        $offset=$coord['offset']['down']+2;

        $coordWay=$coord['ticketWay'];
        $loadCountName="Загрузка №";
        $loadCount=1;
        foreach($ticketWay as $tick) {
            foreach($coordWay as $key => &$value) {
                if(isset($tick[$key])) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($value['column'].$value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'].$value['row']).' '.$tick[$key]);
                    $objPHPExcel->getActiveSheet()->getStyle($value['column'].$value['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $value['row']=$value['row']+$offset;
                }
            }

            foreach($coord['title'] as &$value) {
                $row=$value['row'];
                if(isset($value['newRow'])) {
                    $row=$value['newRow'];
                } else {
                    $value['newRow']=$value['row'];
                }
                $cell=$objPHPExcel->getActiveSheet()->getCell($value['column'].$value['row'])->getValue();

                    $objPHPExcel->getActiveSheet()
                      ->setCellValue($value['column'].$row, $cell);
                    $value['newRow']=$value['newRow']+$offset;
            }

            $objPHPExcel->getActiveSheet()
                ->setCellValue($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'], $loadCountName.$loadCount);
            $loadCount++;
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'])->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'].$coord['special']['loadNumber']['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getColumnDimension($coord['special']['loadNumber']['column'])
                ->setAutoSize(true);
            $coord['special']['loadNumber']['row']=$coord['special']['loadNumber']['row']+$offset;

        }

        return $objPHPExcel;
    }

    public function fillCoordinatesWorksheet($objPHPExcel,$ticketWay, $coord) {

    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getResourceModel()
    {
        if (!$this->resourceModel) {
            $sm = $this->getServiceLocator();
            $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->resourceModel;
    }

    public function getTicketModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
        }
        return $this->ticketModel;
    }
    public function getVehicleModel()
    {
        if (!$this->vehicleModel) {
            $sm = $this->getServiceLocator();
            $this->vehicleModel = $sm->get('Resource\Model\VehicleModel');
        }
        return $this->vehicleModel;
    }
    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

}