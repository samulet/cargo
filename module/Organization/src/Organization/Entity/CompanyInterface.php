<?php

namespace Organization\Entity;

interface CompanyInterface
{
   public function getId();
    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id);

    /**
     * Get description.
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
     * Get activated.
     *
     * @return string activated
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
        public function getUUID();
        public function setUUID($uuid);

            public function getRequisites();

            public function setRequisites($requisites);
            public function getAddressFact();

            public function setAddressFact($addressFact);
            public function getAddressReg();

            public function setAddressReg($addressReg);
            public function getGeneralManager();

            public function setGeneralManager($generalManager);
            public function getTelephone();

            public function setTelephone($telephone);
            public function getEmail();

            public function setEmail($email);
}