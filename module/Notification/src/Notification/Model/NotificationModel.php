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

    public function getMyNotifications($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Notification\Entity\Notification')->findOneBy(
            array('ownerUserId' => new \MongoId($userId))
        );

        $notes = $objectManager->getRepository('Notification\Entity\NotificationNote')->findBy(
            array('ownerNotificationId' => new \MongoId($res->id))
        );
        $result=array();
        foreach($notes as $note) {
            array_push($result,$note);
        }
        return array('item'=>$this->getItem($res->itemId),'note'=>$result);
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

    public function addNotificationNote($uuid,$post) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id=$this->getIdByUuid($uuid);
        $prop_array = get_object_vars($post);
        $prop_array['ownerNotificationId']=$id;

        $res=new NotificationNote();
        foreach ($prop_array as $key => $value) {
            if($key!='ownerNotificationId') {
                $res->$key=$value;
            } else {
                $res->$key=new \MongoId($value);
            }
        }
        $objectManager->persist($res);
        $objectManager->flush();
        $note = $objectManager->getRepository('Notification\Entity\Notification')->findOneBy(
            array('uuid' => $uuid)
        );
        $note->status=$prop_array['status'];
        $objectManager->persist($note);
        $objectManager->flush();

    }

    public function getIdByUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Notification\Entity\Notification')->findOneBy(
            array('uuid' => $uuid)
        );
        if(empty($res)) {
            return null;
        }
        return $res->id;
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