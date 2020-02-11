<?php

namespace Developx\Tk\Tks;

/**
 * Class Jde
 */
class Jde extends TksBase
{
    /**
     * @var array Api methods
     */
    public $methods = [
        'calc' => 'https://api.jde.ru/vD/calculator/price',
        'points' => 'https://api.jde.ru/vD/jMeter/MstByKladr',
        'geo' => 'https://api.jde.ru/vD/geo/search',
        'schedule' => 'https://api.jde.ru/vD/geo/schedule'
    ];
    public $tkName = 'jde';
    public $tkTitle = 'ÆÄÅ';
    public $externalCode = 'JDE_ID';

    public function getPriceTime($cityTo, $cityFrom)
    {
        $options = $this->getCargoOptions();
        $result = $this->getData(
            $this->methods['calc'],
            [
                'from' => $cityFrom,
                'to' => $cityTo,
                'weight' => $options['length'],
                'volume' => $options['volume']
            ],
            'get'
        );
        $result = json_decode($result, true);
        return $this->preparePriceAndTime($result['price'], $result['mindays'] ? $result['mindays'] . ';' . $result['maxdays'] : false);
    }

    public function getAllPoints()
    {
        $points = $this->getData(
            $this->methods['geo'],
            [
                'mode' => '2',
            ],
            'get'
        );
        $points = json_decode($points, true);
        return $points;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points as $point) {
            $preparePoints[$point['city']]['CITY'] = $point['city'];
            $preparePoints[$point['city']]['EXTERNAL'] = $point['code'];

            $preparePoints[$point['city']]['POINTS'][] = [
                'LOC_ID' => false,
                'TK' => $this->tkName,
                'ADR' => $point['addr'],
                'PHONE' => '-',
                'WORK_TIME' => '-',
                'COORD' => $point['coords']['lat'] . ":" . $point['coords']['lng'],
                'TK_ID' => $this->tkName . $point['code']
            ];
        }
        return $preparePoints;
    }
}