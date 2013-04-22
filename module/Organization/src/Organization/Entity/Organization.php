<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\OrganizationInterface;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
/**
 * @ODM\Document(collection="organization")
 */
class Organization implements OrganizationInterface
{
    public function __construct()
    {
        $uuid_gen = new UuidGenerator();
        $this->setId($uuid_gen->generateV4());
    }
    /**
     *
     *  @ODM\Id(strategy="UUID")
     */
    protected $id;

 //   protected $uuid;
    /**
     * @ODM\Id
     * @var int
     */
    protected $ownerId;
    /**
     * @Gedmo\Timestampable(on="create")
     * @ODM\Date
     */
    protected $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ODM\Date
     */
    protected $updated;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $activated;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $description;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $orgName;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $orgType;
    /**
     * Get id.
     *
     * @return int
     */
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
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description.
     *
     * @param string $description
     * @return UserInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get activated.
     *
     * @return string activated
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set activated.
     *
     * @param string $activated
     * @return UserInterface
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get OrgName.
     *
     * @return string
     */
    public function getOrgName()
    {
        return $this->OrgName;
    }
    /**
     * Set OrgName.
     *
     * @param string $orgName
     * @return OrganizationInterface
     */

    public function setOrgName($orgName)
    {
        $this->orgName = $orgName;
        return $this;
    }
    /**
     * Get orgType.
     *
     * @return string
     */
    public function getOrgType()
    {
        return $this->orgType;
    }
    /**
     * Set orgType.
     *
     * @param string $orgType
     * @return OrganizationInterface
     */
    public function setOrgType($orgType)
    {
        $this->orgType = $orgType;
        return $this;
    }
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}

