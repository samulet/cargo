<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 8/13/13
 * Time: 4:05 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Excel\Entity;

use Zend\Form\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Element;
use Zend\Form\Form;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;


/**
 * @ODM\Document(collection="excel")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\Name("excel")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class Excel
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
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Type("Zend\Form\Element\Select")

     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Вид формирования экселя"})
     * @Annotation\Attributes({"options":{"right":"Вывод марщрутов по горизонтали вправо","down":"Вывод марщрутов по вертикали вниз"}})
     * @Annotation\Attributes({"value":"0"})
     */
    public $type;
    /**
     * @Annotation\Type("Zend\Form\Element\File")
     * @Annotation\Options({"label":"Загрузите шаблон"})
     */
    public $file;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Отправить"})
     */
    public $submit;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Annotation\Exclude()
     */
    public $statusRus;
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