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

class CompanyAuthorizedPersonsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($orgListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(
            array(
                'name' => 'companyFounderType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид учредителя',
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
                'name' => 'companyFounderLink',
                'options' => array(
                    'label' => 'Ссылка на учредителя',
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
                'name' => 'companyFounderDelete',
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