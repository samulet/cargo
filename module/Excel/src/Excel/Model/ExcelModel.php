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
use Excel\Entity\ExcelStatic;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_RichText;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;

class ExcelModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;
    protected $resourceModel;
    protected $ticketModel;
    protected $vehicleModel;
    protected $organizationModel;
    protected $companyModel;
    protected $excelModel;

    public function getExcel($id)
    {
        $ticketModel = $this->getTicketModel();
        $ticket = $ticketModel->listTicket($id);
        $ticketWay = $ticketModel->returnAllWays($ticket['id']);
        $orgModel = $this->getAccountModel();
        $org = $orgModel->getAccount($ticket['ownerOrgId']);
        $ticketWay = $this->addAdditionalData($ticketWay);

        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("public/xls/templateTicket.xls");

        $counter = 1;
        $offset = 11;
        $step = 13;
        $mainParams = 1;
        $objPHPExcel->getActiveSheet()
            ->setCellValue('D' . ($mainParams), $ticket['numberInt'])
            ->setCellValue('F' . ($mainParams), get_object_vars($ticket['created'])['date']);
        $mainParams = $mainParams + 2;

        $objPHPExcel->getActiveSheet()
            ->setCellValue('D' . ($mainParams), $org['name'])
            ->setCellValue('D' . (++$mainParams), '')
            ->setCellValue('D' . (++$mainParams), '')
            ->setCellValue('D' . (++$mainParams), $ticket['type'])
            ->setCellValue('D' . (++$mainParams), '')
            ->setCellValue('D' . (++$mainParams), '')
            ->setCellValue('D' . (++$mainParams), $ticket['money'] . ' ' . $ticket['currency']);
        foreach ($ticketWay as $way) {
            if ($counter != 1) {
                $start = $offset + ($counter - 1) * $step;
                $objPHPExcel->getActiveSheet()->getStyle('D' . ($start + 1) . ':G' . ($start + $step - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $start . ':G' . $start)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $start . ':G' . $start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $start . ':G' . $start);
                $copyCounter = $offset + 1;
                for ($i = $start + 1; $i < $start + $step; $i++) {
                    $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':C' . $i);
                    $objPHPExcel->getActiveSheet()->mergeCells('D' . $i . ':G' . $i);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $objPHPExcel->getActiveSheet()->getCell('A' . $copyCounter)->getValue());
                    $copyCounter++;
                }
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A' . ($start), 'Загрузка ' . $counter);
            } else {
                $start = $offset;
            }
            //'timeLoadStart','timeLoadEnd' 'timeUnloadStart','timeUnloadEnd'
            $objPHPExcel->getActiveSheet()
                ->setCellValue('D' . (++$start), $way['cargoOwner'])
                ->setCellValue('D' . (++$start), '')
                ->setCellValue('D' . (++$start), $way['dateStart'] . ' / с ' . $way['timeLoadStart'] . ' по ' . $way['timeLoadEnd'])
                ->setCellValue('D' . (++$start), $way['areaLoad'])
                ->setCellValue('D' . (++$start), $way['dateEnd'] . ' / ' . $way['timeLoadEnd'] . ' по ' . $way['timeUnloadEnd'])
                ->setCellValue('D' . (++$start), $way['areaUnload'])
                ->setCellValue('D' . (++$start), '')
                ->setCellValue('D' . (++$start), '')
                ->setCellValue('D' . (++$start), $way['weight'])
                ->setCellValue('D' . (++$start), $way['pallet'])
                ->setCellValue('D' . (++$start), $way['temperature'])
                ->setCellValue('D' . (++$start), $way['note']);
            $counter++;

        }
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');

        ob_end_flush();
    }

    public function addAdditionalData($ticketWay)
    {
        $comModel = $this->getCompanyModel();
        $ticketModel = $this->getTicketModel();

        foreach ($ticketWay as &$way) {
            $doc = $ticketModel->getDocumentWay($way['id']);
            $document = '';
            foreach ($doc as $d) {
                $document .= $d['docNumber'] . ' ' . $d['docType'] . ' ' . $d['docDate'] . ' ' . $d['docWay'] . ' ' . $d['docNote'] . ' / ';
            }
            $way['documents'] = $document;
            $data = $comModel->returnCompany($way['cargoOwner']);
            $way['cargoOwner'] = $data['property'] . ' ' . $data['name'];
        }
        return $ticketWay;
    }

    public function generateTemplate($id, $mode, $path, $newStringDown)
    {
        $ticketModel = $this->getTicketModel();
        $ticket = $ticketModel->listTicket($id);
        $ticketWay = $ticketModel->returnAllWays($ticket['id']);

        $ticketWay = $this->addAdditionalData($ticketWay);

        $orgModel = $this->getAccountModel();
        $org = $orgModel->getAccount($ticket['ownerOrgId']);
        $ticket['owner'] = $org['name'];


        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load($path);


        $coord = $this->getCoordinates($objPHPExcel);

        $objWriter = $this->fillCoordinates($objPHPExcel, $ticket, $ticketWay, $coord, $mode, $newStringDown);

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');

        ob_end_flush();
    }

    public function getCoordinates($objPHPExcel)
    {
        $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        $lastColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $ticket = ExcelStatic::$list['main'];
        $ticketWay = ExcelStatic::$list['way'];
        $lastColumn++;
        $resultArray = array(
            "ticket" => array(),
            "ticketWay" => array(),
            "special" => array(),
            "title" => array(),
            "offset" => array()
        );
        for ($column = 'A'; $column != $lastColumn; $column++) {
            $offsetMax = 0;
            $offsetMin = 1000000;
            for ($row = 1; $row <= $lastRow; $row++) {
                $cell = $objPHPExcel->getActiveSheet()->getCell($column . $row);
                if (!empty($cell)) {
                    $cellArr = explode('_', $cell);
                    if (!empty($cellArr[1])) {
                        $cellArrMany = explode('/', $cellArr[0]);
                        if (($cellArr[1] == 'main') || ($cellArr[1] == 'way') || ($cellArr[1] == 'special')) {
                            $trueArray = array();
                            foreach ($cellArrMany as $cellArrOne) {
                                $cell = $cellArrOne;
                                $trueArray = $trueArray + array($cell => array("column" => $column, "row" => $row));
                            }
                            if ($cellArr[1] == 'main') {
                                if (isset($ticket[$cell])) {
                                    $resultArray["ticket"] = $resultArray["ticket"] + $trueArray;
                                }
                            } elseif ($cellArr[1] == 'way') {
                                if ($offsetMax < $row) {
                                    $offsetMax = $row;
                                }
                                if ($offsetMin > $row) {
                                    $offsetMin = $row;
                                }
                                $resultArray["offset"]["down"] = $offsetMax - $offsetMin;
                                $resultArray["offset"]["max"] = $offsetMax;
                                if ($cellArr[0] == 'title') {
                                    array_push($resultArray["title"], $trueArray['title']);
                                } else {
                                    if (isset($ticketWay[$cell])) {
                                        $resultArray["ticketWay"] = $resultArray["ticketWay"] + $trueArray;
                                    }
                                }
                            } elseif ($cellArr[1] == 'special') {
                                if ($offsetMax < $row) {
                                    $offsetMax = $row;
                                }
                                if ($offsetMin > $row) {
                                    $offsetMin = $row;
                                }
                                $resultArray["offset"]["down"] = $offsetMax - $offsetMin;
                                $resultArray["offset"]["max"] = $offsetMax;
                                $resultArray["special"] = $resultArray["special"] + $trueArray;
                            }
                        }
                    }
                }
            }

        }

        return $resultArray;
    }

    public function fillCoordinates($objPHPExcel, $ticket, $ticketWay, $coord, $mode, $newStringDown)
    {
        $objPHPExcel = $this->clearFields($objPHPExcel, $coord);
        if ($mode == 'right') {
            $objPHPExcel = $this->fillCoordinatesRight($objPHPExcel, $ticketWay, $coord);
        } elseif ($mode == 'down') {
            $objPHPExcel = $this->fillCoordinatesDown($objPHPExcel, $ticketWay, $coord, $newStringDown);
        } elseif ($mode == 'worksheet') {
            $objPHPExcel = $this->fillCoordinatesWorksheet($objPHPExcel, $ticketWay, $coord);
        }

        foreach ($coord['ticket'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'] . $value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'] . $value['row']) . ' ' . $ticket[$key]);
        }
        return $objPHPExcel;
    }

    public function clearFields($objPHPExcel, $coord)
    {
        foreach ($coord['ticketWay'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'] . $value['row'], '');
        }
        foreach ($coord['special'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'] . $value['row'], '');
        }
        foreach ($coord['title'] as $key => $value) {
            $cell = $objPHPExcel->getActiveSheet()->getCell($value['column'] . $value['row']);
            $cellArr = explode('_', $cell);
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'] . $value['row'], $cellArr[2]);
        }
        foreach ($coord['ticket'] as $key => $value) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue($value['column'] . $value['row'], '');
        }
        return $objPHPExcel;
    }

    public function fillCoordinatesRight($objPHPExcel, $ticketWay, $coord)
    {

        $coordWay = $coord['ticketWay'];
        $loadCountName = "Загрузка №";
        $loadCount = 1;

        foreach ($ticketWay as $tick) {
            foreach ($coordWay as $key => &$value) {
                if (isset($tick[$key])) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($value['column'] . $value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'] . $value['row']) . ' ' . $tick[$key]);
                    $objPHPExcel->getActiveSheet()->getStyle($value['column'] . $value['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $value['column']++;
                }
            }
            $objPHPExcel->getActiveSheet()
                ->setCellValue($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'], $loadCountName . $loadCount);
            $loadCount++;
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'])->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getColumnDimension($coord['special']['loadNumber']['column'])
                ->setAutoSize(true);
            $coord['special']['loadNumber']['column']++;

        }
        return $objPHPExcel;
    }

    public function fillCoordinatesDown($objPHPExcel, $ticketWay, &$coord, $newStringDown)
    {
        $offset = $coord['offset']['down'] + 2;

        $coordWay = $coord['ticketWay'];
        $loadCountName = "Загрузка №";
        $loadCount = 1;
        if (!empty($newStringDown)) {
            $offsetNewRows = (count($ticketWay) - 1) * ($coord['offset']['down'] + 2);
            if ($offsetNewRows != 0) {
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($coord['offset']['max'] + 1, $offsetNewRows);

                foreach ($coord['ticket'] as $key => &$value) {
                    if ($value['row'] > $coord['offset']['max'] + 1) {
                        $value['row'] = $value['row'] + $offsetNewRows;

                    }
                }
            }
        }
        foreach ($ticketWay as $tick) {
            foreach ($coordWay as $key => &$value) {
                if (isset($tick[$key])) {
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue($value['column'] . $value['row'], $objPHPExcel->getActiveSheet()->getCell($value['column'] . $value['row']) . ' ' . $tick[$key]);
                    $objPHPExcel->getActiveSheet()->getStyle($value['column'] . $value['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $value['row'] = $value['row'] + $offset;
                }
            }

            foreach ($coord['title'] as &$value) {
                $row = $value['row'];
                if (isset($value['newRow'])) {
                    $row = $value['newRow'];
                } else {
                    $value['newRow'] = $value['row'];
                }
                $cell = $objPHPExcel->getActiveSheet()->getCell($value['column'] . $value['row'])->getValue();

                $objPHPExcel->getActiveSheet()
                    ->setCellValue($value['column'] . $row, $cell);
                $value['newRow'] = $value['newRow'] + $offset;
            }

            $objPHPExcel->getActiveSheet()
                ->setCellValue($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'], $loadCountName . $loadCount);
            $loadCount++;
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'])->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle($coord['special']['loadNumber']['column'] . $coord['special']['loadNumber']['row'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getColumnDimension($coord['special']['loadNumber']['column'])
                ->setAutoSize(true);
            $coord['special']['loadNumber']['row'] = $coord['special']['loadNumber']['row'] + $offset;

        }

        return $objPHPExcel;
    }

    public function createExcelFromTable($htmltable)
    {

        $username = ''; // user's name
        $usermail = ''; // user's emailid
        $usercompany = ''; // user's company

        $limit = 12;


        $debug = false;
        if (strlen($htmltable) == strlen(strip_tags($htmltable))) {
            echo "<br />Invalid HTML Table after Stripping Tags, nothing to Export.";
            exit;
        }
        $htmltable = strip_tags($htmltable, "<table><tr><th><thead><tbody><tfoot><td><br><br /><b><span>");
        $htmltable = str_replace("<br />", "\n", $htmltable);
        $htmltable = str_replace("<br/>", "\n", $htmltable);
        $htmltable = str_replace("<br>", "\n", $htmltable);
        $htmltable = str_replace("&nbsp;", " ", $htmltable);
        $htmltable = str_replace("\n\n", "\n", $htmltable);
//
//  Extract HTML table contents to array
//
        $htmltable = mb_convert_encoding($htmltable, 'HTML-ENTITIES', "UTF-8");
        $dom = new \DOMDocument;
        $dom->loadHTML($htmltable);
        if (!$dom) {
            echo "<br />Invalid HTML DOM, nothing to Export.";
            exit;
        }

        $dom->preserveWhiteSpace = false; // remove redundant whitespace
        $tables = $dom->getElementsByTagName('table');
        if (!is_object($tables)) {
            echo "<br />Invalid HTML Table DOM, nothing to Export.";
            exit;
        }
        //die(var_dump($dom->saveHTML()));
        $tbcnt = $tables->length - 1; // count minus 1 for 0 indexed loop over tables
        if ($tbcnt > $limit) {
            $tbcnt = $limit;
        }
//
//
// Create new PHPExcel object with default attributes
//
        $tablevar = 'test';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
        $tm = date('Y-m-d-H_i_s');
        $pos = strpos($usermail, "@");
        $user = substr($usermail, 0, $pos);
        $user = str_replace(".", "", $user);
        $tfn = $user . "_" . $tm . "_" . $tablevar . ".xlsx";
//$fname = "AuditLog/".$tfn;
        $fname = $tfn;
        $objPHPExcel->getProperties()->setCreator($username)
            ->setLastModifiedBy($username)
            ->setTitle("Automated Export")
            ->setSubject("Automated Report Generation")
            ->setDescription("Automated report generation.")
            ->setKeywords("Exported File")
            ->setCompany($usercompany)
            ->setCategory("Export");
//
// Loop over tables in DOM to create an array, each table becomes a worksheet
//
        for ($z = 0; $z <= $tbcnt; $z++) {
            $maxcols = 0;
            $totrows = 0;
            $headrows = array();
            $bodyrows = array();
            $r = 0;
            $h = 0;
            $rows = $tables->item($z)->getElementsByTagName('tr');
            $totrows = $rows->length;
            foreach ($rows as $row) {
                $ths = $row->getElementsByTagName('th');

                if (is_object($ths)) {
                    if ($ths->length > 0) {
                        $headrows[$h]['colcnt'] = $ths->length;
                        if ($ths->length > $maxcols) {
                            $maxcols = $ths->length;
                        }
                        $nodes = $ths->length - 1;
                        for ($x = 0; $x <= $nodes; $x++) {
                            $thishdg = $ths->item($x)->nodeValue;
                            $headrows[$h]['th'][] = $thishdg;
                            $headrows[$h]['bold'][] = $this->findBoldText($this->innerHTML($ths->item($x)));
                            if ($ths->item($x)->hasAttribute('style')) {
                                $style = $ths->item($x)->getAttribute('style');
                                $stylecolor = findStyleColor($style);
                                if ($stylecolor == '') {
                                    $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                                } else {
                                    $headrows[$h]['color'][] = $stylecolor;
                                }
                            } else {
                                $headrows[$h]['color'][] = $this->findSpanColor($this->innerHTML($ths->item($x)));
                            }
                            if ($ths->item($x)->hasAttribute('colspan')) {
                                $headrows[$h]['colspan'][] = $ths->item($x)->getAttribute('colspan');
                            } else {
                                $headrows[$h]['colspan'][] = 1;
                            }
                            if ($ths->item($x)->hasAttribute('align')) {
                                $headrows[$h]['align'][] = $ths->item($x)->getAttribute('align');
                            } else {
                                $headrows[$h]['align'][] = 'left';
                            }
                            if ($ths->item($x)->hasAttribute('valign')) {
                                $headrows[$h]['valign'][] = $ths->item($x)->getAttribute('valign');
                            } else {
                                $headrows[$h]['valign'][] = 'top';
                            }
                            if ($ths->item($x)->hasAttribute('bgcolor')) {
                                $headrows[$h]['bgcolor'][] = str_replace("#", "", $ths->item($x)->getAttribute('bgcolor'));
                            } else {
                                $headrows[$h]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $h++;
                    }
                }
            }

            foreach ($rows as $row) {
                $tds = $row->getElementsByTagName('td');
                if (is_object($tds)) {
                    if ($tds->length > 0) {
                        $bodyrows[$r]['colcnt'] = $tds->length;
                        if ($tds->length > $maxcols) {
                            $maxcols = $tds->length;
                        }
                        $nodes = $tds->length - 1;
                        for ($x = 0; $x <= $nodes; $x++) {
                            $thistxt = $tds->item($x)->nodeValue;
                            $bodyrows[$r]['td'][] = $thistxt;
                            $bodyrows[$r]['bold'][] = $this->findBoldText($this->innerHTML($tds->item($x)));
                            if ($tds->item($x)->hasAttribute('style')) {
                                $style = $tds->item($x)->getAttribute('style');
                                $stylecolor = $this->findStyleColor($style);
                                if ($stylecolor == '') {
                                    $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                                } else {
                                    $bodyrows[$r]['color'][] = $stylecolor;
                                }
                            } else {
                                $bodyrows[$r]['color'][] = $this->findSpanColor($this->innerHTML($tds->item($x)));
                            }
                            if ($tds->item($x)->hasAttribute('colspan')) {
                                $bodyrows[$r]['colspan'][] = $tds->item($x)->getAttribute('colspan');
                            } else {
                                $bodyrows[$r]['colspan'][] = 1;
                            }
                            if ($tds->item($x)->hasAttribute('align')) {
                                $bodyrows[$r]['align'][] = $tds->item($x)->getAttribute('align');
                            } else {
                                $bodyrows[$r]['align'][] = 'left';
                            }
                            if ($tds->item($x)->hasAttribute('valign')) {
                                $bodyrows[$r]['valign'][] = $tds->item($x)->getAttribute('valign');
                            } else {
                                $bodyrows[$r]['valign'][] = 'top';
                            }
                            if ($tds->item($x)->hasAttribute('bgcolor')) {
                                $bodyrows[$r]['bgcolor'][] = str_replace("#", "", $tds->item($x)->getAttribute('bgcolor'));
                            } else {
                                $bodyrows[$r]['bgcolor'][] = 'FFFFFF';
                            }
                        }
                        $r++;
                    }
                }
            }
            if ($z > 0) {
                $objPHPExcel->createSheet($z);
            }
            $suf = $z + 1;
            $tableid = $tablevar . $suf;
            $wksheetname = ucfirst($tableid);
            $objPHPExcel->setActiveSheetIndex($z); // each sheet corresponds to a table in html
            $objPHPExcel->getActiveSheet()->setTitle($wksheetname); // tab name
            $worksheet = $objPHPExcel->getActiveSheet(); // set worksheet we're working on
            $style_overlay = array('font' =>
            array('color' =>
            array('rgb' => '000000'), 'bold' => false,),
                'fill' =>
                array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCFF')),
                'alignment' =>
                array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
                'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            );
            $xcol = '';
            $xrow = 1;
            $usedhdrows = 0;
            $heightvars = array(1 => '42', 2 => '42', 3 => '48', 4 => '52', 5 => '58', 6 => '64', 7 => '68', 8 => '76', 9 => '82');
            for ($h = 0; $h < count($headrows); $h++) {
                $th = $headrows[$h]['th'];
                $colspans = $headrows[$h]['colspan'];
                $aligns = $headrows[$h]['align'];
                $valigns = $headrows[$h]['valign'];
                $bgcolors = $headrows[$h]['bgcolor'];
                $colcnt = $headrows[$h]['colcnt'];
                $colors = $headrows[$h]['color'];
                $bolds = $headrows[$h]['bold'];
                $usedhdrows++;
                $mergedcells = false;
                for ($t = 0; $t < count($th); $t++) {
                    if ($xcol == '') {
                        $xcol = 'A';
                    } else {
                        $xcol++;
                    }
                    $thishdg = $th[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $strbold = ($thisbold == true) ? 'true' : 'false';
                    if ($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    } else {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign; // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    $worksheet->setCellValue($xcol . $xrow, $thishdg);
                    $worksheet->getStyle($xcol . $xrow)->applyFromArray($style_overlay);

                    if ($thiscolspan > 1) { // spans more than 1 column
                        $mergedcells = true;
                        $lastxcol = $xcol;
                        for ($j = 1; $j < $thiscolspan; $j++) {
                            $lastxcol++;
                            $worksheet->setCellValue($lastxcol . $xrow, '');
                            $worksheet->getStyle($lastxcol . $xrow)->applyFromArray($style_overlay);
                        }
                        $cellRange = $xcol . $xrow . ':' . $lastxcol . $xrow;

                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $num_newlines = substr_count($thishdg, "\n"); // count number of newline chars
                        if ($num_newlines > 1) {
                            $rowheight = $heightvars[1]; // default to 35
                            if (array_key_exists($num_newlines, $heightvars)) {
                                $rowheight = $heightvars[$num_newlines];
                            } else {
                                $rowheight = 75;
                            }
                            $worksheet->getRowDimension($xrow)->setRowHeight($rowheight); // adjust heading row height
                        }
                        $xcol = $lastxcol;
                    }
                }
                $xrow++;
                $xcol = '';
            }
            //Put an auto filter on last row of heading only if last row was not merged
            if (!$mergedcells) {
                $worksheet->setAutoFilter("A$usedhdrows:" . $worksheet->getHighestColumn() . $worksheet->getHighestRow());
            }

            // Freeze heading lines starting after heading lines
            $usedhdrows++;
            $worksheet->freezePane("A$usedhdrows");

            //
            // Loop thru data rows and write them out
            //
            $xcol = '';
            $xrow = $usedhdrows;
            for ($b = 0; $b < count($bodyrows); $b++) {

                $td = $bodyrows[$b]['td'];
                $colcnt = $bodyrows[$b]['colcnt'];
                $colspans = $bodyrows[$b]['colspan'];
                $aligns = $bodyrows[$b]['align'];
                $valigns = $bodyrows[$b]['valign'];
                $bgcolors = $bodyrows[$b]['bgcolor'];
                $colors = $bodyrows[$b]['color'];
                $bolds = $bodyrows[$b]['bold'];
                for ($t = 0; $t < count($td); $t++) {
                    if ($xcol == '') {
                        $xcol = 'A';
                    } else {
                        $xcol++;
                    }
                    $thistext = $td[$t];
                    $thisalign = $aligns[$t];
                    $thisvalign = $valigns[$t];
                    $thiscolspan = $colspans[$t];
                    $thiscolor = $colors[$t];
                    $thisbg = $bgcolors[$t];
                    $thisbold = $bolds[$t];
                    $strbold = ($thisbold == true) ? 'true' : 'false';
                    if ($thisbg == 'FFFFFF') {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_NONE;
                    } else {
                        $style_overlay['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                    }
                    $style_overlay['alignment']['vertical'] = $thisvalign; // set styles for cell
                    $style_overlay['alignment']['horizontal'] = $thisalign;
                    $style_overlay['font']['color']['rgb'] = $thiscolor;
                    $style_overlay['font']['bold'] = $thisbold;
                    $style_overlay['fill']['color']['rgb'] = $thisbg;
                    if ($thiscolspan == 1) {
                        $worksheet->getColumnDimension($xcol)->setWidth(25);
                    }
                    $worksheet->setCellValue($xcol . $xrow, $thistext);

                    $worksheet->getStyle($xcol . $xrow)->applyFromArray($style_overlay);
                    if ($thiscolspan > 1) { // spans more than 1 column
                        $lastxcol = $xcol;
                        for ($j = 1; $j < $thiscolspan; $j++) {
                            $lastxcol++;
                        }
                        $cellRange = $xcol . $xrow . ':' . $lastxcol . $xrow;

                        $worksheet->mergeCells($cellRange);
                        $worksheet->getStyle($cellRange)->applyFromArray($style_overlay);
                        $xcol = $lastxcol;
                    }
                }
                $xrow++;
                $xcol = '';
            }
            // autosize columns to fit data
            $azcol = 'A';
            for ($x = 1; $x == $maxcols; $x++) {
                $worksheet->getColumnDimension($azcol)->setAutoSize(true);
                $azcol++;
            }

        } // end for over tables
        $objPHPExcel->setActiveSheetIndex(0); // set to first worksheet before close
//
// Write to Browser
//

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$fname");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save($fname);
        $objWriter->save('php://output');
        exit;

    }

    public function innerHTML($node)
    {
        $doc = $node->ownerDocument;
        $frag = $doc->createDocumentFragment();
        foreach ($node->childNodes as $child) {
            $frag->appendChild($child->cloneNode(TRUE));
        }
        return $doc->saveXML($frag);
    }

    public function findSpanColor($node)
    {
        $pos = stripos($node, "color:"); // ie: looking for style='color: #FF0000;'
        if ($pos === false) { //                        12345678911111
            return '000000'; //                                 01234
        }
        $node = substr($node, $pos); // truncate to color: start
        $start = "#"; // looking for html color string
        $end = ";"; // should end with semicolon
        $node = " " . $node; // prefix node with blank
        $ini = stripos($node, $start); // look for #
        if ($ini === false) return "000000"; // not found, return default color of black
        $ini += strlen($start); // get 1 byte past start string
        $len = stripos($node, $end, $ini) - $ini; // grab substr between start and end positions
        return substr($node, $ini, $len); // return the RGB color without # sign
    }

    public function findStyleColor($style)
    {
        $pos = stripos($style, "color:"); // ie: looking for style='color: #FF0000;'
        if ($pos === false) { //                        12345678911111
            return ''; //                                 01234
        }
        $style = substr($style, $pos); // truncate to color: start
        $start = "#"; // looking for html color string
        $end = ";"; // should end with semicolon
        $style = " " . $style; // prefix node with blank
        $ini = stripos($style, $start); // look for #
        if ($ini === false) return ""; // not found, return default color of black
        $ini += strlen($start); // get 1 byte past start string
        $len = stripos($style, $end, $ini) - $ini; // grab substr between start and end positions
        return substr($style, $ini, $len); // return the RGB color without # sign
    }

    public function findBoldText($node)
    {
        $pos = stripos($node, "<b>"); // ie: looking for bolded text
        if ($pos === false) { //                        12345678911111
            return false; //                                 01234
        }
        return true; // found <b>
    }

    public function fillCoordinatesWorksheet($objPHPExcel, $ticketWay, $coord)
    {

    }

    public function createBill($bill) {
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("public/xls/templateBill.xls");
        $ticket=$bill['res'];
        $ownerCargo=$bill['owner'];
        $transferCargo=$bill['acceptedResource']['owner'];
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A6',  $objPHPExcel->getActiveSheet()->getCell('A6')->getValue().$ticket['numberInt'].' от '.date('d-m-Y'))
                ->setCellValue('A8',  $objPHPExcel->getActiveSheet()->getCell('A8')->getValue().' '.$ownerCargo['property'].' '.$ownerCargo['name'])
                ->setCellValue('A9',  $objPHPExcel->getActiveSheet()->getCell('A9')->getValue().' '.$ownerCargo['addressReg'])
                ->setCellValue('A10',  $objPHPExcel->getActiveSheet()->getCell('A10')->getValue().' '.$ownerCargo['inn'].'/'.$ownerCargo['kpp'])
                ->setCellValue('A11',  $objPHPExcel->getActiveSheet()->getCell('A11')->getValue().' '.$ownerCargo['property'].' '.$ownerCargo['name'].' '.$ownerCargo['addressFact'])
                ->setCellValue('A12',  $objPHPExcel->getActiveSheet()->getCell('A12')->getValue().' '.$transferCargo['property'].' '.$transferCargo['name'].' '.$transferCargo['addressFact'])
                ->setCellValue('A14',  $objPHPExcel->getActiveSheet()->getCell('A14')->getValue().' '.$transferCargo['property'].' '.$transferCargo['name'])
                ->setCellValue('A15',  $objPHPExcel->getActiveSheet()->getCell('A15')->getValue().' '.$transferCargo['addressReg'])
                ->setCellValue('A16',  $objPHPExcel->getActiveSheet()->getCell('A16')->getValue().' '.$transferCargo['inn'].'/'.$transferCargo['kpp'])
                ->setCellValue('A21',  $objPHPExcel->getActiveSheet()->getCell('A21')->getValue().' '.$ticket['created'].' - '.date('d-m-Y'))
                ->setCellValue('A24',  $objPHPExcel->getActiveSheet()->getCell('A24')->getValue().' ('.$ownerCargo['generalManager'].')')
                ->setCellValue('H24',  $objPHPExcel->getActiveSheet()->getCell('H24')->getValue().' ('.$ownerCargo['chiefAccountant'].')')
                ->setCellValue('E21',  $ticket['money'])
                ->setCellValue('F21',  $ticket['money'])
                ->setCellValue('I21',  $ticket['money']*0.18)
                ->setCellValue('I22',  $ticket['money']*0.18)
                ->setCellValue('J21',  $ticket['money']*1.18)
                ->setCellValue('J22',  $ticket['money']*1.18)
            ;

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');

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

    public function getAccountModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->organizationModel;
    }

    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Account\Model\CompanyModel');
        }
        return $this->companyModel;
    }
}