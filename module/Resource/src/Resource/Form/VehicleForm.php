<?php
namespace Resource\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class VehicleForm
{

    public function fillFrom($form,$formData,$elements) {
        if(empty($elements)) {
            foreach($formData as $key => $element) {
                $result_array=array(''=>'Выберите значение');
                foreach($element as $el) {
                    $result_array=$result_array+array($el['key']=>$el['value']);
                }
                if($form->get($key)) {
                    $form->get($key)->setOptions(array("value_options"=>$result_array));
                }

            }
        } else {
            foreach($formData as $key => $element) {
                if(is_int(array_search($key,$elements,true)) ) {

                    $result_array=array(''=>'Выберите значение');
                    foreach($element as $el) {
                        $result_array=$result_array+array($el['key']=>$el['value']);
                    }
                    $form->get($key)->setOptions(array("value_options"=>$result_array));
                    if($key=='typeLoad') {
                        $form->get('typeUnload')->setOptions(array("value_options"=>$result_array));
                    }

                }

            }

        }


        return $form;
    }
}