<?php
/**
 * Created by JetBrains PhpStorm.
 * User: salerat
 * Date: 10/30/13
 * Time: 11:42 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Contract\Entity;

class ContractTypeStatic {
    public static $type = array(
        'contractEcs' => array(
            'rusName' => 'Договор экспедиции',
            'fieldset' => 'ContractEcsFieldset'
        ),
        'contractTr'=> array(
            'rusName' => 'Договор перевозки',
            'fieldset' => 'ContractTrFieldset'
        ),
    );
}