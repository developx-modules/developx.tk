<?
namespace Developx\Tk;

class Main
{
    protected static $_instance;

    public static $prices = [];
    public static $points = [];

    public function __construct($locId)
    {
        $data = new Data();
        $tkPriceTimeInfo = $data->getPrices($locId);
        $tkPoints = $data->getPoints($locId);

        foreach ($tkPoints as $key => $point){
            $point['TITLE'] = $tkPriceTimeInfo[$point['TK']]['TITLE'];
            $point['PRICE'] = round($tkPriceTimeInfo[$point['TK']]['PRICE']);
            $point['TIME'] = $tkPriceTimeInfo[$point['TK']]['TIME'];
            $point['TIME_FORMAT'] = $tkPriceTimeInfo[$point['TK']]['TIME_FORMAT'];
            $tkPoints[$key] = $point;
        }
        $this->points = $tkPoints;
        $this->prices = $tkPriceTimeInfo;
    }

    public static function getInstance($locId)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($locId);
        }
        return self::$_instance;
    }
}
?>