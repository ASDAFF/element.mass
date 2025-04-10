<?
namespace WD\Antirutin;

use \Bitrix\Main\Config\Option,
    \WD\Antirutin\Cli,
    \WD\Antirutin\Filter,
    \WD\Antirutin\IBlock,
    \WD\Antirutin\Json,
    \WD\Antirutin\HttpRequest,
    \WD\Antirutin\ProfileTable as Profile;

global $DB, $DBType;
define('WDA_MODULE', 'webdebug.antirutin');
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::registerAutoLoadClasses(WDA_MODULE, ['CWDA' => 'classes/general/CWDA.php', 'CWDA_Plugin' => 'classes/general/CWDA_Plugin.php', 'CWDA_Profile' => 'classes/' . $DBType . '/CWDA_Profile.php', 'WD\Antirutin\Cli' => 'lib/cli.php', 'WD\Antirutin\Filter' => 'lib/filter.php', 'WD\Antirutin\IBlock' => 'lib/iblock.php', 'WD\Antirutin\Html2Text' => 'lib/html2text.php', 'WD\Antirutin\HttpRequest' => 'lib/httprequest.php', 'WD\Antirutin\Json' => 'lib/json.php', 'WD\Antirutin\Log' => 'lib/log.php', 'WD\Antirutin\Options' => 'lib/options.php', 'WD\Antirutin\Plugin' => 'lib/plugin.php', 'WD\Antirutin\PluginElement' => 'lib/pluginelement.php', 'WD\Antirutin\PluginSection' => 'lib/pluginsection.php', 'WD\Antirutin\ProfileTable' => 'lib/profile.php', 'WD\Antirutin\ProfileActionTable' => 'lib/profileaction.php', 'WD\Antirutin\Support' => 'lib/support.php', 'WD\Antirutin\Thread' => 'lib/thread.php', 'WD\Antirutin\Uploader' => 'lib/uploader.php', 'WD\Antirutin\ValueItem' => 'lib/valueitem.php', 'WD\Antirutin\Worker' => 'lib/worker.php',]);
if (php_sapi_name() == 'cli') {
    while (ob_end_clean()) {
    }
}

final class Helper
{
    const ARRAY_INSERT_BEGIN = '_ARRAY_INSERT_BEGIN_';
    const ARRAY_INSERT_AFTER = '_ARRAY_INSERT_AFTER_';
    const ARRAY_INSERT_BEFORE = '_ARRAY_INSERT_BEFORE_';
    const RAND_ID_PREFIX = 'rand_id_';
    static $_2030273107 = [];
    const CACHE_CATALOG_ARRAY = 'CATALOG_ARRAY';
    const CACHE_PLUGIN_PATHS = 'PLUGIN_PATHS';
    const CACHE_PLUGINS = 'PLUGINS';
    const CACHE_CURRENCIES = 'CURRENCIES';
    const CACHE_CURRENCY_CONVERT_FACTOR = 'CURRENCY_CONVERT_FACTOR';
    const CACHE_MEASURES = 'MEASURES';
    const CACHE_VAT = 'VAT';
    const CACHE_VAT_VALUE = 'VAT_VALUES';
    const CACHE_QUERY = 'REQUEST_QUERY';
    const CACHE_IBLOCK_FIELDS = 'IBLOCK_FIELDS';
    const CACHE_PRICES = 'PRICES';
    const CACHE_STORES = 'STORES';
    const TYPE_ELEMENT = 'ELEMENT';
    const TYPE_SECTION = 'SECTION';
    const TYPE_NATIVE = 'NATIVE';
    const TYPE_CUSTOM = 'CUSTOM';
    static $_148592410;

    public static function p($_1219934872, $_1054741842 = false)
    {
        if ($_1054741842 && is_object($GLOBALS['APPLICATION'])) {
            static::obRestart();
        }
        $_1333034553 = 'pre_' . static::randString();
        $_1619799334 = '<style type="text/css">pre#' . $_1333034553 . '{background:none repeat scroll 0 0 #FAFAFA; border-color:#AAB4BE #AAB4BE #AAB4BE #B4B4B4; border-style:dotted dotted dotted solid; border-width:1px 1px 1px 20px; font:normal 11px "Courier New","Courier",monospace; margin:10px 0; padding:5px 0 5px 10px; position:relative; text-align:left; white-space:pre-wrap;}</style>';
        if (is_array($_1219934872) && empty($_1219934872)) $_1219934872 = '--- Array is empty ---';
        if ($_1219934872 === false) $_1219934872 = '[false]'; elseif ($_1219934872 === true) $_1219934872 = '[true]';
        elseif ($_1219934872 === null) $_1219934872 = '[null]';
        $_1619799334 .= '<pre id="' . $_1333034553 . '">' . print_r($_1219934872, true) . '</pre>';
        print $_1619799334;
        if ($_1054741842) {
            die();
        }
    }

    public static function root()
    {
        return \Bitrix\Main\Loader::getDocumentRoot();
    }

    public static function isUtf()
    {
        return defined('BX_UTF') && BX_UTF === true;
    }

    public static function isEmpty($_7477959)
    {
        if (empty($_7477959)) {
            return true;
        }
        return false;
    }

    public static function query($_309630878)
    {
        return \Bitrix\Main\Application::getConnection()->query($_309630878);
    }

    public static function forSql($_165792130)
    {
        return $GLOBALS['DB']->forSql($_165792130);
    }

    public static function loadMessages($_572117297)
    {
        \Bitrix\Main\Localization\Loc::loadMessages($_572117297);
    }

    public static function getMessage($_2046906042, $_1523607773 = null, $_1477326785 = null, $_277813046 = false)
    {
        if (is_array($_1523607773) && !empty($_1523607773)) {
            $_1477326785 = $_1523607773;
        }
        if (is_string($_1523607773) && strlen($_1523607773)) {
            $_2046906042 = $_1523607773 . '_' . $_2046906042;
        }
        if ($_277813046) {
            static::P($_2046906042);
        }
        return \Bitrix\Main\Localization\Loc::getMessage($_2046906042, $_1477326785);
    }

    public static function obRestart()
    {
        $GLOBALS['APPLICATION']->restartBuffer();
    }

    public static function obStop()
    {
        while (ob_get_level()) {
            ob_clean();
        }
    }

    public static function convertEncoding($_1258967340, $_1666009103 = 'UTF-8', $_2073518801 = 'CP1251')
    {
        $_24702611 = '';
        if (is_array($_1258967340)) {
            foreach ($_1258967340 as $_483229610 => $_1868355201) {
                $_1258967340[$_483229610] = static::convertEncoding($_1868355201, $_1666009103, $_2073518801);
            }
        } else {
            $_1258967340 = \Bitrix\Main\Text\Encoding::convertEncoding($_1258967340, $_1666009103, $_2073518801, $_24702611);
        }
        return $_1258967340;
    }

    public static function convertEncodingTo($_1258967340, $_2073518801)
    {
        if (strlen($_2073518801)) {
            $_1666009103 = static::isUtf() ? 'UTF-8' : 'CP1251';
            $_2073518801 = ToLower($_2073518801) == 'windows-1251' ? 'CP1251' : $_2073518801;
            if ($_2073518801 != $_1666009103) {
                $_1258967340 = static::convertEncoding($_1258967340, $_1666009103, $_2073518801);
            }
        }
        return $_1258967340;
    }

    public static function convertEncodingFrom($_1258967340, $_1666009103)
    {
        if (strlen($_1666009103)) {
            $_1666009103 = ToLower($_1666009103) == 'windows-1251' ? 'CP1251' : $_1666009103;
            $_2073518801 = static::isUtf() ? 'UTF-8' : 'CP1251';
            if ($_1666009103 != $_2073518801) {
                $_1258967340 = static::convertEncoding($_1258967340, $_1666009103, $_2073518801);
            }
        }
        return $_1258967340;
    }

    public static function showNote($_487314993, $_1898966306 = false, $_1065511398 = false, $_1052113319 = false)
    {
        if ($_1052113319) {
            ob_start();
        }
        $_1632181664 = [];
        if ($_1898966306) {
            $_1632181664[] = 'wda-note-compact';
        }
        if ($_1065511398) {
            $_1632181664[] = 'wda-note-center';
        }
        if (!empty($_1632181664)) {
            print '<div class="' . implode(' ', $_1632181664) . '">';
        }
        print BeginNote();
        print $_487314993;
        print EndNote();
        if (!empty($_1632181664)) {
            print '</div>';
        }
        if ($_1052113319) {
            return ob_get_clean();
        }
    }

    public static function showSuccess($_2046906042 = null, $_725598482 = null, $_1898966306 = false)
    {
        ob_start();
        \CAdminMessage::ShowMessage(array('MESSAGE' => $_2046906042, 'DETAILS' => $_725598482, 'HTML' => true, 'TYPE' => 'OK',));
        return ob_get_clean();
    }

    public static function showError($_2046906042 = null, $_725598482 = null, $_1898966306 = false)
    {
        $_1632181664 = [];
        if ($_1898966306) {
            $_1632181664[] = 'wda-note-compact';
        }
        ob_start();
        if (!empty($_1632181664)) {
            print '<div class="' . implode(' ', $_1632181664) . '">';
        }
        \CAdminMessage::ShowMessage(array('MESSAGE' => $_2046906042, 'DETAILS' => $_725598482, 'HTML' => true,));
        if (!empty($_1632181664)) {
            print '</div>';
        }
        return ob_get_clean();
    }

    public static function showHeading($_2046906042, $_2090198468 = false)
    {
        $_1619799334 = '';
        $_1035317984 = $_2090198468 ? ' class="wda-table-nomargin"' : '';
        $_1619799334 .= '<table style="width:100%"' . $_1035317984 . '><tbody><tr class="heading"><td>' . $_2046906042 . '</td></tr></tbody></table>';
        return $_1619799334;
    }

    public static function showHint($_1670065241)
    {
        $_2014682869 = toLower(static::randString());
        $_1670065241 = str_replace('"', '\"', $_1670065241);
        $_1670065241 = str_replace('
', '', $_1670065241);
        $_1670065241 = str_replace('
', ' ', $_1670065241);
        $_1619799334 = '<span id="hint_' . $_2014682869 . '"><span></span></span>' . '<script>BX.hint_replace(BX("hint_' . $_2014682869 . '").childNodes[0], "' . $_1670065241 . '");</script>';
        return $_1619799334;
    }

    public static function wordForm($_1373068477, $_1613063246)
    {
        $_1835606157 = substr($_1373068477, -1);
        $_1766425226 = substr($_1373068477, -2, 1);
        if (strlen($_1373068477) >= 2 && $_1766425226 == '1') {
            return $_1613063246['5'];
        } else {
            if ($_1835606157 == '1') return $_1613063246['1']; elseif ($_1835606157 >= 2 && $_1835606157 <= 4) return $_1613063246['2'];
            else return $_1613063246['5'];
        }
    }

    public static function getSitesList($_971809321 = false, $_1267067842 = false)
    {
        $_342661612 = [];
        $_1294301816 = [];
        if ($_971809321) {
            $_1294301816['ACTIVE'] = 'Y';
        }
        $_744370635 = \CSite::GetList($by = 'SORT', $order = 'ASC', $_1294301816);
        while ($_2003713736 = $_744370635->GetNext(false, false)) {
            $_342661612[$_2003713736['ID']] = $_1267067842 ? sprintf('[%s] %s', $_2003713736['ID'], $_2003713736['NAME']) : $_2003713736;
        }
        return $_342661612;
    }

    public static function isHttps()
    {
        return \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps();
    }

    public static function arrayInsert(array &$_1219934872, $_1699259395, $_1175988846, $_1074047643 = null, $_689645553 = null)
    {
        $_2122873231 = false;
        if ($_1074047643 === static::ARRAY_INSERT_BEGIN) {
            $_2122873231 = true;
            $_1219934872 = array_merge(array($_1699259395 => $_1175988846), $_1219934872);
        } elseif (!is_null($_1074047643)) {
            $_170372173 = 0;
            foreach ($_1219934872 as $_483229610 => $_1868355201) {
                $_170372173++;
                if ($_483229610 === $_1074047643) {
                    $_2122873231 = true;
                    $_636572471 = array_slice($_1219934872, 0, $_170372173, true);
                    $_35686628 = array_slice($_1219934872, $_170372173, null, true);
                    $_1219934872 = array_merge($_636572471, array($_1699259395 => $_1175988846), $_35686628);
                    unset($_636572471, $_35686628);
                    break;
                }
            }
        } elseif (!is_null($_689645553)) {
            $_170372173 = 0;
            foreach ($_1219934872 as $_483229610 => $_1868355201) {
                if ($_483229610 === $_689645553) {
                    $_2122873231 = true;
                    $_636572471 = array_slice($_1219934872, 0, $_170372173, true);
                    $_35686628 = array_slice($_1219934872, $_170372173, null, true);
                    $_1219934872 = array_merge($_636572471, array($_1699259395 => $_1175988846), $_35686628);
                    unset($_636572471, $_35686628);
                    break;
                }
                $_170372173++;
            }
        }
        if (!$_2122873231) {
            $_1219934872[$_1699259395] = $_1175988846;
        }
    }

    public static function arrayRemoveEmptyValues(&$_1476967698, $_1196403842 = true)
    {
        foreach ($_1476967698 as $_483229610 => $_1868355201) {
            if ($_1196403842 && !strlen(trim($_1868355201)) || !$_1196403842 && !strlen($_1868355201)) {
                unset($_1476967698[$_483229610]);
            }
        }
    }

    public static function arrayRemoveEmptyValuesRecursive(&$_1476967698)
    {
        foreach ($_1476967698 as $_483229610 => $_1868355201) {
            if (is_array($_1868355201)) {
                static::arrayRemoveEmptyValuesRecursive($_1476967698[$_483229610]);
            } else {
                if (!strlen(trim($_1868355201))) {
                    unset($_1476967698[$_483229610]);
                }
            }
        }
    }

    public static function execAction($_1219934872, $_1314046979, $_1823318322 = false)
    {
        if (is_array($_1219934872)) {
            foreach ($_1219934872 as $_1742818009 => $_1951887497) {
                $_1219934872[$_1742818009] = $_1314046979($_1951887497, $_1823318322);
            }
        } else {
            $_1219934872 = $_1314046979($_1219934872, $_1823318322);
        }
        return $_1219934872;
    }

    public static function isManagedCacheOn()
    {
        return (Option::get('main', 'component_managed_cache_on', 'N') != 'N' || defined('BX_COMP_MANAGED_CACHE'));
    }

    public static function formatSize($_2055620792)
    {
        $_1619799334 = \CFile::FormatSize($_2055620792);
        $_1619799334 = preg_replace('#^([\d]+)[\s]#', '$1.00 ', $_1619799334);
        $_1619799334 = preg_replace('#^([\d]+)\.([\d]{1})[\s]#', '${1}.${2}0 ', $_1619799334);
        return $_1619799334;
    }

    public static function getOption($_1621304270, $_1131970461 = null, $_1907706463 = null)
    {
        $_1907706463 = !is_null($_1907706463) ? $_1907706463 : WDA_MODULE;
        return Option::get($_1907706463, $_1621304270, $_1131970461);
    }

    public static function setOption($_1621304270, $_7477959)
    {
        return Option::set(WDA_MODULE, $_1621304270, $_7477959);
    }

    public static function removeOption($_1621304270)
    {
        return Option::delete(WDA_MODULE, ['name' => $_1621304270]);
    }

    public static function removeAllOptions()
    {
        return Option::delete(WDA_MODULE);
    }

    public static function formatElapsedTime($_505112015)
    {
        $_1619799334 = '';
        if (is_numeric($_505112015)) {
            $_1073321349 = floor($_505112015 / 3600);
            $_505112015 -= $_1073321349 * 60 * 60;
            $_1947401136 = floor($_505112015 / 60);
            $_1947401136 = sprintf('%02d', $_1947401136);
            if ($_1947401136 > 0) {
                $_505112015 = $_505112015 - $_1947401136 * 60;
            }
            $_505112015 = sprintf('%02d', $_505112015);
            $_1619799334 = ($_1073321349 ? $_1073321349 . ':' : '') . $_1947401136 . ':' . $_505112015;
        }
        return $_1619799334;
    }

    public static function getCatalogArray($_147464205)
    {
        $_147464205 = IntVal($_147464205);
        $_1243936018 = &static::$_2030273107[static::CACHE_CATALOG_ARRAY][$_147464205];
        if (!is_array($_1243936018)) {
            $_1243936018 = [];
        }
        if ($_147464205 > 0) {
            if (!empty($_1243936018)) {
                return $_1243936018;
            } elseif (\Bitrix\Main\Loader::includeModule('catalog')) {
                $_1936364387 = \CCatalog::GetByID($_147464205);
                if (is_array($_1936364387) && !empty($_1936364387)) {
                    $_1243936018 = $_1936364387;
                    return $_1243936018;
                } else {
                    $_2000858002 = \CCatalog::GetList([], array('PRODUCT_IBLOCK_ID' => $_147464205));
                    if ($_1936364387 = $_2000858002->getNext(false, false)) {
                        if (\Bitrix\Main\Loader::includeModule('iblock')) {
                            $_1833162651 = \CIBlock::GetList([], array('ID' => $_147464205));
                            if ($_965757115 = $_1833162651->GetNext(false, false)) {
                                $_342661612 = array('IBLOCK_ID' => $_147464205, 'YANDEX_EXPORT' => 'N', 'SUBSCRIPTION' => 'N', 'VAT_ID' => 0, 'PRODUCT_IBLOCK_ID' => 0, 'SKU_PROPERTY_ID' => 0, 'ID' => $_147464205, 'IBLOCK_TYPE_ID' => $_965757115['IBLOCK_TYPE_ID'], 'LID' => $_965757115['LID'], 'NAME' => $_965757115['NAME'], 'OFFERS_IBLOCK_ID' => $_1936364387['IBLOCK_ID'], 'OFFERS_PROPERTY_ID' => $_1936364387['SKU_PROPERTY_ID'], 'OFFERS' => 'N',);
                                return $_342661612;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function getClassFilename($_1035317984)
    {
        $_1470536675 = new \ReflectionClass($_1035317984);
        $_289532664 = $_1470536675->getFileName();
        unset($_1470536675);
        return $_289532664;
    }

    public static function addNotify($_801308098, $_880924094, $_1797407707 = true)
    {
        $_1823318322 = ['MODULE_ID' => WDA_MODULE, 'MESSAGE' => $_801308098, 'TAG' => $_880924094, 'ENABLE_CLOSE' => $_1797407707 ? 'Y' : 'N',];
        static::deleteNotify($_880924094);
        return \CAdminNotify::add($_1823318322);
    }

    public static function deleteNotify($_880924094)
    {
        return \CAdminNotify::deleteByTag($_880924094);
    }

    public static function getNotifyList()
    {
        $_342661612 = [];
        $_860179521 = ['ID' => 'ASC',];
        $_1294301816 = ['MODULE_ID' => WDA_MODULE,];
        $_2074508449 = \CAdminNotify::getList($_860179521, $_1294301816);
        while ($_1951887497 = $_2074508449->getNext()) {
            $_342661612[] = $_1951887497;
        }
        return $_342661612;
    }

    public static function path($_876120280)
    {
        return str_replace('\\', '/', $_876120280);
    }

    public static function removeTrailingBackslash($_1670065241)
    {
        return preg_replace('#[/]*$#', '', $_1670065241);
    }

    public static function scandir($_378088485, $_1823318322 = [])
    {
        $_342661612 = [];
        $_378088485 = static::path($_378088485);
        $_378088485 = static::removeTrailingBackslash($_378088485);
        if (!is_array($_1823318322)) {
            $_1823318322 = [];
        }
        if ($_1823318322['RECURSIVELY'] !== false) {
            $_1823318322['RECURSIVELY'] = true;
        }
        if ($_1823318322['FILES'] !== false) {
            $_1823318322['FILES'] = true;
        }
        if (strlen($_378088485) && is_dir($_378088485)) {
            $_363809662 = opendir($_378088485);
            while (($_1790721841 = readdir($_363809662)) !== false) {
                if (!in_array($_1790721841, ['.', '..'])) {
                    if (is_file($_378088485 . '/' . $_1790721841)) {
                        if ($_1823318322['FILES']) {
                            if (isset($_1823318322['EXT'])) {
                                $_680976228 = toUpper(pathinfo($_1790721841, PATHINFO_EXTENSION));
                                $_1195589565 = (is_string($_1823318322['EXT']) && toUpper($_1823318322['EXT']) == $_680976228) || is_array($_1823318322['EXT']) && in_array($_680976228, array_map(function ($_1790721841) {
                                        return toUpper($_1790721841);
                                    }, $_1823318322['EXT']));
                                if (!$_1195589565) {
                                    continue;
                                }
                            }
                            $_478709638 = null;
                            if (is_callable($_1823318322['CALLBACK'])) {
                                $_478709638 = call_user_func_array($_1823318322['CALLBACK'], [$_378088485 . '/' . $_1790721841, $_1823318322]);
                            }
                            if ($_478709638 === false) {
                                continue;
                            }
                            $_342661612[] = $_378088485 . '/' . $_1790721841;
                        }
                    } elseif (is_dir($_378088485 . '/' . $_1790721841)) {
                        if ($_1823318322['DIRS']) {
                            $_342661612[] = $_378088485 . '/' . $_1790721841;
                        }
                        if ($_1823318322['RECURSIVELY']) {
                            $_342661612 = array_merge($_342661612, static::scandir($_378088485 . '/' . $_1790721841, $_1823318322));
                        }
                    }
                }
            }
            closedir($_363809662);
        }
        sort($_342661612);
        return $_342661612;
    }

    public static function getIBlocks($_1180824402 = true, $_1874246402 = false)
    {
        $_342661612 = [];
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $_860179521 = ['SORT' => 'ASC',];
            $_1294301816 = ['CHECK_PERMISSIONS' => 'Y', 'MIN_PERMISSION' => 'W',];
            if ($_1180824402) {
                $_1401304616 = \CIBlockType::GetList([], $_1294301816);
                while ($_1949322088 = $_1401304616->GetNext(false, false)) {
                    $_360407803 = \CIBlockType::GetByIDLang($_1949322088['ID'], LANGUAGE_ID, false);
                    $_342661612[$_1949322088['ID']] = ['NAME' => $_360407803['NAME'], 'ITEMS' => [],];
                }
            }
            if (!$_1874246402) {
                $_1294301816['ACTIVE'] = 'Y';
            }
            $_1833162651 = \CIBlock::GetList($_860179521, $_1294301816);
            while ($_965757115 = $_1833162651->GetNext(false, false)) {
                if ($_1180824402) {
                    $_342661612[$_965757115['IBLOCK_TYPE_ID']]['ITEMS'][] = $_965757115;
                } else {
                    $_342661612[] = $_965757115;
                }
            }
        }
        foreach (getModuleEvents(WDA_MODULE, 'OnGetIBlockList', true) as $_652681472) {
            executeModuleEventEx($_652681472, array(&$_342661612, $_1180824402, $_1874246402));
        }
        return $_342661612;
    }

    public static function getIBlockFields($_665719817, $_438513112 = null, $_593331727 = null)
    {
        $_470588044 = &static::$_2030273107[static::CACHE_IBLOCK_FIELDS][$_665719817];
        if (!is_array($_470588044) || empty($_470588044)) {
            $_470588044 = [];
            if (\Bitrix\Main\Loader::includeModule('iblock')) {
                $_470588044 = \CIBlock::getFields($_665719817);
            }
        }
        if (!is_null($_438513112)) {
            if ($_593331727) {
                return $_470588044[$_438513112]['DEFAULT_VALUE'];
            }
            return $_470588044[$_438513112];
        }
        return $_470588044;
    }

    public static function getCurrencyList($_1267067842 = false, $_1785590639 = true)
    {
        $_342661612 = &static::$_2030273107[static::CACHE_CURRENCIES];
        if (!is_array($_342661612) || empty($_342661612)) {
            $_342661612 = [];
            if (\Bitrix\Main\Loader::includeModule('currency')) {
                $_912276916 = \CCurrency::GetList($by = 'SORT', $order = 'ASC', LANGUAGE_ID);
                while ($_979607093 = $_912276916->GetNext(false, false)) {
                    $_979607093['IS_BASE'] = FloatVal($_979607093['AMOUNT']) == 1 ? true : false;
                    if (isset($_979607093['DEAULT']) && !isset($_979607093['DEFAULT'])) {
                        $_979607093['DEFAULT'] = $_979607093['DEAULT'];
                        unset($_979607093['DEAULT']);
                    }
                    $_342661612[ToUpper($_979607093['CURRENCY'])] = $_979607093;
                }
                if ($_1785590639) {
                    uasort($_342661612, function ($_2141734786, $_1715659510) {
                        $_1003864344 = $_2141734786['BASE'] == 'Y';
                        $_476225903 = $_1715659510['BASE'] == 'Y';
                        if (!$_1003864344 && $_476225903) {
                            return 1;
                        } elseif ($_1003864344 && !$_476225903) {
                            return -1;
                        } else {
                            return $_2141734786['SORT'] == $_1715659510['SORT'] ? 0 : ($_2141734786['SORT'] > $_1715659510['SORT'] ? 1 : -1);
                        }
                    });
                }
            }
            if ($_1267067842) {
                foreach ($_342661612 as $_689561767 => $_979607093) {
                    $_342661612[$_689561767] = sprintf('[%s] %s', $_979607093['CURRENCY'], $_979607093['FULL_NAME']);
                }
            }
        }
        return $_342661612;
    }

    public static function getBaseCurrency()
    {
        foreach (static::getCurrencyList() as $_979607093) {
            if ($_979607093['BASE'] == 'Y') {
                return $_979607093['CURRENCY'];
            }
        }
        return false;
    }

    public static function getConvertCurrencyFactor($_412636211, $_481184325)
    {
        $_1699259395 = sprintf('%s_%s', $_412636211, $_481184325);
        $_187158499 = &static::$_2030273107[static::CACHE_CURRENCY_CONVERT_FACTOR][$_1699259395];
        if (!isset($_187158499)) {
            $_187158499 = \CCurrencyRates::getConvertFactorEx($_412636211, $_481184325);
        }
        return $_187158499;
    }

    public static function convertCurrency($_760694696, $_412636211, $_481184325 = null, $_884886040 = false, $_325332580 = false)
    {
        if (\Bitrix\Main\Loader::includeModule('currency')) {
            $_1798212678 = static::getCurrencyList();
            if (is_null($_481184325)) {
                $_481184325 = key($_1798212678);
            }
            $_187158499 = static::getConvertCurrencyFactor($_412636211, $_481184325);
            if ($_884886040) {
                return static::currencyFormat($_760694696, $_481184325);
            } elseif ($_325332580) {
                return number_format($_760694696 * $_187158499, 2, '.', '');
            } else {
                return $_760694696 * $_187158499;
            }
        }
        return false;
    }

    public static function currencyFormat($_760694696, $_689561767)
    {
        if (\Bitrix\Main\Loader::includeModule('currency')) {
            return \CCurrencyLang::currencyFormat($_760694696, $_689561767, true);
        }
        return sprintf('%s %s', $_760694696, $_689561767);
    }

    public static function convertCurrencyArray($_220000964, $_481184325 = null, $_884886040 = false)
    {
        return static::convertCurrency($_220000964['PRICE'], $_220000964['CURRENCY'], $_481184325, $_884886040);
    }

    public static function getPriceList($_860179521 = false)
    {
        $_342661612 = [];
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            if ($_860179521 == false) {
                $_860179521 = array('SORT' => 'ASC', 'ID' => 'ASC');
            }
            $_1098393096 = \CCatalogGroup::GetList($_860179521);
            while ($_220000964 = $_1098393096->getNext(false, false)) {
                $_342661612[$_220000964['ID']] = $_220000964;
            }
        }
        return $_342661612;
    }

    public static function getOptimalPrice($_1443081490, $_595476225, $_888119509 = [], $_689561767 = null)
    {
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_888119509 = is_array($_888119509) ? $_888119509 : [];
            if (strlen($_689561767)) {
                \CCatalogProduct::setUsedCurrency($_689561767);
            }
            $_1269017698 = \CCatalogProduct::getOptimalPrice($_1443081490, 1, [], 'N', $_888119509, $_595476225);
            return array_merge(['ID' => $_1269017698['PRICE']['ID'], 'VAT_RATE' => $_1269017698['PRICE']['VAT_RATE'], 'VAT_INCLUDED' => $_1269017698['PRICE']['VAT_INCLUDED'], 'IBLOCK_ID' => $_1269017698['PRICE']['ELEMENT_IBLOCK_ID'], 'PRODUCT_ID' => $_1269017698['PRODUCT_ID'], 'DISCOUNT_LIST' => $_1269017698['DISCOUNT_LIST'],], $_1269017698['RESULT_PRICE']);
        }
        return false;
    }

    public static function formatPriceType($_220000964)
    {
        return sprintf('[%d] [%s] %s', $_220000964['ID'], $_220000964['NAME'], $_220000964['NAME_LANG']);
    }

    public static function formatCurrency($_979607093)
    {
        return sprintf('%s (%s)', $_979607093['CURRENCY'], $_979607093['FULL_NAME']);
    }

    public static function formatSite($_2003713736)
    {
        return sprintf('[%s] %s (%s)', $_2003713736['ID'], $_2003713736['SITE_NAME'], $_2003713736['SERVER_NAME']);
    }

    public static function getStoresList()
    {
        $_342661612 = &static::$_2030273107[static::CACHE_STORES];
        if (!is_array($_342661612)) {
            $_342661612 = [];
        }
        if (empty($_342661612) && \Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStore')) {
            $_1894588984 = \CCatalogStore::getList(['SORT' => 'ASC']);
            while ($_1285979446 = $_1894588984->getNext(false, false)) {
                $_342661612[$_1285979446['ID']] = $_1285979446;
            }
            unset($_1894588984, $_1285979446);
        }
        return $_342661612;
    }

    public static function getMeasuresList($_1267067842 = false)
    {
        $_342661612 = &static::$_2030273107[static::CACHE_MEASURES][$_1267067842];
        if (!is_array($_342661612)) {
            $_342661612 = [];
        }
        if (empty($_342661612) && \Bitrix\Main\Loader::includeModule('catalog')) {
            $_825087893 = \CCatalogMeasure::getList([], []);
            while ($_1440551647 = $_825087893->getNext(false, false)) {
                $_342661612[$_1440551647['ID']] = $_1267067842 ? $_1440551647['MEASURE_TITLE'] : $_1440551647;
            }
            unset($_825087893, $_1440551647);
        }
        return $_342661612;
    }

    public static function getPrice($_1241311239, $_1699259395 = null)
    {
        $_342661612 = &static::$_2030273107[static::CACHE_PRICES][$_1241311239];
        if (!is_array($_342661612)) {
            $_342661612 = [];
        }
        if (empty($_342661612) && \Bitrix\Main\Loader::includeModule('catalog')) {
            $_342661612 = \CCatalogGroup::getById($_1241311239);
            $_342661612['X'] = $_1241311239 . ' => ' . rand(100, 999);
        }
        if (strlen($_1699259395)) {
            return $_342661612[$_1699259395];
        }
        return $_342661612;
    }

    public static function getVatList($_1267067842 = false)
    {
        $_211381391 = &static::$_2030273107[static::CACHE_VAT . ($_1267067842 ? '1' : '2')];
        if (!is_array($_211381391)) {
            $_211381391 = [];
        }
        if (empty($_211381391) && \Bitrix\Main\Loader::includeModule('catalog')) {
            $_265198921 = \CCatalogVat::getList(['RATE' => 'ASC']);
            while ($_874800695 = $_265198921->getNext()) {
                $_211381391[$_874800695['ID']] = $_1267067842 ? $_874800695['NAME'] : $_874800695;
            }
        }
        return $_211381391;
    }

    public static function getVatRateById($_1125047017)
    {
        $_874800695 = static::getVatList();
        if ($_1125047017) {
            $_874800695 = $_874800695[$_1125047017];
            if (is_array($_874800695)) {
                if (stripos($_874800695['NAME'], static::getMessage('WDA_NO_VAT_PRETEXT')) === false) {
                    return $_874800695['RATE'];
                }
            }
        }
        return '';
    }

    public static function getVatNameById($_1125047017)
    {
        $_874800695 = static::getVatList();
        if ($_1125047017) {
            $_874800695 = $_874800695[$_1125047017];
            if (is_array($_874800695)) {
                return $_874800695['NAME'];
            }
        }
        return '';
    }

    public static function setProductPrice($_845447829, $_1241311239, $_760694696, $_689561767, $_329328847 = null)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_1505217634 = ['ID' => $_845447829,];
            \CCatalogProduct::add($_1505217634);
            $_848476725 = ['PRODUCT_ID' => $_845447829, 'CATALOG_GROUP_ID' => $_1241311239, 'PRICE' => floatVal($_760694696), 'CURRENCY' => $_689561767, 'EXTRA_ID' => $_329328847,];
            $_1294301816 = ['PRODUCT_ID' => $_845447829, 'CATALOG_GROUP_ID' => $_1241311239,];
            $_1206479936 = \CPrice::getList([], $_1294301816, false, false, ['ID']);
            if ($_220000964 = $_1206479936->getNext(false, false)) {
                $_842012903 = !!\CPrice::update($_220000964['ID'], $_848476725);
            } else {
                $_842012903 = !!\CPrice::add($_848476725);
            }
        }
        return $_842012903;
    }

    public static function deleteProductPrice($_845447829, $_1241311239 = false)
    {
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_1294301816 = ['PRODUCT_ID' => $_845447829,];
            if (is_numeric($_1241311239) && $_1241311239 > 0) {
                $_1294301816['CATALOG_GROUP_ID'] = $_1241311239;
            }
            $_1206479936 = \CPrice::getList([], $_1294301816, false, false, ['ID']);
            while ($_220000964 = $_1206479936->getNext(false, false)) {
                \CPrice::delete($_220000964['ID']);
            }
        }
        return true;
    }

    public static function setProductPriceExtra($_845447829, $_1241311239, $_329328847)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_1505217634 = ['ID' => $_845447829,];
            \CCatalogProduct::add($_1505217634);
            $_848476725 = ['PRODUCT_ID' => $_845447829, 'CATALOG_GROUP_ID' => $_1241311239, 'EXTRA_ID' => intVal($_329328847),];
            $_1294301816 = ['PRODUCT_ID' => $_845447829, 'CATALOG_GROUP_ID' => $_1241311239,];
            $_1206479936 = \CPrice::getList([], $_1294301816, false, false, ['ID', 'PRICE', 'CURRENCY']);
            $_220000964 = $_1206479936->getNext(false, false);
            if ($_220000964 && $_329328847 <= 0) {
                $_848476725 = array_merge($_848476725, ['PRICE' => $_220000964['PRICE'], 'CURRENCY' => $_220000964['CURRENCY'], 'EXTRA_ID' => null,]);
            } elseif (!$_220000964 && $_329328847 > 0) {
                if ($_1117368213 = \CPrice::getBasePrice($_845447829)) {
                    $_848476725 = array_merge($_848476725, ['PRICE' => $_1117368213['PRICE'], 'CURRENCY' => $_1117368213['CURRENCY'],]);
                }
            }
            if ($_220000964) {
                $_842012903 = !!\CPrice::update($_220000964['ID'], $_848476725);
            } else {
                $_842012903 = !!\CPrice::add($_848476725);
            }
        }
        return $_842012903;
    }

    public static function setProductPriceInterval($_845447829, $_1241311239, array $_888119509)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_1505217634 = ['ID' => $_845447829,];
            \CCatalogProduct::add($_1505217634);
            Helper::deleteProductPrice($_845447829, $_1241311239);
            foreach ($_888119509 as $_220000964) {
                if (strlen($_220000964['price']) && (strlen($_220000964['from']) || strlen($_220000964['to']))) {
                    $_848476725 = ['PRODUCT_ID' => $_845447829, 'CATALOG_GROUP_ID' => $_1241311239, 'PRICE' => floatVal($_220000964['price']), 'CURRENCY' => $_220000964['currency'], 'QUANTITY_FROM' => $_220000964['from'], 'QUANTITY_TO' => $_220000964['to'],];
                    if (\CPrice::add($_848476725)) {
                        $_842012903 = true;
                    }
                }
            }
        }
        return $_842012903;
    }

    public static function setProductStoreAmount($_845447829, $_809413831, $_1986524628)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog') && static::isCatalogStoresAvailable()) {
            $_113449388 = ['PRODUCT_ID' => $_845447829, 'AMOUNT' => floatVal($_1986524628), 'STORE_ID' => $_809413831,];
            $_1266841057 = \CCatalogStoreProduct::getList([], ['STORE_ID' => $_809413831, 'PRODUCT_ID' => $_845447829], false, false, ['ID']);
            if ($_1951887497 = $_1266841057->GetNext(false, false)) {
                $_842012903 = \CCatalogStoreProduct::update($_1951887497['ID'], $_113449388) > 0;
            } else {
                $_842012903 = \CCatalogStoreProduct::add($_113449388) > 0;
            }
        }
        return $_842012903;
    }

    public static function setProductMeasureRatio($_1443081490, $_2131576432)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $_1081404432 = \CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $_1443081490]);
            if ($_141876525 = $_1081404432->getNext(false, false)) {
                if (\CCatalogMeasureRatio::update($_141876525['ID'], ['RATIO' => $_2131576432])) {
                    $_842012903 = true;
                }
            } else {
                if (\CCatalogMeasureRatio::add(['PRODUCT_ID' => $_1443081490, 'RATIO' => $_2131576432])) {
                    $_842012903 = true;
                }
            }
        }
        return $_842012903;
    }

    public static function setProductField($_845447829, $_1556582635, $_7477959 = null)
    {
        $_842012903 = false;
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            if (!is_array($_1556582635)) {
                $_1556582635 = [$_1556582635 => $_7477959];
            }
            $_1505217634 = array_merge(['ID' => $_845447829,], $_1556582635);
            $_842012903 = !!\CCatalogProduct::add($_1505217634);
        }
        return $_842012903;
    }

    public static function setProductBarcodes($_845447829, $_7477959, $_517760661 = false)
    {
        if (\Bitrix\Main\Loader::includeModule('catalog') && static::isCatalogBarcodeAvailable()) {
            $_803729578 = \CCatalogProduct::getById($_845447829);
            if (is_array($_803729578) && $_803729578['BARCODE_MULTI'] == 'Y') {
                return false;
            } else {
                $_1698748300 = is_array($_803729578);
                if (!$_1698748300) {
                    $_1698748300 = \CCatalogProduct::add(['ID' => $_845447829]);
                }
                if ($_1698748300) {
                    $_7477959 = is_array($_7477959) ? $_7477959 : [$_7477959];
                    foreach ($_7477959 as $_483229610 => $_1213321452) {
                        if (!strlen($_1213321452)) {
                            unset($_7477959[$_483229610]);
                        }
                    }
                    $_1146362156 = [];
                    $_1294301816 = ['PRODUCT_ID' => $_845447829];
                    $_1079565188 = ['ID', 'BARCODE'];
                    $_1334738851 = \CCatalogStoreBarCode::getList([], $_1294301816, false, false, $_1079565188);
                    while ($_695021870 = $_1334738851->getNext()) {
                        $_1146362156[$_695021870['ID']] = $_695021870['BARCODE'];
                    }
                    $_792837494 = [];
                    $_1283969725 = [];
                    foreach ($_7477959 as $_1213321452) {
                        if (!in_array($_1213321452, $_1146362156)) {
                            $_1283969725[] = $_1213321452;
                        }
                    }
                    if (!$_517760661) {
                        foreach ($_1146362156 as $_192510181 => $_1213321452) {
                            if (!in_array($_1213321452, $_7477959)) {
                                $_792837494[$_192510181] = $_1213321452;
                            }
                        }
                    }
                    foreach ($_792837494 as $_192510181 => $_1213321452) {
                        \CCatalogStoreBarCode::delete($_192510181);
                    }
                    foreach ($_1283969725 as $_1213321452) {
                        \CCatalogStoreBarCode::add(['PRODUCT_ID' => $_845447829, 'BARCODE' => $_1213321452,]);
                    }
                    return true;
                }
            }
        }
        return false;
    }

    public static function isCatalogNewFilter()
    {
        return checkVersion(static::getModuleVersion('catalog'), '18.6.200');
    }

    public static function isCatalogBarcodeAvailable()
    {
        return \Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStoreBarCode');
    }

    public static function isCatalogStoresAvailable()
    {
        return \Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStoreProduct');
    }

    public static function includeFile($_572117297, $_1823318322 = [])
    {
        $_572117297 = __DIR__ . '/include/template/' . $_572117297 . '.php';
        ob_start();
        if (is_file($_572117297)) {
            static::loadMessages($_572117297);
            $GLOBALS['arParams'] = $_1823318322;
            include($_572117297);
        }
        return ob_get_clean();
    }

    public static function findPlugins($_1089230722, $_1778856800 = true)
    {
        $_1343460415 = static::root();
        $_857455810 = &static::$_2030273107[static::CACHE_PLUGINS][$_1089230722];
        if (!is_array($_857455810) || empty($_857455810)) {
            $_857455810 = array();
            $_782621467 = static::getPluginsDir($_1089230722);
            try {
                $_363809662 = opendir($_1343460415 . $_782621467);
                while ($_896723465 = readdir($_363809662)) {
                    if ($_896723465 != '.' && $_896723465 != '..') {
                        $_91737516 = $_1343460415 . $_782621467 . '/' . $_896723465;
                        if (is_dir($_91737516) && is_file($_91737516 . '/class.php')) {
                            static::loadMessages($_91737516 . '/class.php');
                            static::loadMessages($_91737516 . '/.form.php');
                            require_once($_91737516 . '/class.php');
                        }
                    }
                }
                closedir($_363809662);
            } catch (\SystemException $_391270134) {
            }
            foreach (static::getEventHandlers('OnFindPlugins') as $_939557545) {
                $GLOBALS['_____1463984380'][0]($_939557545, [$_1089230722, $_782621467]);
            }
            $_213870161 = '\Plugin' . toUpper(substr($_1089230722, 0, 1)) . toLower(substr($_1089230722, 1));
            $_1114631409 = &static::$_2030273107[static::CACHE_PLUGIN_PATHS];
            $_1114631409 = [];
            foreach (get_declared_classes() as $_1035317984) {
                if (is_subclass_of($_1035317984, __NAMESPACE__ . $_213870161)) {
                    $_715405253 = $_1035317984::getCode();
                    $_805316893 = static::getClassFilename($_1035317984);
                    $_805316893 = static::path($_805316893);
                    $_1114631409[$_715405253] = $_805316893;
                    if (stripos($_805316893, $_1343460415 . $_782621467) === false) {
                        static::loadMessages($_805316893);
                    }
                }
            }
            foreach (get_declared_classes() as $_1035317984) {
                if (is_subclass_of($_1035317984, __NAMESPACE__ . $_213870161)) {
                    if (!$_1035317984::isDisabled()) {
                        $_715405253 = $_1035317984::getCode();
                        $_857455810[$_715405253] = ['CLASS' => $_1035317984, 'GROUP' => $_1035317984::getGroup(), 'CODE' => $_715405253, 'NAME' => $_1035317984::getName(), 'HINT' => $_1035317984::getHint(),];
                    }
                }
            }
            foreach (static::getEventHandlers('OnAfterFindPlugins') as $_939557545) {
                ExecuteModuleEventEx($_939557545, [&$_857455810]);
            }
            $_857455810 = is_array($_857455810) ? $_857455810 : [];
            foreach ($_857455810 as $_1943344878 => $_1926122490) {
                $_7787193 = !is_array($_1926122490) || !strlen($_1926122490['CODE']) || !strlen($_1926122490['NAME']) || $_1943344878 != $_1926122490['CODE'] || is_numeric($_1943344878) || !strlen($_1926122490['CLASS']) || !class_exists($_1926122490['CLASS']) || !is_subclass_of($_1926122490['CLASS'], __NAMESPACE__ . $_213870161);
                if ($_7787193) {
                    unset($_857455810[$_1943344878]);
                }
            }
            foreach ($_857455810 as $_1943344878 => $_1926122490) {
                $_857455810[$_1943344878]['TYPE'] = static::TYPE_NATIVE;
                $_289532664 = $_1114631409[$_1926122490['CODE']];
                if (stripos($_289532664, $_1343460415) !== 0) {
                    $_348481303 = stripos($_289532664, '/bitrix/modules/');
                    if ($_348481303 !== false) {
                        $_289532664 = $_1343460415 . substr($_289532664, $_348481303);
                    }
                }
                if (strlen($_289532664)) {
                    $_289532664 = substr($_289532664, strlen($_1343460415));
                    $_857455810[$_1943344878]['DIRECTORY'] = Helper::path(pathinfo($_289532664, PATHINFO_DIRNAME));
                    if (stripos($_289532664, $_782621467) === 0) {
                        $_857455810[$_1943344878]['TYPE'] = static::TYPE_NATIVE;
                    }
                }
            }
            foreach ($_857455810 as $_1943344878 => $_1926122490) {
                $_857455810[$_1943344878]['ICON'] = false;
                $_857455810[$_1943344878]['ICON_BASE64'] = false;
                $_2139427520 = $_1926122490['DIRECTORY'] . '/icon.png';
                $_857455810[$_1943344878]['ICON_FILE'] = $_2139427520;
                if (is_file($_1343460415 . $_2139427520)) {
                    $_857455810[$_1943344878]['ICON'] = $_2139427520;
                    $_857455810[$_1943344878]['ICON_BASE64'] = 'data:image/png;base64,' . base64_encode(file_get_contents($_1343460415 . $_2139427520));
                }
            }
            uasort($_857455810, function ($_2141734786, $_1715659510) {
                $_1751095664 = toLower($_2141734786['NAME']);
                $_1311995175 = toLower($_1715659510['NAME']);
                $_1266940161 = strpos($_1751095664, '[') !== false;
                $_1024526414 = strpos($_1311995175, '[') !== false;
                if ($_1266940161 && !$_1024526414) {
                    return 1;
                } elseif (!$_1266940161 && $_1024526414) {
                    return -1;
                } else {
                    return strcmp($_1751095664, $_1311995175);
                }
            });
        }
        if ($_1778856800) {
            $_204856369 = static::getPluginGroups();
            foreach ($_857455810 as $_483229610 => $_1926122490) {
                if (is_array($_1926122490['GROUP'])) {
                    if (!is_array($_204856369[$_1926122490['GROUP']['CODE']])) {
                        $_1926122490['GROUP']['SORT'] = is_numeric($_1926122490['GROUP']) && $_1926122490['GROUP'] > 0 ? $_1926122490['GROUP'] : 500;
                        $_204856369[$_1926122490['GROUP']['CODE']] = $_1926122490['GROUP'];
                    }
                    $_857455810[$_483229610]['GROUP'] = $_1926122490['GROUP']['CODE'];
                }
            }
            foreach ($_204856369 as $_483229610 => $_961466474) {
                $_204856369[$_483229610]['ITEMS'] = [];
            }
            uasort($_204856369, function ($_713308052, $_1212891008) {
                return strcmp($_713308052['SORT'], $_1212891008['SORT']);
            });
            foreach ($_857455810 as $_1926122490) {
                $_204856369[$_1926122490['GROUP']]['ITEMS'][$_1926122490['CODE']] = $_1926122490;
            }
            foreach ($_204856369 as $_483229610 => $_961466474) {
                if (empty($_961466474['ITEMS'])) {
                    unset($_204856369[$_483229610]);
                }
            }
            return $_204856369;
        }
        return $_857455810;
    }

    protected static function getPluginsDir($_1089230722)
    {
        $_378088485 = substr(__DIR__, strlen(static::root()));
        $_348481303 = stripos(__DIR__, '/bitrix/modules/');
        if ($_348481303 !== false) {
            $_378088485 = substr(__DIR__, $_348481303);
        }
        switch ($_1089230722) {
            case static::TYPE_ELEMENT:
                $_378088485 .= '/plugins/element';
                break;
            case static::TYPE_SECTION:
                $_378088485 .= '/plugins/section';
                break;
        }
        return $_378088485;
    }

    public static function getPluginGroups()
    {
        $_204856369 = ['GENERAL' => ['SORT' => 100], 'CATALOG' => ['SORT' => 200], 'SETS' => ['SORT' => 300], 'IMAGES' => ['SORT' => 400], 'MODULES' => ['SORT' => 500], 'CUSTOM' => ['SORT' => 600],];
        foreach ($_204856369 as $_483229610 => $_289719282) {
            $_204856369[$_483229610]['NAME'] = static::getMessage('WDA_PLUGINGROUP_' . $_483229610);
        }
        return $_204856369;
    }

    public static function getPluginLangPrefix($_572117297, &$_1387561055, &$_616585763)
    {
        $_1387561055 = '';
        $_616585763 = '';
        $_438513112 = '';
        $_1114631409 = &static::$_2030273107[static::CACHE_PLUGIN_PATHS];
        if (is_array($_1114631409)) {
            $_876120280 = static::path(pathinfo($_572117297, PATHINFO_DIRNAME));
            $_876120280 = preg_replace('#^(.*?)/lang/[a-z]{2}(.*?)$#i', '$1', $_876120280);
            if (strlen($_876120280) && is_dir($_876120280)) {
                $_876120280 .= '/class.php';
            }
            $_2014682869 = array_search($_876120280, $_1114631409);
            if (strlen($_2014682869)) {
                $_1387561055 = Plugin::LANG_PREFIX . '_' . $_2014682869 . '_';
                $_616585763 = $_1387561055 . 'HINT_';
            }
        }
    }

    public static function getEventHandlers($_1698736561)
    {
        return \Bitrix\Main\EventManager::getInstance()->findEventHandlers(WDA_MODULE, $_1698736561);
    }

    public static function clearFilename($_2139427520)
    {
        return preg_replace('#[^A-z0-9_-]#', '', $_2139427520);
    }

    public static function randString($_1777075955 = false)
    {
        return ($_1777075955 === true ? static::RAND_ID_PREFIX : '') . 'a' . substr(md5(randString(round(16)) . microtime(true)), 1);
    }

    public static function addJsLangPhrases($_927577030, $_1523607773 = null)
    {
        $_1892018643 = static::includeFile('lang_phrases', ['PREFIX' => $_1523607773, 'PHRASES' => $_927577030,]);
        \Bitrix\Main\Page\Asset::getInstance()->addString($_1892018643, true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);
    }

    public static function getPopupContent($_1093438908, $_1823318322 = [])
    {
        ob_start();
        $_378088485 = realpath(__DIR__ . '/include/popup');
        $_1093438908 = static::clearFilename($_1093438908);
        if (strlen($_1093438908)) {
            $_1040733392 = $_378088485 . '/' . $_1093438908 . '.php';
            if (is_file($_1040733392)) {
                static::loadMessages($_1040733392);
                $GLOBALS['arParams'] = $_1823318322;
                require($_1040733392);
            }
        }
        return ob_get_clean();
    }

    public static function getRequestQuery()
    {
        $_208688364 = &static::$_2030273107[static::CACHE_QUERY];
        if (!is_array($_208688364)) {
            $_1143495770 = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray();
            $_585730118 = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray();
            if (isset($_1143495770['ajax_action']) && !static::isUtf()) {
                $_585730118 = static::convertEncoding($_585730118, 'UTF-8', 'CP1251');
            }
            $_208688364 = [$_1143495770, $_585730118];
        }
        return $_208688364;
    }

    public static function getFirstValue($_1476967698, $_1662508380 = false)
    {
        foreach ($_1476967698 as $_7477959) {
            if ($_1662508380) {
                $_7477959 = intVal($_7477959);
                if ($_7477959 > 0) {
                    return $_7477959;
                }
            } elseif (strlen($_7477959)) {
                return $_7477959;
            }
        }
        return false;
    }

    public static function getProfileArray($_1862100254)
    {
        $_928705056 = [];
        if (is_numeric($_1862100254) && $_1862100254 > 0) {
            $_928705056 = Profile::getList(['filter' => ['ID' => $_1862100254]])->fetch();
            if (!is_array($_928705056)) {
                $_928705056 = [];
            }
        }
        return $_928705056;
    }

    public static function createDirectoriesForFile($_1604479403, $_414816284 = false)
    {
        $_271135157 = static::getDirectoryForFile($_1604479403);
        if (!is_dir($_271135157)) {
            @mkdir($_271135157, BX_DIR_PERMISSIONS, true);
        }
        if ($_414816284) {
            $_876120280 = substr(pathinfo($_1604479403, PATHINFO_DIRNAME), strlen(static::root()));
            $_876120280 = trim(static::path($_876120280), '/');
            $_1849821988 = explode('/', $_876120280);
            for ($_812684713 = 1; $_812684713 <= count($_1849821988); $_812684713++) {
                $_876120280 = implode('/', array_slice($_1849821988, 0, $_812684713));
                if (strlen($_876120280)) {
                    $_876120280 = '/' . $_876120280;
                    if (is_dir(static::root() . $_876120280)) {
                        static::changeFileOwner(static::root() . $_876120280);
                    }
                }
            }
        }
        return is_dir($_271135157);
    }

    public static function getDirectoryForFile($_1604479403)
    {
        return pathinfo($_1604479403, PATHINFO_DIRNAME);
    }

    public static function changeFileOwner($_2139427520)
    {
        if (Cli::isCli() && Cli::isRoot() && function_exists('fileowner')) {
            if (is_file($_2139427520) || is_dir($_2139427520)) {
                $_1387860344 = Cli::getBitrixUser();
                if (is_numeric($_1387860344)) {
                    $_1082766497 = @fileowner($_2139427520);
                    if ($_1082766497 === 0) {
                        if (function_exists('chown')) {
                            if (chown($_2139427520, $_1387860344)) {
                                if (function_exists('chgrp')) {
                                    if (chgrp($_2139427520, $_1387860344)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    } elseif ($_1082766497 === $_1387860344) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function memSet()
    {
        static::$_148592410 = memory_get_usage();
    }

    public static function memGet()
    {
        if (is_null(static::$_148592410)) {
            static::$_148592410 = 0;
        }
        return \CFile::formatSize(memory_get_usage() - static::$_148592410);
    }

    public static function selectBox($_1816364984, $_1476967698, $_1003151472 = null, $_961790076 = null, $_125890858 = null, $_174428025 = null, $_2127404181 = true)
    {
        $_1757886727 = static::randString(true);
        $_1476967698 = ['REFERENCE' => array_values($_1476967698), 'REFERENCE_ID' => array_keys($_1476967698),];
        if (is_null($_1003151472)) {
            $_1003151472 = reset($_1476967698['REFERENCE_ID']);
        }
        if (strlen($_174428025)) {
            $_125890858 .= sprintf(' id="%s"', $_174428025);
        }
        $_1028790420 = static::selectBoxFromArray($_1816364984, $_1476967698, $_1003151472, $_961790076, $_125890858);
        $_1028790420 = sprintf('<div id="%s">%s</div>', $_1757886727, $_1028790420);
        if ($_2127404181) {
            $_1028790420 .= sprintf("<script>wdaSelect2($('#%s > select'));</script>", $_1757886727);
        }
        return $_1028790420;
    }

    public static function selectBoxFromArray($_2082788189, $_1272243446, $_822387247 = "", $_316756035 = "", $_1951705318 = "")
    {
        $_1743009586 = htmlspecialcharsbx($_2082788189);
        $_1446791349 = '<select ' . $_1951705318 . ' name="' . $_1743009586 . '">';
        if (isset($_1272243446['reference']) && is_array($_1272243446['reference'])) $_1750286957 = $_1272243446['reference']; elseif (isset($_1272243446['REFERENCE']) && is_array($_1272243446['REFERENCE'])) $_1750286957 = $_1272243446['REFERENCE'];
        else $_1750286957 = array();
        if (isset($_1272243446['reference_id']) && is_array($_1272243446['reference_id'])) $_859087515 = $_1272243446['reference_id']; elseif (isset($_1272243446['REFERENCE_ID']) && is_array($_1272243446['REFERENCE_ID'])) $_859087515 = $_1272243446['REFERENCE_ID'];
        else $_859087515 = array();
        if ($_316756035 <> '') $_1446791349 .= '<option value="">' . $_316756035 . '</option>';
        foreach ($_1750286957 as $_812684713 => $_1406287827) {
            $_1446791349 .= '<option';
            if (is_array($_822387247)) {
                if (in_array($_859087515[$_812684713], $_822387247)) {
                    $_1446791349 .= ' selected';
                }
            } elseif (strcasecmp($_859087515[$_812684713], $_822387247) == 0) {
                $_1446791349 .= ' selected';
            }
            $_1446791349 .= ' value="' . htmlspecialcharsbx($_859087515[$_812684713]) . '">' . htmlspecialcharsbx($_1406287827) . '</option>';
        }
        return $_1446791349 . '</select>';
    }

    public static function replaceActionRandInputValues($_997246851)
    {
        $_342661612 = [];
        foreach ($_997246851 as $_483229610 => $_1868355201) {
            $_483229610 = preg_replace('#_' . Helper::RAND_ID_PREFIX . '[A-z0-9]{32}#i', '', $_483229610);
            if (isset($_342661612[$_483229610])) {
                if (!is_array($_342661612[$_483229610])) {
                    $_342661612[$_483229610] = [$_342661612[$_483229610]];
                }
                $_342661612[$_483229610][] = $_1868355201;
            } else {
                $_342661612[$_483229610] = $_1868355201;
            }
        }
        return $_342661612;
    }

    public static function getModuleVersion($_1907706463)
    {
        include static::root() . '/bitrix/modules/' . $_1907706463 . '/install/version.php';
        return $arModuleVersion['VERSION'];
    }

    public static function getCurrentDomain()
    {
        return preg_replace('#:(\d+)$#', '', \Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost());
    }

    public static function getUserTitle($_1272622672, $_1156430534 = 1)
    {
        $_1272622672 = intVal($_1272622672);
        $_1156430534 = $_1156430534 >= 1 && $_1156430534 <= 4 ? $_1156430534 : 1;
        if ($_1272622672 > 0) {
            $_222350050 = \CUser::getByID($_1272622672);
            if ($_580925084 = $_222350050->fetch()) {
                $_34609928 = \CUser::FormatName(\CSite::getNameFormat(), $_580925084, true, false);
                $_792914969 = '<a title="' . static::getMessage('MAIN_EDIT_USER_PROFILE') . '" href="/bitrix/admin/user_edit.php?ID=' . $_580925084['ID'] . '&lang=' . LANGUAGE_ID . '" target="_blank">' . $_580925084['ID'] . '</a>';
                switch ($_1156430534) {
                    case 1:
                        $_34609928 = sprintf('%s', $_34609928);
                        break;
                    case 2:
                        $_34609928 = sprintf('(%s) %s', $_580925084['LOGIN'], $_34609928);
                        break;
                    case 3:
                        $_34609928 = sprintf('[%s] (%s) %s', $_580925084['ID'], $_580925084['LOGIN'], $_34609928);
                        break;
                    case 4:
                        $_34609928 =sprintf('[%s] (%s) %s', $_792914969, $_580925084['LOGIN'], $_34609928);
                        break;
                }
                return $_34609928;
            }
        }
        return false;
    }

    public static function calendar($_174428025, $_280663544 = false)
    {
        return static::includeFile('calendar', ['INPUT_ID' => $_174428025, 'WITH_TIME' => $_280663544,]);
    }

    public static function translate($_1670065241)
    {
        $_1619799334 = $_1670065241;
        $_1699259395 = static::getOption('translate_key_yandex', null, 'main');
        $_1387561055 = 'ru-en';
        $_1825916521 = 'bitrix';
        if (!static::isUtf()) {
            $_1670065241 = static::convertEncoding($_1670065241, 'CP1251', 'UTF-8');
        }
        $_1670065241 = urlencode($_1670065241);
        $_2013481515 = sprintf('https://translate.yandex.net/api/v1.5/tr.json/translate?key=%s&lang=%s&clientId=%s&text=%s', $_1699259395, $_1387561055, $_1825916521, $_1670065241);
        $_1714917240 = HttpRequest::getHttpContent($_2013481515);
        if (strlen($_1714917240)) {
            $_795884440 = \Bitrix\Main\Web\Json::decode($_1714917240);
            if (is_array($_795884440) && isset($_795884440['text']) && is_array($_795884440['text'])) {
                $_1619799334 = reset($_795884440['text']);
            }
        }
        return $_1619799334;
    }

    public static function toUnderlineCase($_1670065241, $_1407606827 = true)
    {
        $_1670065241 = preg_replace('#([a-z]{1})([A-Z]{1})#', '$1_$2', $_1670065241);
        if ($_1407606827) {
            $_1670065241 = toUpper($_1670065241);
        } else {
            $_1670065241 = toLower($_1670065241);
        }
        return $_1670065241;
    }

    public static function toCamelCase($_1670065241)
    {
        return preg_replace_callback('#_([A-z])#', function ($_1050251376) {
            return toUpper($_1050251376[1]);
        }, $_1670065241);
    }

    public static function getMailtoLink(array $_1219934872, $_1398237281 = false)
    {
        $_1219934872['EMAIL'] = strlen($_1219934872['EMAIL']) ? $_1219934872['EMAIL'] : 'info@nulled.com';
        $_1219934872['SUBJECT'] = strlen($_1219934872['SUBJECT']) ? sprintf('subject=%s', $_1219934872['SUBJECT']) : '';
        $_1219934872['BODY'] = strlen($_1219934872['BODY']) ? sprintf('body=%s', $_1219934872['BODY']) : '';
        $_1219934872['ATTR'] = strlen($_1219934872['ATTR']) ? ' ' . $_1219934872['ATTR'] : '';
        $_208688364 = [$_1219934872['SUBJECT'], $_1219934872['BODY']];
        $_208688364 = array_filter($_208688364);
        $_1707924649 = implode('&', $_208688364);
        $_2013481515 = $_1219934872['EMAIL'] . (strlen($_1707924649) ? '?' . $_1707924649 : '');
        if ($_1398237281) {
            return sprintf('mailto:%s', $_2013481515);
        }
        return sprintf('<a href="mailto:%s"%s>%s</a>', $_2013481515, $_1219934872['ATTR'], $_1219934872['TEXT']);
    }

    public static function interpretPrice($_760694696, $_666012849, $_650810083 = 0)
    {
        if (strlen($_666012849)) {
            if (is_string($_760694696)) {
                $_760694696 = str_replace(',', '.', $_760694696);
            }
            if (preg_match('#^\+([\d.]+)%$#', $_666012849, $_1050251376)) {
                $_760694696 += round($_760694696 * $_1050251376[1] / 100, 2);
            } elseif (preg_match('#^\-([\d.]+)%$#', $_666012849, $_1050251376)) {
                $_760694696 -= round($_760694696 * $_1050251376[1] / 100, 2);
            } elseif (preg_match('#^([\d.]+)%$#', $_666012849, $_1050251376)) {
                $_760694696 = round($_760694696 * $_1050251376[1] / 100, 2);
            } elseif (preg_match('#^\+([\d.]+)$#', $_666012849, $_1050251376)) {
                $_760694696 += $_1050251376[1];
            } elseif (preg_match('#^\-([\d.]+)$#', $_666012849, $_1050251376)) {
                $_760694696 -= $_1050251376[1];
            }
            if ($_760694696 < $_650810083) {
                $_760694696 = $_650810083;
            }
        }
        return $_760694696;
    }

    public static function roundEx($_2131576432, $_1321521670 = 0, $_1638790197 = null)
    {
        $_772863400 = pow(10, $_1321521670);
        $_1638790197 = in_array($_1638790197, array('round', 'floor', 'ceil')) ? $_1638790197 : 'round';
        return call_user_func($_1638790197, $_2131576432 * $_772863400) / $_772863400;
    }

    public static function convertDecPoint($_165792130)
    {
        $_1581225885 = localeconv();
        $_72635898 = $_1581225885['decimal_point'] == '.';
        $_668979253 = $_1581225885['decimal_point'] == '.' ? ',' : '.';
        return str_replace($_72635898 ? ',' : '.', $_72635898 ? '.' : ',', $_165792130);
    }

    public static function evalReturn($_2014682869)
    {
        if (!strlen($_2014682869)) {
            $_2014682869 = 'null';
        }
        return eval(sprintf('return %s;', $_2014682869));
    }

    public static function generateGuid($_2079568438 = false)
    {
        $_1619799334 = sprintf('%s-%s-%s-%s-%s', randString(8), randString(4), randString(4), randString(4), randString12);
        if ($_2079568438) {
            $_1619799334 = toUpper($_1619799334);
        } else {
            $_1619799334 = toLower($_1619799334);
        }
        return $_1619799334;
    }

    public static function evalExpression($_666012849, $_165792130 = null, $_1693898781 = null)
    {
        $_666012849 = preg_replace('#;[\s]*#', '', $_666012849);
        if (!is_null($_165792130)) {
            $_165792130 = is_string($_165792130) || is_numeric($_165792130) ? $_165792130 : '';
            $_1693898781 = is_string($_1693898781) && strlen($_1693898781) ? $_1693898781 : 'x';
            $_666012849 = sprintf('$%s = %s; return (%s)', $_1693898781, var_export($_165792130, true), $_666012849);
        }
        $_666012849 .= ';';
        return eval($_666012849);
    }

    public static function execCommand($_1962965108, $_165792130 = null)
    {
        $_1619799334 = '';
        if (function_exists('exec')) {
            if (strpos($_1962965108, '#VALUE#') !== false) {
                $_1962965108 = str_replace('#VALUE#', $_165792130, $_1962965108);
            }
            @exec($_1962965108, $_342661612);
            $_1619799334 = implode(' ', $_342661612);
        }
        return $_1619799334;
    }

    public static function ucFirst($_1670065241)
    {
        return toUpper(mb_substr($_1670065241, 0, 1)) . mb_substr($_1670065241, 1);
    }

    public static function lcFirst($_1670065241)
    {
        return toLower(mb_substr($_1670065241, 0, 1)) . mb_substr($_1670065241, 1);
    }

    public static function mb_str_ireplace($_1510980160, $_2049050116, $_885958625)
    {
        return preg_replace('#(' . preg_quote($_1510980160) . ')#iu', $_2049050116, $_885958625);
    }
} ?>