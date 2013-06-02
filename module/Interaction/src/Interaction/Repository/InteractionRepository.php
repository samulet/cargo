<?php
namespace Interaction\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class InteractionRepository extends DocumentRepository
{
    public function getAllAvailableInteraction()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableInteraction($userId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerUserId')->equals(
                new \MongoId($userId)
            )
            ->getQuery()->execute();
    }
}