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

class CompanyAddressFieldset extends Fieldset implements InputFilterProviderInterface {
    public function __construct() {
        parent::__construct();
       // $this->setHydrator(new DoctrineHydrator());
        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
        $this->add(
            array(
                'name' => 'addressType',
                'options' => array(
                    'label' => 'Вид адреса'
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