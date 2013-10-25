<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 7/2/13
 * Time: 11:30 PM
 * To change this template use File | Settings | File Templates.
 */

namespace User\Controller;


use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Element\Checkbox;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class UserController extends AbstractActionController
{

    protected $userModel;


    public function roleAction()
    {
        return new ViewModel(array(
            'res' => 'asd'
        ));
    }

    public function getUserModel()
    {

        if (!$this->userModel) {
            $sm = $this->getServiceLocator();
            $this->userModel = $sm->get('User\Model\UserModel');
        }
        return $this->userModel;
    }

}
