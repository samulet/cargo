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

class CompanyOkvedFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($accListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(
            array(
                'name' => 'companyOkvedCode',
                'options' => array(
                    'label' => 'Код ОКВЭД',
                    'label_attributes' => array(
                        'class' => 'control-label'
                    ),
                ),
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );
        $this->add(
            array(
                'name' => 'companyOkvedDelete',
                'type' => 'Zend\Form\Element\Button',
                'options' => array(
                    'label' => 'Удалить'
                ),
                'attributes' => array(
                    'onclick' => 'deleteFieldset(this);'

                )
            )
        );

    }

    public function getInputFilterSpecification()
    {
        return array();
    }


}