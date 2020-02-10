<?
namespace Developx\Tk;

use Developx\Tk\Data;

/**
 * Class Options
 *
 * Get options from db
 */
class Options
{
    protected static $_instance;

    /** @var array */
    public $arOptions = [
        'CITY_TYPE_ID' => false,
        'DAYS_DOP' => false,
        'PRICE_DOP' => false,
        'DEF_WEIGHT' => false,
        'DEF_LENGTH' => false,
        'DEF_WIDE' => false,
        'DEF_HEIGHT' => false,
        'PEC_ACTIVE' => false,
        'PEC_KEY' => false,
        'CDEK_ACTIVE' => false,
        'DL_ACTIVE' => false,
        'DL_KEY' => false,
        'JDE_ACTIVE' => false,
        'ENERGY_ACTIVE' => false,
        'ENERGY_API_KEY' => false,
    ];

    /** @var string */
    public $moduleName = 'developx.tk';

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->time = time();
        foreach ($this->arOptions as $key => $prop){
            $this->arOptions[$key] = \COption::GetOptionString($this->moduleName, $key);
        }

        if (!($this->arOptions['DAYS_DOP'])) {
            $this->arOptions['DAYS_DOP'] = 0;
        }
        if (!($this->arOptions['PRICE_DOP'])) {
            $this->arOptions['PRICE_DOP'] = 0;
        }

        if (!($this->arOptions['DEF_WEIGHT'])) {
            $this->arOptions['DEF_WEIGHT'] = 1;
        }

        if (!($this->arOptions['DEF_LENGTH'])) {
            $this->arOptions['DEF_LENGTH'] = 0.2;
        }

        if (!($this->arOptions['DEF_WIDE'])) {
            $this->arOptions['DEF_WIDE'] = 0.2;
        }

        if (!($this->arOptions['DEF_HEIGHT'])) {
            $this->arOptions['DEF_HEIGHT'] = 0.2;
        }
    }

    /**
     * @param string $keyCode
     * @return string
     **/
    public function getApiKey($keyCode)
    {
        return $this->arOptions[$keyCode];
    }

    /**
     * @return float
     **/
    private function getWeight()
    {
        return $this->arOptions['DEF_WEIGHT'];
    }

    /**
     * @return float
     **/
    private function getLength()
    {
        return $this->arOptions['DEF_LENGTH'];
    }

    /**
     * @return float
     **/
    private function getWide()
    {
        return $this->arOptions['DEF_WIDE'];
    }

    /**
     * @return float
     **/
    private function getHeight()
    {
        return $this->arOptions['DEF_HEIGHT'];
    }

    /**
     * @return array
     **/
    public function getCargoOptions()
    {
        $result['weight'] = self::getWeight();
        $result['length'] = self::getLength();
        $result['wide'] = self::getWide();
        $result['height'] = self::getHeight();
        $result['volume'] = $result['length'] * $result['wide'] * $result['height'];
        return $result;
    }

    /**
     * @return integer
     **/
    public function getCityTypeId()
    {
        return $this->arOptions['CITY_TYPE_ID'];
    }
}
?>