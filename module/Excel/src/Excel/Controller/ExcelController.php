<?php

namespace Excel\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AddList\Form\AddListForm;


class ExcelController extends AbstractActionController
{
    protected $excelModel;

    public function getExcelAction() {

        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $excelModel=$this->getExcelModel();
        $excelModel->getExcel($id);
    }

    public function getExcelModel()
    {
        if (!$this->excelModel) {
            $sm = $this->getServiceLocator();
            $this->excelModel = $sm->get('Excel\Model\ExcelModel');
        }
        return $this->excelModel;
    }


}
