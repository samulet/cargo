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

    public function fillOrg($form,$formData) {

        $result_array=array(''=>'Выберите аккаунт');
        foreach($formData as $key=>$value) {
            $result_array=$result_array+array($key=>$value);
        }
        if($form->has('currentOrg')) {
            $form->get('currentOrg')->setOptions(array("value_options"=>$result_array));
        }
        return $form;
    }
    public function fillCom($form,$formData) {

        $result_array=array(''=>'Выберите компанию');
        foreach($formData as $key=>$value) {
            $result_array=$result_array+array($key=>$value);
        }
        if($form->has('currentCom')) {
            $form->get('currentCom')->setOptions(array("value_options"=>$result_array));
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
    public function fillMultiFields($form, $formWay,$formWayDoc) {
        $resultArray=array();
        foreach ($form as $wayEl) {
            if( is_object($wayEl)) {
                $label=$wayEl->getLabel();
                if(!empty($label)) {
                    $resultArray=$resultArray+array($wayEl->getName().'_ticket'=>$label.' (Заявка)');
                }
            }

        }

        foreach($formWay as $wayEl) {




            if( is_object($wayEl)) {
                $label=$wayEl->getLabel();
                if(!empty($label)) {
                    $name=$wayEl->getName();
                    if( ($name=='timeLoadStart') || ($name=='timeLoadEnd') ) {
                        $label='Время загрузки '.$label;
                    }
                    if( ($name=='timeUnloadStart') || ($name=='timeUnloadEnd') ) {
                        $label='Время разгрузки '.$label;
                    }
                     $resultArray=$resultArray+array($name.'_ticketWay'=>$label.' (Груз)');
                }
            }
        }

        foreach($formWayDoc as $wayEl) {

            if( is_object($wayEl)) {
                $label=$wayEl->getLabel();
                if(!empty($label)) {
                    $resultArray=$resultArray+array($wayEl->getName().'_ticketWayDoc'=>$label.' (Документ)');
                }
            }
        }
        if($form->has('multiField')) {
            $form->get('multiField')->setOptions(array("value_options"=>$resultArray));
        }
        return $form;
    }
}