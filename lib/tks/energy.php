<?php

namespace Developx\Tk\Tks;

use Developx\Tk\Options;

/**
 * Class Energy
 */
class Energy extends TksBase
{
    /**
     * @var array Api methods
     */
    public $methods = [
        'citys' => 'https://mainapi.nrg-tk.ru/v3/cities',
        'price' => 'https://mainapi.nrg-tk.ru/v3/price',
    ];
    public $tkName = 'energy';
    public $tkTitle = '�������';
    public $externalCode = 'ENERGY_ID';
    public $apiKeyCode = 'ENERGY_API_KEY';

    public function getPriceTime($cityTo, $cityFrom)
    {
        $options = $this->getCargoOptions();
        $result = $this->getData(
            $this->methods['price'],
            json_encode([
                "idCityFrom" => (int)$cityFrom,
                "idCityTo" => (int)$cityTo,
                "cover" => 0,
                "idCurrency" => 0,
                "items" => [
                    [
                        "weight" => floatval($options['weight']),
                        "width" => floatval($options['wide']),
                        "height" => floatval($options['height']),
                        "length" => floatval($options['length'])
                    ]
                ],
                "declaredCargoPrice" => 0,
                "idClient" => 0
            ]),
            'post',
            [
                'NrgApi-DevToken: ' . $this->getApiKey()
            ]
        );

        $result = json_decode($result, true);
        if (isset($result['transfer'][0]['interval'])) {
            $time = preg_replace('/[^\d-]/', '', $result['transfer'][0]['interval']);
            $timeArr = explode('-', $time);
            if ($timeArr[1]) {
                $time = implode(';', $timeArr);
            }
        } else {
            $time = false;
        }
        return $this->preparePriceAndTime($result['transfer'][0]['price'], $time);
    }

    public function getAllPoints()
    {
        $citys = $this->getData(
            $this->methods['citys'],
            false,
            'get',
            [
                'NrgApi-DevToken: ' . $this->getApiKey(),
            ]
        );
        $citys = json_decode($citys, true);
        return $citys;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points['cityList'] as $point) {
            $preparePoints[$point['name']]['CITY'] = $point['name'];
            $preparePoints[$point['name']]['EXTERNAL'] = $point['id'];
            foreach ($point['warehouses'] as $terminal) {
                $preparePoints[$point['name']]['POINTS'][] = [
                    'LOC_ID' => false,
                    'TK' => $this->tkName,
                    'ADR' => $terminal['address'],
                    'PHONE' => $terminal['phone'] ? $terminal['phone'] : '-',
                    'WORK_TIME' => $terminal['calcSchedule']['arrival'] ? $terminal['calcSchedule']['arrival'] : '-',
                    'COORD' => $terminal['latitude'] . ":" . $terminal['longitude'],
                    'TK_ID' => $this->tkName . $terminal['id']
                ];
            }
        }
        return $preparePoints;
    }
}