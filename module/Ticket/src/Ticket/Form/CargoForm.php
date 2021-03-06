<?php
namespace Ticket\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class CargoForm
{

    public function fillFrom($form, $formData, $elements)
    {
        foreach ($formData as $key => $element) {
            $result_array = array('' => 'Выберите значение');
            foreach ($element as $el) {
                $result_array = $result_array + array($el['key'] => $el['value']);
            }
            $form->get($key)->setOptions(array("value_options" => $result_array));
        }

        return $form;
    }
}