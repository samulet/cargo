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
    public function getMyAvailableListName($id)
    {
        if(!empty($id)) {
            $id=new \MongoId($id);
            $field='id';
        } else {
            $id=null;
            $field='parentId';
        }

        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field($field)->equals(
                $id
            )
            ->getQuery()->execute();
    }
}