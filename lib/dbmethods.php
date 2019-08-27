<?
namespace Developx\Tk;
use Developx\Tk\DB\DeliveryInfoTable;
use Developx\Tk\DB\PointsTable;

class Dbmethods
{
    public static function addPoint($data)
    {
        $result = PointsTable::add($data);

        if ($result->isSuccess())
        {
            return $result->getId();
        }
        return false;
    }

    public static function getPoint($data)
    {
        $result = PointsTable::getList(array(
            'select' => array('ID'),
            'filter' => array("TK_ID" => $data['TK_ID']),
        ));
        if ($point = $result->fetch())
        {
            return $point;
        }
        return false;
    }

    public static function getPointByLoc($locId)
    {
        $arResult = [];
        $result = PointsTable::getList(array(
            'select' => array('*'),
            'filter' => array("LOC_ID" => $locId),
        ));
        while ($point = $result->fetch())
        {
            $arResult[] = $point;
        }
        return $arResult;
    }

    public static function updatePoint($id, $data)
    {
        PointsTable::update($id, $data);
    }

    public static function clearPoints()
    {
        $logs = PointsTable::getList(array(
            'select' => array('ID'),
        ));
        while ($log = $logs->fetch())
        {
            PointsTable ::delete($log["ID"]);
        }
    }

    public function getInfo($locId){
        $arResult = [];
        $result = DeliveryInfoTable::getList(array(
            'select' => array('*'),
            'filter' => array("LOC_ID" => $locId),
        ));
        while ($res = $result->fetch())
        {
            $arResult[$res['TK']] = $res;
        }
        return $arResult;
    }

    public function saveInfo($data){
        $result = DeliveryInfoTable::add($data);

        if ($result->isSuccess())
        {
            return $result->getId();
        }
        return false;
    }

    public static function clearInfo()
    {
        $logs = DeliveryInfoTable::getList(array(
            'select' => array('ID'),
        ));
        while ($log = $logs->fetch())
        {
            DeliveryInfoTable ::delete($log["ID"]);
        }
    }
}
?>