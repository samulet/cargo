<?php

namespace Admin\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    protected $auctionModel;
    protected $ticketModel;
    protected $resourceModel;

    public function indexAction()
    {


    }


}
