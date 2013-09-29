<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 9/29/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Organization\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use AddList\Form\AddListForm;
use AddList\Model\AddListModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyAddressFieldset extends Fieldset implements InputFilterProviderInterface {

    public function __construct($orgListId = null) {
        parent::__construct();
       // $this->setHydrator(new DoctrineHydrator());
        $sm=$this->getFormFactory()->getFormElementManager()->getServiceLocator();

    //    $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
     //   $addListModel = new AddListModel();


    //    $formData=$addListModel->returnDataArray(array(),'company',$orgListId);
     //   $fillFrom=new AddListForm();
     //   die(var_dump($orgListId));
        //"value_options" => $fillFrom->getSelectValueList($formData,'companyAddressType')
        $this->add(
            array(
                'name' => 'addressType',
                'type' =>'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид адреса',

                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
    }

    public function getInputFilterSpecification() {
        return array();
    }


}