<?php

use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Paint\Orm\PaintTable;

class paint extends CModule
{
    public $MODULE_ID = "paint";
    public $MODULE_VERSION = "1.0.0";
    public $MODULE_VERSION_DATE = "2016-12-25";
    public $MODULE_NAME = "Paint";
    public $MODULE_DESCRIPTION = "Paint";
    public $PATH;


    public function __construct()
    {
        $this->PATH = $_SERVER["DOCUMENT_ROOT"].'/local/modules/'.$this->MODULE_ID;
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
        $this->installDb();
    }

    function DoUninstall()
    {
        $this->unInstallDb();
        $this->unInstallFiles();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installFiles()
    {
        CopyDirFiles($this->PATH."/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        CopyDirFiles($this->PATH."/install/css", $_SERVER["DOCUMENT_ROOT"]."/local/templates/lenvendo/css/".$this->MODULE_ID, true, true);
        CopyDirFiles($this->PATH."/install/js", $_SERVER["DOCUMENT_ROOT"]."/local/templates/lenvendo/js/".$this->MODULE_ID, true, true);
    }

    public function unInstallFiles()
    {
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/components/lenvendo/paint"));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/components/lenvendo/paint.list"));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/components/lenvendo/paint.new"));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/components/lenvendo/paint.edit"));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/components/lenvendo/paint.detail"));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/templates/lenvendo/css/" . $this->MODULE_ID));
        Directory::deleteDirectory(($_SERVER["DOCUMENT_ROOT"] . "/local/templates/lenvendo/js/" . $this->MODULE_ID));
    }

    public function installDb()
    {
        if(Loader::includeModule($this->MODULE_ID)) {

            $app = Application::getInstance();
            $connection = $app->getConnection();
            if (!$connection->isTableExists(PaintTable::getTableName())) {
                PaintTable::getEntity()->createDbTable();
            }
        }
    }

    public function unInstallDb()
    {
        if(Loader::includeModule($this->MODULE_ID))
        {
            if ($connection = Application::getInstance()->getConnection()) {
                if ($connection->isTableExists(PaintTable::getTableName())) {
                    $connection->dropTable(PaintTable::getTableName());
                }
            }
        }
    }
}