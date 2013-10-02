<?php

namespace Organization\Entity;

use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Collection;
use \Organization\Form\CompanyAddressFieldset;
/**
 * @ODM\Document(collection="company", repositoryClass="Organization\Repository\CompanyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("company")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Company
{
    public function __construct($ownerOrgId, $param=null)
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
        if($param!='contractAgent') {
            $this->setOwnerOrgId(new \MongoId($ownerOrgId));
        }
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
     * @Annotation\Exclude()
     */
    public $dirty;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Полное наименование юр. лица"})
     */
    public $name;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Краткое наименование юр. лица"})
     */
    public $shortName;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"ИНН"})
     */
    public $inn;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"ОГРН"})
     */
    public $ogrn;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"КПП"})
     */
    public $kpp;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер налоговой"})
     */
    public $taxNumber;
    /**
     * @ODM\Date
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Дата постановки на учет"})
     */
    public $dateStart;



    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Адреса", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Organization\Form\CompanyAddressFieldset"}})

     */


    public $address= array();

    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Контакты", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Organization\Form\CompanyContactsFieldset"}})

     */
    public $contact= array();

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Способ образования"})
     */
    public $createWay;
    /**
     * @ODM\Date
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Дата регистрации"})
     */
    public $dateRegistration;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер налоговой, где проходила регистрация"})
     */
    public $nalogNumber;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Размер уставного капитала"})
     */
    public $capitalValue;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Количество учредителей"})
     */
    public $founderCount;

    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Учредители", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Organization\Form\CompanyFounderFieldset"}})

     */
    public $founder= array();
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Вид собственности"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $property;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Генеральный директор"})
     */
    public $generalManager;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Наименование должности должностного лица"})
     */
    public $official;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Главный бухгалтер"})
     */
    public $chiefAccountant;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})

     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Основание деятельности должностного лица"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $note;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Фактический адрес"})
     */
    public $addressFact;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Юридический адрес"})
     */
    public $addressReg;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Банковские реквизиты"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $requisites;



    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"ОКВЭД"})
     */
    public $okv;



    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})

     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Cотрудники юр лица с контактами"})
     * @var string
     * @ODM\Field(type="string")
     */
    public $personal;

    /**
     * @ODM\Date
     */
    public $deletedAt;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;
    /**
     * @return mixed
     */
/*





Адреса (Вид адреса, почтовый индекс, субъект РФ, город, населенный пункт, улица, номер дома, корпус, квартира)
Контакты (Вид контакта, код страны, код города, номер, дополнительный номер), например телефон, факс и т.п.

Способ образования
Дата регистрации
Номер налоговой, где проходила регистрация
Размер уставного капитала
Количество учредителей

Учредители (вид учредителя, ссылка на учредителя)

Уполномоченные лица (вид полномочия, основание деятельности, ассылка на физ лицо)

Коды ОКВЭД

Номер страхования в ПФР
Номер ПФР
Дата постановки в ПФР
Номер страхования ФМС
Номер ФМС
Дата постановки ФМС

Лицензии (наименование, срок действия, дата выдачи, кем выдано)

Заявители при регистрации (вид заявителя, ссылка на заявителя)

Вид системы налогового учета
Процентная ставка налога

Ссылка на файлы выписки из ЕГРЮЛ/ЕГРЮИП

Устав
Ссылка на файлы устава

Остальные учредительные документы

Наименование документа с решением о создании юр лица
Номер документа
Дата документа

Ссылки на файлы сканов документа

Наименование документа о назначении генерального Директора
Номер документа
Дата документа

Ссылки на файлы сканов документа

Для счета

Ссылки на номер счета
Ссылка на главного бухгалтера (физ лицо)

Важное
Ссылки на других ответственных лиц с указанием области ответственности (сотрудники контрагента) Область ответственности - должность для физ лица для данного контрагента.
Ссылки на сайты (соц сети сайты визитки и т.д.)



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
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
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

    public function getRequisites()
    {
        return $this->requisites;
    }

    public function setRequisites($requisites)
    {
        $this->requisites = $requisites;
        return $this;
    }

    public function getAddressFact()
    {
        return $this->addressFact;
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

    /**
     * Set name.
     *
     * @param string $name
     * @return OrganizationInterface
     */

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}