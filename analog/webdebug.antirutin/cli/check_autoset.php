<?php
define('WDA_CRON', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true); 
$_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT = realpath(__DIR__.'/../../../../');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/init_admin.php');
set_time_limit(0);
ignore_user_abort(true);
$strModuleId = 'webdebug.antirutin';
if(\Bitrix\Main\Loader::includeModule($strModuleId)) {
	print 'Y';
}
?>