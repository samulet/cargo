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
     /*   "ownerships" => array(
            "name" => "Формы собственности компаний" ,
            "listName"=> "company",
            "field" => "property",
            "fieldRusName" => "Компания Форма собственности" ,
            'parentId' => null,
            "child" => null
        ), */
        "requisites" => array(
            "name" => "Реквизиты компаний",
            "listName"=> "company",
            "field" => "requisites",
            "fieldRusName" => "Компания - Реквизиты" ,
            'parentId' => null,
            "child" => null
        ),
        "companyAddressType" => array(
            "name" => "Вид адреса",
            "listName"=> "company",
            "field" => "companyAddressType",
            "fieldRusName" => "Компания - Вид адреса" ,
            'parentId' => null,
            "child" => null
        ),

        "companyFounderType" => array(
            "name" => "Вид учредителя",
            "listName"=> "company",
            "field" => "companyFounderType",
            "fieldRusName" => "Компания - Вид учредителя" ,
            'parentId' => null,
            "child" => null
        ),

        "companyCompanyContactType" => array(
            "name" => "Вид контакта",
            "listName"=> "company",
            "field" => "companyCompanyContactType",
            "fieldRusName" => "Компания - Вид контакта" ,
            'parentId' => null,
            "child" => null
        ),

        "companyApplicantsType" => array(
            "name" => "Вид заявителя",
            "listName"=> "company",
            "field" => "companyApplicantsType",
            "fieldRusName" => "Компания - Вид заявителя" ,
            'parentId' => null,
            "child" => null
        ),

        "companyDocumentType" => array(
            "name" => "Наименование документа",
            "listName"=> "company",
            "field" => "companyDocumentType",
            "fieldRusName" => "Компания - Наименование документа" ,
            'parentId' => null,
            "child" => null
        ),
        "companyAuthorizedPersonType" => array(
            "name" => "Уполномоченные лица",
            "listName"=> "company",
            "field" => "companyAuthorizedPersonType",
            "fieldRusName" => "Компания - Уполномоченные лица" ,
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

     /*   "country" => "Страны",
        "regions" => "Регионы",
        "city" => "Города" 	,
        "address" => "Адреса" */
    );

}