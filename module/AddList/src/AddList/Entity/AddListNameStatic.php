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
            "fieldRusName" => "Марка ТС" ,
            'parentId' => null,
            "child" => array(
                "veh-models" => array(
                    "name" => "Модели транспортных средств",
                    "listName"=> "vehicle",
                    "field" => "model",
                    "fieldRusName" => "Модель ТС" ,
                    'parentId' => 'veh-marks',
                    "child" => null
                )
            )
        ),

        "veh-status" => array(
            "name" => "Статусы транспортных средств",
            "listName"=> "vehicle",
            "field" => "status",
            "fieldRusName" => "ТС статус" ,
            'parentId' => null,
            "child" => null
        ),
        "veh-type" => array(
            "name" => "Типы транспортных средств",
            "listName"=> "vehicle",
            "field" => "type",
            "fieldRusName" => "ТС тип" ,
            'parentId' => null,
            "child" => null
        ),
        "ownerships" => array(
            "name" => "Формы собственности компаний" ,
            "listName"=> "company",
            "field" => "property",
            "fieldRusName" => "Компания Форма собственности" ,
            'parentId' => null,
            "child" => null
        ),
        "requisites" => array(
            "name" => "Реквизиты компаний",
            "listName"=> "company",
            "field" => "requisites",
            "fieldRusName" => "Компания - Реквизиты" ,
            'parentId' => null,
            "child" => null
        ),
        "prod-group" => array(
            "name" => "Продуктовая группа груза",
            "listName"=> "ticketWay",
            "field" => "cargoName",
            "fieldRusName" => "Заявка - груз" ,
            'parentId' => null,
            "child" => null
        ),
        "doc-type" => array(
            "name" => "Виды документов заявки",
            "listName"=> "ticketWay",
            "field" => "docType",
            "fieldRusName" => "Вид документа" ,
            'parentId' => null,
            "child" => null
        ),
        "load-type" => array(
            "name" => "Виды загрузки",
            "listName"=> "ticketWay",
            "field" => "typeLoad",
            "fieldRusName" => "Заявка - тип загрузки" ,
            'parentId' => null,
            "child" => null
        ),
        "offer-status" => array(
            "name" => "Статусы предложений",
            "listName"=> "interactionNote",
            "field" => "status",
            "fieldRusName" => "Предложения - статус" ,
            'parentId' => null,
            "child" => null
        ),
        "temp-cond" => array(
            "name" => "Температурные режимы",
            "listName"=> "ticketWay",
            "field" => "temperature",
            "fieldRusName" => "Заявка температурный режим" ,
            'parentId' => null,
            "child" => null
        ),
        "tick-always"=> array(
            "name" => "Постоянно",
            "listName"=> "ticketWay",
            "field" => "always",
            "fieldRusName" => "Заявка Постоянно (загрузка)" ,
            'parentId' => null,
            "child" => null
        ),
        "tick-prepare"=> array(
            "name" => "Готов к загрузке",
            "listName"=> "ticketWay",
            "field" => "prepareToLoad",
            "fieldRusName" => "Заявка Постоянно (загрузка)" ,
            'parentId' => null,
            "child" => null
        ),

     /*   "country" => "Страны",
        "regions" => "Регионы",
        "city" => "Города" 	,
        "address" => "Адреса" */
    );

}