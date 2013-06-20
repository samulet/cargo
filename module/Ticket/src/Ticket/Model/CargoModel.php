<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/15/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Ticket\Model;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Ticket\Entity\Cargo;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Types\Type;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;

class CargoModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;


    public function addCargo($post, $owner_id, $owner_org_id, $id)
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
            $res = $objectManager->getRepository('Ticket\Entity\Cargo')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $res = new Cargo();
        }
        $model=explode('-',$prop_array['model']);
        $prop_array['model']=$model[2];
        foreach ($prop_array as $key => $value) {
            $res->$key = $value;
        }
        $objectManager->persist($res);
        $objectManager->flush();
        return $res->uuid;
    }

    public function listCargo($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $uuid_gen = new UuidGenerator();
        if ($uuid_gen->isValid($id)) {
            $res = $objectManager->getRepository('Ticket\Entity\Cargo')->findOneBy(array('uuid' => $id));
        } else {
            $res = $objectManager->getRepository('Ticket\Entity\Cargo')->find(new \MongoId($id));
        }

        if(!empty($res)) {
            return get_object_vars($res);
        }
        return null;
    }

    public function returnAllCargo()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $cargosCollection = $objectManager->getRepository('Ticket\Entity\Cargo')->getAllAvailableCargo();

        $result = array();
        $organizationModel = $this->getOrganizationModel();
        foreach ($cargosCollection as $cargo) {
            /** @var \Ticket\Entity\Cargo $cargo */
            $organization = $organizationModel->getOrganization($cargo->getOwnerOrgId());
            array_push($result, array('res' => get_object_vars($cargo), 'org' => $organization));
        }
        return $result;
    }

    public function returnMyCargo($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Cargo')
            -> getMyAvailableCargo($owner_id);
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

    public function deleteCargo($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $cargo = $objectManager->getRepository('Ticket\Entity\Cargo')->findOneBy(array('uuid' => $uuid));
        if (!$cargo) {
            throw DocumentNotFoundException::documentNotFound('Ticket\Entity\Cargo', $uuid);
        }
        $objectManager->remove($cargo);
        $objectManager->flush();
    }

    public function copyCargo($uuid) {
        $res=$this->listCargo($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addCargo($res,$res['ownerId'],$res['ownerOrgId'],null);
    }

    public function getIdByUuid($uuid) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $cargo = $objectManager->getRepository('Ticket\Entity\Cargo')->findOneBy(array('uuid' => $uuid));
        return $cargo->id;
    }

}
