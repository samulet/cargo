<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 6/3/13
 * Time: 2:01 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Interaction\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

/**
 * @ODM\Document(collection="interactionNote", repositoryClass="Interaction\Repository\InteractionNoteRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("interactionNote")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class InteractionNote
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
    public $ownerInteractionId;
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
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Статус"})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $status;

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