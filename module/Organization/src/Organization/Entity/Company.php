<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\CompanyInterface;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
/**
* @ODM\Document(collection="company")
*/
class Company
{
    public function __construct($ownerOrgId)
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
        $this->setOwnerOrgId(new \MongoId($ownerOrgId));
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
    public $ownerOrgId;

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
    public $description;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $requisites;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $addressFact;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $addressReg;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $generalManager;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $telephone;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    public $email;
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
    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }
    public function getRequisites()
    {
        return $this->requisites;
    }

    public function setRequisites($requisites)
    {
        $this->requisites = $requisites;
        return $this;
    }
    public function getAddressFact()
    {
        return $this->addressFact;
    }

    public function setAddressFact($addressFact)
    {
        $this->addressFact= $addressFact;
        return $this;
    }
    public function getAddressReg()
    {
        return $this->addressReg;
    }

    public function setAddressReg($addressReg)
    {
        $this->addressReg= $addressReg;
        return $this;
    }
    public function getGeneralManager()
    {
        return $this->generalManager;
    }

    public function setGeneralManager($generalManager)
    {
        $this->generalManager = $generalManager;
        return $this;
    }
    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone=$telephone ;
        return $this;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email=$email ;
        return $this;
    }
    public function getOwnerOrgId()
    {
        return $this->ownerOrgId;
    }

    public function setOwnerOrgId($ownerOrgId)
    {
        $this->ownerOrgId = $ownerOrgId;
        return $this;
    }
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
}