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
        $vehicles=$res->returnVehicles(array('deletedAt'=>null,'activated'=>'1','ownerId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())));
        return new ViewModel(array(
            'res' => $vehicles
        ));
    }

    public function myAction()
    {
        $res = $this->getVehicleModel();
        $vehicles=$res->returnVehicles(array('deletedAt'=>null,'ownerId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentCom())));

        return new ViewModel(array(
            'res' => $vehicles
        ));
    }

    public function myAccAction()
    {
        $res = $this->getVehicleModel();
        $vehicles=$res->returnVehicles(array('deletedAt'=>null,'ownerOrgId'=>new \MongoId($this->zfcUserAuthentication()->getIdentity()->getCurrentAcc())));
        return new ViewModel(array(
            'res' => $vehicles
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
        $formArray=array();

        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

        $formData=$addListModel->returnDataArray($formArray,'vehicle',$accListId,$comListId);

        $fillFrom=new AddListForm();
        $form=$fillFrom->fillFrom($form,$formData);


        $formData=$addListModel->returnDataArray($formArray,'ticketWay',$accListId,$comListId);
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

                    $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();

                    $veh=$vehicleModel->addVehicle($this->getRequest()->getPost(), $comListId, $accListId, $id);

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
        $formArray=array('mark','model','type','status');

        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

        $formData=$addListModel->returnDataArray($formArray,'vehicle',$accListId,$comListId);
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
        $formArray=array('mark','model','type','status');

        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

        $formData=$addListModel->returnDataArray($formArray,'vehicle',$accListId,$comListId);
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
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
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
        $formArray=array('mark','model','type','status');

        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $accListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentAcc();

        $formData=$addListModel->returnDataArray($formArray,'vehicle',$accListId,$comListId);
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
