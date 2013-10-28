<?php
namespace Ticket\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class CargoRepository extends DocumentRepository
{
    public function getAllAvailableCargo()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getMyAvailableCargo($owner_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerId')->equals(
                new \MongoId($owner_id)
            )
            ->getQuery()->execute();
    }
}