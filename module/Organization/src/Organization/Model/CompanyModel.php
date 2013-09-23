<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 1:35 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Model;

use Organization\Entity\Company;

use Organization\Entity\ContractAgents;
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


    public function __construct()
    {

    }

    protected $serviceLocator;

    public function returnCompanies($org_id, $number = '30', $page = '1')
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $cursor = $objectManager->getRepository('Organization\Entity\Company')->getMyAvailableCompany(new \MongoId($org_id));

        $com = array();
        foreach ($cursor as $cur) {
            $arr = get_object_vars($cur);
            unset($arr['created']);
            unset($arr['updated']);
            array_push($com, $arr);
        }
        return $com;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function createCompany($post, $org_id, $com_id)
    {
        if(!empty($post)) {
            if(is_array($post)) {
                $prop_array=$post;
            } else {
                $prop_array = get_object_vars($post);
            }
        }
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

            if (!empty($com_id)) {
                $com = $objectManager->getRepository('Organization\Entity\Company')->find($com_id);
            } else {
                $com = new Company($org_id);
            }


            foreach ($prop_array as $key => $value) {
                $com->$key=$value;
            }
            $objectManager->persist($com);
            $objectManager->flush();

            $objectManager->persist($com);
            $objectManager->flush();
            return true;

    }
    public function getAllCompanies() {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $companiesObj = $objectManager->createQueryBuilder('Organization\Entity\Company')
            ->getQuery()
            ->execute();
        $resultArray=array();
        foreach($companiesObj as $com) {
            array_push($resultArray,get_object_vars($com));
        }
        return $resultArray;
    }
    public function getCompany($id)
    {
        $uuid_gen = new UuidGenerator();
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        if ($uuid_gen->isValid($id)) {
            $org = $objectManager-> getRepository('Organization\Entity\Company')->findOneBy(array('uuid' => $id));
        } else {
            $org = $objectManager-> getRepository('Organization\Entity\Company')->findOneBy(array('id' => new \MongoId($id)));
        }

        if(!empty($org)) {
            return get_object_vars($org);
        } else {
            return null;
        }

    }


    public function getCompanyIdByUUID($com_uuid)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com_id = $objectManager->getRepository('Organization\Entity\Company')->findOneBy(array('uuid' => $com_uuid));
        return $com_id->id;
    }

    public function returnCompany($com_id)
    {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $com_obj = $objectManager->getRepository('Organization\Entity\Company')->find($com_id);
        if(!empty($com_obj)) {
            $com = get_object_vars($com_obj);
            unset($com['created']);
            unset($com['updated']);
        } else {
            $com=null;
        }
        return $com;
    }

    public function deleteCompany($com_id)
    {

        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');

        $qb = $objectManager->getRepository('Organization\Entity\Company')->find(new \MongoId($com_id));
        if (!$qb) {
            throw DocumentNotFoundException::documentNotFound('Resource\Entity\Vehicle', $com_id);
        }
        $objectManager->remove($qb);
        $objectManager->flush();
    }
    public function addBootstrap3Class(&$form) {

        foreach ($form as $el) {
            $attr=$el->getAttributes();
            if(!empty($attr['type'])) {
                if(($attr['type']!='checkbox')&&($attr['type']!='multi_checkbox')) {
                    $el->setAttributes(array( 'class' => 'form-control' ));
                }
            }

        }
    }
    public function isContractAgentExist($contactAgentId,$comId) {
        $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $agent = $objectManager-> getRepository('Organization\Entity\ContractAgents')->findOneBy(array('contactAgentId' => $contactAgentId,'comId'=>$comId));
        if(empty($agent)) {
            return false;
        } else {
            return true;
        }
    }
    public function addContractAgentToCompany($post,$comUuid) {
        if(!empty($post['contactAgentId'])) {
            $objectManager = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
            $comId=$this->getCompanyIdByUUID($comUuid);
            if(!$this->isContractAgentExist($post['contactAgentId'],$comId)) {
                $agent= new ContractAgents($comId,$post['contactAgentId'],'company');
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
}