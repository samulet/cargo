<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/25/13
 * Time: 4:24 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Account\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CompanyUserFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('company_user');


        $this->add(
            array(
                'name' => 'email',
                'options' => array(
                    'label' => 'Почта пользователя'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );

    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
    \*/
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
            ),
            'price' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Float'
                    )
                )
            )
        );
    }
}