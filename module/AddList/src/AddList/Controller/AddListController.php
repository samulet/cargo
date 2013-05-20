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
        $addListModel = $this->getAddListModel();
        $addListModel->addList($this->getRequest()->getPost());
        return $this->redirect()->toUrl('/addList/add');
    }

}
