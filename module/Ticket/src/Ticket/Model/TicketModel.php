<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Ticket\Model;

use Ticket\Entity\Ticket;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
use Ticket\Entity\TicketWay;

class TicketModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $cargoModel;
    protected $notificationModel;

    public function addTicketWay($propArraySplit,$ownerTicketId,$resId) {
        $result=array();
        foreach($propArraySplit as $key =>$value) {
            $elementSplit=explode('-',$key);
            if(!empty($elementSplit['1'])) {
                $result['elementSplit'.$elementSplit['1']][$elementSplit['0']]=$value;
                if(empty($result['elementSplit'.$elementSplit['1']]['ownerTicketId'])) {
                    $result['elementSplit'.$elementSplit['1']]['ownerTicketId']=$ownerTicketId;
                }
            } else {
                $result['elementSplit0'][$elementSplit['0']]=$value;
                if(empty($result['elementSplit0']['ownerTicketId'])) {
                    $result['elementSplit0']['ownerTicketId']=$ownerTicketId;
                }
            }
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($resId)) {
            $objectManager->createQueryBuilder('Ticket\Entity\TicketWay')
                ->remove()
                ->field('ownerTicketId')->equals(new \MongoId($resId))
                ->getQuery()
                ->execute();
        }
        foreach($result as $res) {
            $ticketWay = new TicketWay();
            foreach ($res as $key => $value) {
                if($key!="ownerTicketId") {
                    $ticketWay->$key = $value;
                } else {
                    $ticketWay->$key=new \MongoId($value);
                }
            }
            $objectManager->persist($ticketWay);
            $objectManager->flush();
        }


    }

    public function addTicket($post, $owner_id, $owner_org_id, $id)
    {

        if(!empty($post)) {
            if(is_array($post)) {
                $prop_array=$post;
            } else {
                $prop_array = get_object_vars($post);
            }
        }
        $prop_array_split=$prop_array;
        unset($prop_array_split['tsId']);
        unset($prop_array_split['kindOfLoad']);
        unset($prop_array_split['submit']);


  //      $prop_array_new['tsId']=$prop_array['tsId'];
   //     $prop_array_new['kindOfLoad']=$prop_array['kindOfLoad'];
        $prop_array_new=array();
        $prop_array=$prop_array_new;



        $prop_array['ownerId'] = $owner_id;
        $prop_array['ownerOrgId'] = $owner_org_id;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($id)) {
            $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $res = new Ticket();
        }
        foreach ($prop_array as $key => $value) {
             $res->$key=new \MongoId($value);
        }
        $objectManager->persist($res);
        $objectManager->flush();

        $this->addTicketWay($prop_array_split,$res->id,$this->getIdByUuid($id));

        $noteModel=$this->getNotificationModel();

        $noteModel->addNotification($res->id,$owner_id,$owner_org_id);
        return $res->uuid;
    }

    public function listTicket($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $id));
        return get_object_vars($res);
    }

    public function listTicketById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($id)));
        if(!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }
    }

    public function returnAllTicket()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getAllAvailableTicket();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        if(empty($rezObj)) {
            return null;
        }
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $veh=$cargo->listCargo($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org,'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }

    public function returnMyTicket($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getMyAvailableTicket($owner_id);
        $rezs = array();
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $veh=$cargo->listCargo($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            array_push($rezs, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

    public function deleteTicket($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $recourse = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $uuid));
        if (!$recourse) {
            throw DocumentNotFoundException::documentNotFound('Ticket\Entity\Ticket', $uuid);
        }
        $objectManager->remove($recourse);
        $objectManager->flush();
    }
    public function copyTicket($uuid) {
        $res=$this->listTicket($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addTicket($res,$res['ownerId'],$res['ownerOrgId'],null);
    }

    public function getCargoModel()
    {
        if (!$this->cargoModel) {
            $sm = $this->getServiceLocator();
            $this->cargoModel = $sm->get('Ticket\Model\CargoModel');
        }
        return $this->cargoModel;
    }

    public function returnAllWays($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\TicketWay')->findBy(
            array('ownerTicketId' => new \MongoId($id))
        );
        $result=array();
        foreach($res as $re){
            array_push($result,get_object_vars($re));
        }
        return $result;
    }

    public function getIdByUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
            array('uuid' => $uuid)
        );
        if(empty($res)) {
            return null;
        }
        return $res->id;
    }

    public function getUuidById($id) {
        if(empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
            array('id' => new \MongoId($id))
        );
        if(empty($res)) {
            return null;
        }
        return $res->uuid;
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