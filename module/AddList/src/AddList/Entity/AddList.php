<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/20/13
 * Time: 2:31 AM
 * To change this template use File | Settings | File Templates.
 */

namespace AddList\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;



/**
 * @ODM\Document(collection="addList", repositoryClass="AddList\Repository\AddListRepository")
 * @Annotation\Name("addList")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class AddList
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
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Лист:"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $ownerOrgId;
    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Лист:"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $ownerUserId;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Лист:"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $listId;

    /**
     * @ODM\ObjectId
     * @var int
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Лист:"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $parentFieldId;

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
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Занчение в БД"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $key;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})

     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Имя поля"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $value;
    /**
     * @Annotation\Exclude()
     * @var string
     * @ODM\Field(type="string")
     */
    public $global;
    /**
     * @var array
     * @ODM\Collection(strategy="pushAll")
     * @Annotation\Type("Zend\Form\Element\Collection")
     * @Annotation\Options({"label":"Реквизиты", "should_create_template" : "true", "count" : 1,"allow_add" : "true",
     *                      "target_element" : {"type":"\Organization\Form\CompanyBankAccountFieldset"}})

     */
    public $requisites= array();
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
    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }
}