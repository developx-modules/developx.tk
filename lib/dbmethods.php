<?

namespace Developx\Tk;

use Developx\Tk\DB\DeliveryInfoTable;
use Developx\Tk\DB\PointsTable;

/**
 * Class Dbmethods
 */
class Dbmethods
{

    /**
     * @param array $data
     * @return integer
     **/
    public static function addPoint($data)
    {
        $result = PointsTable::add($data);

        if ($result->isSuccess()) {
            return $result->getId();
        }
        return false;
    }

    /**
     * @param array $data
     * @return array
     **/
    public static function getPoint($data)
    {
        $result = PointsTable::getList(array(
            'select' => array('ID'),
            'filter' => array("TK_ID" => $data['TK_ID']),
        ));
        if ($point = $result->fetch()) {
            return $point;
        }
        return false;
    }

    /**
     * @param integer $locId
     * @return array
     **/
    public static function getPointByLoc($locId)
    {
        $arResult = [];
        $result = PointsTable::getList(array(
            'select' => array('*'),
            'filter' => array("LOC_ID" => $locId),
        ));
        while ($point = $result->fetch()) {
            $arResult[] = $point;
        }
        return $arResult;
    }

    /**
     * @param integer $id
     * @param array $data
     **/
    public static function updatePoint($id, $data)
    {
        PointsTable::update($id, $data);
    }

    /**
     * Clear all points
     **/
    public static function clearPoints()
    {
        $logs = PointsTable::getList(array(
            'select' => array('ID'),
        ));
        while ($log = $logs->fetch()) {
            PointsTable::delete($log["ID"]);
        }
    }

    /**
     * Get cached transport companys for location
     *
     * @param integer $locId
     * @return array
     **/
    public function getCachedData($locId)
    {
        $arResult = [];
        $result = DeliveryInfoTable::getList(array(
            'select' => array('*'),
            'filter' => array("LOC_ID" => $locId),
        ));
        while ($res = $result->fetch()) {
            $arResult[$res['TK']] = $res;
        }
        return $arResult;
    }

    /**
     * @param array $data
     * @return integer
     **/
    public function cacheData($data)
    {
        $result = DeliveryInfoTable::add($data);

        if ($result->isSuccess()) {
            return $result->getId();
        }
        return false;
    }

    public static function clearInfo()
    {
        $logs = DeliveryInfoTable::getList(array(
            'select' => array('ID'),
        ));
        while ($log = $logs->fetch()) {
            DeliveryInfoTable::delete($log["ID"]);
        }
    }
}

?>