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
    public function getGlobalAvailableList($id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('global')->equals('global')
            ->field('listId')->equals(
                new \MongoId($id)
            )
            ->getQuery()->execute();
    }
    public function getLocalAvailableList($id,$orgListId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('ownerOrgId')->equals(new \MongoId($orgListId))
            ->field('listId')->equals(
                new \MongoId($id)
            )
            ->getQuery()->execute();
    }
    public function getMyAvailableList($id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('listId')->equals(
                new \MongoId($id)
            )
            ->getQuery()->execute();
    }
    public function getOneMyAvailableList($id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('id')->equals(
                new \MongoId($id)
            )
            ->getQuery()->execute();
    }
}