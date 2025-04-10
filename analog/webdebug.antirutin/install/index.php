<? global $MESS;
IncludeModuleLangFile(__FILE__);

class webdebug_antirutin extends CModule
{
    var $MODULE_ID = 'webdebug.antirutin';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = 'Y';
    var $Errors;

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->PARTNER_NAME = GetMessage('WDA_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('WDA_PARTNER_URI');
        $this->MODULE_NAME = GetMessage('WDA_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('WDA_MODULE_DESCR');
    }

    function InstallDB()
    {
        global $DB, $DBType;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');
        if ($this->errors !== false) {
            return false;
        }
        return true;
    }

    function UnInstallDB()
    {
        global $DB, $DBType;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
        if ($this->errors !== false) {
            return false;
        }
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/js/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/themes/.default/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/.default', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
        return true;
    }

    function UnInstallFiles($_1639732024 = true)
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/js/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js');
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/themes/.default/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/.default/');
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        return true;
    }

    function DoInstall()
    {
        if (!check_bitrix_sessid()) return false;
        if ($this->InstallDB()) {
            RegisterModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallEvents();
            $this->InstallAgents();
            $this->InstallSecurity();
            \Bitrix\Main\Config\Option::set($this->MODULE_ID, 'disable_old_module', 'Y');
        } else {
            $APPLICATION->ThrowException(implode('', $this->errors));
            return false;
        }
        return true;
    }

    function DoUninstall()
    {
        global $DB;
        if (!check_bitrix_sessid()) return false;
        if ($this->UnInstallDB()) {
            $this->UnInstallAgents();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallSecurity();
            UnRegisterModule($this->MODULE_ID);
        } else {
            $APPLICATION->ThrowException(implode('', $this->errors));
            return false;
        }
        return true;
    }

    function InstallAgents()
    {
        \CAgent::AddAgent('\WD\Antirutin\Uploader::agentRemoveTmpUploads();', $this->MODULE_ID, 'N', 3600);
        return true;
    }

    function UnInstallAgents()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallSecurity()
    {
        if (\Bitrix\Main\Loader::includeModule('security')) {
            $_1835083187 = \CSecurityFilterMask::getList();
            $_414368465 = [];
            while ($_1353976176 = $_1835083187->fetch()) {
                $_414368465[] = ['MASK' => $_1353976176['FILTER_MASK'], 'SITE_ID' => $_1353976176['SITE_ID'],];
            }
            $_414368465[] = ['MASK' => '/bitrix/admin/wda_new.php*', 'SITE_ID' => '',];
            \CSecurityFilterMask::update($_414368465);
        }
    }

    function UnInstallSecurity()
    {
        if (\Bitrix\Main\Loader::includeModule('security')) {
            $_1835083187 = \CSecurityFilterMask::getList();
            $_293639515 = ['/bitrix/admin/wda_new.php*'];
            $_414368465 = [];
            while ($_1353976176 = $_1835083187->fetch()) {
                if (!in_array($_1353976176['FILTER_MASK'], $_293639515)) {
                    $_414368465[] = ['MASK' => $_1353976176['FILTER_MASK'], 'SITE_ID' => $_1353976176['SITE_ID'],];
                }
            }
            \CSecurityFilterMask::update($_414368465);
        }
    }
} ?>