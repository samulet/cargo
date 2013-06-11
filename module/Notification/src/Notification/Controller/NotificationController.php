<?php

namespace Notification\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Notification\Form\NotificationForm;
use AddList\Form\AddListForm;

class NotificationController extends AbstractActionController
{
    protected $notificationModel;

    public function indexAction()
    {
        $notificationModel = $this->getNotificationModel();
        $notification=$notificationModel->getNotifications($this->zfcUserAuthentication()->getIdentity()->getId(),'sent');
        return new ViewModel(array(
            'notification' =>$notification
        ));

    }

    public function myAction()
    {


    }

    public function addAction() {

    }



    public function getNotificationModel()
    {
        if (!$this->notificationModel) {
            $sm = $this->getServiceLocator();
            $this->notificationModel = $sm->get('Notification\Model\NotificationModel');
        }
        return $this->notificationModel;
    }



}
