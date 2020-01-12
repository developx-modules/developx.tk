<?php
namespace Developx\Tk\Tks;
use Developx\Tk\Options;

/**
 * Class Dellin
 */
class Dellin extends TksBase
{
    /**
     * @var array Api methods
     */
    public $methods = [
        'terminals' => 'https://api.dellin.ru/v3/public/terminals.json',
        'price' => 'https://api.dellin.ru/v1/public/calculator.json',
        'find_city' => 'https://api.dellin.ru/v2/public/kladr.json',
    ];
    public $tkName = 'dellin';
    public $externalCode = 'DL_KLADR';
    public $apiKeyCode = 'DL_KEY';

    public function getPriceTime($kladrTo, $options, $cityFrom){

        $price = $this->getData(
            $this->methods['price'],
            [
                'appkey' => Options::getApiKey($this->apiKeyCode),
                'derivalPoint' => $cityFrom,
                'arrivalPoint' => $kladrTo,
                'sizedVolume' => $options['volume'],
                'sizedWeight' => $options['weight'],
                'arrivalDoor' => false,
                'derivalDoor' => false
            ],
            'json'
        );
        $price = json_decode($price, true);
        return [
            "PRICE" => $price['price'],
            "TIME" => $price['time']['value']
        ];
    }

    public function findCity($cityName){
        $cityArr = $this->getData(
            $this->methods['find_city'],
            [
                'appkey' => Options::getApiKey($this->apiKeyCode),
                'q' => $cityName,
                'limit' => 1
            ],
            'json'
        );
        return json_decode($cityArr['cities'][0]);
    }

    public function getAllPoints(){
        $terminals = $this->getData(
            $this->methods['terminals'],
            [
                'appkey' => Options::getApiKey($this->apiKeyCode)
            ],
            'json'
        );
        $terminals = json_decode($terminals, true);
        $terminals = file_get_contents($terminals['url']);
        $terminals = json_decode($terminals, true);
        return $terminals;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points['city'] as $point){
            $preparePoints[$point['name']]['CITY'] = $point['name'];
            $preparePoints[$point['name']]['EXTERNAL'] = $point['code'];
            foreach ($point['terminals']['terminal'] as $terminal) {
                if (!$terminal['receiveCargo']) continue;
                $preparePoints[$point['name']]['POINTS'][] = [
                    'LOC_ID' => false,
                    'TK' => $this->tkName,
                    'ADR' => $terminal['fullAddress'],
                    'PHONE' => $terminal['mainPhone'] ? $terminal['mainPhone'] : '-',
                    'WORK_TIME' => $terminal['calcSchedule']['arrival'] ? $terminal['calcSchedule']['arrival'] : '-',
                    'COORD' => $terminal['latitude'].":".$terminal['longitude'],
                    'TK_ID' => $this->tkName.$terminal['id']
                ];

            }
        }
        return $preparePoints;
    }
}