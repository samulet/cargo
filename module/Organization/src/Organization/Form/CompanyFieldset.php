<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/24/13
 * Time: 9:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CompanyFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('company');


        $this->add(
            array(
                'name' => 'name',
                'options' => array(
                    'label' => 'Имя компании'
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
                    'label' => 'Форма собственности'
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
                    'label' => 'Описание компании'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );

        $this->add(
            array(
                'name' => 'requisites',
                'options' => array(
                    'label' => 'Реквизиты'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addressReg',
                'options' => array(
                    'label' => 'Юридически адресс'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addressFact',
                'options' => array(
                    'label' => 'Фактический адресс'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
        $this->add(
            array(
                'name' => 'generalManager',
                'options' => array(
                    'label' => 'Генеральынй директор'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
        $this->add(
            array(
                'name' => 'telephone',
                'options' => array(
                    'label' => 'Контактный телефон'
                ),
                'attributes' => array(
                    'required' => 'required'
                )
            )
        );
        $this->add(
            array(
                'name' => 'email',
                'options' => array(
                    'label' => 'Контактная почта'
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