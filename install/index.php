<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Developx\Tk\DB\DeliveryInfoTable;
use Developx\Tk\DB\PointsTable;

if (class_exists('developx_tk')) {
    return;
}

class developx_tk extends CModule
{
    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $this->MODULE_ID = 'developx.tk';
        $this->MODULE_VERSION = '1.0.0';
        $this->MODULE_VERSION_DATE = '2019-08-14 00:00:00';
        $this->MODULE_NAME = Loc::getMessage('dx_tk_module_name');
        $this->MODULE_DESCRIPTION = Loc::getMessage('dx_tk_module_description');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'Developx';
        $this->PARTNER_URI = 'https://developx.ru';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->InstallFiles();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        $this->uninstallFiles();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function InstallFiles()
    {
      //  CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/developx.tk/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        return true;
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            DeliveryInfoTable::getEntity()->createDbTable();
            PointsTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(DeliveryInfoTable::getTableName());
            $connection->dropTable(PointsTable::getTableName());
        }
        return true;
    }

    public function uninstallFiles()
    {

    }

}
