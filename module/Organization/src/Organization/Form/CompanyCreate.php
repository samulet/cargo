<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 9:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CompanyCreate extends Form
{
    public function __construct()
    {
        parent::__construct('company_create');

        $this->setAttribute('method', 'post')
            ->setHydrator(new ClassMethodsHydrator(false))
            ->setInputFilter(new InputFilter());

        $this->add(array(
            'type' => 'Organization\Form\CompanyFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Send'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf'
        ));


    }
}