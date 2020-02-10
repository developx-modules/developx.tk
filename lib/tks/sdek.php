<?php
namespace Developx\Tk\Tks;
require_once('sdk-sdek/CalculatePriceDeliveryCdek.php');

/**
 * Class Sdek
 */
class Sdek extends TksBase
{
    /**
     * @var array Api methods
     */
    public $methods = [
        'pvzlist' => 'https://integration.cdek.ru/pvzlist.php?type=ALL'
    ];
    public $tkName = 'sdek';
    public $tkTitle = 'СДЕК';
    public $externalCode = 'SDEK_ID';

    public function getPriceTime($cityTo, $cityFrom){
        $calc = new \CalculatePriceDeliveryCdek();
        $options = $this->getCargoOptions();
        $calc->setSenderCityId($cityFrom);
        $calc->setReceiverCityId($cityTo);
        $calc->setTariffId(10);
        $calc->addGoodsItemByVolume($options['weight'], round($options['volume'],2));

        if ($calc->calculate() === true) {
            $res = $calc->getResult();

            return [
                "PRICE" => $res['result']['price'],
                "TIME" => $res['result']['deliveryPeriodMin'].';'.$res['result']['deliveryPeriodMax']
            ];
        }
        return false;

    }

    public function getAllPoints (){
        $pvzList = $this->getData(
            $this->methods['pvzlist'],
            [],
            'get'
        );
        $xml = simplexml_load_string($pvzList);
        return $xml;
    }

    public function preparePoints($points){
        foreach($points as $key => $val){
            $cityCode = (string)$val['CityCode'];
            $type = (string)$val['Type'];
            if ($type != 'PVZ') continue;
            $city = (string)$val["City"];
            if(strpos($city,'(') !== false)
                $city = trim(substr($city,0,strpos($city,'(')));
            if(strpos($city,',') !== false)
                $city = trim(substr($city,0,strpos($city,',')));


            $preparePoints[$city]['CITY'] = $city;
            $preparePoints[$city]['EXTERNAL'] = $cityCode;

            $preparePoints[$city]['POINTS'][] = [
                'LOC_ID' => false,
                'TK' => $this->tkName,
                'ADR' => (string)$val['Address'],
                'PHONE' => (string)$val['Phone'],
                'WORK_TIME' => (string)$val['WorkTime'],
                'COORD' => (string)$val['coordY'].":".(string)$val['coordX'],
                'TK_ID' => $this->tkName.(string)$val['Code']
            ];
        }
        return $preparePoints;
    }

}