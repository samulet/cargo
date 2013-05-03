<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/1/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Ticket\Controller;

use Entity\Ticket;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TicketController extends AbstractActionController
{
    protected $ticketModel;

    public function indexAction() {

    }

    public function getResourceModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->ticketModel;
    }
}
