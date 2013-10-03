<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 9/29/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */
namespace AddList\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use AddList\Form\AddListForm;
use AddList\Model\AddListModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AddListRequisitesFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($orgListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
        $this->add(
            array(
                'name' => 'addListRequisitesAccountNumber',
                'options' => array(
                    'label' => 'Номер счета',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addListRequisitesBankName',
                'options' => array(
                    'label' => 'Банк получателя',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addListRequisitesBik',
                'options' => array(
                    'label' => 'БИК',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addListRequisitesKorr',
                'options' => array(
                    'label' => 'Кор. Счет',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );
        $this->add(
            array(
                'name' => 'addListRequisitesInn',
                'options' => array(
                    'label' => 'ИНН Банка',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'addListRequisitesKpp',
                'options' => array(
                    'label' => 'КПП Банка',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'addListRequisitesOgrn',
                'options' => array(
                    'label' => 'ОГРН',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );

    }

    public function getInputFilterSpecification()
    {
        return array();
    }


}