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
                $id
            )
            ->getQuery()->execute();
    }
    public function getLocalAvailableList($id,$accListId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('global')->equals(null)
            ->field('ownerOrgId')->equals(new \MongoId($accListId))
            ->field('listId')->equals(
                $id
            )
            ->getQuery()->execute();
    }
    public function getLocalAvailableAccList($id,$accListId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('global')->equals(null)
            ->field('account')->equals(new \MongoId($accListId))
            ->field('listId')->equals(
                $id
            )
            ->getQuery()->execute();
    }
    public function getLocalAvailableComList($id,$accListId)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('global')->equals(null)
            ->field('company')->equals(new \MongoId($accListId))
            ->field('listId')->equals(
                $id
            )
            ->getQuery()->execute();
    }
    public function getMyAvailableList($id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null) ->sort('parentFieldId', 'desc')->field('listId')->equals(
                $id
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