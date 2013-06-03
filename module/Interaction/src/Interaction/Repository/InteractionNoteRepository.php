<?php
namespace Interaction\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class InteractionNoteRepository extends DocumentRepository
{
    public function getAllAvailableInteractionNote()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableInteractionNote($ownerInteractionId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerInteractionId')->equals(
                new \MongoId($ownerInteractionId)
            )
            ->getQuery()->execute();
    }
    public function getLastStatusInteractionNote($ownerInteractionId) {
        return $this->createQueryBuilder()

            ->field('deletedAt')->equals(null)->field('ownerInteractionId')->equals(
                new \MongoId($ownerInteractionId)
            )->sort('createdAt', 'desc')
            ->limit(1)
            ->getQuery()
            ->execute();
    }
}