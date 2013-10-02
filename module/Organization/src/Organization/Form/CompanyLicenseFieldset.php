<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 9/29/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Organization\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use AddList\Form\AddListForm;
use AddList\Model\AddListModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CompanyLicenseFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($orgListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());


        $this->add(
            array(
                'name' => 'companyLicenseName',
                'options' => array(
                    'label' => 'Наименование лицензии',
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
                'name' => 'companyLicenseWhile',
                'options' => array(
                    'label' => 'Срок действия',
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
            'name' => 'companyLicenseDate',
            'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => 'Дата выдачи',
                'label_attributes' => array(
                    'class'  => 'control-label'
                ),
            ),
            'attributes' => array (
                'class' => 'form-control'

            )
        ));
        $this->add(
            array(
                'name' => 'companyLicenseIssueName',
                'options' => array(
                    'label' => 'Кем выдано',
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
                'name' => 'companyLicenseDelete',
                'type' => 'Zend\Form\Element\Button',
                'options' => array(
                    'label' => 'Удалить'
                ),
                'attributes' => array (
                    'onclick' => 'deleteFieldset(this);'

                )
            ));
    }

    public function getInputFilterSpecification()
    {
        return array();
    }


}