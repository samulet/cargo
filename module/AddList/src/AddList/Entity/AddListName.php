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
use Doctrine\ODM\MongoDB\Mapping\Types\Type;


/**
 * @ODM\Document(collection="addListName", repositoryClass="AddList\Repository\AddListNameRepository")
 * @Annotation\Name("addListName")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class AddListName
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
    public $parentId;

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
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Имя списка"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */

    public $listName;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Имя поля"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */

    public $field;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^(([a-zA-Z0-9_\(\)\s]+)|([А-Яа-я0-9_\(\)\s]+))$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Имя поля на русском"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */

    public $fieldRusName;


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