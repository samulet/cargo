<?php
namespace AddList\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class AddListForm
{

    public function fillFrom($form,$formData) {
        foreach($formData as $key => $element) {
            $result_array=array(''=>'Выберите значение');
            foreach($element as $el) {
                $result_array=$result_array+array($el['key']=>$el['value']);
            }
            $form->get($key)->setOptions(array("value_options"=>$result_array));
        }

        return $form;
    }
}