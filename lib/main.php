<?
namespace Developx\Tk;

/**
 * Class Main
 */
class Main
{
    protected static $_instance;

    public static $prices = []; //массив с ценами
    public static $points = []; //массив с пунктами транспотрных компаний

    /**
     * Constructor
     *
     * @param integer $locId
     **/
    public function __construct($locId)
    {
        $data = new Data();
        self::$prices = $data->getPrices($locId);
        self::$points = $data->getPoints($locId);
    }

    /**
     * @param integer $locId
     * @return object
     **/
    public static function getInstance($locId)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($locId);
        }
        return self::$_instance;
    }
}
?>