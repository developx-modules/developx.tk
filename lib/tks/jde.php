<?php
namespace Developx\Tk\Tks;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentOutOfRangeException;

class Jde extends TksBase
{
    public $methods = [
        'calc' => 'https://api.jde.ru/vD/calculator/price',
        'points' => 'https://api.jde.ru/vD/jMeter/MstByKladr',
        'geo' => 'https://api.jde.ru/vD/geo/search',
        'schedule' => 'https://api.jde.ru/vD/geo/schedule'
    ];
    public $tkName = 'jde';
    public $externalCode = 'JDE_ID';
    public $tkTitle = 'ЖелДорЭкспедиция';

    public function getPriceTime($cityTo, $options, $cityFrom){

        $price = $this->getData(
            $this->methods['calc'],
            [
                'from' => $cityFrom,
                'to' => $cityTo,
                'weight' => $options['length'],
                'volume' => $options['volume']
            ],
            'get'
        );
        $price = json_decode($price,true);
        return [
            "PRICE" => $price['price'],
            "TIME" => $price['mindays'].';'.$price['maxdays']
        ];
    }

    public function getSchedule($code)
    {
        $points = $this->getData(
            $this->methods['schedule'],
            [
                'code' => $code,
            ],
            'get'
        );
        $points = json_decode($points,true);
        return $points;
    }

    public function getAllPoints(){
        $points = $this->getData(
            $this->methods['geo'],
            [
                'mode' => '2',
            ],
            'get'
        );
        $points = json_decode($points,true);
        return $points;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points as $point){
            $preparePoints[$point['city']]['CITY'] = $point['city'];
            $preparePoints[$point['city']]['EXTERNAL'] = $point['code'];

            $preparePoints[$point['city']]['POINTS'][] = [
                'LOC_ID' => false,
                'TK' => $this->tkName,
                'ADR' => $point['addr'],
                'PHONE' => '-',
                'WORK_TIME' => '-',
                'COORD' => $point['coords']['lat'].":".$point['coords']['lng'],
                'TK_ID' => $this->tkName.$point['code']
            ];
        }
        return $preparePoints;
    }
}