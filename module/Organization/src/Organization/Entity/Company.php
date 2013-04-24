<?php

namespace Organization\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Organization\Entity\CompanyInterface;
/**
* @ODM\Document(collection="company")
*/
class Company implements CompanyInterface
{
    /**
     * @ODM\Id(strategy="Id")
     */
    protected $id;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $uuid;

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
    protected $requisites;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $addressFact;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $addressReg;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $generalManager;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $telephone;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $email;
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
}