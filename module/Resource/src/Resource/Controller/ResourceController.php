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

class ResourceController extends AbstractActionController
{

    protected $companyUserModel;
    protected $resourceModel;

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
        return new ViewModel(array(
            'res' => $res->returnMyResource($this->zfcUserAuthentication()->getIdentity()->getId())
        ));
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function editAction()
    {
        $resModel = $this->getResourceModel();
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $resModel->listResource($id);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('Resource\Entity\Resource');
        return new ViewModel(array(
            'form' => $form,
            'res' => $res,
            'id' => $id
        ));
    }

    public function listAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $res = $this->getResourceModel();
        return new ViewModel(array(
            'res' => $res->listResource($id)
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
        $uuid=$this->getEvent()->getRouteMatch()->getParam('id');
        $res = $this->getResourceModel();
        $uuid=$res->copyResource($uuid);
        return $this->redirect()->toUrl('/resources/edit/'.$uuid);
    }

}
