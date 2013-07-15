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
use AddList\Form\AddListForm;

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
        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();
        return new ViewModel(array(
            'res' => $res->returnAllResource(),
            'roles'=>$roles
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
        $post=$this->getRequest()->getPost();
        $type = $this->getEvent()->getRouteMatch()->getParam('type');
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        $resourceModel = $this->getResourceModel();

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');
        $formWay= $builder->createForm('Resource\Entity\ResourceWay');

        $veh = $this->getVehicleModel();
        $myV=$veh->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId());
        $resForm=new AddListForm();

        $form=$resForm->fillTS($form,$myV);

        $tsUuid = $this->getEvent()->getRouteMatch()->getParam('id');


        $form_array=array();

        $addListModel = $this->getAddListModel();

        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);

        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);


        $form=$resForm->fillFrom($form,$formData);

        if(!empty($tsUuid)) {
            $tsId=$veh->getIdByUuid($tsUuid);
            $form->get('tsId')->setValue($tsId);
        }
        $typeForm=array();
        if(empty($type)) {
            if(!empty($post->submit)) {
                $error=0;
                $formWay->setData($post);
                if(!$formWay->isValid()) {
                    $error++;
                }
                $form->setData($post);
                if(!$form->isValid()) {
                    $error++;
                }

                if(empty($error)) {

                    $comUserModel = $this->getCompanyUserModel();
                    $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();

                    $org_id = $comUserModel->getOrgIdByUserId($user_id);


                    $resourceModel->addResource($post, $user_id, $org_id, $id);

                    return $this->redirect()->toUrl('/resources/my');
                }
            }
        } else {
            $resource = $resourceModel->listResource($id);
            $resourceWay=$resourceModel->returnAllWays($resource['id']);
            //die(var_dump($resourceWay));
            if( ($type=='copy')||($type=='edit')||($type=='list') ) {
                $form->setData($resource);

                $formsArray=array();
                $formWay->setData($resourceWay[0]);
                if($type=='edit') {
                    $typeForm['action']='edit';
                    $typeForm['id']=$id;
                }
                elseif($type=='copy') {
                    $typeForm['action']='copy';
                    $typeForm['id']=$id;
                } elseif($type=='list') {
                        foreach ($formWay as $wayEl) {
                            $wayEl->setAttributes(array( 'disabled' => 'disabled' ));
                        }

                    foreach ($form as $el) {
                        $el->setAttributes(array( 'disabled' => 'disabled' ));
                    }
                    $typeForm['action']='list';
                    $typeForm['id']=$id;
                }
            } elseif($type=='search') {

                    foreach ($formWay as $wayEl) {
                        $wayEl->setAttributes(array('required'  => '' ));
                    }

                foreach ($form as $el) {
                    $el->setAttributes(array('required'  => '' ));
                }
                $typeForm['action']='search';
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'formWay' =>$formWay,
            'typeForm'=>$typeForm
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
        $resForm=new AddListForm();

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


    public function searchAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');

        $formWay= $builder->createForm('Resource\Entity\ResourceWay');

        $veh = $this->getVehicleModel();
        $myV=$veh->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId());
        $resForm=new AddListForm();

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
    public function getResultsAction()
    {
        $res = $this->getResourceModel();
        $resource=$res->returnResultsResource($this->getRequest()->getPost());
        return new ViewModel(array(
            'res' => $resource
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
        $resForm=new AddListForm();

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
