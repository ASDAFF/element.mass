<?
$strLang = 'WDA_POPUP_PROFILE_CRON_';

$MESS[$strLang.'EXTERNAL_ID'] = 'Идентификатор задания';
$MESS[$strLang.'MINUTE'] = 'Минута';
$MESS[$strLang.'HOUR'] = 'Час';
$MESS[$strLang.'DAY'] = 'День';
$MESS[$strLang.'MONTH'] = 'Месяц';
$MESS[$strLang.'WEEKDAY'] = 'День недели';
$MESS[$strLang.'DELETE'] = '';

$MESS[$strLang.'BUTTON_DELETE'] = 'Удалить';
$MESS[$strLang.'BUTTON_DELETE_CONFIRM'] = 'Удалить выбранное задание?';

$MESS[$strLang.'NO_TASKS'] = 'Заданий планировщика нет.';

$MESS[$strLang.'PARAM_PHP'] = 'Путь к PHP на сервере:';
$MESS[$strLang.'PARAM_PHP_CONFIG'] = 'Доп. конфиги PHP:';
$MESS[$strLang.'PARAM_PHP_MBSTRING'] = 'Добавлять параметры mbstring:';

$MESS[$strLang.'CANNOT_AUTOSET'] = '<b>Внимание!</b> На Вашем сервере нет возможности автоматической установки заданий планировщика.<br/>Необходимо устанавливать задания вручную.';
$MESS[$strLang.'COMMAND'] = '<b>Команда для ручной настройки (возможно, команда требует уточнения):</b>';

$strLogPlace = '/upload/webdebug.antirutin/log';
$MESS[$strLang.'LOGS_NOTICE'] = '<b>Логи</b> по каждому из профилей хранятся в папке <a href="/bitrix/admin/fileman_admin.php?lang='.LANGUAGE_ID.'&site=&path='.urlencode($strLogPlace).'&show_perms_for=0&check_for_file=Y" target="_blank">'.$strLogPlace.'</a>.';

?>