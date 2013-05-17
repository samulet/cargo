<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/15/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Resource\Model;

use Resource\Entity\Vehicle;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;

class VehicleModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;

    public function addVehicle($post, $owner_id, $owner_org_id, $id)
    {
        if(!empty($post)) {
            $prop_array = get_object_vars($post);
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
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();
    }

    public function listVehicle($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Vehicle')->findOneBy(array('uuid' => $id));
        if(!empty($res)) {
            return get_object_vars($res);
        }
        return null;
    }

    public function returnAllVehicle()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Resource\Entity\Vehicle')->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org));
        }
        return $rezs;
    }

    public function returnMyVehicle($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qb = $objectManager->createQueryBuilder('Resource\Entity\Vehicle')->field('ownerId')->equals(
            new \MongoId($owner_id)
        )->eagerCursor(true);
        $query = $qb->getQuery();
        $rezObj = $query->execute();
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
        $qb4 = $objectManager->createQueryBuilder('Resource\Entity\Vehicle');
        $qb4->remove()->field('uuid')->equals($uuid)->getQuery()
            ->execute();
    }


}