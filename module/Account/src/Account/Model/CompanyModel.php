<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Account\Model;

use Account\Entity\Company;

use Account\Entity\ContractAgents;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use User\Entity\User;

class CompanyModel implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    protected $queryBuilderModel;
    protected $accountModel;

    public function returnCompanies($accId,$params = array())
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $params =array('deletedAt' => null)+$params;

        if(!empty($accId)) {
            $params['ownerAccId']=new \MongoId($accId);
        }
        $accModel = $this->getAccountModel();
        $company = $objectManager->createQueryBuilder('Account\Entity\Company');
        $queryBuilderModel = $this->getQueryBuilderModel();
        $cursor = $queryBuilderModel->createQuery($company, $params)->getQuery()->execute();
        $com = array();
        foreach ($cursor as $cur) {

            $arr = get_object_vars($cur);
            if(!empty($arr['ownerAccId'])) {
                $arr['accUuid']=$accModel->getAccount($arr['ownerAccId']);
            }
            array_push($com, $arr);
        }
        return $com;
    }

    public function unityContractAgent($comId,$comUnityId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $contract = $objectManager->createQueryBuilder('Account\Entity\ContractAgents');
        $queryBuilderModel = $this->getQueryBuilderModel();
        $contract=$queryBuilderModel->createQuery($contract,array('contactAgentId' => new \MongoId($comId)));
        $contract=$queryBuilderModel->createSetQuery($contract,array('contactAgentId' => new \MongoId($comUnityId)));
        $com=$contract->findAndUpdate()->getQuery()->execute();

        if (!$com) {
            throw DocumentNotFoundException::documentNotFound('Account\Entity\Company', $comId,$comUnityId);
        }
    }

    public function update($paramsFind,$paramsUpdate) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $company = $objectManager->createQueryBuilder('Account\Entity\Company');
        $queryBuilderModel = $this->getQueryBuilderModel();
        $company=$queryBuilderModel->createQuery($company,$paramsFind);
        $company=$queryBuilderModel->createSetQuery($company,$paramsUpdate);
        $com=$company->findAndUpdate()->getQuery()->execute();

        if (!$com) {
            throw DocumentNotFoundException::documentNotFound('Account\Entity\Company', $paramsFind,$paramsUpdate);
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

    public function createCompany($propArray, $accId, $comId)
    {
        if (!empty($propArray)) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

            if (!empty($comId)) {
                if ($comId == 'contractAgent') {
                    $com = new Company($accId, 'contractAgent');
                    $propArray['dirty'] = '1';
                } else {
                    $com = $objectManager->getRepository('Account\Entity\Company')->find($comId);
                }

            } else {
                $com = new Company($accId);
            }

            $propArray['activated'] = '1';

            foreach ($propArray as $key => $value) {
                if (!empty($value)) {
                    $com->$key = $value;
                }

            }
            $objectManager->persist($com);
            $objectManager->flush();
            return $com->id;
        } else {
            return false;
        }


    }

    public function getAllCompanies()
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $companiesObj = $objectManager->createQueryBuilder('Account\Entity\Company')
            ->getQuery()
            ->execute();
        $resultArray = array();
        foreach ($companiesObj as $com) {
            array_push($resultArray, get_object_vars($com));
        }
        return $resultArray;
    }

    public function getCompany($id)
    {
        $uuid_gen = new UuidGenerator();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if ($uuid_gen->isValid($id)) {
            $acc = $objectManager->getRepository('Account\Entity\Company')->findOneBy(array('uuid' => $id));
        } else {
            $acc = $objectManager->getRepository('Account\Entity\Company')->findOneBy(array('id' => new \MongoId($id)));
        }

        if (!empty($acc)) {
            return get_object_vars($acc);
        } else {
            return null;
        }

    }


    public function getCompanyIdByUUID($com_uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comId = $objectManager->getRepository('Account\Entity\Company')->findOneBy(array('uuid' => $com_uuid));
        return $comId->id;
    }

    public function returnCompany($comId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com_obj = $objectManager->getRepository('Account\Entity\Company')->find($comId);
        if (!empty($com_obj)) {
            $com = get_object_vars($com_obj);
            unset($com['created']);
            unset($com['updated']);
        } else {
            $com = null;
        }
        return $com;
    }



    public function deleteCompany($comId)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $qb = $objectManager->getRepository('Account\Entity\Company')->find(new \MongoId($comId));
        if (!$qb) {
            throw DocumentNotFoundException::documentNotFound('Account\Entity\Company', $comId);
        }
        $objectManager->remove($qb);
        $objectManager->flush();
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

    public function isContractAgentExist($contactAgentId, $comId)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $agent = $objectManager->getRepository('Account\Entity\ContractAgents')->findOneBy(
            array('contactAgentId' => $contactAgentId, 'comId' => $comId)
        );
        if (empty($agent)) {
            return false;
        } else {
            return true;
        }
    }

    public function addContractAgentToCompany($post, $comUuid)
    {
        if (!empty($post['contactAgentId'])) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $comId = $this->getCompanyIdByUUID($comUuid);
            if (!$this->isContractAgentExist($post['contactAgentId'], $comId)) {
                $agent = new ContractAgents($comId, $post['contactAgentId'], 'company');
                $objectManager->persist($agent);
                $objectManager->flush();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    public function getContractAgentsFromCompany($comUuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $comId = $this->getCompanyIdByUUID($comUuid);
        $agents = $objectManager->getRepository('Account\Entity\ContractAgents')->findBy(
            array('comId' => new \MongoId($comId))
        );
        $resultArray = array();
        foreach ($agents as $agent) {
            $com = $this->getCompany($agent->contactAgentId);
            if(!empty($com['activated'])) {
                if($com['activated']=='1') {
                    array_push($resultArray, $com);
                }
            }

        }
        return $resultArray;
    }

    public function getCompanyOfCurrentAccount($curAcc)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $coms = $objectManager->getRepository('Account\Entity\Company')->findBy(
            array('ownerAccId' => new \MongoId($curAcc))
        );
        $resultArray = array();
        foreach ($coms as $com) {
            array_push($resultArray, get_object_vars($com));
        }
        return $resultArray;
    }

    public function getQueryBuilderModel()
    {
        if (!$this->queryBuilderModel) {
            $sm = $this->getServiceLocator();
            $this->queryBuilderModel = $sm->get('QueryBuilder\Model\QueryBuilderModel');
        }
        return $this->queryBuilderModel;
    }
    public function getAccountModel()
    {
        if (!$this->accountModel) {
            $sm = $this->getServiceLocator();
            $this->accountModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->accountModel;
    }
}