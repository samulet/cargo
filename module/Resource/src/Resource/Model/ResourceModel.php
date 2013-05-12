<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Resource\Model;

use Resource\Entity\Resource;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class ResourceModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    public function addResource($post,$owner_id,$owner_org_id,$id) {
        $prop_array=get_object_vars($post);
        $prop_array['ownerId']=$owner_id;
        $prop_array['ownerOrgId']=$owner_org_id;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if(!empty($id)) $res=$objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $id));
        else $res = new Resource();
        foreach ($prop_array as $key => $value) {
            $res->$key=$value;
        }
        //$res->setName($post['name']);
        $objectManager->persist($res);
        $objectManager->flush();
        //die(var_dump($prop_array));
    }

    public function listResource($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res=$objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $id));
        return get_object_vars($res);
    }

    public function returnAllResource() {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Resource\Entity\Resource')->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs=array();
        $orgModel = $this->getOrganizationModel();
        foreach ($rezObj as $cur) {
            //$rez=array('uuid'=>$org_obj->getUUID(),'description'=>$org_obj->getDescription(), 'type'=>$org_obj->getType(),
          // die(var_dump($cur->id));
            $obj_vars=get_object_vars($cur);
            $org=$orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs,array('res' => $obj_vars, 'org'=>$org));
        }
        return $rezs;
    }

    public function returnMyResource($owner_id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Resource\Entity\Resource')->field('ownerId')->equals(new \MongoId($owner_id))->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs=array();
        foreach ($rezObj as $cur) {
            array_push($rezs,get_object_vars($cur));
        }
        return $rezs;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    public function getOrganizationModel()
    {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 'On');
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }
    public function deleteResource($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb4 = $objectManager->createQueryBuilder('Resource\Entity\Resource');
        $qb4->remove()->field('uuid')->equals($uuid) ->getQuery()
            ->execute();
    }


}