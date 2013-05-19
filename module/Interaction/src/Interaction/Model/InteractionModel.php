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

    public function addInteraction($sendItemId,$receiveItemUuid,$ownerUserId ) {
        $resourceModel=$this->getResourceModel();
        $ticketModel = $this->getTicketModel();

        $receiveItemId=$resourceModel->getIdByUuid($receiveItemUuid);
        if(empty($receiveItemId)) {
            $receiveItemId=$ticketModel->getIdByUuid($receiveItemUuid);
            $element=$ticketModel->listTicketById($receiveItemId);

        } else {
            $element=$resourceModel->listResourceById($receiveItemId);
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $interaction = new Interaction();
        $interaction->sendItemId=new \MongoId($sendItemId);
        $interaction->receiveItemId=new \MongoId($receiveItemId);
        $interaction->ownerUserId=new \MongoId($ownerUserId);
        $interaction->receiveUserId=new \MongoId($element['ownerId']);
        $objectManager->persist($interaction);
        $objectManager->flush();
    }

    public function getInteractions($userId,$type) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if($type=='sent') {
            $intObj = $objectManager->getRepository('Interaction\Entity\Interaction')->getSentAvailableInteraction($userId);
        } else {
            $intObj = $objectManager->getRepository('Interaction\Entity\Interaction')->getReceiveAvailableInteraction($userId);
        }
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
                $sendItem=array('uuid'=>$ticket['uuid'],'status'=>$sendStatus);
                $receiveItem=array('uuid'=>$resource['uuid'],'status'=>$receiveStatus);
            } else {
                $ticket= $ticketModel->listTicketById($int->receiveItemId);
                $receiveStatus="Заявка";
                $sendStatus="Ресурс";

                $sendItem=array('uuid'=>$resource['uuid'],'status'=>$sendStatus);
                $receiveItem=array('uuid'=>$ticket['uuid'],'status'=>$receiveStatus);
            }
            array_push($result, array('uuid'=>$int->uuid,'sendItem'=>$sendItem,'receiveItem'=>$receiveItem,'status'=>$this->getInteractionStatus($int->id)));
        }
        return $result;
    }

    public function getInteractionStatus($ownerInteractionId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intNoteObj = $objectManager->getRepository('Interaction\Entity\InteractionNote')->getLastStatusInteractionNote($ownerInteractionId);
        foreach($intNoteObj as $intNote) {
            if(empty($intNote)) {
                return 'Нет статуса';
            } else {

                return $intNote->status;
            }
        }
        return 'Нет статуса';
    }

    public function getListProposalData($sendUuid,$userId) {
        $resourceModel=$this->getResourceModel();
        $ticketModel = $this->getTicketModel();
        $resourceId=$resourceModel->getIdByUuid($sendUuid);
        if(!empty($resourceId)) {
            $ticket=$ticketModel->returnMyTicket($userId);
            return $ticket;
        } else {
            $resource=$resourceModel->returnMyResource($userId);
            return $resource;
        }
    }

    public function getProposal($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id=$this->getInteractionIdByUuid($uuid);
        $proposalObject=$objectManager->getRepository('Interaction\Entity\InteractionNote')->getMyAvailableInteractionNote($id);
        $result=array();
        foreach($proposalObject as $proposal) {
            if(empty($proposal)) {
                return null;
            }
            array_push($result, get_object_vars($proposal));
        }
        return $result;
    }

    public function addProposal($uuid,$post) {

        $prop_array=get_object_vars($post);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id=$this->getInteractionIdByUuid($uuid);

        $interaction = new InteractionNote();

        $prop_array['ownerInteractionId']=new \MongoId($id);

        foreach ($prop_array as $key => $value) {
            $interaction->$key = $value;

        }

        $objectManager->persist($interaction);
        $objectManager->flush();
    }

    public function getInteractionIdByUuid($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $int = $objectManager->getRepository('Interaction\Entity\Interaction')->findOneBy(array('uuid' => $uuid));
        if(!empty($int)) {
            return $int->id;
        } else {
            return null;
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