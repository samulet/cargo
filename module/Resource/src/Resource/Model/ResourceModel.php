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

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
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

    public function addResource($post, $owner_id, $owner_org_id, $id)
    {
        if(!empty($post)) {
            if(is_array($post)) {
                $prop_array=$post;
            } else {
                $prop_array = get_object_vars($post);
            }

        }
        $prop_array['ownerId'] = $owner_id;
        $prop_array['ownerOrgId'] = $owner_org_id;
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($id)) {
            $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $res = new Resource();
        }
        foreach ($prop_array as $key => $value) {
            if($key!="tsId") {
                $res->$key = $value;
            } else {
                $res->$key=new \MongoId($value);
            }
        }
        $objectManager->persist($res);
        $objectManager->flush();
        return $res->uuid;
    }

    public function listResource($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $id));
        return get_object_vars($res);
    }

    public function returnAllResource()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Resource\Entity\Resource')->getAllAvailableResource();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        if(empty($rezObj)) {
            return null;
        }
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org));
        }
        return $rezs;
    }

    public function returnMyResource($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Resource\Entity\Resource')->getMyAvailableResource($owner_id);
        $rezs = array();
        foreach ($rezObj as $cur) {
            array_push($rezs, get_object_vars($cur));
        }
        return $rezs;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getOrganizationModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Organization\Model\OrganizationModel');
        }
        return $this->organizationModel;
    }

    public function deleteResource($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $recourse = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $uuid));
        if (!$recourse) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Resource', $uuid);
        }
        $objectManager->remove($recourse);
        $objectManager->flush();
    }
    public function copyResource($uuid) {
        $res=$this->listResource($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addResource($res,$res['ownerId'],$res['ownerOrgId'],null);
    }



}