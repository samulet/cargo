<?php

namespace Account\Entity;

use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\Form\Annotation;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Collection;

/**
 * @ODM\Document(collection="company", repositoryClass="Account\Repository\CompanyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("company")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Company
{
    public function __construct($ownerAccId, $param = null)
    {
        $uuid_gen = new UuidGenerator();
        $this->setUUID($uuid_gen->generateV4());
        if ($param != 'contractAgent') {
            $this->setOwnerOrgId(new \MongoId($ownerAccId));
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
    public $ownerAccId;

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
     *                      "target_element" : {"type":"\Account\Form\CompanyAddressFieldset"}})

     */


    public $address = array();

    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Контакты", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyContactsFieldset"}})

     */
    public $contact = array();

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
     *                      "target_element" : {"type":"\Account\Form\CompanyFounderFieldset"}})

     */
    public $founder = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Уполномоченные лица", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyAuthorizedPersonsFieldset"}})

     */
    public $authorizedPerson = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Коды ОКВЭД", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyOkvedFieldset"}})

     */
    public $okved = array();

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер страхования в ПФР"})
     */
    public $insuranceNumberInPfr;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"19. Номер ПФР"})
     */
    public $numberInPfr;
    /**
     * @ODM\Date
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Дата постановки в ПФР"})
     */
    public $dateRegistrationPfr;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер страхования ФМС"})
     */
    public $insuranceNumberFms;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Номер ФМС"})
     */
    public $numberInFms;
    /**
     * @ODM\Date
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required(false)
     * @Annotation\Options({"label":"Дата постановки ФМС"})
     */
    public $dateRegistrationFms;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Лицензии", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyLicenseFieldset"}})

     */
    public $license = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Заявители при регистрации", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyApplicantsFieldset"}})

     */
    public $applicants = array();
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Вид системы налогового учета"})
     */
    public $taxSystem;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Процентная ставка налога"})
     */
    public $taxPercent;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Ссылка на файлы выписки из ЕГРЮЛ/ЕГРЮИП"})
     */
    public $egrulLink;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Ссылка на файлы устава"})
     */
    public $law;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Остальные учредительные документы", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyDocumentsFieldset"}})

     */
    public $documents = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Ссылки на номер счета", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyBankAccountFieldset"}})

     */
    public $bankAccount = array();
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Ссылка на главного бухгалтера"})
     */
    public $accountantLink;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Ссылки на других ответственных лиц с указанием области ответственности", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyAnotherPersonsFieldset"}})

     */
    public $anotherPersons = array();
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Ссылки на сайты", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Account\Form\CompanyWebsitesFieldset"}})

     */
    public $websites = array();
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

    public $property = '';

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
        return $this->ownerAccId;
    }

    public function setOwnerOrgId($ownerAccId)
    {
        $this->ownerAccId = $ownerAccId;
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
     * @return AccountInterface
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
     * @return AccountInterface
     */

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}