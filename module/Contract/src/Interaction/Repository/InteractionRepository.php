<?php
namespace Contract\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ContractRepository extends DocumentRepository
{
    public function getAllAvailableContract()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getSentAvailableContract($userId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerUserId')->equals(
                new \MongoId($userId)
            )
            ->getQuery()->execute();
    }

    public function getReceiveAvailableContract($userId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('receiveUserId')->equals(
                new \MongoId($userId)
            )
            ->getQuery()->execute();
    }
}