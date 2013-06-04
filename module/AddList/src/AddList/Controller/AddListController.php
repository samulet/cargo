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
        $parent = $this->getEvent()->getRouteMatch()->getParam('parent');
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');
        return new ViewModel(array(
            'form' => $form,
            'uuid' =>$listNameUuid,
            'parent'=>$parent
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
        $target=$listUUID;
        $parent=$this->getEvent()->getRouteMatch()->getParam('parent');
        if($parent=='parent') {
            $parentField=$listUUID;
            $listUUID=null;
            $target=$parentField;
        } else {
            $parentField=null;
        }
        $addListModel = $this->getAddListModel();

       $listId= $addListModel->addList($this->getRequest()->getPost(),$listUUID,$parentField);

        $listName=$addListModel->getListName((string)$listId['listId']);
        return $this->redirect()->toUrl('/addList/my-fields/'.$listName['uuid']);
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

        if(!empty($list['list']['parentId'])) {
            $parentList=$addListModel->getListName($list['list']['parentId']);
        } else {
            $parentList=null;
        }
        $listChild=$addListModel->getChildName($list['list']['id']);


        return new ViewModel(array(
            'field' => $list['field'],
            'list' => $list['list'],
            'parentList' =>$parentList,
            'listChild'=>$listChild
        ));
    }

    public function editAction() {
        $listUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $listData=$addListModel->getOneList($listUuid);
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');

        return new ViewModel(array(
            'fieldUuid' => $listUuid,
            'listData' => $listData,
            'form'=>$form
        ));
    }

    public function editFieldAction() {
        $listUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $listId=$addListModel->editField($listUuid,$this->getRequest()->getPost());
        $listName=$addListModel->getListName((string)$listId['listId']);
        return $this->redirect()->toUrl('/addList/my-fields/'.$listName['uuid']);
    }

    public function listParentAction() {
        $parentListUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $listChild=$addListModel->listParentAction($parentListUuid);
        $listParent=$addListModel->getOneList($parentListUuid);
        return new ViewModel(array(
            'listChild'=>$listChild,
            'listParent'=>$listParent
        ));
    }
}
