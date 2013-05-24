<?php
namespace AddList\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class AddListNameForm
{

    public function fillParentFrom($form,$formData) {

        $result_array=array('empty'=>'Нет родителя');
        foreach($formData as $el) {
           $result_array=$result_array+array($el['id']=>$el['fieldRusName'].' - '.$el['field']);
        }
        $form->get('parentId')->setOptions(array("value_options"=>$result_array));
        return $form;
    }
}