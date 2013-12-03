<?php

namespace User\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use ZfcUser\Entity\UserInterface;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

/**
 * @ODM\Document(collection="user")
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class User implements UserInterface
{
    /**
     * @ODM\Id
     * @var int
     */
    protected $id;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $uuid;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $username;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $email;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $displayName;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $password;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    protected $state;

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
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $roles = array();
    /**
     * @ODM\ObjectId
     * @var int
     */
    protected $currentAcc;
    /**
     * @ODM\ObjectId
     * @var int
     */
    protected $currentCom;
    /**
     * @ODM\Date
     */
    protected $deletedAt;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $name = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $docs = array();
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $snils;
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $inn;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $phone = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $site = array();
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $avatar;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $sign = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $social = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     */
    protected $status = array();
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

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDocs($docs)
    {
        $this->docs = $docs;
        return $this;
    }
    public function getDocs()
    {
        return $this->docs;
    }

    public function setSnils($snils)
    {
        $this->snils = $snils;
        return $this;
    }
    public function getSnils()
    {
        return $this->snils;
    }

    public function setInn($inn)
    {
        $this->inn = $inn;
        return $this;
    }
    public function getInn()
    {
        return $this->inn;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }
    public function getPhone()
    {
        return $this->phone;
    }

    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }
    public function getSite()
    {
        return $this->site;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }
    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getSign()
    {
        return $this->sign;
    }
    public function setSign($sign)
    {
        $this->sign = $sign;
        return $this;
    }

    public function getSocial()
    {
        return $this->social;
    }
    public function setSocial($social)
    {
        $this->social = $social;
        return $this;
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

    public function getCurrentAcc()
    {
        return $this->currentAcc;
    }

    public function getCurrentCom()
    {
        return $this->currentCom;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return UserInterface
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return UserInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return UserInterface
     */
    public function setState($state)
    {
        $this->state = $state;
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
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid = null)
    {
        if(empty($uuid)) {
            $uuidGen = new UuidGenerator();
            $this->uuid=$uuidGen->generateV4();
        } else {
            $this->uuid = $uuid;
        }
        return $this;
    }


    public function setData($data) {
        if($data !== null && is_array($data)){
            foreach(array_keys(get_class_vars(__CLASS__)) as $key){
                if(isset($entity[$key]) && ($key!='id') && ($key!='uuid') ){
                    $this->$key = $entity[$key];
                }
            }
        }
        return $this;

    }

    public function getData() {
        $data = array();
        foreach(array_keys(get_class_vars(__CLASS__)) as $key){
            $data[$key]=$this->$key;
        }
        return $data;
    }

}
