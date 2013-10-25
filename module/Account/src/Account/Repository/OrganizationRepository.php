<?php
namespace Account\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AccountRepository extends DocumentRepository
{
    public function getMyAvailableAccount($org_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('id')->equals(
                new \MongoId($org_id)
            )
            ->getQuery()->execute();
    }
}