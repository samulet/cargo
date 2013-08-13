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

    public function generateTemplateAction() {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
       // $post=get_object_vars($this->getRequest()->getPost());
        $request = $this->getRequest();
        if($request->isPost()) {

            $post = $request->getPost()->toArray();
            if(!empty($post['newStringDown'])) {
                $newStringDown=$post['newStringDown'];
            } else {
                $newStringDown='';
            }
            $file    = $this->params()->fromFiles('file');

            $excelModel=$this->getExcelModel();
            $excelModel->generateTemplate($id,$post['type'],$file['tmp_name'],$newStringDown);
        } else {
            $builder = new AnnotationBuilder();
            $form = $builder->createForm('Excel\Entity\Excel');
            return new ViewModel(array(
                'form' =>$form,
                'id'=>$id
            ));
        }

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
