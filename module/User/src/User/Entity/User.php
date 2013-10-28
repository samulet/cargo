<?php

namespace User\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use ZfcUser\Entity\UserInterface;

/**
 * @ODM\Document(collection="user")
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class User implements UserInterface, ProviderInterface
{
    /**
     * @ODM\Id
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Имя пользователя"})
     */
    protected $username;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Email"})
     */
    protected $email;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Отображаемое имя"})
     */
    protected $displayName;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Пароль"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
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
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Выберите роль пользователя",
     *                      "value_options" : {"forwarder":"Логист","carrier":"Перевозчик","customer":"Заказчик"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    protected $roles;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;
    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Выберите организацию от которой вы работаете"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $currentAcc;
    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Выберите компанию от которой вы работаете"})
     * @Annotation\Attributes({"value":"0"})
     */
    public $currentCom;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = array();
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
}
