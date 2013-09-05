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
use Resource\Entity\ResourceWay;

class ResourceModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $vehicleModel;
    protected $notificationModel;
    protected $companyModel;
    protected $queryBuilderModel;

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
        if(!empty($prop_array_split['typeLoad'])) {
            unset($prop_array_split['typeLoad']);
            $prop_array_new['typeLoad']=$prop_array['typeLoad'];
        }

        unset($prop_array_split['submit']);


        $prop_array_new['tsId']=$prop_array['tsId'];


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

    public function returnResultsResource($post) {
        $propArray = get_object_vars($post);
        unset($propArray['submit']);

        $propArrayResult=array();
        foreach($propArray as $key => $value) {
            if(!empty($value)) {
                $propArrayResult[$key]=$value;
            }
        }
        $propArrayResultFullForm=array();
        if( !empty($propArrayResult['typeLoad']) ) {
            $propArrayResultFullForm['typeLoad']=$propArrayResult['typeLoad'];
            unset($propArrayResult['typeLoad']);
        }
        $orgModel = $this->getOrganizationModel();

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default') ;
        if(empty($propArrayResultFullForm)) {
            $rezObj = $objectManager->getRepository('Resource\Entity\ResourceWay')->findBy($propArrayResult);

            if(!empty($rezObj)) {
                $result = array();
                $cargo = $this->getVehicleModel();
                foreach ($rezObj as $cur) {

                    $cur=$objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('id' => new \MongoId($cur->ownerResourceId)));

                    $veh=$cargo->listVehicle($cur->tsId);
                    $ways=$this->returnAllWays($cur->id);
                    $org = $orgModel->getOrganization($cur->ownerOrgId);
                    array_push($result, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways,"org"=>$org));
                }
                return $result;
            } else {
                return null;
            }
        } else {

            $ticketFindObjects = $objectManager->getRepository('Resource\Entity\Resource')->findBy($propArrayResultFullForm);

            if(!empty($ticketFindObjects)) {

                $result = array();

                foreach($ticketFindObjects as $ticketFindObject) {

                    if(!empty($propArrayResult)) {
                        $propArrayResult['ownerResourceId']= new \MongoId($ticketFindObject->id);
                        // die(var_dump($propArrayResult));
                        $rezObj = $objectManager->getRepository('Resource\Entity\ResourceWay')->findBy($propArrayResult);
                        if(!empty($rezObj)) {
                            $cargo = $this->getVehicleModel();
                            foreach ($rezObj as $cur) {
                                $cur=$objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('id' => new \MongoId($cur->ownerResourceId)));
                                $veh=$cargo->listVehicle($cur->tsId);
                                $ways=$this->returnAllWays($cur->id);
                                $org = $orgModel->getOrganization($cur->ownerOrgId);
                                array_push($result, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways,"org"=>$org));
                            }

                        }
                    } else {
                        $cargo = $this->getVehicleModel();
                        $veh=$cargo->listVehicle($ticketFindObject->tsId);
                        $ways=$this->returnAllWays($ticketFindObject->id);
                        $org = $orgModel->getOrganization($ticketFindObject->ownerOrgId);
                        array_push($result, array('res'=>get_object_vars($ticketFindObject),'veh'=>$veh,'ways'=>$ways,"org"=>$org));
                    }

                }
                return $result;
            }
        }

    }

    public function listResource($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(array('uuid' => $id));
        if(empty($res)) {
            return null;
        } else {
            return get_object_vars($res);
        }

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

    public function returnResources($searchArray) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $resObj = $objectManager->createQueryBuilder('Resource\Entity\Resource');
        $queryBuilderModel=$this->getQueryBuilderModel();
        $resObj=$queryBuilderModel->createQuery($resObj, $searchArray)->getQuery()->execute();
        if(empty($resObj)) {
            return null;
        }
        $result = array();
        $comModel = $this->getCompanyModel();
        $vehicle = $this->getVehicleModel();
        foreach ($resObj as $cur) {
            $obj_vars = get_object_vars($cur);
            if(!empty($cur->tsId)) {
                $veh=$vehicle->listVehicle($cur->tsId);
            }  else {
                $veh=null;
            }
            $ways=$this->returnAllWays($cur->id);

            $com = $comModel->getCompany($obj_vars['ownerId']);
            array_push($result, array('res' => $obj_vars, 'owner' => $com,'veh'=>$veh,'ways'=>$ways));
        }
        return $result;
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
            if(!empty($cur->tsId)) {
                $veh=$vehicle->listVehicle($cur->tsId);
            }  else {
                $veh=null;
            }
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
            if(!empty($cur->tsId)) {
                $veh=$vehicle->listVehicle($cur->tsId);
            } else {
                $veh=null;
            }

            $ways=$this->returnAllWays($cur->id);
            array_push($rezs, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }
    public function returnMyAccResource($orgId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgId);
        $resultArray=array();
        foreach($com as $c) {
            $rezObj = $objectManager->getRepository('Resource\Entity\Resource')->getMyAvailableResource($c['id']);
            $vehicle = $this->getVehicleModel();
            foreach ($rezObj as $cur) {
                if(!empty($cur->tsId)) {
                    $veh=$vehicle->listVehicle($cur->tsId);
                } else {
                    $veh=null;
                }
                $ways=$this->returnAllWays($cur->id);
                array_push($resultArray, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways, 'owner'=>$c));
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

    public function getUuidById($id) {
        if(empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Resource\Entity\Resource')->findOneBy(
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
    public function addBootstrap3Class(&$form,&$formWay) {
        foreach ($formWay as $wayEl) {
            $attr=$wayEl->getAttributes();
            if(!empty($attr['type'])) {
                if(($attr['type']!='checkbox')) {
                    $wayEl->setAttributes(array( 'class' => 'form-control' ));
                }
            }
        }
        foreach ($form as $el) {
            $attr=$el->getAttributes();
            if(!empty($attr['type'])) {
                if(($attr['type']!='checkbox')&&($attr['type']!='multi_checkbox')) {
                    $el->setAttributes(array( 'class' => 'form-control' ));
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