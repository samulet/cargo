<?php
namespace Contract\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ContractNoteRepository extends DocumentRepository
{
    public function getAllAvailableContractNote()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getMyAvailableContractNote($ownerContractId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerContractId')->equals(
                new \MongoId($ownerContractId)
            )
            ->getQuery()->execute();
    }

    public function getLastStatusContractNote($ownerContractId)
    {
        return $this->createQueryBuilder()

            ->field('deletedAt')->equals(null)->field('ownerContractId')->equals(
                new \MongoId($ownerContractId)
            )->sort('createdAt', 'desc')
            ->limit(1)
            ->getQuery()
            ->execute();
    }
}