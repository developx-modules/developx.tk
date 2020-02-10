<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Developx\Tk\Tkmain;
use Bitrix\Main\Context;

Loader::includeModule('developx.tk');

class DevelopxTkComponent extends \CBitrixComponent
{
    private $tkDataObj;
    private function getRequest()
    {
        return Context::getCurrent()->getRequest();
    }

    private function getPoints()
    {
        return $this->tkDataObj->getPoints($this->arResult['CITY_ID']);
    }

    private function getPrices()
    {
        return $this->tkDataObj->getPrices($this->arResult['CITY_ID']);
    }

    private function getCurrentLocation()
    {
        return $this->tkDataObj->getLocationById($this->arResult['CITY_ID']);
    }

    private function getLocations()
    {
        return $this->tkDataObj->getLocations();
    }


    public function executeComponent()
    {
        $request = $this->getRequest();
        if (!empty($request['CITY_ID'])){
            $this->arResult['CITY_ID'] = $request['CITY_ID'];
        }else{
            $this->arResult['CITY_ID'] = $this->arParams['CITY_DEFAULT'];
        }
        if (isset($this->arResult['CITY_ID'])) {
            $this->tkDataObj = new Developx\Tk\Data();
            $this->arResult['POINTS'] = $this->getPoints();
            $this->arResult['PRICES'] = $this->getPrices();
            $this->arResult['CURRENT_LOCATION'] = $this->getCurrentLocation();
            $this->arResult['LOCATIONS'] = $this->getLocations();
        }
        $this->includeComponentTemplate();
    }
}
