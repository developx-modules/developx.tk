<?php

namespace Developx\Tk\Tks;

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
    public $tkName = 'pec';
    public $tkTitle = 'ПЭК';
    public $externalCode = 'PEC_ID';
    public $apiKeyCode = 'PEC_KEY';

    public function getPriceTime($cityTo, $cityFrom)
    {
        $options = $this->getCargoOptions();
        $price = $this->getData(
            $this->methods['calc'],
            [
                'places' =>
                    [[$options['length'], $options['wide'], $options['height'], $options['volume'], $options['weight']]],
                'take' => ['town' => -$cityFrom],
                'deliver' => ['town' => -$cityTo]
            ],
            'get'
        );
        $price = json_decode($price, true);

        if (!empty($price['periods_days'])) {
            $period = (strpos($price['periods_days'], ' - ') !== false) ? str_replace(' - ', ';', $price['periods_days']) : $price['periods_days'];
        } else {
            $period = false;
        }
        return $this->preparePriceAndTime($price['autonegabarit'][2], $period);
    }

    public function getAllPoints()
    {
        $sdk = new \PecomKabinet('user', $this->getApiKey());
        $result = $sdk->call(
            'branches',
            'all',
            [],
            true
        );
        $sdk->close();
        return $result;
    }

    public function preparePoints($points)
    {
        $preparePoints = [];
        foreach ($points['branches'] as $terminal) {
            foreach ($terminal['divisions'] as $division) {

                foreach ($division['warehouses'] as $pt) {
                    $cityName = $terminal['title'];
                    unset($pt['divisionTimeOfWork']);
                    unset($pt['timeOfWork']);

                    if (BX_UTF == 'Y') {
                        if ($cityName == 'Москва Восток') {
                            $cityName = 'Москва';
                        }
                    } else {
                        if (iconv('UTF-8', 'windows-1251', $cityName) == 'Москва Восток') {
                            $cityName = iconv('windows-1251', 'UTF-8', 'Москва');
                        }
                    }
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