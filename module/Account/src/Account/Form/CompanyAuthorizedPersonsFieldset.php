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

class CompanyAuthorizedPersonsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($accListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(
            array(
                'name' => 'companyAuthorizedPersonType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид полномочия',
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
                'name' => 'companyAuthorizedPersonWork',
                'options' => array(
                    'label' => 'Основание деятельности',
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
                'name' => 'companyAuthorizedPersonLink',
                'options' => array(
                    'label' => 'Ссылка на физ лицо (email в системе)',
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
                'name' => 'companyAuthorizedPersonDelete',
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