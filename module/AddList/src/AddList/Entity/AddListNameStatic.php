<?php
/**
 * Created by JetBrains PhpStorm.
 * User: solov
 * Date: 7/30/13
 * Time: 7:56 PM
 * To change this template use File | Settings | File Templates.
 */
namespace AddList\Entity;
class AddListNameStatic
{
    public static $list = array(
        "veh-marks" => array(
            "name" => "Марки транспортных средств",
            "listName"=> "vehicle",
            "field" => "mark",
            "fieldRusName" => "Реквизиты" ,
            "child" => array(
                "veh-models" => array(
                    "name" => "Модели транспортных средств",
                    "listName"=> "vehicle",
                    "field" => "model",
                    "fieldRusName" => "Реквизиты" ,
                    "child" => null
                )
            )
        ),

        "veh-status" => array(
            "name" => "Статусы транспортных средств",
            "listName"=> "vehicle",
            "field" => "status",
            "fieldRusName" => "ТС - статус" ,
            "child" => null
        ),
        "veh-type" => array(
            "name" => "Типы транспортных средств",
            "listName"=> "vehicle",
            "field" => "type",
            "fieldRusName" => "ТС - тип" ,
            "child" => null
        ),
        "ownerships" => array(
            "name" => "Формы собственности компаний" ,
            "listName"=> "company",
            "field" => "property",
            "fieldRusName" => "Компания - Форма собственности" ,
            "child" => null
        ),
        "requisites" => array(
            "name" => "Реквизиты компаний",
            "listName"=> "company",
            "field" => "requisites",
            "fieldRusName" => "Компания - Реквизиты" ,
            "child" => null
        ),
        "prod-group" => array(
            "name" => "Продуктовая группа груза",
            "listName"=> "ticketWay",
            "field" => "cargoName",
            "fieldRusName" => "Заявка - груз" ,
            "child" => null
        ),
        "doc-type" => array(
            "name" => "Виды документов заявки",
            "listName"=> "ticketWay",
            "field" => "docType",
            "fieldRusName" => "Вид документа" ,
            "child" => null
        ),
        "load-type" => array(
            "name" => "Виды загрузки",
            "listName"=> "ticketWay",
            "field" => "typeLoad",
            "fieldRusName" => "Заявка - тип загрузки" ,
            "child" => null
        ),
        "offer-status" => array(
            "name" => "Статусы предложений",
            "listName"=> "interactionNote",
            "field" => "status",
            "fieldRusName" => "Предложения - статус" ,
            "child" => null
        ),
        "temp-cond" => array(
            "name" => "Температурные режимы",
            "listName"=> "ticketWay",
            "field" => "temperature",
            "fieldRusName" => "Заявка - температурный режим" ,
            "child" => null
        ),
     /*   "country" => "Страны",
        "regions" => "Регионы",
        "city" => "Города" 	,
        "address" => "Адреса" */
    );

}