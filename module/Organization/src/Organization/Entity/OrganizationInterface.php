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
     * Get OrgName.
     *
     * @return string
     */
    public function getOrgName();

    /**
     * Set OrgName.
     *
     * @param string $OrgName
     * @return UserInterface
     */
    public function setOrgName($OrgName);

    /**
     * Get orgType.
     *
     * @return string
     */
    public function getOrgType();

    /**
     * Set orgType.
     *
     * @param string $orgType
     * @return UserInterface
     */
    public function setOrgType($orgType);
    public function getOwnerId();
    public function setOwnerId($ownerId);
}
