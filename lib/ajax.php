<?
namespace Developx\Tk;

use Developx\Tk\Tks\Pec;
use Developx\Tk\Tks\Sdek;
use Developx\Tk\Tks\Jde;
use Developx\Tk\Tks\Energy;
use Developx\Tk\Tks\Dellin;
use Developx\Tk\Dbmethods;

\Bitrix\Main\Loader::includeModule("iblock");

class Ajax
{
    public function getAjaxAction($action){
        if ($action == 'getPecPoints'){
            $result = self::getPecPoints();
        }elseif($action == 'getSdekPoints'){
            $result = self::getSdekPoints();
        }elseif($action == 'getJdePoints'){
            $result = self::getJdePoints();
        }elseif($action == 'getEnergyPoints'){
            $result = self::getEnergyPoints();
        }elseif($action == 'getDellinPoints'){
            $result = self::getDellinPoints();
        }elseif($action == 'clearPoints'){
            $result = self::clearPoints();
        }elseif($action == 'clearPrices'){
            $result = self::clearPrices();
        }
        echo $result;
    }

    private function getPecPoints(){
        $tkPrice = new Pec();
        return $tkPrice->initAutoAddPointsData();
    }

    private function getSdekPoints(){
        $tkPrice = new Sdek();
        return $tkPrice->initAutoAddPointsData();
    }

    private function getJdePoints(){
        $tkPrice = new Jde();
        return $tkPrice->initAutoAddPointsData();
    }

    private function getEnergyPoints(){
        $tkPrice = new Energy();
        return $tkPrice->initAutoAddPointsData();
    }

    private function getDellinPoints(){
        $tkPrice = new Dellin();
        return $tkPrice->initAutoAddPointsData();
    }

    private function clearPoints()
    {
        Dbmethods::clearPoints();
        return 'Success';
    }

    private function clearPrices()
    {
        Dbmethods::clearInfo();
        return 'Success';
    }
}
?>