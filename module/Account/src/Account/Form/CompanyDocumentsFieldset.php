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

class CompanyDocumentsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($accListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(
            array(
                'name' => 'companyDocumentType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Наименование документа',
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
                'name' => 'companyDocumentNumber',
                'options' => array(
                    'label' => 'Номер документа',
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
                'name' => 'companyDocumentDate',
                'type' => 'Zend\Form\Element\Date',
                'options' => array(
                    'label' => 'Дата документа',
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
                'name' => 'companyDocumentLink',
                'options' => array(
                    'label' => 'Ссылки на файлы сканов документа',
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
                'name' => 'companyDocumentDelete',
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