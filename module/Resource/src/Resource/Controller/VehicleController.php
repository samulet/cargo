<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/15/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Resource\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Resource\Form\VehicleForm;
class VehicleController extends AbstractActionController
{

    protected $companyUserModel;
    protected $vehicleModel;
    protected $addListModel;

    public function indexAction()
    {
        $res = $this->getVehicleModel();
        return new ViewModel(array(
            'res' => $res->returnAllVehicle()
        ));
    }

    public function myAction()
    {
        $res = $this->getVehicleModel();
        return new ViewModel(array(
            'res' => $res->returnMyVehicle($this->zfcUserAuthentication()->getIdentity()->getId())
        ));
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Vehicle');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);
        $formData=$addListModel->returnDataArray($form_array,'vehicle',$orgListId);

        $fillFrom=new VehicleForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function editAction()
    {
        $resModel = $this->getVehicleModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listVehicle($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Vehicle');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);
        $formData=$addListModel->returnDataArray($form_array,'vehicle',$orgListId);
        $fillFrom=new VehicleForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $resModel = $this->getVehicleModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listVehicle($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Vehicle');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);
        $formData=$addListModel->returnDataArray($form_array,'vehicle',$orgListId);
        $fillFrom=new VehicleForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'id' => $id
        ));

    }

    public function deleteAction()
    {
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getVehicleModel();
        $resModel->deleteVehicle($uuid);
        return $this->redirect()->toUrl('/vehicles/my');
    }

    public function addVehicleAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $comUserModel = $this->getCompanyUserModel();
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $org_id = $comUserModel->getOrgIdByUserId($user_id);
        $res = $this->getVehicleModel();
        $res->addVehicle($this->getRequest()->getPost(), $user_id, $org_id, $id);
        return $this->redirect()->toUrl('/vehicles/my');
    }

    public function getVehicleModel()
    {
        if (!$this->vehicleModel) {
            $sm = $this->getServiceLocator();
            $this->vehicleModel = $sm->get('Resource\Model\VehicleModel');
        }
        return $this->vehicleModel;
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

        $resModel = $this->getVehicleModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listVehicle($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Vehicle');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);
        $formData=$addListModel->returnDataArray($form_array,'vehicle',$orgListId);
        $fillFrom=new VehicleForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form,
            'res' => $res
        ));
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
