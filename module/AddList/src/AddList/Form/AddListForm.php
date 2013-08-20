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
            if($form->has($key)) {
                $attr=$form->get($key)->getAttributes();
                if(!empty($attr['type'])) {
                    if($attr['type']=='multi_checkbox') {
                        unset($result_array['']);
                    }
                }
                $form->get($key)->setOptions(array("value_options"=>$result_array));
            }

        }

        return $form;
    }

    public function fillCG($form,$formData) {

        $result_array=array(''=>'Выберите ТС');
        foreach($formData as $data) {
            $result_array=$result_array+array($data['id']=>$data['carNumber'].' '.$data['mark'] . ' / ' . $data['model']);
        }
        if($form->has('tsId')) {
            $form->get('tsId')->setOptions(array("value_options"=>$result_array));
        }
        return $form;
    }

    public function fillTS($form,$formData) {

        $result_array=array(''=>'Выберите ТС');
        foreach($formData as $data) {
            $result_array=$result_array+array($data['id']=>$data['carNumber'].' '.$data['mark'] . ' / ' . $data['model']);
        }
        if($form->has('tsId')) {
            $form->get('tsId')->setOptions(array("value_options"=>$result_array));
        }
        return $form;
    }

    public function fillCargoOwner($form,$formData) {

        $result_array=array(''=>'Выберите Грузовладельца');
        foreach($formData as $key =>$value) {
            $result_array=$result_array+array($key=>$value);
        }
        if($form->has('cargoOwner')) {
            $form->get('cargoOwner')->setOptions(array("value_options"=>$result_array));
        }
        return $form;
    }

    public function fillFromVehicleSpecial($form,$formData,$elements) {
            foreach($formData as $key => $element) {

                if(is_int(array_search($key,$elements,true)) ) {

                    $result_array=array(''=>'Выберите значение');
                    $attr=$form->get($key)->getAttributes();
                    if(!empty($attr['type'])) {
                        if($attr['type']=='multi_checkbox') {

                            unset($result_array['']);
                        }
                    }
                    foreach($element as $el) {
                        $result_array=$result_array+array($el['key']=>$el['value']);
                    }
                    $form->get($key)->setOptions(array("value_options"=>$result_array));
                    if($key=='typeLoad') {
                        $form->get('typeUnload')->setOptions(array("value_options"=>$result_array));
                    }

                }

            }
        return $form;
    }
}