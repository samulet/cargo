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