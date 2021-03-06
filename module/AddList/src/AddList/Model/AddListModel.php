<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/20/13
 * Time: 2:31 AM
 * To change this template use File | Settings | File Templates.
 */

namespace AddList\Model;

use AddList\Entity\AddListName;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use AddList\Entity\AddList;
use AddList\Entity\AddListNameStatic;

class AddListModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;

    public function getAllDataArray($prefix)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result = array();
        $listName = $this->getListNameFull();
        foreach ($listName as $liName) {
            $id = $liName['id'];
            $list = $objectManager->getRepository('AddList\Entity\AddList')->getMyAvailableList($id);
            $res = array();
            foreach ($list as $li) {
                $obj_vars = get_object_vars($li);
                if (!empty($obj_vars['parentFieldId'])) {

                    $parentList = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                        array('id' => new \MongoId($obj_vars['parentFieldId']))
                    );
                    $parentListArr = get_object_vars($parentList);
                    if (!empty($parentListArr)) {
                        $pr = 'parent-' . $parentListArr['key'] . '-';
                    } else {
                        $pr = null;
                    }
                } else {
                    $pr = null;
                }
                array_push($res, array('key' => $pr . $obj_vars['key'], 'value' => $obj_vars['value']));
            }
            $result = $result + array($liName['field'] => $res);
        }
        return $result;
    }

    public function getGlobalArray($prefix)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result = array();
        $listName = $this->getListNameFull();
        foreach ($listName as $liName) {
            $id = $liName['id'];
            $list = $objectManager->getRepository('AddList\Entity\AddList')->getGlobalAvailableList($id);
            $res = array();
            foreach ($list as $li) {
                $obj_vars = get_object_vars($li);
                if (!empty($obj_vars['parentFieldId'])) {

                    $parentList = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                        array('id' => new \MongoId($obj_vars['parentFieldId']), 'global' => 'global')
                    );
                    $parentListArr = get_object_vars($parentList);
                    if (!empty($parentListArr)) {
                        $pr = 'parent-' . $parentListArr['key'] . '-';
                    } else {
                        $pr = null;
                    }
                } else {
                    $pr = null;
                }
                array_push($res, array('key' => $pr . $obj_vars['key'], 'value' => $obj_vars['value']));
            }
            $result = $result + array($liName['field'] => $res);
        }
        return $result;
    }

    public function getLocalArray($prefix, $itemId, $param)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result = array();
        $listName = $this->getListNameFull();
        foreach ($listName as $liName) {
            $id = $liName['id'];
            if ($param == 'account') {
                $list = $objectManager->getRepository('AddList\Entity\AddList')->getLocalAvailableAccList($id, $itemId);
            } elseif ($param == 'company') {
                $list = $objectManager->getRepository('AddList\Entity\AddList')->getLocalAvailableComList($id, $itemId);
            }
            $res = array();
            foreach ($list as $li) {
                $obj_vars = get_object_vars($li);
                if (!empty($obj_vars['parentFieldId'])) {

                    $parentList = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                        array('id' => new \MongoId($obj_vars['parentFieldId']))
                    );
                    $parentListArr = get_object_vars($parentList);
                    if (!empty($parentListArr)) {
                        $pr = 'parent-' . $parentListArr['key'] . '-';
                    } else {
                        $pr = null;
                    }
                } else {
                    $pr = null;
                }
                array_push($res, array('key' => $pr . $obj_vars['key'], 'value' => $obj_vars['value']));
            }
            $result = $result + array($liName['field'] => $res);
        }
        return $result;
    }

    public function returnDataArray($arrFields, $prefix, $accListId, $comListId = null)
    {

        $localArrayAcc = $this->getLocalArray($prefix, $accListId, 'account');

        $globalArray = $this->getGlobalArray($prefix);
        $localArray = array_merge_recursive($globalArray, $localArrayAcc);
        if (!empty($comListId)) {
            $localArrayCom = $this->getLocalArray($prefix, $comListId, 'company');
            $localArray = array_merge_recursive($localArray, $localArrayCom);
        }


        return $localArray;
    }


    public function russianToTranslit($str)
    {
        $cyr = array(
            'а',
            'б',
            'в',
            'г',
            'д',
            'e',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ъ',
            'ь',
            'ю',
            'я',
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ъ',
            'Ь',
            'Ю',
            'Я'
        );
        $lat = array(
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'zh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'ts',
            'ch',
            'sh',
            'sht',
            'a',
            'y',
            'yu',
            'ya',
            'A',
            'B',
            'V',
            'G',
            'D',
            'E',
            'Zh',
            'Z',
            'I',
            'Y',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'F',
            'H',
            'Ts',
            'Ch',
            'Sh',
            'Sht',
            'A',
            'Y',
            'Yu',
            'Ya'
        );
        return str_replace($cyr, $lat, $str);
    }

    public function addList($post, $listUUID, $parentField, $userId, $orgId, $comId)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');


        $propArray = get_object_vars($post);


        if (is_string($parentField)) {

            $listId = $this->getIdByUUID($parentField);
            $mongoId = new \MongoId($listId);
            $propArray['parentFieldId'] = new \MongoId($this->getIdByUUID($parentField));
            $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                array('id' => $mongoId)
            );

            $listName = $this->getListName('veh-models');
            $propArray['listId'] = $listName['id'];

        }
        if (is_string($listUUID)) {
            $propArray['listId'] = $listUUID;

        }

        $res = new AddList();

        if (!empty($propArray['requisites'])) {

            $propArray['value'] = 'р/c' . $propArray['requisites'][0]['addListRequisitesAccountNumber'] . ' ' . $propArray['requisites'][0]['addListRequisitesBankName'];
        }

        if (!empty($propArray['forAccount'])) {

            if ($propArray['forAccount'] == 'company') {
                if (!empty($propArray['company'])) {

                    $propArray['company'] = new \MongoId($comId);
                } else {
                    $propArray['company'] = new \MongoId($comId);
                }
            } elseif ($propArray['forAccount'] == 'account') {
                $propArray['account'] = new \MongoId($orgId);
            }

        }
        $propArray['key'] = $propArray['value'];

        foreach ($propArray as $key => $value) {
            if (!empty($value)) {
                $res->$key = $value;
            }

        }

        $res->ownerOrgId = new \MongoId($orgId);
        $res->ownerUserId = new \MongoId($userId);
        $objectManager->persist($res);
        $objectManager->flush();

        return get_object_vars($res);
    }

    public function addListName($post, $list_uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (empty($list_uuid)) {
            $res = new AddListName();
        } else {
            $res = $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(
                array('uuid' => $list_uuid)
            );
            unset($res->parentId);
        }

        $propArray = get_object_vars($post);
        if ($propArray['parentId'] == 'empty') {
            unset($propArray['parentId']);
        }

        foreach ($propArray as $key => $value) {
            if ($res->$key != 'parentId') {
                $res->$key = $value;
            } else {
                $res->$key = new \MongoId($value);
            }
        }
        $objectManager->persist($res);
        $objectManager->flush();
    }


    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getListAdmin($uuid)
    {
        $id = $uuid;

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('AddList\Entity\AddList')->getMyAvailableList($id);
        $list = $this->getListName($id);

        $result = array();
        foreach ($res as $re) {

            $vars = get_object_vars($re);

            $organizationModel = $this->getAccountModel();

            $acc = $organizationModel->getAccount($vars['ownerOrgId']);
            if (!empty($acc)) {
                $vars['ownerOrgId'] = $acc;
            }
            if (!empty($vars['parentFieldId'])) {
                $parent = $objectManager->getRepository('AddList\Entity\AddList')->getOneMyAvailableList(
                    $vars['parentFieldId']
                );
                foreach ($parent as $par) {
                    $parent = $par;
                }
                $parent = get_object_vars($parent);
            } else {
                $parent = null;
            }
            array_push($result, array('it' => $vars, 'parent' => $parent));
        }


        return array('field' => $result, 'list' => $list);
    }

    public function getList($uuid, $accListId)
    {
        $id = $uuid;

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('AddList\Entity\AddList')->getLocalAvailableList($id, $accListId);
        $list = $this->getListName($id);

        $result = array();
        foreach ($res as $re) {

            $vars = get_object_vars($re);
            $organizationModel = $this->getAccountModel();

            $acc = $organizationModel->getAccount($vars['ownerOrgId']);

            if (!empty($acc)) {
                $vars['ownerOrgId'] = $acc;
            }
            if (!empty($vars['parentFieldId'])) {
                $parent = $objectManager->getRepository('AddList\Entity\AddList')->getOneMyAvailableList(
                    $vars['parentFieldId']
                );
                foreach ($parent as $par) {
                    $parent = $par;
                }
                $parent = get_object_vars($parent);
            } else {
                $parent = null;
            }
            array_push($result, array('it' => $vars, 'parent' => $parent));
        }


        return array('field' => $result, 'list' => $list);
    }

    public function getListNameFull($list = null)
    {
        if (empty($list)) {
            $list = AddListNameStatic::$list;
        }
        $trueListNameArray = array();
        foreach ($list as $liKey => $liName) {
            $liName['uuid'] = $liKey;
            $liName['id'] = $liKey;
            if (!empty($liName['child'])) {
                $trueListNameArray = $trueListNameArray + $this->getListNameFull($liName['child']);
                unset($liName['child']);
            }
            array_push($trueListNameArray, $liName);
        }
        return $trueListNameArray;
    }

    public function getListName($id = null)
    {
        $list = AddListNameStatic::$list;
        if (is_string($id)) {
            if (!empty($list[$id])) {
                $list = $list[$id];
                $list['id'] = $id;
                $list['uuid'] = $id;
            } elseif (!empty($list['veh-marks']['child'][$id])) {
                $list = $list['veh-marks']['child'][$id];
                $list['id'] = $id;
                $list['uuid'] = $id;
            }
        }

        return $list;
    }

    public function deleteList($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(array('uuid' => $uuid));
        if (!$list) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Resource', $uuid);
        }
        $objectManager->remove($list);
        $objectManager->flush();
    }

    public function getAllListName()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->getAllAvailableListName();
        $result = array();
        foreach ($list as $re) {
            array_push($result, get_object_vars($re));
        }
        return $result;
    }

    public function getIdByUUID($uuid)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(array('uuid' => $uuid));
        if (empty($list)) {
            $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(array('uuid' => $uuid));
        }
        if (empty($list)) {
            return null;
        } else {
            return $list->id;
        }

    }

    public function getChildName($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(
            array('parentId' => new \MongoId($id))
        );
        if (empty($list)) {
            $result = null;
        } else {
            $result = get_object_vars($list);
        }
        return $result;
    }

    public function getOneList($uuid)
    {
        $id = $this->getIdByUUID($uuid);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->getOneMyAvailableList($id);
        foreach ($list as $li) {
            $list = $li;
        }
        if (empty($list)) {
            $result = null;
        } else {
            $result = get_object_vars($list);
        }
        return $result;


    }

    public function editField($uuid, $post, $orgId, $comId)
    {
        $propArray = get_object_vars($post);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
            array('uuid' => $uuid)
        );
        $propArray['key'] = $propArray['value'];
        if (!empty($propArray['requisites'])) {
            $propArray['requisites'] = $propArray['requisites'][0];
            $propArray['value'] = 'р/c' . $propArray['requisites']['addListRequisitesAccountNumber'] . ' ' . $propArray['requisites']['addListRequisitesBankName'];
        }
        if (!empty($propArray['forAccount'])) {

            if ($propArray['forAccount'] == 'company') {
                if (!empty($propArray['company'])) {

                    $propArray['company'] = new \MongoId($comId);
                } else {
                    $propArray['company'] = new \MongoId($comId);
                }
            } elseif ($propArray['forAccount'] == 'account') {
                $propArray['account'] = new \MongoId($orgId);
            }

        }

        foreach ($propArray as $key => $value) {
            if (!empty($value)) {
                $list->$key = $value;
            }

        }
        $objectManager->persist($list);
        $objectManager->flush();

        return get_object_vars($list);
    }

    public function listParentAction($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $listParent = $this->getOneList($uuid);
        $listChild = $objectManager->getRepository('AddList\Entity\AddList')->findBy(
            array('parentFieldId' => new \MongoId($listParent['id']))
        );
        $result = array();
        foreach ($listChild as $re) {
            array_push($result, get_object_vars($re));
        }
        return $result;
    }

    public function getListUuidById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(array('id' => new \MongoId($id)));
        return $list->uuid;
    }

    public function getAccountModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->organizationModel;
    }

    public function getChildUuid($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
            array('parentFieldId' => new \MongoId($id))
        );
        return $list->uuid;
    }

    public function addListTranslator()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $listName = $objectManager->getRepository('AddList\Entity\AddListName')->createQueryBuilder()
            ->getQuery()->execute();
        foreach ($listName as $name) {
            $trueResult = '';
            $newList = AddListNameStatic::$list;

            foreach ($newList as $newListElementKey => $newListElementValue) {

                if (($newListElementValue['field'] == $name->field) && ($newListElementValue['listName'] == $name->listName)) {
                    $trueResult = $newListElementKey;

                }
                echo $newListElementValue['field'] . ' == ' . $name->field . ' / ' . $newListElementValue['listName'] . ' == ' . $name->listName . ' || ';
            }

            if (empty($trueResult)) {
                if ((AddListNameStatic::$list["veh-marks"]["child"]["veh-models"]['field'] == $name->field) && (AddListNameStatic::$list["veh-marks"]["child"]["veh-models"]['listName'] == $name->listName)) {
                    $trueResult = "veh-models";
                }
            }

            if (!empty($trueResult)) {
                $list = $objectManager->getRepository('AddList\Entity\AddList')->findBy(
                    array('listId' => new \MongoId($name->id))
                );
                foreach ($list as $li) {

                    $objectManager->getRepository('AddList\Entity\AddList')->createQueryBuilder()

                        ->findAndUpdate()
                        ->field('id')->equals(new \MongoId($li->id))
                        ->field('listId')->set($trueResult)
                        ->getQuery()
                        ->execute();
                }
            }
        }
    }

    public function addBootstrap3Class(&$form)
    {
        foreach ($form as $el) {
            $attr = $el->getAttributes();
            if (!empty($attr['type'])) {
                if (($attr['type'] != 'checkbox') && ($attr['type'] != 'multi_checkbox') && ($attr['type'] != 'radio')) {
                    $el->setAttributes(array('class' => 'form-control'));
                }
            }
        }
    }

}