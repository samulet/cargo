<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 8/15/13
 * Time: 9:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Excel\Entity;
class ExcelStatic
{
    public static $list = array(
        "main" => array(
            "created" => "Дата создания заявки",
            "updated" => "Дата обновления заявки",
            "currency" => "Валюта",
            "typeTicket" => "Тип заявки",
            "money" => "Стоимость",
            "formPay" => "Тип платежа",
            "type" => "Тип ТС",
            "owner" => "Заказчик",
        ),
        "way" => array(
            "cargoOwner" => "Владелец груза",
            "cargoName" => "Имя груза",
            "adr" => "ADR",
            "cubs" => "Кубы",
            "dimensionsLength" => "Размеры - длина",
            "dimensionsHeight" => "Размеры - высота",
            "dimensionsWidth" => "Размеры - ширина",
            "cargoValue" => "Объем груза",
            "weight" => "Вес",
            "rubles" => "Рубли",
            "pallet" => "Паллеты",
            "box" => "Коробки",
            "temperature" => "Температура",
            "airSuspension" => "Пневмоход",
            "areaLoad" => "Место загрузки",
            "areaUnload" => "Место разгрузки",
            "typeLoad" => "Тип загрузки",
            "typeUnload" => "Тип разгрузки",
            "note" => "Примечание",
            "dateStart" => "Дата загрузки",
            "timeStart" => "Время загрузки",
            "dateEnd" => "Дата разгрузки",
            "timeEnd" => "Время разгрузки",
            "documents" => "Список документов",



        ),
        "special" => array(
            "loadNumber" => "Номер загрузки",
        )

    );

}