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
}