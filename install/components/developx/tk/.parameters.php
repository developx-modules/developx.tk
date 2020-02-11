<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("developx.tk"))
    return;

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "CITY_DEFAULT" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("DX_TK_PARAM_DEFAULT_CITY"),
            "TYPE" => "INTEGER",
            "DEFAULT" => "",
        ),
        "YANDEX_API_KEY" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("DX_TK_PARAM_YANDEX_API_KEY"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
    ),

);
?>
