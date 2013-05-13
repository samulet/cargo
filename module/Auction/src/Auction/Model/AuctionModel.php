<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/3/13
 * Time: 7:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Auction\Model;

use Auction\Entity\Auction;
use Auction\Entity\AuctionBid;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class AuctionModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    public function getAuctions()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Auction\Entity\Auction')->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs = array();
        foreach ($rezObj as $cur) {
            array_push($rezs, get_object_vars($cur));
        }
        return $rezs;
    }

    public function getPrice($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $auc = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $uuid));
        $str = 'up';
        if ($auc == null) {
            $auc = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $uuid));
            $str = 'down';
        }
        return array(
            'currency' => $auc->currency,
            'money' => $auc->money,
            'dateEnd' => $auc->dateEnd,
            'dateStart' => $auc->dateStart,
            'str' => $str
        );
    }

    public function addAuctionBid($post, $owner_id)
    {
        $prop_array = get_object_vars($post);
        $prop_array['ownerId'] = $owner_id;

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = new Resource();
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();
    }

    public function addAuction($post, $ownerItemId)
    {
        $prop_array = get_object_vars($post);
        $prop_array['ownerItemId'] = $ownerItemId;

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = new Auction();
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();
    }

    public function getItemId($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $auc = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $uuid));
        if ($auc == null) {
            $auc = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
                array('uuid' => $uuid)
            );
        }
        if ($auc == null) $auc = $objectManager->getRepository('Auction\Entity\Auction')->findOneBy(
            array('uuid' => $uuid)
        );
        return $auc->id;
    }

    public function  addBidEngine($aucId, $userId, $post)
    {
        $prop_array = get_object_vars($post);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = new AuctionBid($aucId, $userId, $prop_array['bid'], $prop_array['currency']);
        $objectManager->persist($res);
        $objectManager->flush();
    }

    public function getItemUUID($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $auc = $objectManager->getRepository('Auction\Entity\Auction')->findOneBy(array('uuid' => $uuid));
        $rez = $objectManager->getRepository('Resource\Entity\Resource')->find($auc->ownerItemId);
        $item = 'res';
        if ($rez == null) {
            $rez = $objectManager->getRepository('Ticket\Entity\Ticket')->find($auc->ownerItemId);
            $item = 'tick';
        }
        return array('uuid' => $rez->uuid, 'item' => $item);
    }

    public function getBids($uuid)
    {
        $id = $this->getAuctionId($uuid);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $bids = $objectManager->getRepository('Auction\Entity\AuctionBid')->findBy(array('aucId' => new \MongoId($id)));
        $rezs = array();
        foreach ($bids as $cur) {
            array_push($rezs, get_object_vars($cur));
        }
        return $rezs;
    }

    public function getAuctionId($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $id = $objectManager->getRepository('Auction\Entity\Auction')->findOneBy(array('uuid' => $uuid));
        return $id->id;
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