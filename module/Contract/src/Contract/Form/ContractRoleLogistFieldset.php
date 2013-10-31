<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 9/29/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CompanyAddressFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($accListId = null)
    {
        parent::__construct();
        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'categories',
                'options' => array(
                    'label' => 'Please choose categories for this product',
                    'count' => 1,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => array(
                        'type' => 'Contract\Form\ContractRoleLogistFieldset'
                    )
                )
            )
        );


    }

    public function getInputFilterSpecification()
    {
        return array();
    }


}