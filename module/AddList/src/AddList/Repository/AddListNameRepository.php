<?php
namespace AddList\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AddListNameRepository extends DocumentRepository
{
    public function getAllAvailableListName()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }
    public function getMyAvailableListName($id,$child=null)
    {
        if(!empty($id)) {
            $id=new \MongoId($id);
            $field='id';
        } else {
            $id=null;
            $field='parentId';
        }
        if(!empty($child)) {
            $id=new \MongoId($id);
            $field='parentId';
        }
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field($field)->equals(
                $id
            )
            ->getQuery()->execute();
    }
}