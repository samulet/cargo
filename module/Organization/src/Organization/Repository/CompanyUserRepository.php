<?php
namespace Resource\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class VehicleRepository extends DocumentRepository
{
    public function getAllAvailableVehicle()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableVehicle($owner_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerId')->equals(
                new \MongoId($owner_id)
            )
            ->getQuery()->execute();
    }
}