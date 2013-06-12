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
        $notification=$notificationModel->getAdminNotifications();
        return new ViewModel(array(
            'notification' =>$notification
        ));

    }

    public function myAction()
    {
        $notificationModel = $this->getNotificationModel();
        $notification=$notificationModel->getMyNotifications($this->zfcUserAuthentication()->getIdentity()->getId());
        return new ViewModel(array(
            'notification' =>$notification
        ));

    }

    public function addAction() {
        $sendUuid= $this->getEvent()->getRouteMatch()->getParam('id');

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Notification\Entity\NotificationNote');


        return new ViewModel(array(
            'form' => $form,
            'uuid'=>$sendUuid
        ));
    }

    public function addNotificationNoteAction() {
        $sendUuid= $this->getEvent()->getRouteMatch()->getParam('id');
        $post=$this->getRequest()->getPost();
        $notificationModel = $this->getNotificationModel();
        $notificationModel->addNotificationNote($sendUuid,$post);
        return $this->redirect()->toUrl('/notifications');
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
