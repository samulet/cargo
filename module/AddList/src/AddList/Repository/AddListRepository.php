<?php
namespace AddList\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AddListRepository extends DocumentRepository
{
    public function getAllAvailableList()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableList($id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('id')->equals(
                new \MongoId($id)
            )
            ->getQuery()->execute();
    }
}