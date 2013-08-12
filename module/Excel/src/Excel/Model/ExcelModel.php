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