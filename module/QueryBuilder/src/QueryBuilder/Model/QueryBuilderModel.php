<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace QueryBuilder\Model;


use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

class QueryBuilderModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    public function createQuery($qb, $searchArray)
    {
        foreach ($searchArray as $key => $value) {
            $qb->field($key)->equals($value);
        }
        return $qb;
    }

    public function isTicket($itemId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $itemId= $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($itemId)));
        if(!empty($itemId)) {
            return true;
        } else {
            return false;
        }
    }

    public function isResource($itemId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $itemId= $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('id' => new \MongoId($itemId)));
        if(!empty($itemId)) {
            return true;
        } else {
            return false;
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


}