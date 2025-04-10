<?php
$_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT = realpath(dirname(__FILE__).'/../../..');
define('WDA_CRON', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS',true); 
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
set_time_limit(0);
ignore_user_abort(true);
if(CModule::IncludeModule('webdebug.antirutin')) {
	if(\WD\Antirutin\Helper::getOption('disable_old_module') == 'Y'){
		print 'Old module is disabled. More info: https://www.webdebug.ru/marketplace/webdebug.antirutin/?tab=faq'.PHP_EOL;
		die();
	}
	CWDA::CronExec($argv);
}
?>