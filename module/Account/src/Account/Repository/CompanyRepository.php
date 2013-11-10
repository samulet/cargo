<?php
namespace Account\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class CompanyRepository extends DocumentRepository
{
    public function getMyAvailableCompany($owner_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerAccId')->equals(
                new \MongoId($owner_id)
            )
            ->getQuery()->execute();
    }
}