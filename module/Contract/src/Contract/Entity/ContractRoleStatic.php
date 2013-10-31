<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 11/1/13
 * Time: 1:08 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Contract\Entity;


class ContractRoleStatic {
    public static $role = array(
        'contractRoleCustomer' => array(
            'rusName' => 'Заказчик',
            'fieldset' => 'ContractRoleCustomerFieldset'
        ),
        'contractRoleLogist'=> array(
            'rusName' => 'Экспедитор',
            'fieldset' => 'ContractRoleLogistFieldset'
        ),
        'contractRoleOwner'=> array(
            'rusName' => 'Грузовладелец',
            'fieldset' => 'ContractRoleOwnerFieldset'
        ),
        'contractRolePayer'=> array(
            'rusName' => 'Плательщик',
            'fieldset' => 'ContractRolePayerFieldset'
        ),
    );
}