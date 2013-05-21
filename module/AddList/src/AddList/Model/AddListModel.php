<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/20/13
 * Time: 2:31 AM
 * To change this template use File | Settings | File Templates.
 */

namespace AddList\Model;

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

    public function returnDataArray($arrFields,$prefix) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $result=array();
        foreach($arrFields as $arrField)
        {
            $elName=$prefix.'-'.$arrField;
            $res = $objectManager->getRepository('AddList\Entity\AddList')->getMyAvailableList($elName);
            if(!empty($res))
            {
                $results_list=array();
                foreach($res as $re) {
                    $obj_vars = get_object_vars($re);
                    array_push($results_list, array('key'=>$obj_vars['key'],'value'=>$obj_vars['value']));
                }
                $result=$result+array($arrField=>$results_list);
            }
        }
        return $result;
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

    public function addList($post,$list_uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if(empty($list_uuid)) {
            $res = new AddList();
        } else {
            $res=  $objectManager->getRepository('AddList\Entity\AddList')->findOneBy(
                array('uuid' => $list_uuid)
            );
        }
        $prop_array = get_object_vars($post);
        $prop_array['key']=$this->russianToTranslit( $prop_array['value']);
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
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

    public function getList($post) {
        $post=get_object_vars($post);
        if(empty($post))
        {
            return null;
        }
        $listName=$post['listName'];

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('AddList\Entity\AddList')->getMyAvailableList($listName);
        $result=array();
        foreach($res as $re)
        {
            array_push($result,get_object_vars($re));
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
}