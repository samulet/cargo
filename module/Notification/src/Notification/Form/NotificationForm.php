<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 5/30/13
 * Time: 12:21 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Notification\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class NotificationForm
{

    public function fillNotification($form, $formData)
    {
        $result_array = array('' => 'Выберите то, что хотите предложить');
        foreach ($formData as $data) {
            $result_array = $result_array + array($data['res']['id'] => $data['res']['uuid']);
        }

        $form->get('sendItemId')->setOptions(array("value_options" => $result_array));
        return $form;
    }
}