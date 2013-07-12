<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 2:01 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Ticket\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;


/**
 * @ODM\Document(collection="ticketWay")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("ticketWay")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class TicketWay
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
    public $ownerTicketId;

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
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Name"})
     */
    public $name;



    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Description"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $description;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Capacity:"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $capacity;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Sizes:"})
     */
    public $sizes;

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
     * @Annotation\Options({"label":"Пункт загрузки"})
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
     * @Annotation\Options({"label":"Пункт выгрузки"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $areaUnload;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Грузовладелец"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $cargoOwner;


    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Наименование груза"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $cargoName;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"ADR",
     *                      "value_options" : {"0":"","1":"1","2":"2","3":"3"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $adr;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Кубы"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $cubs;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - длина"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsLength;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - высота"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsHeight;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - ширина"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsWidth;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Объем груза, м3"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $cargoValue;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Вес"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $weight;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Рубли"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $rubles;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Паллеты"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $pallet;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Коробки"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $box;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Температурный режим"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $temperature;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Пневмоход"})
     */

    public $airSuspension;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Сцепка"})
     */

    public $coupling;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Тип ТС"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $type;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер документа"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $docNumber;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Вид документа"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $docType;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата докумениа"})
     */

    public $docDate;


    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Комментарий документ"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $docNote;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Возвратный документ","value_options":{"":"Выберите значение","yes":"да", "no":"нет"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"yes","no","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $docWay;

    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Options({"label":"Тип загрузки"})
     * @Annotation\Validator({"name" : "NotEmpty",
     * "options" : {"messages" : {\Zend\Validator\NotEmpty::IS_EMPTY : "Выберите элемент из списка." } } })
     * @Annotation\Attributes({"value":"0"})
     */
    public $typeLoad =array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Options({"label":"Тип выгрузки"})
     * @Annotation\Validator({"name" : "NotEmpty",
     * "options" : {"messages" : {\Zend\Validator\NotEmpty::IS_EMPTY : "Выберите элемент из списка." } } })
     * @Annotation\Attributes({"value":"0"})
     */
    public $typeUnload = array();


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
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата готовности к загрузке"})
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
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата готовности к разгрузке"})
     */

    public $dateEnd;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Время готовности ТС к разгрузке"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeEnd;

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