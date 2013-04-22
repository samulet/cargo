<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\CompanyUserInterface;
/**
* @ODM\Document(collection="companyUser")
*/
class CompanyUser implements CompanyUserInterface
{
    /**
     * @ODM\Id(strategy="UUID")
     */
    protected $id;
    /**
     * @ODM\Id(strategy="UUID")
     */
    protected $userId;
    /**
     * @ODM\Id(strategy="UUID")
     */
    protected $companyId;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $userRights;
        /**
         * @ODM\Id
         * @var int
         */
    protected $orgId;

    public function getId()
    {
        return $this->id;
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