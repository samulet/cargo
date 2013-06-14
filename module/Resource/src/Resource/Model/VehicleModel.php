<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/15/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Resource\Model;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Resource\Entity\Vehicle;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

class VehicleModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $notificationModel;

    public function addVehicle($post, $owner_id, $owner_org_id, $id)
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
            $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $res = new Vehicle();
        }
        $model=explode('-',$prop_array['model']);
        $prop_array['model']=$model[2];
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();

        $noteModel=$this->getNotificationModel();

        $noteModel->addNotification($res->id,$owner_id,$owner_org_id);

        return $res->uuid;
    }

    public function listVehicle($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $uuid_gen = new UuidGenerator();
        if ($uuid_gen->isValid($id)) {
            $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $id));
        } else {
            $res = $objectManager->getRepository('Resource\Entity\Vehicle')->find(new \MongoId($id));
        }

        if(!empty($res)) {
            return get_object_vars($res);
        }
        return null;
    }

    public function returnAllVehicle()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $vehiclesCollection = $objectManager->getRepository('Resource\Entity\Vehicle')->getAllAvailableVehicle();

        $result = array();
        $organizationModel = $this->getOrganizationModel();
        foreach ($vehiclesCollection as $vehicle) {
            /** @var \Resource\Entity\Vehicle $vehicle */
            $organization = $organizationModel->getOrganization($vehicle->getOwnerOrgId());
            array_push($result, array('res' => get_object_vars($vehicle), 'org' => $organization));
        }
        return $result;
    }

    public function returnMyVehicle($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Resource\Entity\Vehicle')
           -> getMyAvailableVehicle($owner_id);
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

    public function deleteVehicle($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $vehicle = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $uuid));
        if (!$vehicle) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $uuid);
        }
        $objectManager->remove($vehicle);
        $objectManager->flush();
    }

    public function copyVehicle($uuid) {
        $res=$this->listVehicle($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addVehicle($res,$res['ownerId'],$res['ownerOrgId'],null);
    }

    public function getIdByUuid($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $vehicle = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $uuid));
        return $vehicle->id;
    }
    public function getUuidById($id) {
        if(empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(
            array('id' => new \MongoId($id))
        );
        if(empty($res)) {
            return null;
        }
        return $res->uuid;
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
