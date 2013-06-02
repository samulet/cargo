<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Interaction\Model;

use Interaction\Entity\Interaction;
use Interaction\Entity\InteractionNote;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class InteractionModel implements ServiceLocatorAwareInterface
{
    protected $ticketModel;
    protected $resourceModel;
    protected $serviceLocator;

    public function addInteraction($sendItemId,$receiveItemId,$ownerUserId ) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $interaction = new Interaction();
        $interaction->ownerUserId=new \MongoId($sendItemId);
        $interaction->ownerUserId=new \MongoId($receiveItemId);
        $interaction->ownerUserId=new \MongoId($ownerUserId);
        $objectManager->persist($interaction);
        $objectManager->flush();
    }

    public function getInteractions($userId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intObj = $objectManager->getRepository('Interaction\Entity\Interaction')->getMyAvailableTicket($userId);
        $result = array();
        $resourceModel=$this->getResourceModel();
        $ticketModel = $this->getTicketModel();
        foreach ($intObj as $int) {
            $resource=$resourceModel->listResourceById($int->sendItemId);
            if(empty($resource)) {
                $ticket= $ticketModel->listTicketById($int->sendItemId);
                $resource=$resourceModel->listResourceById($int->receiveItemId);
                $receiveStatus="Ресурс";
                $sendStatus="Заявка";
                $sendItem=array('uuid'=>$ticket->uuid,'status'=>$sendStatus);
                $receiveItem=array('uuid'=>$resource->uuid,'status'=>$receiveStatus);
            } else {
                $ticket= $ticketModel->listTicketById($int->receiveItemId);
                $receiveStatus="Заявка";
                $sendStatus="Ресурс";
                $sendItem=array('uuid'=>$resource->uuid,'status'=>$sendStatus);
                $receiveItem=array('uuid'=>$ticket->uuid,'status'=>$receiveStatus);
            }
            array_push($result, array('uuid'=>$int->uuid,'sendItem'=>$sendItem,'receiveItem'=>$receiveItem,'status'=>$this->getInteractionStatus($int->id)));
        }
        return $result;
    }

    public function getInteractionStatus($ownerInteractionId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intNoteObj = $objectManager->getRepository('Interaction\Entity\InteractionNote')->getLastStatusInteractionNote($ownerInteractionId);
        if(empty($intNoteObj)) {
            return 'Нет статуса';
        } else {
            return $intNoteObj->status;
        }
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