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

class CompanyContactsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($orgListId = null)
    {
        parent::__construct();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());

        $this->add(
            array(
                'name' => 'companyContactType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид контакта',
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
          'name' => 'contactCodeCountry',
          'options' => array(
              'label' => 'Код страны',
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
          'name' => 'contactCodeCity',
          'options' => array(
              'label' => 'Код города',
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
          'name' => 'contactNumber',
          'options' => array(
              'label' => 'Номер',
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
                  'name' => 'contactNumberAdditional',
                  'options' => array(
                      'label' => 'Дополнительный номер',
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