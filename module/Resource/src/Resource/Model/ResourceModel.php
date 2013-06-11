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
use Resource\Entity\ResourceWay;

class ResourceModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $vehicleModel;
    protected $notificationModel;

    public function addResourceWay($propArraySplit,$ownerResourceId,$resId) {
        $result=array();
        foreach($propArraySplit as $key =>$value) {
            $elementSplit=explode('-',$key);
            if(!empty($elementSplit['1'])) {
                $result['elementSplit'.$elementSplit['1']][$elementSplit['0']]=$value;
                if(empty($result['elementSplit'.$elementSplit['1']]['ownerResourceId'])) {
                    $result['elementSplit'.$elementSplit['1']]['ownerResourceId']=$ownerResourceId;
                }
            } else {
                $result['elementSplit0'][$elementSplit['0']]=$value;
                if(empty($result['elementSplit0']['ownerResourceId'])) {
                    $result['elementSplit0']['ownerResourceId']=$ownerResourceId;
                }
            }
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($resId)) {
            $objectManager->createQueryBuilder('Resource\Entity\ResourceWay')
                ->remove()
                ->field('ownerResourceId')->equals(new \MongoId($resId))
                 ->getQuery()
                ->execute();
        }
            foreach($result as $res) {
                $resourceWay = new ResourceWay();
                foreach ($res as $key => $value) {
                    if($key!="ownerResourceId") {
                        $resourceWay->$key = $value;
                    } else {
                        $resourceWay->$key=new \MongoId($value);
                    }
                }
                $objectManager->persist($resourceWay);
                $objectManager->flush();
            }


    }

    public function addResource($post, $owner_id, $owner_org_id, $id)
    {

        if(!empty($post)) {
            if(is_array($post)) {
                $prop_array=$post;
            } else {
                $prop_array = get_object_vars($post);
            }


        }
        $prop_array_split=$prop_array;
        unset($prop_array_split['tsId']);
        unset($prop_array_split['kindOfLoad']);
        unset($prop_array_split['submit']);


        $prop_array_new['tsId']=$prop_array['tsId'];
        $prop_array_new['kindOfLoad']=$prop_array['kindOfLoad'];

        $prop_array=$prop_array_new;



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


        $this->addResourceWay($prop_array_split,$res->id,$this->getIdByUuid($id));

        $noteModel=$this->getNotificationModel();

        $noteModel->addNotification($res->id,$owner_id,$owner_org_id);

        return $res->uuid;
    }

    public function listResource($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $id));
        return get_object_vars($res);
    }

    public function listResourceById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('id' => new \MongoId($id)));
        if(!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }
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
        $vehicle = $this->getVehicleModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $veh=$vehicle->listVehicle($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org,'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }

    public function returnMyResource($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Resource\Entity\Resource')->getMyAvailableResource($owner_id);
        $rezs = array();
        $vehicle = $this->getVehicleModel();
        foreach ($rezObj as $cur) {
            $veh=$vehicle->listVehicle($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            array_push($rezs, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
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

    public function getVehicleModel()
    {
        if (!$this->vehicleModel) {
            $sm = $this->getServiceLocator();
            $this->vehicleModel = $sm->get('Resource\Model\VehicleModel');
        }
        return $this->vehicleModel;
    }

    public function returnAllWays($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\ResourceWay')->findBy(
            array('ownerResourceId' => new \MongoId($id))
        );
        $result=array();
        foreach($res as $re){
            array_push($result,get_object_vars($re));
        }
        return $result;
    }

    public function getIdByUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(
            array('uuid' => $uuid)
        );
        if(empty($res)) {
            return null;
        }
        return $res->id;
    }

    public function getNotificationModel()
    {
        if (!$this->notificationModel) {
            $sm = $this->getServiceLocator();
            $this->notificationModel = $sm->get('Notification\Model\NotificationModel');
        }
        return $this->notificationModel;
    }

}