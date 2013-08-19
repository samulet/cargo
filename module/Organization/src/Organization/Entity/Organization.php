<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\OrganizationInterface;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;


/**
 *
 * @ODM\Document(collection="organization", repositoryClass="Organization\Repository\OrganizationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Organization
{
    public function __construct($ownerId)
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
        $this->setOwnerId(new \MongoId($ownerId));
    }

    /**
     * @ODM\Id
     * @var int
     */
    public $id;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $uuid;

    /**
     * @ODM\ObjectId
     * @var int
     */
    public $ownerId;
    /**
     * @Gedmo\Timestampable(on="create")
     * @ODM\Date
     */
    public $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ODM\Date
     */
    public $updated;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $activated;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $description;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $name;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $type;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $lastItemNumber;
    /**
     * @ODM\Date
     */
    public $deletedAt;

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     * @return OrganizationInterface
     */

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     * @return OrganizationInterface
     */
    public function setType($type)
    {
        $this->type = $type;
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

    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }
}

