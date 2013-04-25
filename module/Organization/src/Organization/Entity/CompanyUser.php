<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\CompanyUserInterface;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
/**
* @ODM\Document(collection="companyUser")
*/
class CompanyUser implements CompanyUserInterface
{
    public function __construct($org_id,$user_id)
    {

        $this->setOrgId(new \MongoId($org_id));
        $this->setUserId(new \MongoId($user_id));
    }
    /**
     * @ODM\Id
     * @var int
     */
    protected $id;
    /**
     * @ODM\ObjectId
     * @var int
     */
    protected $userId;
    /**
     * @ODM\ObjectId
     * @var int
     */
    protected $companyId;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $userRights;
    /**
     * @ODM\ObjectId
     * @var int
     */
    protected $orgId;

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
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
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
    function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
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