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
use Doctrine\ODM\MongoDB\Id\UuidGenerator;


class VehicleModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $notificationModel;
    protected $companyModel;
    protected $queryBuilderModel;

    public function addVehicle($post, $owner_id, $owner_org_id, $id)
    {
        if (!empty($post)) {
            if (is_array($post)) {
                $prop_array = $post;
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
        $model = explode('-', $prop_array['model']);
        $prop_array['model'] = $model[2];
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }

        $error = 0;

        try {
            $objectManager->persist($res);
            $objectManager->flush();

        } catch (\Exception $e) {
            $error++;

        }

        try {
            $noteModel = $this->getNotificationModel();
            $noteModel->addNotification($res->id, $owner_id, $owner_org_id);

        } catch (\Exception $e) {

            $error++;
        }

        if (!empty($error)) {
            return null;
        } else {
            return $res->uuid;
        }
    }

    public function listVehicle($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $uuid_gen = new UuidGenerator();
        if (!empty($id)) {
            if ($uuid_gen->isValid($id)) {
                $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $id));
            } else {
                $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(
                    array('id' => new \MongoId($id))
                );
            }

        } else {
            $res = null;
        }
        if (!empty($res)) {
            return get_object_vars($res);
        }
        return null;
    }

    public function returnVehicles($searchArray)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $vehObj = $objectManager->createQueryBuilder('Resource\Entity\Vehicle');
        $queryBuilderModel=$this->getQueryBuilderModel();
        $vehObj=$queryBuilderModel->createQuery($vehObj, $searchArray)->getQuery()->execute();
        $result = array();
        $comModel = $this->getCompanyModel();

        foreach ($vehObj as $vehicle) {
            $com = $comModel->getCompany($vehicle->ownerId);
            array_push($result, array('res' => get_object_vars($vehicle), 'owner' => $com));
        }
        return $result;
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
            ->getMyAvailableVehicle($owner_id);
        $rezs = array();
        foreach ($rezObj as $cur) {
            array_push($rezs, get_object_vars($cur));
        }
        return $rezs;
    }

    public function returnMyAccVehicle($orgId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgId);
        $resultArray = array();
        foreach($com as $c) {
            $rezObj = $objectManager->getRepository('Resource\Entity\Vehicle')
                ->getMyAvailableVehicle($c['id']);
            foreach ($rezObj as $cur) {
                $resultVars=get_object_vars($cur);
                $resultVars['vehicleOwnerTrue']=$c;
                array_push($resultArray,$resultVars );
            }
        }
        return $resultArray;
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

    public function copyVehicle($uuid)
    {
        $res = $this->listVehicle($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addVehicle($res, $res['ownerId'], $res['ownerOrgId'], null);
    }

    public function getIdByUuid($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $vehicle = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $uuid));
        if (!empty($vehicle)) {
            return $vehicle->id;
        } else {
            return null;
        }


    }

    public function getUuidById($id)
    {
        if (empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(
            array('id' => new \MongoId($id))
        );
        if (empty($res)) {
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

    public function addBootstrap3Class(&$form)
    {

        foreach ($form as $el) {
            $attr = $el->getAttributes();
            if (!empty($attr['type'])) {
                if (($attr['type'] != 'checkbox') && ($attr['type'] != 'multi_checkbox')) {
                    $el->setAttributes(array('class' => 'form-control'));
                }
            }

        }
    }
    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }
    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }
}
