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
    public $tkTitle = 'Энергия';
    public $externalCode = 'ENERGY_ID';
    public $apiKeyCode = 'ENERGY_API_KEY';

    public function getPriceTime($cityTo, $cityFrom){
        $options = $this->getCargoOptions();
        $price = $this->getData(
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

        $price = json_decode($price, true);
        $time = preg_replace('/[^\d-]/', '', $price['transfer'][0]['interval']);
        $timeArr = explode('-', $time);
        if ($timeArr[1]){
            $time = implode(';', $timeArr);
        }
        return [
            "PRICE" => $price['transfer'][0]['price'],
            "TIME" => $time
        ];
    }

    public function getAllPoints(){
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
        foreach ($points['cityList'] as $point){
            $preparePoints[$point['name']]['CITY'] = $point['name'];
            $preparePoints[$point['name']]['EXTERNAL'] = $point['id'];
            foreach ($point['warehouses'] as $terminal) {
                $preparePoints[$point['name']]['POINTS'][] = [
                    'LOC_ID' => false,
                    'TK' => $this->tkName,
                    'ADR' => $terminal['address'],
                    'PHONE' => $terminal['phone'] ? $terminal['phone'] : '-',
                    'WORK_TIME' => $terminal['calcSchedule']['arrival'] ? $terminal['calcSchedule']['arrival'] : '-',
                    'COORD' => $terminal['latitude'].":".$terminal['longitude'],
                    'TK_ID' => $this->tkName.$terminal['id']
                ];
            }
        }
        return $preparePoints;
    }
}