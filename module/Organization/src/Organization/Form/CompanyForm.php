<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/29/13
 * Time: 11:19 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Organization\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CompanyForm
{

    public function fillFrom($form,$formData,$elements) {
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