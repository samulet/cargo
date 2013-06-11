<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 6/3/13
 * Time: 2:01 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Notification\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

/**
 * @ODM\Document(collection="notificationNote", repositoryClass="Notification\Repository\NotificationNoteRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("notificationNote")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class NotificationNote
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
    public $ownerNotificationId;
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":500}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"}})
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
     * @Annotation\Options({"label":"Статус",
     *                      "value_options" : {"consideration":"На рассмотрении","published":"Опубликована"}})
     * @Annotation\Validator({"name":"InArray",
     *                        "options":{"haystack":{"1","2","3"},
     *                              "messages":{"notInArray":"Please Select a Class"}}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $status;
    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Exclude()
     */
    public $statusRus;
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