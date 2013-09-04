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
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":100}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Пункт загрузки"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $areaLoad;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":100}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Пункт выгрузки"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $areaUnload;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Грузовладелец"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
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

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"ADR",
     *                      "value_options" : {"0":"","1":"1","2":"2","3":"3"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"0","1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     * @Annotation\Required(false)
     */
    public $adr;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Кубы"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $cubs;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - длина, м"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsLength;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - высота, м"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsHeight;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Габариты - ширина, м"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $dimensionsWidth;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Объем груза, м3"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $cargoValue;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Вес"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $weight;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Рубли"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $rubles;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Паллеты"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $pallet;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Коробки"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $box;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Температурный режим"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({"value":"0"})
     */
    public $temperature;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Постоянно (загрузка)",
     *                      "value_options" : {"0":"","По рабочим дням":"По рабочим дням","Ежедневно":"Ежедневно"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"0","Ежедневно","По рабочим дням"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     * @Annotation\Required(false)
     */
    public $always;
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
     */
    public $type='';

    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\MultiCheckbox")
     * @Annotation\Options({"label":"Тип загрузки"})
     * @Annotation\Validator({"name" : "NotEmpty",
     * "options" : {"messages" : {\Zend\Validator\NotEmpty::IS_EMPTY : "Выберите элемент из списка." } } })
     * @Annotation\Attributes({"value":"0"})
     * @Annotation\Required(false)
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
     * @Annotation\Required(false)
     */
    public $typeUnload = array();


    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})

     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Примечание"})
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required(false)
     */



    public $note;



    /**
     * @ODM\Date
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Дата готовности к загрузке"})
     */

    public $dateStart;
    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Загрузка с"})
     */

    public $dateStartFilterFrom;
    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"По"})
     */

    public $dateStartFilterTo;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Круглосуточно"})
     */

    public $aroundDay;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"label":"",
     *                      "value_options" : {"prepareToLoad":"","dateStart":"","always":""}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"prepareToLoad","dateStart","always"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":""})
     * @Annotation\Required({"required":"true" })
     */

    public $setLoadType;



    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Готов к загрузке",
     *                      "value_options" : {"0":"","Только сегодня":"Только сегодня","Сегодня и завтра":"Сегодня и завтра"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"0","Только сегодня","Сегодня и завтра"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     * @Annotation\Required(false)
     */
    public $prepareToLoad;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"+ дней",
     *                      "value_options" : {"0":"0","1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"0","1","2","3","4","5","6","7","8","9","10"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     * @Annotation\Required(false)
     */
    public $dateStartPlus;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":5}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({
     * "type":"text",
     * "onkeyup" : "time_control(this);",
     * "placeholder" : "12:00"
     * })
     * @Annotation\Options({"label":"C"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeLoadStart;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":5}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({
     * "type":"text",
     * "onkeyup" : "time_control(this);",
     * "placeholder" : "14:00"
     * })
     * @Annotation\Options({"label":"По"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeLoadEnd;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":5}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({
     * "type":"text",
     * "onkeyup" : "time_control(this);",
     * "placeholder" : "12:00"
     * })
     * @Annotation\Options({"label":"С"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeUnloadStart;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":5}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$/"}})
     * @Annotation\Attributes({
     * "type":"text",
     * "onkeyup" : "time_control(this);",
     * "placeholder" : "12:00"
     * })
     * @Annotation\Options({"label":"По"})
     * @Annotation\Required(false)
     * @var string
     * @ODM\Field(type="string")
     */
    public $timeUnloadEnd;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Options({"label":"Дата готовности к разгрузке"})
     * @Annotation\Required(false)
     */

    public $dateEnd;



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