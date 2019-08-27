<?
namespace Developx\Tk;

use Developx\Tk\Data;

class Options
{
    public static $moduleName = 'developx.tk';

    public function getApiKey($keyCode)
    {
        return \COption::GetOptionString(self::$moduleName, $keyCode);
    }

    private function getWeight()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_WEIGHT');
        return $result;
    }

    private function getLength()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_LENGTH');
        return $result;
    }

    private function getWide()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_WIDE');
        return $result;
    }

    private function getHeight()
    {
        $result = \COption::GetOptionString(self::$moduleName, 'DEF_HEIGHT');
        return $result;
    }

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