<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Notification\Model;

use Notification\Entity\Notification;
use Notification\Entity\NotificationNote;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class NotificationModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;
    protected $resourceModel;
    protected $ticketModel;

    public function addNotification($itemId,$ownerUserId,$ownerOrgId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $not=new Notification();
        $not->itemId=new \MongoId($itemId);
        $not->ownerUserId=new \MongoId($ownerUserId);
        $not->ownerOrgId=new \MongoId($ownerOrgId);
        $objectManager->persist($not);
        $objectManager->flush();
    }

    public function getAdminNotifications() {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $notes =$objectManager->getRepository('Notification\Entity\Notification')->createQueryBuilder()
            ->getQuery()->execute();

        $result=array();
        foreach($notes as $note) {
            $res=$this->getItem($note->itemId);
            $note=get_object_vars($note);
            $note['itemId']=$res['uuid'];
            $note['type']=$res['type'];
            array_push($result,$note);

        }
        return $result;
    }

    public function getItem($id) {
        $resourceModel=$this->getResourceModel();
        $resUuid=$resourceModel->getUuidById($id);
        $type='Ресурс';
        if(empty($resUuid)) {
            $ticketModel=$this->getTicketModel();
            $resUuid=$ticketModel->getUuidById($id);
            $type='Заявка';
        }
        return array('uuid'=>$resUuid,'type'=>$type);
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getResourceModel()
    {
        if (!$this->resourceModel) {
            $sm = $this->getServiceLocator();
            $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->resourceModel;
    }

    public function getTicketModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
        }
        return $this->ticketModel;
    }

}