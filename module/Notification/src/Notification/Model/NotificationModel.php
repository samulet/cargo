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

class NotificationModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;
    protected $resourceModel;
    protected $ticketModel;
    protected $vehicleModel;
    protected $queryBuilderModel;


    public function addNotification($itemId,$ownerUserId,$ownerOrgId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $not=new Notification();
        $not->itemId=new \MongoId($itemId);
        $not->ownerUserId=new \MongoId($ownerUserId);
        $not->ownerOrgId=new \MongoId($ownerOrgId);
        $objectManager->persist($not);
        $objectManager->flush();
    }

    public function getNotifications($searchArray) {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $noteObj = $objectManager->createQueryBuilder('Notification\Entity\Notification');
        $queryBuilderModel=$this->getQueryBuilderModel();
        $noteObj=$queryBuilderModel->createQuery($noteObj, $searchArray)->getQuery()->execute();
        $fullResult=array();
        foreach($noteObj as $re) {

            $result=$this->getNotificationNotes($re->itemId,array('ownerNotificationId' => new \MongoId($re->id)));
            $fullResult=array_merge($fullResult,$result);
        }
        return $fullResult;
    }

    public function getNotificationNotes($itemId,$searchArray) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $noteObj = $objectManager->createQueryBuilder('Notification\Entity\NotificationNote');
        $queryBuilderModel=$this->getQueryBuilderModel();
        $noteObj=$queryBuilderModel->createQuery($noteObj, $searchArray)->getQuery()->execute();
        $result=array();
        $item=$this->getItem($itemId);
        foreach($noteObj as $note) {
            $nt=get_object_vars($note);
            $nt['type']=$item['type'];
            $nt['itemId']=$item['uuid'];
            array_push($result,$nt);
        }
        return $result;
    }

    public function getAdminNotifications($orgId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $notes =$objectManager->getRepository('Notification\Entity\Notification')->findBy(
            array('ownerOrgId' => new \MongoId($orgId))
        );

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

    public function getNewNotifications($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Notification\Entity\Notification')->findBy(
            array('ownerUserId' => new \MongoId($userId))
        );
        $fullResult=array();
        foreach($res as $re) {
            $notes = $objectManager->getRepository('Notification\Entity\NotificationNote')->findBy(
                array('ownerNotificationId' => new \MongoId($re->id),'read' => '0')
            );
            $result=array();
            $item=$this->getItem($re->itemId);

            foreach($notes as $note) {
                $nt=get_object_vars($note);
                $nt['type']=$item['type'];
                $nt['itemId']=$item['uuid'];
                array_push($result,$nt);
            }
            $fullResult=$fullResult+$result;
        }

        return $fullResult;
    }

    public function addRead($post) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $note = $objectManager->getRepository('Notification\Entity\NotificationNote')->findOneBy(
            array('uuid' => $post->read)
        );
        $note->read=1;
        $objectManager->persist($note);
        $objectManager->flush();
    }

    public function getMyNotifications($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        ;
        $res = $objectManager->getRepository('Notification\Entity\Notification')->findBy(
            array('ownerUserId' => new \MongoId($userId))
        );

        $fullResult=array();
        foreach($res as $re) {
            $notes = $objectManager->getRepository('Notification\Entity\NotificationNote')->findBy(
                array('ownerNotificationId' => new \MongoId($re->id))
            );
            $result=array();
            $item=$this->getItem($re->itemId);

            foreach($notes as $note) {
                $nt=get_object_vars($note);
                $nt['type']=$item['type'];
                $nt['itemId']=$item['uuid'];
                array_push($result,$nt);
            }
            $fullResult=$fullResult+$result;
        }

        return $fullResult;
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
        if(empty($resUuid)) {
            $vehicleModel=$this->getVehicleModel();
            $resUuid=$vehicleModel->getUuidById($id);
            $type='ТС';
        }
        return array('uuid'=>$resUuid,'type'=>$type);
    }

    public function addNotificationNote($uuid,$post) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id=$this->getIdByUuid($uuid);
        $prop_array = get_object_vars($post);
        $prop_array['ownerNotificationId']=$id;
        $prop_array['read']=0;
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
    public function getVehicleModel()
    {
        if (!$this->vehicleModel) {
            $sm = $this->getServiceLocator();
            $this->vehicleModel = $sm->get('Resource\Model\VehicleModel');
        }
        return $this->vehicleModel;
    }
    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }
}