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
    protected $queryBuilderModel;
    protected $interactionModel;

    public function unSplitArray($propArraySplit)
    {
        $result = array();
        foreach ($propArraySplit as $key => $value) {
            $elementSplit = explode('-', $key);
            $elementDocSplit = explode('_', $elementSplit[0]);
            if (!empty($elementSplit['1'])) {
                if (isset($elementDocSplit[1])) {

                    $result['elementSplit' . $elementSplit['1']]['doc'][$elementDocSplit['1']][$elementDocSplit['0']] = $value;
                } else {
                    $result['elementSplit' . $elementSplit['1']][$elementSplit['0']] = $value;

                }

            } else {

                if (isset($elementDocSplit[1])) {
                    $result['elementSplit0']['doc'][$elementDocSplit['1']][$elementDocSplit['0']] = $value;
                } else {

                    $result['elementSplit0'][$elementSplit['0']] = $value;
                }
            }
        }
        return $result;
    }

    public function addDocumentWay($docArray, $ownerTicketWayId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        foreach ($docArray as $doc) {
            $documentWay = new DocumentWay();
            $doc['ownerTicketWayId'] = new \MongoId($ownerTicketWayId);;
            foreach ($doc as $key => $value) {
                $documentWay->$key = $value;
            }
            $objectManager->persist($documentWay);
            $objectManager->flush();

        }
    }

    public function getDocumentWay($ownerTicketWayId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $docs = $objectManager->getRepository('Ticket\Entity\DocumentWay')->findBy(
            array('ownerTicketWayId' => new \MongoId($ownerTicketWayId))
        );
        $result = array();
        foreach ($docs as $doc) {
            array_push($result, get_object_vars($doc));
        }
        return $result;
    }

    public function addTicketWay($propArraySplit, $ownerTicketId, $resId)
    {
        $result = array();

        foreach ($propArraySplit as $key => $value) {
            $elementSplit = explode('-', $key);
            $elementDocSplit = explode('_', $elementSplit[0]);
            if (!empty($elementSplit['1'])) {
                if (isset($elementDocSplit[1])) {
                    $result['elementSplit' . $elementSplit['1']]['doc'][$elementDocSplit['1']][$elementDocSplit['0']] = $value;
                } else {
                    $result['elementSplit' . $elementSplit['1']][$elementSplit['0']] = $value;
                }
                if (empty($result['elementSplit' . $elementSplit['1']]['ownerTicketId'])) {
                    $result['elementSplit' . $elementSplit['1']]['ownerTicketId'] = $ownerTicketId;
                }
            } else {
                if (isset($elementDocSplit[1])) {
                    $result['elementSplit0']['doc'][$elementDocSplit['1']][$elementDocSplit['0']] = $value;
                } else {
                    $result['elementSplit0'][$elementSplit['0']] = $value;
                }
                if (empty($result['elementSplit0']['ownerTicketId'])) {
                    $result['elementSplit0']['ownerTicketId'] = $ownerTicketId;
                }
            }
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($resId)) {
            $deleteWays = $objectManager->createQueryBuilder('Ticket\Entity\TicketWay')
                ->field('ownerTicketId')->equals(new \MongoId($resId))
                ->getQuery()
                ->execute();
            foreach ($deleteWays as $deleteWay) {
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
        foreach ($result as $res) {
            $ticketWay = new TicketWay();
            $documentWay = $res['doc'];
            unset($res['doc']);
            if ($res['setLoadType'] == "prepareToLoad") {
                unset($res['dateStart']);
                unset($res['dateStartPlus']);
                unset($res['always']);
            } elseif ($res['setLoadType'] == "dateStart") {
                unset($res['prepareToLoad']);
                unset($res['always']);
            } elseif ($res['setLoadType'] == "always") {
                unset($res['dateStart']);
                unset($res['dateStartPlus']);
                unset($res['prepareToLoad']);
            }



            foreach ($res as $key => $value) {
                if(!empty($value)) {
                    if ($key != "ownerTicketId") {
                        $ticketWay->$key = $value;
                    } else {
                        $ticketWay->$key = new \MongoId($value);
                    }
                }

            }
            $objectManager->persist($ticketWay);
            $objectManager->flush();
            $this->addDocumentWay($documentWay, $ticketWay->id);
        }


    }

    public function addTicket($post, $owner_id, $owner_org_id, $id)
    {
        $orgModel = $this->getOrganizationModel();

        if (!empty($post)) {
            if (is_array($post)) {
                $prop_array = $post;
            } else {
                $prop_array = get_object_vars($post);
            }
        }
        $prop_array_split = $prop_array;


        unset($prop_array_split['submit']);
        $prop_array_new = array();
        $prop_array = $prop_array_new;


        $prop_array['ownerId'] = $owner_id;
        $prop_array['ownerOrgId'] = $owner_org_id;
        $prop_array['currency'] = $prop_array_split['currency'];
        $prop_array['money'] = $prop_array_split['money'];
        $prop_array['typeTicket'] = $prop_array_split['typeTicket'];
        $prop_array['formPay'] = $prop_array_split['formPay'];
        $prop_array['type'] = $prop_array_split['type'];
        $prop_array['rate'] = $prop_array_split['rate'];
        if (!empty($prop_array_split['includeNds'])) {
            $prop_array['includeNds'] = $prop_array_split['includeNds'];
        }


        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if (!empty($id)) {
            $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
                array('uuid' => $id)
            );
        } else {
            $lastItemNumber = $orgModel->getOrganization($owner_org_id)['lastItemNumber'];
            $lastItemNumber++;
            $prop_array['numberInt'] = $lastItemNumber;
            $orgModel->increaseLastItemNumber($owner_org_id, $lastItemNumber);
            $res = new Ticket();
        }
        foreach ($prop_array as $key => $value) {
            if (($key == 'ownerId') || ($key == 'ownerOrgId')) {
                $res->$key = new \MongoId($value);
            } else {
                $res->$key = $value;
            }

        }
        $objectManager->persist($res);
        $objectManager->flush();

        $this->addTicketWay($prop_array_split, $res->id, $this->getIdByUuid($id));

        $noteModel = $this->getNotificationModel();

        $noteModel->addNotification($res->id, $owner_id, $owner_org_id);
        return $res->uuid;
    }

    public function listTicket($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('uuid' => $id));
        if (!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }

    }

    public function listTicketById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(array('id' => new \MongoId($id)));
        if (!empty($res)) {
            return get_object_vars($res);
        } else {
            return null;
        }
    }


    public function returnTickets($params) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $ticket = $objectManager->createQueryBuilder('Ticket\Entity\Ticket');
        $queryBuilderModel=$this->getQueryBuilderModel();
        $rezObj = $queryBuilderModel->createQuery($ticket, $params)->getQuery()->execute();

        $result = array();
        $comModel = $this->getCompanyModel();
        $noteModel = $this->getNotificationModel();
        $intModel = $this->getInteractionModel();
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {

            $veh = $cargo->listCargo($cur->tsId);
            $ways = $this->returnAllWays($cur->id);
            $resultArray=get_object_vars($cur);
            $resultArray['created']=$resultArray['created']->format('d-m-Y');
            $resultArray['statusGlobal']=$noteModel->getItemStatus($cur->id);
            $resultArray['statusWork']=$intModel->getItemStatus($cur->id);
            array_push(
                $result,
                array(
                    'res' => $resultArray,
                    'veh' => $veh,
                    'ways' => $ways,
                    'owner' => $comModel->getCompany($cur->ownerId)
                )
            );
        }
        return $result;
    }
    public function returnAllTicket()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getAllAvailableTicket();
        $rezs = array();
        $orgModel = $this->getOrganizationModel();
        if (empty($rezObj)) {
            return null;
        }
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $obj_vars = get_object_vars($cur);
            $veh = $cargo->listCargo($cur->tsId);
            $ways = $this->returnAllWays($cur->id);
            $org = $orgModel->getOrganization($obj_vars['ownerOrgId']);
            array_push($rezs, array('res' => $obj_vars, 'org' => $org, 'veh' => $veh, 'ways' => $ways));
        }
        return $rezs;
    }

    public function returnMyTicket($owner_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->getMyAvailableTicket($owner_id);
        $rezs = array();
        $comModel = $this->getCompanyModel();
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $veh = $cargo->listCargo($cur->tsId);
            $ways = $this->returnAllWays($cur->id);
            $resultArray=get_object_vars($cur);
            $resultArray['created']=$resultArray['created']->format('d-m-Y');
            array_push(
                $rezs,
                array(
                    'res' => $resultArray,
                    'veh' => $veh,
                    'ways' => $ways,
                    'owner' => $comModel->getCompany($owner_id)
                )
            );
        }
        return $rezs;
    }

    public function returnMyTicketById($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $rezObj = $objectManager->getRepository('Ticket\Entity\Ticket')->findBy(array('id' => new \MongoId($id)));
        $rezs = array();
        $comModel = $this->getCompanyModel();
        $cargo = $this->getCargoModel();
        foreach ($rezObj as $cur) {
            $veh = $cargo->listCargo($cur->tsId);
            $ways = $this->returnAllWays($cur->id);
            $resultArray=get_object_vars($cur);
            $resultArray['created']=$resultArray['created']->format('d-m-Y');
            array_push(
                $rezs,
                array(
                    'res' => $resultArray,
                    'veh' => $veh,
                    'ways' => $ways,
                    'owner' => $comModel->getCompany($cur->ownerId)
                )
            );
        }
        return $rezs;
    }

    public function returnMyAccTicket($orgId)
    {
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgId);
        $resultArray = array();
        foreach ($com as $c) {
            $resultArray=array_merge($resultArray, $this->returnTickets(array('ownerId'=> new \MongoId($c['id']) )));
        }
        return $resultArray;
    }

    public function searchItemAct($qb, $searchArray)
    {
        foreach ($searchArray as $key => $value) {
            $qb->field($key)->equals($value);
        }
        return $qb;
    }

    public function searchTicketWay($rezObj)
    {
        if (!empty($rezObj)) {
            $result = array();
            foreach ($rezObj as $cur) {
               $result=array_merge($result,$this->returnMyTicketById($cur->ownerTicketId));
            }
            foreach($result as &$el) {
                $el=serialize($el);
            }
            $resultArray=array_unique($result);
            foreach($resultArray as &$el) {
                $el=unserialize($el);
            }
            return $resultArray;
        } else {
            return array();
        }
    }

    public function searchTicket($ticketFindObjects)
    {
        $result = array();
        if (!empty($ticketFindObjects)) {
            foreach($ticketFindObjects as $cur) {
                $result=array_merge($result,$this->returnMyTicketById($cur->id));
            }
        }
        return $result;
    }

    public function sortTicketWayAct($qb, $sortArray)
    {

    }

    public function rangeSearch($qb,$propFilterResult) {
        if(!empty($propFilterResult)) {
            foreach($propFilterResult as $key =>$range) {
                if(!empty($range['from'])) {
                    if( (substr($key , 0, 4)=='date') || ($key=='created') ) {
                        $range['from']=new \DateTime($range['from']);
                    }
                }
                if(!empty($range['to'])) {
                    if( (substr($key , 0, 4)=='date') || ($key=='created') ) {
                        $range['to']=new \DateTime($range['to']);
                    }
                }
                if( (!empty($range['from']))&&(!empty($range['to'])) ) {
                    $qb->field($key)->gte($range['from'])->lte($range['to']);
                } elseif (!empty($range['from'])) {
                    $qb->field($key)->gte($range['from']);
                } elseif (!empty($range['to'])) {
                    $qb->field($key)->lte($range['to']);
                }
            }
        }
        return $qb;

    }

    public function returnSearchTicket($post)
    {
        $propArray = get_object_vars($post);
        unset($propArray['submit']);

        $propArrayTicketWay = array();
        $propFilterResult = array();
        foreach ($propArray as $key => $value) {
            if (!empty($value)) {

                    $subStrFrom=substr($key , strlen($key)-10, 10);
                    $subStrTo=substr($key , strlen($key)-8, 8);
                    if($subStrFrom=='FilterFrom') {
                        $propFilterResult[substr($key , 0, strlen($key)-10)]['from']=$value;
                    } elseif($subStrTo=='FilterTo') {
                        $propFilterResult[substr($key , 0, strlen($key)-8)]['to']=$value;
                    } else {
                        $propArrayTicketWay[$key] = $value;
                    }

            }
        }
        $propFilterResultTicket=array();



        //$propArrayTicket = array('activated' => '1');
        $propArrayTicket = array();
        $unsetTicketArray = array('currency', 'money', 'formPay', 'typeTicket', 'ownerId', 'type', 'rate');
        foreach ($unsetTicketArray as $unsetTicketString) {
            if (!empty($propArrayTicketWay[$unsetTicketString])) {
                if ($unsetTicketString != 'ownerId') {
                    $propArrayTicket[$unsetTicketString] = $propArrayTicketWay[$unsetTicketString];
                } else {
                    $propArrayTicket[$unsetTicketString] = new \MongoId($propArrayTicketWay[$unsetTicketString]);
                }
                unset($propArrayTicketWay[$unsetTicketString]);
            }
        }

        $propAcceptedResource=array();

        if(!empty($propArrayTicketWay['accepted'])) {
            $propAcceptedResource['accepted']=$propArrayTicketWay['created'];
            unset($propArrayTicketWay['created']);
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $qTicket = $objectManager->createQueryBuilder('Ticket\Entity\Ticket');
        $qTicketWay = $objectManager->createQueryBuilder('Ticket\Entity\TicketWay');



        $qTicketWay=$this->searchItemAct($qTicketWay, $propArrayTicketWay);
        $qTicketWay = $this->rangeSearch($qTicketWay,$propFilterResult);

        $qTicket=$this->searchItemAct($qTicket, $propArrayTicket);
        $qTicket= $this->rangeSearch($qTicket,$propFilterResultTicket);

        $resultTicketFromWay = $this->searchTicketWay($qTicketWay->getQuery()->execute());
        $resultTicketFromTicket=$this->searchTicket($qTicket->getQuery()->execute());

        return $this->arrayIntersect($resultTicketFromWay, $resultTicketFromTicket);
    }

    public function arrayIntersect($arr1, $arr2) {
        foreach($arr1 as &$el) {
            $el=serialize($el);
        }
        foreach($arr2 as &$el) {
            $el=serialize($el);
        }
        $resultArray=array_intersect($arr1,$arr2);
        foreach($resultArray as &$el) {
            $el=unserialize($el);
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

    public function copyTicket($uuid)
    {
        $res = $this->listTicket($uuid);
        unset($res['created']);
        unset($res['updated']);
        unset($res['id']);
        unset($res['uuid']);
        return $this->addTicket($res, $res['ownerId'], $res['ownerOrgId'], null);
    }

    public function multiFieldProc($multiField) {
        $multiFieldArray=array();
        foreach($multiField as $field) {
            $explodedField=explode('_',$field);
            if($explodedField[0]!='multiField') {
                $multiFieldArray[$explodedField[1]][$explodedField[0]]=true;
            }

        }
        return $multiFieldArray;
    }

    public function getCargoModel()
    {
        if (!$this->cargoModel) {
            $sm = $this->getServiceLocator();
            $this->cargoModel = $sm->get('Ticket\Model\CargoModel');
        }
        return $this->cargoModel;
    }

    public function returnAllWays($id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\TicketWay')->findBy(
            array('ownerTicketId' => new \MongoId($id))
        );
        $result = array();
        $comModel = $this->getCompanyModel();
        foreach ($res as $re) {
            $cargoOwnerTrue = $comModel->getCompany($re->cargoOwner);
            $resultArray = get_object_vars($re);
            $resultArray['cargoOwnerTrue'] = $cargoOwnerTrue;
            $resultArray['docArray'] =  $this->getDocumentWay($re->id);
            if(!empty( $resultArray['dateEnd'])) {
                $resultArray['dateEnd']=$resultArray['dateEnd']->format('Y-m-d');
            }
            if(!empty( $resultArray['dateStart'])) {
                $resultArray['dateStart']=$resultArray['dateStart']->format('Y-m-d');
            }

            array_push($result, $resultArray);
        }
        return $result;
    }

    public function getIdByUuid($uuid)
    {
        if (empty($uuid)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
            array('uuid' => $uuid)
        );
        if (empty($res)) {
            return null;
        }
        return $res->id;
    }

    public function getUuidById($id)
    {
        if (empty($id)) {
            return null;
        }
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $res = $objectManager->getRepository('Ticket\Entity\Ticket')->findOneBy(
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

    public function getCargoOwnerData($orgListId)
    {
        $comModel = $this->getCompanyModel();
        $com = $comModel->returnCompanies($orgListId);
        if (!empty($com)) {
            $result = array();
            foreach ($com as $c) {
                $result = $result + array($c['id'] => $c['name']);
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

    public function addBootstrap3Class(&$form, &$formsArray)
    {
        foreach ($formsArray as $formElement) {
            $formWay = $formElement['formWay'];
            foreach ($formElement['formsDocArray'] as $docWay) {
                foreach ($docWay as $wayEl) {
                    $attr = $wayEl->getAttributes();
                    if (!empty($attr['type'])) {
                        if ((($attr['type'] != 'checkbox') && ($attr['type']!='multi_checkbox') && ($attr['type'] != 'radio'))) {
                            $wayEl->setAttributes(array('class' => 'form-control'));
                        }
                    }

                }
            }
            foreach ($formWay as $wayEl) {
                $attr = $wayEl->getAttributes();
                if (!empty($attr['type'])) {
                    if ((($attr['type'] != 'checkbox') && ($attr['type']!='multi_checkbox')&& ($attr['type'] != 'radio'))) {
                        $wayEl->setAttributes(array('class' => 'form-control'));
                    }
                }
            }
        }

        foreach ($form as $el) {
            $attr = $el->getAttributes();
            if (!empty($attr['type'])) {
                if (($attr['type'] != 'checkbox')&& ($attr['type']!='multi_checkbox')) {
                    $el->setAttributes(array('class' => 'form-control'));
                }
            }
        }
    }
    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }

    public function getInteractionModel()
    {
        if (!$this->interactionModel) {
            $sm = $this->getServiceLocator();
            $this->interactionModel = $sm->get('Interaction\Model\InteractionModel');
        }
        return $this->interactionModel;
    }
}