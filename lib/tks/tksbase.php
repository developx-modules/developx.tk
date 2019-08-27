<?php
namespace Developx\Tk\Tks;

use Developx\Tk\Dbmethods;
use Developx\Tk\Data;
use Developx\Tk\Options;

abstract class TksBase
{
    abstract function getPriceTime($cityName, $options, $cityFrom);

    /*
     * Класы для обновления пунктов самовывоза в бд
     * */
    abstract function preparePoints($points);
    abstract function getAllPoints();

    public $moduleName = 'developx.tk';

    public function getData($link, $fields = false, $type = 'post', $headers = false)
    {
        $ch = curl_init();
        if ($type == 'json'){
            $fields = json_encode($fields);
        }
        if ($type == 'get' && $fields){
            $link .= '?'.http_build_query($fields);
        }
        curl_setopt($ch, CURLOPT_URL, $link);
        if ($type == 'post' || $type == 'json') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }

        if ($type == 'json') {
            $headers[] = 'Content-Type: application/json ; charset=utf-8';
            $headers[] = 'Content-Length: ' . strlen($fields);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $string = curl_exec($ch);
        if(curl_exec($ch) === false){
            echo 'Ошибка curl: ' . curl_error($ch);
        }
        curl_close($ch);
        return $string;
    }

    public function initAutoAddPointsData()
    {
        $exCode = $this->getLocationExternal();
        $pointsData = $this->getAllPoints();
        $pointsPreared = $this->preparePoints($pointsData);



        $pointsResult = [];

        $tk = new Data();
        $locations = $tk->getLocations();
        foreach ($locations as $loc){
            $locations_[$loc['LOC_NAME']] = $loc['LOC_ID'];
        }
        $finded = 0;
        $notFind = '';
        foreach ($pointsPreared as $item){
            $pointLocation = $locations_[$item['CITY']];
            if (!empty($pointLocation)){
                foreach ($item['POINTS'] as $point){
                    $point['LOC_ID'] = $pointLocation;
                    $pointsResult[] = $point;
                }
                $finded++;
                $this->setLocationExternal($locations[$pointLocation], $item['EXTERNAL'], $exCode);
            }else{
                $notFind .= $item['CITY'] . ', ';
            }
        }
        $notFind = substr($notFind,0,-2);

        $this->updatePointsDB($pointsResult);

        $result = '';
        $result .= 'Количество городов '.$this->tkName.' - '.count($pointsPreared).'<br>';
        $result .= 'Количество городов на сайте - '.count($locations).'<br>';
        $result .= 'Количество найденных городов '.$this->tkName.' - '.$finded.'<br>';
        $result .= 'Не найденые города ('.$notFind.')<br>';
        $result .= 'Количество добавленных терминалов - '.count($pointsResult).'<br>';
        return $result;
    }

    public function updatePointsDB($pointsArray)
    {
        foreach ($pointsArray as $point) {
            if ($id = Dbmethods::getPoint($point)){
                Dbmethods::updatePoint($id, $point);
            }else{
                Dbmethods::addPoint($point);
            }
        }
    }

    public function getLocationExternal()
    {
        $code = $this->externalCode;
        $res = \Bitrix\Sale\Location\ExternalServiceTable::getList(array(
            'select' => array(
                '*',
            )
        ));
        while($item = $res->fetch())
        {
            $exServiceList[$item['CODE']] = $item['ID'];
        }
        if (empty($exServiceList[$code])){
            $res = \Bitrix\Sale\Location\ExternalServiceTable::add(array(
                'CODE' => $this->externalCode,
            ));
            if($res->isSuccess())
            {
                return $res->getId();
            }else
            {
                print_r($res->getErrorMessages());
            }
        }else{
            return $exServiceList[$code];
        }
    }

    public function setLocationExternal($location, $val, $exId)
    {
        if (empty($location['EXTERNAL'][$this->externalCode]) && !empty($val)){
            \Bitrix\Sale\Location\LocationTable::update(
                $location['LOC_ID'],
                array(
                    'SORT' => 100,
                    'EXTERNAL' => array(
                        array(
                            'SERVICE_ID' => $exId,
                            'XML_ID' => $val
                        ),
                    ),
                )
            );
        }
    }


}