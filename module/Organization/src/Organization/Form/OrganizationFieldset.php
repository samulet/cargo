<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 4/22/13
 * Time: 11:14 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Organization\Form;

use Organization\Entity\Organization;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class OrganizationFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('organization');
        $this->setHydrator(new ClassMethodsHydrator(false))
            ->setObject(new Organization());

        $this->add(array(
            'name' => 'orgName',
            'options' => array(
                'label' => 'Имя организации'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

        $this->add(array(
            'name' => 'orgType',
            'options' => array(
                'label' => 'Тип органзиации'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => 'Описание организации'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

     /*   $this->add(array(
            'name' => 'price',
            'options' => array(
                'label' => 'Price of the product'
            ),
            'attributes' => array(
                'required' => 'required'
            )
        ));

        $this->add(array(
            'type' => 'Application\Form\BrandFieldset',
            'name' => 'brand',
            'options' => array(
                'label' => 'Brand of the product'
            )
        ));*/
/*
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'label' => 'Please choose categories for this product',
                'count' => 2,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Organization\Form\OrganizationFieldset'
                )
            )
        ));*/
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