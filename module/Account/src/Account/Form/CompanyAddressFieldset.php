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
        $this->add(
            array(
                'name' => 'companyAddressType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид адреса',
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
                'name' => 'companyAddressIndex',
                'options' => array(
                    'label' => 'Почтовый индекс',
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
                'name' => 'companyAddressSubject',
                'options' => array(
                    'label' => 'Субъект РФ',
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
                'name' => 'companyAddressCity',
                'options' => array(
                    'label' => 'Город',
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
                'name' => 'companyAddressTown',
                'options' => array(
                    'label' => 'Населенный пункт',
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
                'name' => 'companyAddressStreet',
                'options' => array(
                    'label' => 'Улица',
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
                'name' => 'companyAddressHouse',
                'options' => array(
                    'label' => 'Номер дома',
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
                'name' => 'companyAddressPart',
                'options' => array(
                    'label' => 'Корпус',
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
                'name' => 'companyAddressRoom',
                'options' => array(
                    'label' => 'Квартира',
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
                'name' => 'companyAddressDelete',
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