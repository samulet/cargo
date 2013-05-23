<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/20/13
 * Time: 2:31 AM
 * To change this template use File | Settings | File Templates.
 */

namespace AddList\Controller;

use Entity\Recources;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AddList\Form\AddListNameForm;

class AddListController extends AbstractActionController
{
    protected $addListModel;

    public function indexAction()
    {

    }
    public function addAction()
    {
        $listNameUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $fieldId = $this->getEvent()->getRouteMatch()->getParam('fieldId');
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');
        return new ViewModel(array(
            'form' => $form,
            'uuid' =>$listNameUuid,
            'fieldId' => $fieldId
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

    public function addListAction() {

        $listUUID= $this->getEvent()->getRouteMatch()->getParam('id');
        $parentField=$this->getEvent()->getRouteMatch()->getParam('fieldId');
        $addListModel = $this->getAddListModel();

        $listToReturn=$addListModel->addList($this->getRequest()->getPost(),$listUUID,$parentField);

        return $this->redirect()->toUrl('/addList/my-fields/'.$listToReturn);
    }

    public function myAction() {;

        $addListModel = $this->getAddListModel();
        $list=$addListModel->getListName($this->getRequest()->getPost());

        return new ViewModel(array(

            'list' => $list
        ));
    }

    public function deleteAction() {
        $list_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $addListModel->deleteList($list_uuid);
        return $this->redirect()->toUrl('/addList/my');
    }

    public function addNameAction() {
        $addListNameForm= new AddListNameForm();
        $addListModel = $this->getAddListModel();
        $formData=$addListModel->getAllListName();
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddListName');
        $form=$addListNameForm->fillParentFrom($form,$formData);
        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function addListNameAction() {
        $list_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $addListModel->addListName($this->getRequest()->getPost(),$list_uuid);
        return $this->redirect()->toUrl('/addList/addName');
    }

    public function myFieldsAction() {
        $listNameUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $list=$addListModel->getList($listNameUuid);
        return new ViewModel(array(
            'field' => $list['field'],
            'list' => $list['list']
        ));
    }
}
