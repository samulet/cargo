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
use AddList\Form\AddListForm;
class AddListController extends AbstractActionController
{
    protected $addListModel;
    protected $organizationModel;
    protected $companyUserModel;
    protected $companyModel;

    public function indexAction()
    {

    }
    public function addAction()
    {
        $listNameUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $parent = $this->getEvent()->getRouteMatch()->getParam('parent');
        $addListModel = $this->getAddListModel();

        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();
        if(!empty($parent)) {
            $listName=$addListModel->getOneList($listNameUuid);
        } else {
            $listName=$addListModel->getList($listNameUuid,$orgListId);
        }

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');
        $addListModel->addBootstrap3Class($form);
        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();


        $comModel = $this->getCompanyModel();
        $comData=$comModel->getCompanyOfCurrentAccount($orgListId);

        $fillFrom=new AddListForm();
        $fillFrom->fillComNew($form,$comData,'company');

        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');


        if(array_search("admin",$roles,true)) {

            $form->get('forAccount')->setAttribute('required', false);
        }

        return new ViewModel(array(
            'form' => $form,
            'uuid' =>$listNameUuid,
            'parent'=>$parent,
            'listName'=>$listName,
            'roles'=>$roles

        ));
    }


    public function editAction() {
        $listUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $actionType = $this->getEvent()->getRouteMatch()->getParam('parent');
        $addListModel = $this->getAddListModel();
        $listData=$addListModel->getOneList($listUuid);
        $listName=$addListModel->getListName($listData['listId']);

        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');


        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();
        $listParent=null;
        if(!empty($listData['parentFieldId'])) {
            $listParent=$addListModel->getOneList($addListModel->getListUuidById($listData['parentFieldId']));
        }
        if($actionType=='list') {
            foreach ($form as $el) {
                $el->setAttributes(array( 'disabled' => 'disabled' ));
            }
        }
        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();
        $comModel = $this->getCompanyModel();
        $comData=$comModel->getCompanyOfCurrentAccount($orgListId);

        $fillFrom=new AddListForm();
        $fillFrom->fillComNew($form,$comData,'company');
        $form->setData($listData);


        return new ViewModel(array(
            'fieldUuid' => $listUuid,
            'uuid'=>$listData['listId'],
            'form'=>$form,
            'roles'=>$roles,
            'listName'=>$listName,
            'listParent'=>$listParent,
            'actionType' =>$actionType
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
        $post=$this->getRequest()->getPost();
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


        $userId=$this->zfcUserAuthentication()->getIdentity()->getId();

        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();

        $listId= $addListModel->addList($post,$listUUID,$parentField,$userId,$orgListId,$comListId);


        if(empty($listName['parentId'])) {
            return $this->redirect()->toUrl('/addList/my-fields/'.$listId['listId']);
        } else {
            $parentUuid=$addListModel->getListUuidById($listId['parentFieldId']);

            return $this->redirect()->toUrl('/addList/list-parent/'.$parentUuid);
        }

    }


    public function myAction() {;

        $addListModel = $this->getAddListModel();
        $list=$addListModel->getListName();

        return new ViewModel(array(

            'list' => $list
        ));
    }

    public function deleteAction() {
        $list_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $list=$addListModel->getOneList($list_uuid);
        $addListModel->deleteList($list_uuid);
        $listName=$addListModel->getListName((string)$list['listId']);
        return $this->redirect()->toUrl('/addList/my-fields/'.$listName['uuid']);
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

        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();


        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();

        if(array_search("admin",$roles,true)) {
            $list=$addListModel->getListAdmin($listNameUuid);
        } else {
            $list=$addListModel->getList($listNameUuid,$orgListId);
        }





        if(!empty($list['list']['parentId'])) {
            $parentList=$addListModel->getListName($list['list']['parentId']);
        } else {
            $parentList=null;
        }
        $listChild=null;
        if(!empty($list['list']['child'])) {
            $listChild=$addListModel->getListName('veh-models');
        }
        //$listChild=$addListModel->getChildName($list['list']['id']);



        return new ViewModel(array(
            'field' => $list['field'],
            'list' => $list['list'],
            'parentList' =>$parentList,
            'listChild'=>$listChild
        ));
    }


    public function editFieldAction() {
        $listUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $comListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentCom();
        $orgListId=$this->zfcUserAuthentication()->getIdentity()->getCurrentOrg();
        $listId=$addListModel->editField($listUuid,$this->getRequest()->getPost(),$orgListId,$comListId);
        $listName=$addListModel->getListName((string)$listId['listId']);
        return $this->redirect()->toUrl('/addList/my-fields/'.$listName['uuid']);
    }

    public function listParentAction() {
        $parentListUuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $listChild=$addListModel->listParentAction($parentListUuid);
        $listParent=$addListModel->getOneList($parentListUuid);
        $listName=$addListModel->getListName($listParent['listId']);
        return new ViewModel(array(
            'listChild'=>$listChild,
            'listParent'=>$listParent,
            'uuid'=>$parentListUuid,
            'listName'=>$listName
        ));
    }

    public function getAccountModel()
    {
        if (!$this->organizationModel) {
            $sm = $this->getServiceLocator();
            $this->organizationModel = $sm->get('Account\Model\AccountModel');
        }
        return $this->organizationModel;
    }

    public function getCompanyUserModel()
    {
        if (!$this->companyUserModel) {
            $sm = $this->getServiceLocator();
            $this->companyUserModel = $sm->get('Account\Model\CompanyUserModel');
        }
        return $this->companyUserModel;
    }

    public function gotToTheChildAction() {
        $listId = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $uuid=$addListModel->getChildUuid($listId);
        return $this->redirect()->toUrl('/addList/list-parent/'.$uuid);
    }

    public function addListTranslatorAction() {
        $addListModel = $this->getAddListModel();
        $addListModel->addListTranslator();

    }
    public function getCompanyModel()
    {
        if (!$this->companyModel) {
            $sm = $this->getServiceLocator();
            $this->companyModel = $sm->get('Account\Model\CompanyModel');
        }
        return $this->companyModel;
    }
}
