<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
use Developx\Tk;
Loader::includeModule("developx.tk");

$AjaxEvent = new Tk\Ajax();
$AjaxEvent->getAjaxAction($_GET['ACTION']);
?>