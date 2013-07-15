<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/1/13
 * Time: 12:15 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

/**
 * @ODM\Document(collection="auction")
 * @Annotation\Name("auction")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Admin
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
    public $ownerItemId;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Валюта:",
     *                      "value_options" : {"RUR":"RUR","EUR":"EUR","USD":"USD"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $currency;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[A-Za-zА-Яа-я0-9 _-]*$/iu"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Ставка:"})
     * @Annotation\Required({"required":"true" })
     * @var string
     * @ODM\Field(type="string")
     */
    public $money;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата конца:"})
     */
    public $dateEnd;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата начала:"})
     */
    public $dateStart;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Attributes({"type":"hidden"})
     * @Annotation\Required({"required":"true" })
     */
    public $str;
    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     */
    public $submit;

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