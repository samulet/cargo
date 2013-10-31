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
            'rusName' => 'Заказчик'
        ),
        'contractRoleLogist'=> array(
            'rusName' => 'Экспедитор'
        ),
        'contractRoleOwner'=> array(
            'rusName' => 'Грузовладелец'
        ),
        'contractRolePayer'=> array(
            'rusName' => 'Плательщик'
        ),
    );
}