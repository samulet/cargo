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

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class TicketModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;
    protected $organizationModel;

    public function addTicket($post, $owner_id, $org_id, $id)
    {
        $prop_array = get_object_vars($post);
        $prop_array['ownerId'] = $owner_id;
        $prop_array['ownerOrgId'] = $org_id;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($id)) {
            $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $id));
        }
        else {
            $res = new Ticket();
        }
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();
    }

    public function listTicket($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $id));
        return get_object_vars($res);
    }

    public function returnAllTicket()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Ticket\Entity\Ticket')->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('tick' => $obj_vars, 'org' => $org));
        }
        return $rezs;
    }

    public function returnMyTicket($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Ticket\Entity\Ticket')->field('ownerId')->equals(
            new \MongoId($owner_id)
        )->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs = array();
        foreach ($rezObj as $cur) {
            array_push($rezs, get_object_vars($cur));
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
        $qb4 = $objectManager->createQueryBuilder('Ticket\Entity\Ticket');
        $qb4->remove()->field('uuid')->equals($uuid)->getQuery()
            ->execute();
    }
}