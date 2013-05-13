<?php

namespace Organization\Entity;

interface OrganizationInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id);

    /**
     * Get Description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description.
     *
     * @param string $description
     * @return UserInterface
     */
    public function setDescription($description);

    /**
     * Get password.
     *
     * @return string password
     */
    public function getActivated();

    /**
     * Set activated.
     *
     * @param string $activated
     * @return UserInterface
     */
    public function setActivated($activated);

    public function getCreated();

    public function setCreated($created);

    public function setUpdated($updated);

    public function getUpdated();

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name.
     *
     * @param string $Name
     * @return UserInterface
     */
    public function setName($name);

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set type.
     *
     * @param string $type
     * @return UserInterface
     */
    public function setType($type);

    public function getOwnerId();

    public function setOwnerId($ownerId);

    public function getUUID();

    public function setUUID($uuid);
}
