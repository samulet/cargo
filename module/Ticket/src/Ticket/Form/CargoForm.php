<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 9:38 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Ticket\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CargoForm
{
    public function fillFrom($form,$formData,$elements) {
        foreach($formData as $key => $element) {
            $result_array=array();
            foreach($element as $el) {
                $result_array=$result_array+array($el['key']=>$el['value']);
            }
            $form->get($key)->setOptions(array("value_options"=>$result_array));
        }

        return $form;
    }
}