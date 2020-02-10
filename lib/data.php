<?
namespace Developx\Tk;

use Developx\Tk\Tks\Dellin;
use Developx\Tk\Tks\Energy;
use Developx\Tk\Tks\Pec;
use Developx\Tk\Tks\Sdek;
use Developx\Tk\Tks\Jde;

/**
 * Class Data
 */
class Data
{
    /**
     * @param integer $locId
     * @return array
     **/
    public function getPrices($locId){
        $tks = [
            'pec' => new Pec(),
            'sdek' => new Sdek(),
            'jde' => new Jde(),
            'dellin' => new Dellin(),
            'energy' => new Energy()
        ];
        $dbCachedData = Dbmethods::getCachedData($locId);
        $cityTo = $this->getLocationById($locId);
        $cityFrom = $this->getCityFrom();

        $arResult = [];
        foreach ($tks as $tk){
            if (
                empty($cityFrom['EXTERNAL'][$tk->externalCode]) ||
                empty($cityTo['EXTERNAL'][$tk->externalCode])
            ){
                continue;
            }

            if (empty($dbCachedData[$tk->tkName])) {
                $tkResult = $tk->getPriceTime($cityTo['EXTERNAL'][$tk->externalCode], $cityFrom['EXTERNAL'][$tk->externalCode]);
                if ($this->checkPriceAndTime($tkResult)) {
                    Dbmethods::cacheData([
                        'LOC_ID' => $locId,
                        'TK' => $tk->tkName,
                        'TIME' => $tkResult['TIME'],
                        'PRICE' => $tkResult['PRICE']
                    ]);
                }
            } else {
                $tkResult = $dbCachedData[$tk->tkName];
            }

            if ($this->checkPriceAndTime($tkResult)) {
                $arResult[$tk->tkName] = [
                    'CODE' => $tk->tkName,
                    'TITLE' => $tk->tkTitle,
                    'PRICE' => $tkResult['PRICE'],
                    'TIME' => $tkResult['TIME'],
                    'TIME_FORMAT' => $this->formatTime($tkResult['TIME'])
                ];
            }
        }
        return $arResult;
    }

    /**
     * @param integer $locId
     * @return array
     **/
    public function getPoints($locId){
        $points = Dbmethods::getPointByLoc($locId);
        foreach ($points as $key => $point) {
            $coords = explode(':', $point['COORD']);
            $points[$key]['GPS_N'] = $coords[0];
            $points[$key]['GPS_S'] = $coords[1];
        }
        return $points;
    }

    /**
     * @param array $result
     * @return boolean
     **/
    private function checkPriceAndTime($result)
    {
        if (!empty($result['PRICE']) && !empty($result['TIME'])) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     **/
    private function getCityFrom()
    {
        $cityFromId = \COption::GetOptionString('sale','location',false);
        return $this->getLocationById($cityFromId, true);
    }

    /**
     * @return array
     **/
    public function getLocations()
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => array(
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                'TYPE_ID' => $this->arOptions['CITY_TYPE_ID'],
            ),
            'select' => array(
                '*',
                'EXTERNAL.*',
                'EXTERNAL.SERVICE.CODE',
                'LOC_ID' => 'ID',
                'LOC_CODE' => 'CODE',
                'LOC_NAME' => 'NAME.NAME',
            ),
            'order' => array(
                'LOC_NAME' => 'asc'
            )
        ));
        $arResult = [];
        while ($item = $res->fetch())
        {
            $item['EXTERNAL'][$item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_CODE']] = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
            if (empty($arResult[$item['ID']])) {
                $arResult[$item['ID']] = $item;
            } else {
                $arResult[$item['ID']]['EXTERNAL'][$item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_CODE']] = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
            }
        }
        return $arResult;
    }

    /**
     * @param integer $id
     * @param boolean $code
     * @return array
     **/
    public function getLocationById($id, $code = false)
    {
        $filter['=NAME.LANGUAGE_ID'] = LANGUAGE_ID;
        if ($code) {
            $filter['CODE'] = $id;
        } else {
            $filter['ID'] = $id;
        }
        $res = \Bitrix\Sale\Location\LocationTable::getList(array(
            'filter' => $filter,
            'select' => [
                '*',
                'EXTERNAL.*',
                'EXTERNAL.SERVICE.CODE',
                'LOC_ID' => 'ID',
                'LOC_CODE' => 'CODE',
                'LOC_NAME' => 'NAME.NAME',
            ]
        ));
        $arResult = [];
        while ($item = $res->fetch())
        {
            $item['EXTERNAL'][$item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_CODE']] = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
            if (empty($arResult)) {
                $arResult = $item;
            } else {
                $arResult['EXTERNAL'][$item['SALE_LOCATION_LOCATION_EXTERNAL_SERVICE_CODE']] = $item['SALE_LOCATION_LOCATION_EXTERNAL_XML_ID'];
            }
        }
        return $arResult;
    }



    /**
     * @param string $time
     * @return string
     **/
    private function formatTime($time)
    {
        if (strpos($time, ';') != false) {
            $times = explode(';', $time);
            $result = $times[0] . ' - ' . $times[1] . ' ' . $this->declension($times[1], ['день', 'дня', 'дней']);
        }else{
            $result = $time . ' ' . $this->declension($time, ['день', 'дня', 'дней']);
        }
        return $result;
    }

    /**
     * @param string $digit
     * @param array $expr
     * @param boolean $onlyword
     * @return string
     **/
    private function declension($digit, $expr, $onlyword = true)
    {
        if (!is_array($expr)) {
            $expr = array_filter(explode(' ', $expr));
        }
        if (empty($expr[2])) {
            $expr[2] = $expr[1];
        }
        $i = preg_replace('/[^0-9]+/s', '', $digit) % 100;
        if ($onlyword) {
            $digit = '';
        }
        if ($i >= 5 && $i <= 20) {
            $res = $digit . ' ' . $expr[2];
        } else {
            $i %= 10;
            if($i == 1){
                $res = $digit . ' ' . $expr[0];
            } elseif($i >= 2 && $i <= 4) {
                $res = $digit . ' ' . $expr[1];
            } else {
                $res = $digit . ' ' . $expr[2];
            }
        }
        return trim($res);
    }
}
?>