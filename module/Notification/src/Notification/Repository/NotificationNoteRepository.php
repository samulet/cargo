<?php
namespace Notification\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class NotificationNoteRepository extends DocumentRepository
{
    public function getAllAvailableNotificationNote()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getMyAvailableNotificationNote($ownerNotificationId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerNotificationId')->equals(
                new \MongoId($ownerNotificationId)
            )
            ->getQuery()->execute();
    }

    public function getLastStatusNotificationNote($ownerNotificationId)
    {
        return $this->createQueryBuilder()

            ->field('deletedAt')->equals(null)->field('ownerNotificationId')->equals(
                new \MongoId($ownerNotificationId)
            )->sort('createdAt', 'desc')
            ->limit(1)
            ->getQuery()
            ->execute();
    }
}