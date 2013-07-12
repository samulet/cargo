<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 2:01 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Resource\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

/**
 * @ODM\Document(collection="resourceWay")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("resourceWay")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class ResourceWay
{
    public function __construct()
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
    }

    /**
     * @ODM\Id
     * @var int
     * @Annotation\Exclude()
     */
    public $id;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Exclude()
     */
    public $uuid;
    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Exclude()
     */
    public $ownerResourceId;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ODM\Date
     * @Annotation\Exclude()
     */
    public $created;
    /**
     * @Gedmo\Timestampable(on="update")
     * @ODM\Date
     * @Annotation\Exclude()
     */
    public $updated;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Exclude()
     */
    public $activated;



    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Место загрузки ТС"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $areaLoad;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Место разгрузки ТС"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $areaUnload;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Примечание"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $note;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Валюта",
     *                      "value_options" : {"RUR":"RUR","EUR":"EUR","USD":"USD"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"RUR","EUR","USD"},
     *                              "messages":{"notInArray":"Выберите элемент из списка"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $currency;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Стоимость"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $money;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Валюта",
     *                      "value_options" : {"bank":"Безналичный перевод","card":"Банковской картой","currency":"Наличными"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"bank","card","currency"},
     *                              "messages":{"notInArray":"Выберите элемент из списка"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $formPay;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата готовности ТС к загрузке"})
     */

    public $dateStart;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Время готовности ТС к загрузке"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeStart;



    /**
     * @ODM\Date
     * @Annotation\Exclude()
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

    public function getSizes()
    {
        return $this->sizes;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setAddressFact($addressFact)
    {
        $this->addressFact = $addressFact;
        return $this;
    }

    public function getAddressReg()
    {
        return $this->addressReg;
    }

    public function setAddressReg($addressReg)
    {
        $this->addressReg = $addressReg;
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
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
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

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getTent()
    {
        return $this->name;
    }

    public function setTent($tent)
    {
        $this->tent = $tent;
        return $this;
    }
}