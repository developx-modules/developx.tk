<?php
namespace Developx\Tk\Tks;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentOutOfRangeException;
use Developx\Tk\Options;

require_once('sdk-pec/pecom_kabinet.php');

/**
 * Class Pec
 */
class Pec extends TksBase
{
    /**
     * @var array Api methods
     */
    public $methods = [
        'calc' => 'http://calc.pecom.ru/bitrix/components/pecom/calc/ajax.php',
        'towns' => 'https://new.pecom.ru/ru/calc/towns.php',
    ];
    public $cityIdFrom = 463;
    public $tkName = 'pec';
    public $externalCode = 'PEC_ID';
    public $apiKeyCode = 'PEC_KEY';

    public function getPriceTime($cityTo, $options, $cityFrom){
        $price = $this->getData(
            $this->methods['calc'],
            [
                'places' =>
                    [ [$options['length'], $options['wide'], $options['height'], $options['volume'], $options['weight']] ],
                'take' => [ 'town' => -$cityFrom ],
                'deliver' => [ 'town' => -$cityTo ]
            ],
            'get'
        );
        $price = json_decode($price,true);
        $period = (strpos($price['periods_days'],' - ') !== false)?str_replace(' - ', ';',$price['periods_days']):$price['periods_days'];


        return [
            "PRICE" => $price['autonegabarit'][2],
            "TIME" => $period
        ];
    }

    public function getAllTowns(){
        $towns = $this->getData(
            $this->methods['towns'],
            [],
            'post'
        );
        $towns = json_decode($towns,1);
    }

    public function getPointByTitle ($title)
    {
        $sdk = new \PecomKabinet('user', Options::getApiKey($this->apiKeyCode));
        $result = $sdk->call(
            'branches',
            'findbytitle',
            ['title' => $title],
            true
        );
        $sdk->close();
        return $result;
    }

    public function getAllPoints()
    {
        // Создание экземпляра класса
        $sdk = new \PecomKabinet('user', Options::getApiKey($this->apiKeyCode));

        // Вызов метода
        $result = $sdk->call(
            'branches',
            'all',
            [],
            true
        );

        // Освобождение ресурсов
        $sdk->close();

        return $result;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points['branches'] as $terminal){
            /*if ($terminal['bitrixId'] == 463) {
                pre($terminal);
                die();
            }*/
            foreach ($terminal['divisions'] as $division) {

                foreach ($division['warehouses'] as $pt) {
                    $cityName = $terminal['title'];
                    unset($pt['divisionTimeOfWork']);
                    unset($pt['timeOfWork']);

                    if ($cityName == 'Москва Восток') $cityName = 'Москва';

                    if (empty($preparePoints[$cityName])) {
                        $preparePoints[$cityName]['CITY'] = $cityName;
                        $preparePoints[$cityName]['EXTERNAL'] = $terminal['bitrixId'];
                    }


                    $preparePoints[$cityName]['POINTS'][] = [
                        'LOC_ID' => false,
                        'TK' => $this->tkName,
                        'ADR' => $pt['address'],
                        'PHONE' => $pt['telephone'],
                        'WORK_TIME' => '-',
                        'COORD' => str_replace(',', ':', $pt['coordinates']),
                        'TK_ID' => $this->tkName . $pt['id']
                    ];

                }
            }
        }
        return $preparePoints;
    }
}