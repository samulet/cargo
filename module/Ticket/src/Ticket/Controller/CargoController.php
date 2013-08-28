<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/15/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Ticket\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Ticket\Form\CargoForm;
class CargoController extends AbstractActionController
{

    protected $companyUserModel;
    protected $cargoModel;
    protected $addListModel;
    protected $ticketModel;

    public function indexAction()
    {
        $res = $this->getTicketModel();
        return new ViewModel(array(
            'res' => $res->returnAllTicket()
        ));
    }

    public function myAction()
    {
        $res = $this->getTicketModel();
        $ticket=$res->returnMyTicket($this->zfcUserAuthentication()->getIdentity()->getCurrentCom());
        return new ViewModel(array(
            'res' => $ticket
        ));

    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Cargo');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $formData=$addListModel->returnDataArray($form_array,'cargo');

        $fillFrom=new CargoForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function editAction()
    {
        $resModel = $this->getCargoModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listCargo($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Cargo');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $formData=$addListModel->returnDataArray($form_array,'cargo');
        $fillFrom=new CargoForm();
        $form=$fillFrom->fillFrom($form,$formData,$form_array);
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $this->getCargoModel();
        return new ViewModel(array(
            'res' => $res->listCargo($id)
        ));

    }

    public function deleteAction()
    {
        $uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $resModel = $this->getCargoModel();
        $resModel->deleteCargo($uuid);
        return $this->redirect()->toUrl('/cargos/my');
    }

    public function addCargoAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $comUserModel = $this->getCompanyUserModel();
        $user_id = $this->zfcUserAuthentication()->getIdentity()->getId();
        $org_id = $comUserModel->getOrgIdByUserId($user_id);
        $res = $this->getCargoModel();
        $res->addCargo($this->getRequest()->getPost(), $user_id, $org_id, $id);
        return $this->redirect()->toUrl('/cargos/my');
    }

    public function getCargoModel()
    {
        if (!$this->cargoModel) {
            $sm = $this->getServiceLocator();
            $this->cargoModel = $sm->get('Ticket\Model\CargoModel');
        }
        return $this->cargoModel;
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

        $resModel = $this->getCargoModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listCargo($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Ticket\Entity\Cargo');
        $addListModel = $this->getAddListModel();
        $form_array=array('mark','model','type','status');
        $formData=$addListModel->returnDataArray($form_array,'cargo');
        $fillFrom=new CargoForm();
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
    public function getTicketModel()
    {
        if (!$this->ticketModel) {
            $sm = $this->getServiceLocator();
            $this->ticketModel = $sm->get('Ticket\Model\TicketModel');
        }
        return $this->ticketModel;
    }
}
