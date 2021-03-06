<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 12:21 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Resource\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ResourceForm
{

    public function fillFrom($form, $formData, $elements)
    {
        foreach ($formData as $key => $element) {
            $result_array = array('' => 'Выберите значение');
            foreach ($element as $el) {
                $result_array = $result_array + array($el['key'] => $el['value']);
            }
            if ($form->has($key)) {
                $form->get($key)->setOptions(array("value_options" => $result_array));
            }

        }

        return $form;
    }

    public function fillTS($form, $formData)
    {

        $result_array = array('' => 'Выберите ТС');
        foreach ($formData as $data) {
            $result_array = $result_array + array($data['id'] => $data['carNumber'] . ' ' . $data['mark'] . ' / ' . $data['model']);
        }

        $form->get('tsId')->setOptions(array("value_options" => $result_array));
        return $form;
    }
}