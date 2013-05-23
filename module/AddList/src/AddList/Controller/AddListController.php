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
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');
        return new ViewModel(array(
            'form' => $form
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
        $list_uuid = $this->getEvent()->getRouteMatch()->getParam('id');
        $addListModel = $this->getAddListModel();
        $addListModel->addList($this->getRequest()->getPost(),$list_uuid);
        return $this->redirect()->toUrl('/addList/my');
    }

    public function myAction() {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm('AddList\Entity\AddList');
        $addListModel = $this->getAddListModel();
        $list=$addListModel->getList($this->getRequest()->getPost());
        return new ViewModel(array(
            'form' => $form,
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
}
