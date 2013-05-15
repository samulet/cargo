<?php

namespace Organization\Entity;

interface CompanyUserInterface
{


    public function getId();

    public function setId($id);

    public function getUserId();

    public function setUserId($userId);

    public function getCompanyId();

    public function setCompanyId($companyId);

    public function getUserRights();

    public function setUserRights($userRights);

    public function getOrgId();

    public function setOrgId($orgId);
}