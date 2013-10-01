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

class CompanyAddressFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($orgListId = null)
    {
        parent::__construct();
        // $this->setHydrator(new DoctrineHydrator());
        //$sm = $this->getFormFactory()->getFormElementManager()->getServiceLocator();

        $this->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
        //   $addListModel = new AddListModel();


        //    $formData=$addListModel->returnDataArray(array(),'company',$orgListId);
        //   $fillFrom=new AddListForm();
        //   die(var_dump($orgListId));
        //"value_options" => $fillFrom->getSelectValueList($formData,'companyAddressType')
        //Адреса (Вид адреса, почтовый индекс, субъект РФ, город, населенный пункт, улица, номер дома, корпус, квартира)

        $this->add(
            array(
                'name' => 'companyAddressType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Вид адреса',
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
                'name' => 'addressIndex',
                'options' => array(
                    'label' => 'Почтовый индекс',
                    'label_attributes' => array(
                        'class'  => 'control-label'
                    ),
                ),
                'attributes' => array (
                    'class' => 'form-control'
                )
            )
        );
        $this->add(array(
            'name' => 'addressSubject',
            'options' => array(
                'label' => 'Субъект РФ',
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
                'name' => 'addressCity',
                'options' => array(
                    'label' => 'Город',
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
                'name' => 'addressTown',
                'options' => array(
                    'label' => 'Населенный пункт',
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
                'name' => 'addressStreet',
                'options' => array(
                    'label' => 'Улица',
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
                'name' => 'addressHouse',
                'options' => array(
                    'label' => 'Номер дома',
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
                'name' => 'addressPart',
                'options' => array(
                    'label' => 'Корпус',
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
                'name' => 'addressRoom',
                'options' => array(
                    'label' => 'Квартира',
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
                'name' => 'companyAddressDelete',
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