<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/1/13
 * Time: 12:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resource\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Resource\Form\ResourceForm;

class ResourceController extends AbstractActionController
{

    protected $companyUserModel;
    protected $resourceModel;
    protected $vehicleModel;
    protected $interactionModel;
    protected $addListModel;

    public function indexAction()
    {
        $res = $this->getResourceModel();
        return new ViewModel(array(
            'res' => $res->returnAllResource()
        ));
    }

    public function myAction()
    {
        $res = $this->getResourceModel();
        $resource=$res->returnMyResource($this->zfcUserAuthentication()->getIdentity()->getId());
        return new ViewModel(array(
            'res' => $resource
        ));
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');

        $formWay= $builder->createForm('Resource\Entity\ResourceWay');

        $veh = $this->getVehicleModel();
        $myV=$veh->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId());
        $resForm=new ResourceForm();

        $form=$resForm->fillTS($form,$myV);

        $tsUuid = $this->getEvent()->getRouteMatch()->getParam('id');


        $form_array=array();

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);


        $form=$resForm->fillFrom($form,$formData,$form_array);


        if(!empty($tsUuid)) {
            $tsId=$veh->getIdByUuid($tsUuid);
            $form->get('tsId')->setValue($tsId);
        }

        return new ViewModel(array(
            'form' => $form,
            'formWay' =>$formWay

        ));
    }

    public function editAction()
    {
        $resModel = $this->getResourceModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listResource($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');

        $formWay= $builder->createForm('Resource\Entity\ResourceWay');

        $veh = $this->getVehicleModel();
        $myV=$veh->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId());
        $resForm=new ResourceForm();

        $form=$resForm->fillTS($form,$myV);

        $way=$resModel->returnAllWays($res['id']);

        $form->get('tsId')->setValue($res['tsId']);



        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'formWay'=>$formWay,
            'way'=>$way,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $resModel = $this->getResourceModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listResource($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');

        $formWay= $builder->createForm('Resource\Entity\ResourceWay');

        $veh = $this->getVehicleModel();
        $myV=$veh->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId());
        $resForm=new ResourceForm();

        $form=$resForm->fillTS($form,$myV);

        $way=$resModel->returnAllWays($res['id']);

        $form->get('tsId')->setValue($res['tsId']);



        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'formWay'=>$formWay,
            'way'=>$way,
            'id' => $id
        ));

    }

    public function deleteAction()
    {
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getResourceModel();
        $resModel->deleteResource($uuid);
        return $this->redirect()->toUrl('/resources/my');
    }

    public function addResourceAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $comUserModel = $this->getCompanyUserModel();
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $org_id = $comUserModel->getOrgIdByUserId($user_id);
        $res = $this->getResourceModel();
        $res->addResource($this->getRequest()->getPost(), $user_id, $org_id, $id);
        return $this->redirect()->toUrl('/resources/my');
    }

    public function getResourceModel()
    {
        if (!$this->resourceModel) {
            $sm = $this->getServiceLocator();
            $this->resourceModel = $sm->get('Resource\Model\ResourceModel');
        }
        return $this->resourceModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Organization\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }
    public function copyAction() {
        $resModel = $this->getResourceModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listResource($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,

        ));
    }
    public function getVehicleModel()
    {
        if (!$this->vehicleModel) {
            $sm = $this->getServiceLocator();
            $this->vehicleModel = $sm->get('Resource\Model\VehicleModel');
        }
        return $this->vehicleModel;
    }

    public function getInteractionModel()
    {
        if (!$this->interactionModel) {
            $sm = $this->getServiceLocator();
            $this->interactionModel = $sm->get('Interaction\Model\InteractionModel');
        }
        return $this->interactionModel;
    }
    public function getAddListModel()
    {
        if (!$this->addListModel) {
            $sm = $this->getServiceLocator();
            $this->addListModel = $sm->get('AddList\Model\AddListModel');
        }
        return $this->addListModel;
    }
}
