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
    public function getMyAvailableList($listName)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('listName')->equals(
                $listName
            )
            ->getQuery()->execute();
    }
}