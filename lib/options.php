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
    /** @var string */
    public static $moduleName = 'developx.tk';

    /**
     * @param string $keyCode
     * @return string
     **/
    public function getApiKey($keyCode)
    {
        return \COption::GetOptionString(self::$moduleName, $keyCode);
    }

    /**
     * @return float
     **/
    private function getWeight()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_WEIGHT');
        return $result;
    }

    /**
     * @return float
     **/
    private function getLength()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_LENGTH');
        return $result;
    }

    /**
     * @return float
     **/
    private function getWide()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_WIDE');
        return $result;
    }

    /**
     * @return float
     **/
    private function getHeight()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_HEIGHT');
        return $result;
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
}
?>