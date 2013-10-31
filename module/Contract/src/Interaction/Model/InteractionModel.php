<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Contract\Model;

use Contract\Entity\Contract;
use Contract\Entity\ContractNote;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class ContractModel implements ServiceLocatorAwareInterface
{
    protected $ticketModel;
    protected $resourceModel;
    protected $serviceLocator;
    protected $queryBuilderModel;

    public function addContract($sendItemId, $receiveItemUuid, $ownerUserId)
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
        $contract = new Contract();
        $contract->sendItemId = new \MongoId($sendItemId);
        $contract->receiveItemId = new \MongoId($receiveItemId);
        $contract->ownerUserId = new \MongoId($ownerUserId);
        $contract->receiveUserId = new \MongoId($element['ownerId']);
        $objectManager->persist($contract);
        $objectManager->flush();
    }

    public function getContracts($searchArray)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intObj = $objectManager->createQueryBuilder('Contract\Entity\Contract');
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
                    'status' => $this->getContractStatus($int->id)
                )
            );
        }
        return $result;
    }

    public function acceptContract($sendUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Contract\Entity\Contract')->createQueryBuilder()
            ->findAndUpdate()
            ->field('uuid')->equals($sendUuid)
            ->field('accepted')->set('1')
            ->getQuery()
            ->execute();
    }

    public function getContractStatus($ownerContractId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $intNoteObj = $objectManager->getRepository('Contract\Entity\ContractNote')->getLastStatusContractNote(
            $ownerContractId
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
        $id = $this->getContractIdByUuid($uuid);
        $proposalObject = $objectManager->getRepository(
            'Contract\Entity\ContractNote'
        )->getMyAvailableContractNote($id);
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
        $id = $this->getContractIdByUuid($uuid);

        $contract = new ContractNote();

        $propArray['ownerContractId'] = new \MongoId($id);

        foreach ($propArray as $key => $value) {
            $contract->$key = $value;

        }

        $objectManager->persist($contract);
        $objectManager->flush();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $objectManager->getRepository('Contract\Entity\Contract')->createQueryBuilder()
            ->findAndUpdate()
            ->field('uuid')->equals($uuid)
            ->field('status')->set($propArray['status'])
            ->getQuery()
            ->execute();
    }

    public function getContractIdByUuid($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $int = $objectManager->getRepository('Contract\Entity\Contract')->findOneBy(array('uuid' => $uuid));
        if (!empty($int)) {
            return $int->id;
        } else {
            return null;
        }
    }

    public function getItemStatus($itemId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $note = $objectManager->getRepository('Contract\Entity\Contract')->findOneBy(
            array('receiveItemId' => new \MongoId($itemId), 'accepted' => '1')
        );

        if (empty($note)) {
            $note = $objectManager->getRepository('Contract\Entity\Contract')->findOneBy(
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
        $contractCur = $this->getContracts(array('accepted' => '1', 'ownerUserId' => new \MongoId($currentCom)));
        $contractRes = $this->getContracts(
            array('accepted' => '1', 'receiveUserId' => new \MongoId($currentCom))
        );
        return array_merge($contractCur, $contractRes);
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