<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\CompanyUserInterface;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
/**
* @ODM\Document(collection="companyUser")
*/
class CompanyUser
{
    public function __construct($org_id,$user_id)
    {

        $this->setCompanyId(new \MongoId($org_id));
        $this->setUserId(new \MongoId($user_id));
    }
    /**
     * @ODM\Id
     * @var int
     */
    public $id;
    /**
     * @ODM\ObjectId
     * @var int
     */
    public $userId;
    /**
     * @ODM\ObjectId
     * @var int
     */
    public $companyId;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $userRights;
    /**
     * @ODM\ObjectId
     * @var int
     */
    public $orgId;

    public function getId()
    {
        return $this->id;
    }

        function setId($id)
        {
            $this->id = $id;
            return $this;
        }
    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
     public function getOrgId()
     {
        return $this->orgId;
     }
     /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function getUserRights()
    {
        return $this->userRights;
    }
    function setUserRights($userRights)
    {
        $this->userRights = $userRights;
        return $this;
    }
}