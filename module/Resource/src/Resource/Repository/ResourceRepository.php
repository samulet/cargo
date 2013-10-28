<?php
namespace Resource\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ResourceRepository extends DocumentRepository
{
    public function getAllAvailableResource()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getMyAvailableResource($owner_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerId')->equals(
                new \MongoId($owner_id)
            )
            ->getQuery()->execute();
    }
}