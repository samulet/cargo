<?php
namespace Resource\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class VehicleForm
{

    public function fillFrom($form,$formData,$elements) {
        foreach($elements as $element) {
            $result_array=array();
            foreach($formData[$element] as $el) {
                $result_array=$result_array+array($el['key']=>$el['value']);
            }
            $form->get($element)->setOptions(array("value_options"=>$result_array));
        }
        return $form;
    }
}