<?php

namespace Auction\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ResourceController extends AbstractActionController
{
    protected $auctionModel;



    public function getResourceModel()
    {
        if (!$this->auctionModel) {
            $sm = $this->getServiceLocator();
            $this->auctionModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->auctionModel;
    }
}
