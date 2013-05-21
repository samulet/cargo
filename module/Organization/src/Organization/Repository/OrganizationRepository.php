<?php
namespace Organization\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class OrganizationRepository extends DocumentRepository
{
    public function getMyAvailableOrganization($org_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('id')->equals(
                new \MongoId($org_id)
            )
            ->getQuery()->execute();
    }
}