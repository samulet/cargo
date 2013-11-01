<?php

namespace Account\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="companyUser")
 */
class CompanyUser
{
    public function __construct($accId, $user_id, $param, $roles)
    {


        if ($param == 'admin') {
            $this->orgId = new \MongoId($accId);
        } else {
            $this->setCompanyId(new \MongoId($accId));
        }
        $this->setUserId(new \MongoId($user_id));
        $this->roles = $roles;
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
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    public $roles;

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