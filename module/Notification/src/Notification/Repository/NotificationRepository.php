<?php
namespace Notification\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class NotificationRepository extends DocumentRepository
{
    public function getAllAvailableNotification()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getSentAvailableNotification($userId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerUserId')->equals(
                new \MongoId($userId)
            )
            ->getQuery()->execute();
    }

    public function getReceiveAvailableNotification($userId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('receiveUserId')->equals(
                new \MongoId($userId)
            )
            ->getQuery()->execute();
    }
}