<?php
namespace AddList\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AddListRepository extends DocumentRepository
{
    public function getAllAvailableListName()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableListName($listName)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('listName')->equals(
                $listName
            )
            ->getQuery()->execute();
    }
}