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
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class InteractionModel implements ServiceLocatorAwareInterface
{
    protected $ticketModel;
    protected $resourceModel;
    protected $serviceLocator;
    protected $queryBuilderModel;

    public function addInteraction($sendItemId, $receiveItemUuid, $ownerUserId)
    {
        $resourceModel = $this->getResourceModel();
        $ticketModel = $this->getTicketModel();

        $receiveItemId = $resourceModel->getIdByUuid($receiveItemUuid);
        if (empty($receiveItemId)) {
            $receiveItemId = $ticketModel->getIdByUuid($receiveItemUuid);
            $element = $ticketModel->listTicketById($receiveItemId);

        } else {
            $element = $resourceModel->listResourceById($receiveItemId);
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $interaction = new Interaction();
        $interaction->sendItemId = new \MongoId($sendItemId);
        $interaction->receiveItemId = new \MongoId($receiveItemId);
        $interaction->ownerUserId = new \MongoId($ownerUserId);
        $interaction->receiveUserId = new \MongoId($element['ownerId']);
        $objectManager->persist($interaction);
        $objectManager->flush();
    }

    public function getInteractions($searchArray)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intObj = $objectManager->createQueryBuilder('Interaction\Entity\Interaction');
        $queryBuilderModel = $this->getQueryBuilderModel();
        $intObj = $queryBuilderModel->createQuery($intObj, $searchArray)->getQuery()->execute();
        $result = array();
        $resourceModel = $this->getResourceModel();
        $ticketModel = $this->getTicketModel();
        foreach ($intObj as $int) {
            $resource = $resourceModel->listResourceById($int->sendItemId);
            if (empty($resource)) {
                $ticket = $ticketModel->listTicketById($int->sendItemId);
                $resource = $resourceModel->listResourceById($int->receiveItemId);
                $receiveStatus = "Ресурс";
                $sendStatus = "Заявка";
                $sendItem = array('uuid' => $ticket['uuid'], 'status' => $sendStatus);
                $receiveItem = array('uuid' => $resource['uuid'], 'status' => $receiveStatus);
            } else {
                $ticket = $ticketModel->listTicketById($int->receiveItemId);
                $receiveStatus = "Заявка";
                $sendStatus = "Ресурс";

                $sendItem = array('uuid' => $resource['uuid'], 'status' => $sendStatus);
                $receiveItem = array('uuid' => $ticket['uuid'], 'status' => $receiveStatus);
            }
            array_push(
                $result,
                array(
                    'uuid' => $int->uuid,
                    'sendItem' => $sendItem,
                    'receiveItem' => $receiveItem,
                    'status' => $this->getInteractionStatus($int->id)
                )
            );
        }
        return $result;
    }

    public function acceptInteraction($sendUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Interaction\Entity\Interaction')->createQueryBuilder()
            ->findAndUpdate()
            ->field('uuid')->equals($sendUuid)
            ->field('accepted')->set('1')
            ->getQuery()
            ->execute();
    }

    public function getInteractionStatus($ownerInteractionId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intNoteObj = $objectManager->getRepository('Interaction\Entity\InteractionNote')->getLastStatusInteractionNote(
            $ownerInteractionId
        );
        foreach ($intNoteObj as $intNote) {
            if (empty($intNote)) {
                return 'Нет статуса';
            } else {

                return $intNote->status;
            }
        }
        return 'Нет статуса';
    }

    public function getListProposalData($sendUuid, $userId)
    {
        $resourceModel = $this->getResourceModel();
        $ticketModel = $this->getTicketModel();
        $resourceId = $resourceModel->getIdByUuid($sendUuid);
        if (!empty($resourceId)) {
            $ticket = $ticketModel->returnMyTicket($userId);
            return $ticket;
        } else {
            $resource = $resourceModel->returnMyResource($userId);
            return $resource;
        }
    }

    public function getProposal($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id = $this->getInteractionIdByUuid($uuid);
        $proposalObject = $objectManager->getRepository(
            'Interaction\Entity\InteractionNote'
        )->getMyAvailableInteractionNote($id);
        $result = array();
        foreach ($proposalObject as $proposal) {
            if (empty($proposal)) {
                return null;
            }
            array_push($result, get_object_vars($proposal));
        }
        return $result;
    }

    public function addProposal($uuid, $post)
    {

        $propArray = get_object_vars($post);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id = $this->getInteractionIdByUuid($uuid);

        $interaction = new InteractionNote();

        $propArray['ownerInteractionId'] = new \MongoId($id);

        foreach ($propArray as $key => $value) {
            $interaction->$key = $value;

        }

        $objectManager->persist($interaction);
        $objectManager->flush();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Interaction\Entity\Interaction')->createQueryBuilder()
            ->findAndUpdate()
            ->field('uuid')->equals($uuid)
            ->field('status')->set($propArray['status'])
            ->getQuery()
            ->execute();
    }

    public function getInteractionIdByUuid($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $int = $objectManager->getRepository('Interaction\Entity\Interaction')->findOneBy(array('uuid' => $uuid));
        if (!empty($int)) {
            return $int->id;
        } else {
            return null;
        }
    }

    public function getItemStatus($itemId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $note = $objectManager->getRepository('Interaction\Entity\Interaction')->findOneBy(
            array('receiveItemId' => new \MongoId($itemId), 'accepted' => '1')
        );

        if (empty($note)) {
            $note = $objectManager->getRepository('Interaction\Entity\Interaction')->findOneBy(
                array('sendItemId' => new \MongoId($itemId), 'accepted' => '1')
            );

        }


        if (!empty($note->status)) {
            return $note->status;
        } else {
            return 'Нет статуса';
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

    public function getTicketsInWork($currentCom)
    {
        $interactionCur = $this->getInteractions(array('accepted' => '1', 'ownerUserId' => new \MongoId($currentCom)));
        $interactionRes = $this->getInteractions(
            array('accepted' => '1', 'receiveUserId' => new \MongoId($currentCom))
        );
        return array_merge($interactionCur, $interactionRes);
    }


    public function getTicketModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
        }
        return $this->ticketModel;
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