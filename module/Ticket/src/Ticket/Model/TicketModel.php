<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Ticket\Model;

use Ticket\Entity\Ticket;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;

use Ticket\Entity\TicketWay;
use Ticket\Entity\DocumentWay;

class TicketModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $organizationModel;
    protected $cargoModel;
    protected $notificationModel;
    protected $companyUserModel;
    protected $companyModel;

    public function unSplitArray($propArraySplit) {
        $result=array();
        foreach($propArraySplit as $key =>$value) {
            $elementSplit=explode('-',$key);
            $elementDocSplit=explode('_',$elementSplit[0]);
            if(!empty($elementSplit['1'])) {
                if(isset($elementDocSplit[1])) {

                    $result['elementSplit'.$elementSplit['1']]['doc'][$elementDocSplit['1']][$elementDocSplit['0']]=$value;
                } else {
                    $result['elementSplit'.$elementSplit['1']][$elementSplit['0']]=$value;

                }

            } else {

                if(isset($elementDocSplit[1])) {
                    $result['elementSplit0']['doc'][$elementDocSplit['1']][$elementDocSplit['0']]=$value;
                } else {

                    $result['elementSplit0'][$elementSplit['0']]=$value;
                }
            }
        }
        return $result;
    }

    public function addDocumentWay($docArray,$ownerTicketWayId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        foreach($docArray as $doc) {
            $documentWay = new DocumentWay();
            $doc['ownerTicketWayId']=new \MongoId($ownerTicketWayId);;
            foreach ($doc as $key => $value) {
                $documentWay->$key = $value;
            }
            $objectManager->persist($documentWay);
            $objectManager->flush();

        }
    }

    public function getDocumentWay($ownerTicketWayId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $docs = $objectManager->getRepository('Ticket\Entity\DocumentWay')->findBy(
            array('ownerTicketWayId' => new \MongoId($ownerTicketWayId))
        );
        $result=array();
        foreach($docs as $doc){
            array_push($result,get_object_vars($doc));
        }
        return $result;
    }

    public function addTicketWay($propArraySplit,$ownerTicketId,$resId) {
        $result=array();

        foreach($propArraySplit as $key =>$value) {
            $elementSplit=explode('-',$key);
            $elementDocSplit=explode('_',$elementSplit[0]);
            if(!empty($elementSplit['1'])) {
                if(isset($elementDocSplit[1])) {
                    $result['elementSplit'.$elementSplit['1']]['doc'][$elementDocSplit['1']][$elementDocSplit['0']]=$value;
                } else {
                    $result['elementSplit'.$elementSplit['1']][$elementSplit['0']]=$value;
                }
                if(empty($result['elementSplit'.$elementSplit['1']]['ownerTicketId'])) {
                    $result['elementSplit'.$elementSplit['1']]['ownerTicketId']=$ownerTicketId;
                }
            } else {
                if(isset($elementDocSplit[1])) {
                    $result['elementSplit0']['doc'][$elementDocSplit['1']][$elementDocSplit['0']]=$value;
                } else {
                    $result['elementSplit0'][$elementSplit['0']]=$value;
                }
                if(empty($result['elementSplit0']['ownerTicketId'])) {
                    $result['elementSplit0']['ownerTicketId']=$ownerTicketId;
                }
            }
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($resId)) {
            $deleteWays=$objectManager->createQueryBuilder('Ticket\Entity\TicketWay')
                ->field('ownerTicketId')->equals(new \MongoId($resId))
                ->getQuery()
                ->execute();
            foreach($deleteWays as $deleteWay) {
                $objectManager->createQueryBuilder('Ticket\Entity\DocumentWay')
                    ->remove()
                    ->field('ownerTicketWayId')->equals(new \MongoId($deleteWay->id))
                    ->getQuery()
                    ->execute();
            }
            $objectManager->createQueryBuilder('Ticket\Entity\TicketWay')
                ->remove()
                ->field('ownerTicketId')->equals(new \MongoId($resId))
                ->getQuery()
                ->execute();
        }
        foreach($result as $res) {
            $ticketWay = new TicketWay();
            $documentWay=$res['doc'];
            unset($res['doc']);
            foreach ($res as $key => $value) {
                if($key!="ownerTicketId") {
                    $ticketWay->$key = $value;
                } else {
                    $ticketWay->$key=new \MongoId($value);
                }
            }
            $objectManager->persist($ticketWay);
            $objectManager->flush();
            $this->addDocumentWay($documentWay,$ticketWay->id);
        }


    }

    public function addTicket($post, $owner_id, $owner_org_id, $id)
    {

        if(!empty($post)) {
            if(is_array($post)) {
                $prop_array=$post;
            } else {
                $prop_array = get_object_vars($post);
            }
        }
        $prop_array_split=$prop_array;


        unset($prop_array_split['submit']);


  //      $prop_array_new['tsId']=$prop_array['tsId'];
   //     $prop_array_new['kindOfLoad']=$prop_array['kindOfLoad'];
        $prop_array_new=array();
        $prop_array=$prop_array_new;



        $prop_array['ownerId'] = $owner_id;
        $prop_array['ownerOrgId'] = $owner_org_id;
        $prop_array['currency']=$prop_array_split['currency'];
        $prop_array['money']=$prop_array_split['money'];
        $prop_array['typeTicket']=$prop_array_split['typeTicket'];
        $prop_array['formPay']=$prop_array_split['formPay'];

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($id)) {
            $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $res = new Ticket();
        }
        foreach ($prop_array as $key => $value) {
            if(($key=='ownerId')||($key=='ownerOrgId')) {
                $res->$key=new \MongoId($value);
            } else{
                $res->$key=$value;
            }

        }
        $objectManager->persist($res);
        $objectManager->flush();

        $this->addTicketWay($prop_array_split,$res->id,$this->getIdByUuid($id));

        $noteModel=$this->getNotificationModel();

        $noteModel->addNotification($res->id,$owner_id,$owner_org_id);
        return $res->uuid;
    }

    public function listTicket($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $id));
        if(!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }

    }

    public function listTicketById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($id)));
        if(!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }
    }

    public function returnAllTicket()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getAllAvailableTicket();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        if(empty($rezObj)) {
            return null;
        }
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $veh=$cargo->listCargo($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org,'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }

    public function returnMyTicket($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getMyAvailableTicket($owner_id);
        $rezs = array();
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $veh=$cargo->listCargo($cur->tsId);
            $ways=$this->returnAllWays($cur->id);
            array_push($rezs, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
        }
        return $rezs;
    }

    public function returnSearchTicket($post) {
        $propArray = get_object_vars($post);
        unset($propArray['submit']);

        $propArrayResult=array();
        foreach($propArray as $key => $value) {
            if(!empty($value)) {
                $propArrayResult[$key]=$value;
            }
        }
        $propArrayResultFullForm=array();
        if( !empty($propArrayResult['currency']) ) {
            $propArrayResultFullForm['currency']=$propArrayResult['currency'];
            unset($propArrayResult['currency']);
        }
        if( !empty($propArrayResult['money']) ) {
            $propArrayResultFullForm['money']=$propArrayResult['money'];
            unset($propArrayResult['money']);
        }
        if( !empty($propArrayResult['formPay']) ) {
            $propArrayResultFullForm['formPay']=$propArrayResult['formPay'];
            unset($propArrayResult['formPay']);
        }
        if( !empty($propArrayResult['typeTicket']) ) {
            $propArrayResultFullForm['typeTicket']=$propArrayResult['typeTicket'];
            unset($propArrayResult['typeTicket']);
        }


        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default') ;
        if(empty($propArrayResultFullForm)) {
            $rezObj = $objectManager->getRepository('Ticket\Entity\TicketWay')->findBy($propArrayResult);

            if(!empty($rezObj)) {
                $result = array();
                $cargo = $this->getCargoModel();
                foreach ($rezObj as $cur) {

                    $cur=$objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($cur->ownerTicketId)));

                    $veh=$cargo->listCargo($cur->tsId);
                    $ways=$this->returnAllWays($cur->id);
                    array_push($result, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
                }
                return $result;
            } else {
                return null;
            }
        } else {

            $ticketFindObjects = $objectManager->getRepository('Ticket\Entity\Ticket')->findBy($propArrayResultFullForm);

            if(!empty($ticketFindObjects)) {
                $result = array();

                foreach($ticketFindObjects as $ticketFindObject) {
                    if(!empty($propArrayResult)) {
                        $propArrayResult['ownerTicketId']= new \MongoId($ticketFindObject->id);
                       // die(var_dump($propArrayResult));
                        $rezObj = $objectManager->getRepository('Ticket\Entity\TicketWay')->findBy($propArrayResult);
                        if(!empty($rezObj)) {
                            $cargo = $this->getCargoModel();
                            foreach ($rezObj as $cur) {
                                $cur=$objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($cur->ownerTicketId)));
                                $veh=$cargo->listCargo($cur->tsId);
                                $ways=$this->returnAllWays($cur->id);
                                array_push($result, array('res'=>get_object_vars($cur),'veh'=>$veh,'ways'=>$ways));
                            }

                        }
                    } else {
                        $cargo = $this->getCargoModel();
                        $veh=$cargo->listCargo($ticketFindObject->tsId);
                        $ways=$this->returnAllWays($ticketFindObject->id);
                        array_push($result, array('res'=>get_object_vars($ticketFindObject),'veh'=>$veh,'ways'=>$ways));
                    }

                }
                return $result;
            }
        }


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

    public function deleteTicket($uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $recourse = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $uuid));
        if (!$recourse) {
            throw DocumentNotFoundException::documentNotFound('Ticket\Entity\Ticket', $uuid);
        }
        $objectManager->remove($recourse);
        $objectManager->flush();
    }
    public function copyTicket($uuid) {
        $res=$this->listTicket($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addTicket($res,$res['ownerId'],$res['ownerOrgId'],null);
    }

    public function getCargoModel()
    {
        if (!$this->cargoModel) {
            $sm = $this->getServiceLocator();
            $this->cargoModel = $sm->get('Ticket\Model\CargoModel');
        }
        return $this->cargoModel;
    }

    public function returnAllWays($id) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\TicketWay')->findBy(
            array('ownerTicketId' => new \MongoId($id))
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
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
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
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
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
    public function getCargoOwnerData($userId) {
        $comUserModel=$this->getCompanyUserModel();
        $orgId=$comUserModel->getOrgIdByUserId($userId);

        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgId);
        if(!empty($com)) {
            $result=array();
            foreach($com as $c) {
                $result=$result+array( $c['id'] => $c['name']);
            }
            return $result;
        } else {
            return null;
        }
    }
    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Organization\Model\CompanyModel');
        }
        return $this->companyModel;
    }
}