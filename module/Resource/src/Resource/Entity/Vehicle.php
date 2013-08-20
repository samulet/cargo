<?php
namespace Resource\Entity;

use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Form\Element;
use Zend\Form\Form;

/**
 * @ODM\Document(collection="vehicle",repositoryClass="Resource\Repository\VehicleRepository")

 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("vehicle")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Vehicle
{
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
    public $ownerId;
    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Exclude()
     */
    public $ownerOrgId;
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
     * @ODM\Index(unique=true)
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"серия и номер ПТС"})
     */
    public $serialNumber;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"VIN транспортного средства"})
     */
    public $vin;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Марка ТС"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $mark;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Модель ТС"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $model;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Тип ТС"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $type;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Статус ТС"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"value":"0"})
     */
    public $status;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Год выпуска",
     *                      "value_options" : {"2013":"2013","2012":"2012","2011":"2011","2010":"2010","2009":"2009","2008":"2008",
     * "2007":"2007","2006":"2006","2005":"2005","2004":"2004","2003":"2003","2002":"2002","2001":"2001","2000":"2000","1999":"1999",
     * "1998":"1998","1997":"1997","1996":"1996","1995":"1995","1994":"1994","1993":"1993","1992":"1992","1991":"1991","1990":"1990",
     * "1989":"1989","1988":"1988","1987":"1987","1986":"1986","1985":"1985","1984":"1984","1983":"1983","1982":"1982","1981":"1981",
     * "1980":"1980"
     * }})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1980","1981","1982","1983","1984","1985","1986","1987","1988","1989","1990","1991",
     * "1992","1993","1994","1995","1996","1997","1998","1999","2000","2001","2002","2003","2004","2005","2006","2007","2008","2009",
     * "2010","2011","2012","2013"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */

    public $dateMade;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Cерия и номер свидетельства"})
     */
    public $serialNumberDoc;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер регистрационного знака ТС "})
     */
    public $carNumber;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Экологический класс ТС",
     *                      "value_options" : {"0":"0","1":"1","2":"2","3":"3","4":"4","5":"5"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"0","1","2","3","4","5"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $ecologicalClass;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Разрешенная максимальная масса"})
     */
    public $allowedMaxMass;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Количество осей"})
     */
    public $axles;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Грузоподъемность"})
     */
    public $capacity;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Фио собственника"})
     */
    public $ownerName;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Город, район, насел пункт, улица, дом, корпус/строение, квартира/офис"})
     */
    public $whoGave ;
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
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[0-9]+$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Объем кузова"})
     */
    public $bodyValue;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Тип кузова"})
     */
    public $bodyType;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;
    /**
     * @ODM\Date
     * @Annotation\Exclude()
     */
    public $deletedAt;


    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Полуприцеп"})
     */

    public $semitrailer;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Тягач"})
     */

    public $tractor;

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
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Грузовик"})
     */

    public $lorry;



    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Гидролифт"})
     */

    public $hydroLift;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"EKMT"})
     */

    public $EKMT;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"TIR"})
     */

    public $TIR;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"ADR",
     *                      "value_options" : {"1":"1","2":"2","3":"3"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $adr;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})

     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Примечание"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $note;



    /**
     * @return \Resource\Entity\Vehicle
     */
    public function __construct()
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }

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
     *
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
     *
     * @return Vehicle
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

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getSizes()
    {
        return $this->sizes;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
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
     *
     * @return $this
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
