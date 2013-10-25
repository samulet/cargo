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

        //$notification1=$notificationModel->getAdminNotifications($this->zfcUserAuthentication()->getIdentity()->getCurrentAcc());
        $notification=$notificationModel->getNotifications(array('ownerOrgId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentAcc())));

        return new ViewModel(array(
            'notification' =>$notification
        ));

    }

    public function myAction()
    {
        $notificationModel = $this->getNotificationModel();
        $notification=$notificationModel->getNotifications(array('ownerUserId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())));
        return new ViewModel(array(
            'notification' =>$notification
        ));

    }

    public function newAction() {
        $notificationModel = $this->getNotificationModel();
        $notification=$notificationModel->getNotifications(array('ownerUserId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())), array('read'=>'0'));
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Notification\Entity\NotificationNote');
        return new ViewModel(array(
            'notification' =>$notification,
            'form'=>$form
        ));
    }

    public function readAction() {
        $post=$this->getRequest()->getPost();
        $notificationModel = $this->getNotificationModel();
        $notificationModel->addRead($post);
        return $this->redirect()->toUrl('/notifications/new');
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
