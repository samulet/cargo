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
use AddList\Form\AddListForm;
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
        $post=$this->getRequest()->getPost();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $type = $this->getEvent()->getRouteMatch()->getParam('type');

        $vehicleModel = $this->getVehicleModel();

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Vehicle');
        $addListModel = $this->getAddListModel();
        $form_array=array();
        $orgUserModel=$this->getCompanyUserModel();
        $userListId=$this->zfcUserAuthentication()->getIdentity()->getId();
        $orgListId=$orgUserModel->getOrgIdByUserId($userListId);
        $formData=$addListModel->returnDataArray($form_array,'vehicle',$orgListId);

        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);


        $formData=$addListModel->returnDataArray($form_array,'ticketWay',$orgListId);
        $form=$fillFrom->fillFromVehicleSpecial($form,$formData,array('typeLoad'));

        $typeForm='';
        if(empty($type)) {
            if(!empty($post->submit)) {
                $error=0;


                $form->setData($post);;

                if(!$form->isValid()) {
                    $error++;

                }


                if(empty($error)) {


                    $comUserModel = $this->getCompanyUserModel();
                    $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
                    $org_id = $comUserModel->getOrgIdByUserId($user_id);

                    $veh=$vehicleModel->addVehicle($this->getRequest()->getPost(), $user_id, $org_id, $id);
                    if(empty($veh)) {
                        return $this->redirect()->toUrl('/vehicles/error');
                    } else {
                        return $this->redirect()->toUrl('/vehicles/my');
                    }
                }

            }
        } else {
            $vehicle = $vehicleModel->listVehicle($id);

            if( ($type=='copy')||($type=='edit')||($type=='list') ) {
                $form->setData($vehicle);
                if($type=='edit') {
                    $typeForm['action']='edit';
                    $typeForm['id']=$id;
                }
                elseif($type=='copy') {
                    $form->get('serialNumber')->setValue('');
                    $form->get('vin')->setValue('');
                    $form->get('serialNumberDoc')->setValue('');
                    $form->get('carNumber')->setValue('');

                    $typeForm['action']='copy';
                    $typeForm['id']=$id;
                } elseif($type=='list') {
                    foreach ($form as $el) {
                        $el->setAttributes(array( 'disabled' => 'disabled' ));
                    }
                    $typeForm['action']='list';
                    $typeForm['id']=$id;
                }
            }
        }
        $vehicleModel->addBootstrap3Class($form);
        return new ViewModel(array(
            'form' => $form,
           'typeForm'=>$typeForm
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
        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'id' => $id
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
        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);
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



    public function errorAction() {

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
        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);
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
