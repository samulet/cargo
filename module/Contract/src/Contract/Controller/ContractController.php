<?php

namespace Contract\Controller;

use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AddList\Form\AddListForm;

class ContractController extends AbstractActionController
{
    protected $contractModel;
    protected $companyUserModel;


    public function addAction()
    {


    }
    public function getContractModel()
    {
        if (!$this->contractModel) {
            $sm = $this->getServiceLocator();
            $this->contractModel = $sm->get('Contract\Model\ContractModel');
        }
        return $this->contractModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
}
