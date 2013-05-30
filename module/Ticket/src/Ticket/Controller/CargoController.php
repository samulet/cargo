<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 9:37 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Ticket\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Ticket\Form\TicketForm;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\View\Helper\FormSelect;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CargoController extends AbstractActionController
{
    protected $ticketModel;
    protected $companyUserModel;
    protected $addListModel;

    public function indexAction()
    {


    }

    public function myAction()
    {

    }

    public function addAction()
    {

    }

    public function editAction()
    {

    }

    public function listAction()
    {


    }

    public function deleteAction()
    {

    }



    public function getAddListModel()
    {
        if (!$this->addListModel) {
            $sm = $this->getServiceLocator();
            $this->addListModel = $sm->get('AddList\Model\AddListModel');
        }
        return $this->addListModel;
    }
}
