<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/22/13
 * Time: 11:14 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Organization\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class OrganizationFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('organization');


        $this->add(
            array(
                'name' => 'name',
                'options' => array(
                    'label' => 'Имя организации'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );

        $this->add(
            array(
                'name' => 'type',
                'options' => array(
                    'label' => 'Тип органзиации'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );

        $this->add(
            array(
                'name' => 'description',
                'options' => array(
                    'label' => 'Описание организации'
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