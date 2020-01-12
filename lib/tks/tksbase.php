<?php
namespace Developx\Tk\Tks;

use Developx\Tk\Dbmethods;
use Developx\Tk\Data;

/**
 * Class TksBase
 */
abstract class TksBase
{
    /**
     * @param string $cityName
     * @param array $options
     * @param string $cityFrom
     **/
    abstract function getPriceTime($cityName, $options, $cityFrom);

    /**
     * Prepare data from tk api for import
     *
     * @param array
     * @return array
     **/
    abstract function preparePoints($points);

    /**
     * All tk points from api
     *
     * @return array
    **/
    abstract function getAllPoints();

    /**
     * @param string $link
     * @param boolean $fields
     * @param string $type
     * @param boolean $headers
     * @return array
     **/
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
        $result = curl_exec($ch);
        if(curl_exec($ch) === false){
            echo 'Ошибка curl: ' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * @return string
     **/
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

    /**
     * @param array $pointsArray
     **/
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

    /**
     * Get location external or add new if not exist
     *
     * @return integer
     **/
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

    /**
     * @param array $location
     * @param string $val
     * @param integer $exId
     **/
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