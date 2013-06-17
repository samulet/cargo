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
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use AddList\Entity\AddList;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class AddListModel implements ServiceLocatorAwareInterface
{

    protected $serviceLocator;


    public function getGlobalArray($prefix) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result=array();
        $listName = $objectManager->getRepository('AddList\Entity\AddListName')->getMyAvailableListsByName($prefix);
        foreach($listName as $liName) {
            $id=(string)$liName->id;
            $list = $objectManager->getRepository('AddList\Entity\AddList')->getGlobalAvailableList($id);
            $res=array();
            foreach($list as $li) {
                $obj_vars = get_object_vars($li);
                if(!empty($obj_vars['parentFieldId'])) {

                    $parentList= $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                        array('id' =>  new \MongoId($obj_vars['parentFieldId']),'global'=>'global')
                    );
                    $parentListArr=get_object_vars($parentList);
                    if(!empty($parentListArr)) {
                        $pr='parent-'.$parentListArr['key'].'-';
                    } else {
                        $pr=null;
                    }
                } else {
                    $pr=null;
                }
                array_push($res, array('key'=>$pr.$obj_vars['key'],'value'=>$obj_vars['value']));
            }
            $result=$result+array((string)$liName->field => $res);
        }
        return $result;
    }

    public function getLocalArray($prefix,$orgListId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result=array();
        $listName = $objectManager->getRepository('AddList\Entity\AddListName')->getMyAvailableListsByName($prefix);
        foreach($listName as $liName) {
            $id=(string)$liName->id;
            $list = $objectManager->getRepository('AddList\Entity\AddList')->getLocalAvailableList($id,$orgListId);
            $res=array();
            foreach($list as $li) {
                $obj_vars = get_object_vars($li);
                if(!empty($obj_vars['parentFieldId'])) {

                    $parentList= $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                        array('id' =>  new \MongoId($obj_vars['parentFieldId']),'ownerOrgId'=>new \MongoId($orgListId))
                    );
                    $parentListArr=get_object_vars($parentList);
                    if(!empty($parentListArr)) {
                        $pr='parent-'.$parentListArr['key'].'-';
                    } else {
                        $pr=null;
                    }
                } else {
                    $pr=null;
                }
                array_push($res, array('key'=>$pr.$obj_vars['key'],'value'=>$obj_vars['value']));
            }
            $result=$result+array((string)$liName->field => $res);
        }
        return $result;
    }

    public function returnDataArray($arrFields,$prefix,$orgListId) {
        $localArray=$this->getLocalArray($prefix,$orgListId);
        $globalArray=$this->getGlobalArray($prefix);

        return array_merge_recursive($globalArray,$localArray);
    }

    public function russianToTranslit($str) {
        $cyr  = array('а','б','в','г','д','e','ж','з','и','й','к','л','м','н','о','п','р','с','т','у',
            'ф','х','ц','ч','ш','щ','ъ','ь', 'ю','я','А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У',
            'Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ь', 'Ю','Я' );
        $lat = array( 'a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u',
            'f' ,'h' ,'ts' ,'ch','sh' ,'sht' ,'a' ,'y' ,'yu' ,'ya','A','B','V','G','D','E','Zh',
            'Z','I','Y','K','L','M','N','O','P','R','S','T','U',
            'F' ,'H' ,'Ts' ,'Ch','Sh' ,'Sht' ,'A' ,'Y' ,'Yu' ,'Ya' );
        return str_replace($cyr, $lat, $str);
    }

    public function addList($post,$listUUID,$parentField,$userId,$orgId) {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');


        $prop_array = get_object_vars($post);



        if(is_string($parentField)) {

            $listId=$this->getIdByUUID($parentField);
            $mongoId=new \MongoId($listId);
            $prop_array['parentFieldId']=new \MongoId($this->getIdByUUID($parentField));
            $list=  $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                array('id' =>  $mongoId)
            );

            $listName=  $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(
                array('parentId' =>  new \MongoId($list->listId))
            );
            $prop_array['listId']=new \MongoId($listName->id);

        }
        if(is_string($listUUID)) {
            $prop_array['listId']=new \MongoId($this->getIdByUUID($listUUID));

        }

        $res = new AddList();


        $prop_array['key']=$this->russianToTranslit( $prop_array['value']);

        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $res->ownerOrgId= new \MongoId($orgId);
        $res->ownerUserId=new \MongoId($userId);
        $objectManager->persist($res);
        $objectManager->flush();

        return get_object_vars($res);
    }

    public function addListName($post,$list_uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if(empty($list_uuid)) {
            $res = new AddListName();
        } else {
            $res=  $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(
                array('uuid' => $list_uuid)
            );
            unset($res->parentId);
        }

        $prop_array = get_object_vars($post);
        if($prop_array['parentId']=='empty') {
            unset($prop_array['parentId']);
        }

        foreach ($prop_array as $key => $value) {
            if($res->$key!='parentId') {
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



    public function getList($uuid,$orgListId) {
        $id=$this->getIdByUUID($uuid);

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('AddList\Entity\AddList')->getLocalAvailableList($id,$orgListId);
        $list=$this->getListName($id);

        $result=array();
        foreach($res as $re)
        {

            $vars=get_object_vars($re);
            if(!empty($vars['parentFieldId'])) {
                $parent = $objectManager->getRepository('AddList\Entity\AddList')->getOneMyAvailableList($vars['parentFieldId']);
                foreach($parent as $par)
                {
                    $parent=$par;
                }
                $parent=get_object_vars($parent);
             } else {
                $parent=null;
            }
            array_push($result,array('it'=>$vars,'parent'=>$parent));
        }




        return array('field'=>$result,'list'=>$list);
    }



    public function getListName($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if(!is_string($id)) {
            $propArray=get_object_vars($id);

        } else {
            $propArray=$id;
        }
        $res = $objectManager->getRepository('AddList\Entity\AddListName')->getMyAvailableListName($propArray);
        $result=array();

        foreach($res as $re)
        {

            $child=$objectManager->getRepository('AddList\Entity\AddListName')->getMyAvailableListName($re->id,'child');
            $counter=0;
            foreach($child as $ch) {
                $child=$ch;
                $counter++;
            }
            if($counter==0) {
                $child=null;
            }
            $childArray=array();
            while(!empty($child)) {

                    if(empty($childArray)) {
                        $childArray= array('list'=>get_object_vars($child), 'child'=>null);

                    } else {
                        $arrTmp=$childArray['child'];
                        $targetChild=&$childArray['child'];
                        while(!empty($arrTmp)) {
                            $arrTmp=$arrTmp['child'];
                            $targetChild=&$targetChild['child'];
                        }
                        $targetChild= array('list'=>get_object_vars($child), 'child'=>null);

                    }



                $child=$objectManager->getRepository('AddList\Entity\AddListName')->getMyAvailableListName($child->id,'child');
                $counter=0;
                foreach($child as $ch) {
                    $counter++;
                    $child=$ch;
                }
                if($counter==0) {
                    $child=null;
                }

            }

            array_push($result,array('list'=>get_object_vars($re), 'child'=>$childArray));
        }

        if(!empty($propArray)) {
            $result=$result[0]['list'];
        }
        if(empty($result)) {
            $result=null;
        }

        return $result;
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

    public function getAllListName() {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->getAllAvailableListName();
        $result=array();
        foreach($list as $re)
        {
            array_push($result,get_object_vars($re));
        }
        return $result;
    }

    public function getIdByUUID($uuid) {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(array('uuid' => $uuid));
        if(empty($list)) {
            $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(array('uuid' => $uuid));
        }
        return $list->id;
    }

    public function getChildName($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddListName')->findOneBy(array('parentId' => new \MongoId($id)));
        if(empty($list)) {
            $result=null;
        } else {
            $result=get_object_vars($list);
        }
        return $result;
    }

    public function getOneList($uuid) {
        $id=$this->getIdByUUID($uuid);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->getOneMyAvailableList($id);
        foreach($list as $li) {
            $list=$li;
        }
        if(empty($list)) {
            $result=null;
        } else {
            $result=get_object_vars($list);
        }
        return $result;


    }

    public function editField($uuid,$post) {
        $prop_array = get_object_vars($post);
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list=  $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
            array('uuid' => $uuid)
        );
        $prop_array['key']=$this->russianToTranslit( $prop_array['value']);

        foreach ($prop_array as $key => $value) {
            $list->$key = $value;
        }
        $objectManager->persist($list);
        $objectManager->flush();
        return get_object_vars($list);
    }

    public function listParentAction($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $listParent=  $this->getOneList($uuid);
        $listChild=  $objectManager->getRepository('AddList\Entity\AddList')->findBy(
            array('parentFieldId' =>  new \MongoId($listParent['id']))
        );
        $result=array();
        foreach($listChild as $re)
        {
            array_push($result,get_object_vars($re));
        }
        return $result;
    }

    public function getListUuidById($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $list = $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(array('id' => new \MongoId($id)));
        return $list->uuid;
    }

}