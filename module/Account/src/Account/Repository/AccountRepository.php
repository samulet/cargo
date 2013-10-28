<?php
namespace Account\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AccountRepository extends DocumentRepository
{
    public function getMyAvailableAccount($accId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('id')->equals(
                new \MongoId($accId)
            )
            ->getQuery()->execute();
    }
}