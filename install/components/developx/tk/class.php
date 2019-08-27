<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Developx\Tk\Tkmain;
use Bitrix\Main\Context;
use Developx\Tk\Main;
use Developx\Tk\Data;

Loader::includeModule('developx.tk');

class DevelopxTk extends \CBitrixComponent
{
    private function getRequest()
    {
        return Context::getCurrent()->getRequest();
    }

    private function getData($locId)
    {
        $tkMain = Main::getInstance($locId);
        return $tkMain->points;
    }

    private function getLocation($locId)
    {
        $tkData = new Data();
        return $tkData->getLocationById($locId);
    }

    private function getLocations()
    {
        $tkData = new Data();
        return $tkData->getLocations();
    }

    public function executeComponent()
    {
        $request = $this->getRequest();
        $cityId = false;
        if (!empty($request['CITY_ID'])){
            $cityId = $request['CITY_ID'];
        }else{
            $cityId = $this->arParams['CITY_DEFAULT'];
        }
        if ($cityId) {
            $this->arResult['ITEMS'] = $this->getData($cityId);
            $this->arResult['LOCATION'] = $this->getLocation($cityId);
            $this->arResult['LOCATIONS'] = $this->getLocations();
            $this->includeComponentTemplate();
        }
    }
}
