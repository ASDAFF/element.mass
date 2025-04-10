<?
$strLang = 'WDA_POPUP_TASK_SETTINGS_';
$strHint = $strLang.'HINT_';

$MESS[$strLang.'MODULE_SETTINGS'] = 'Значение из настроек модуля';
$MESS[$strLang.'MODULE_SETTINGS_VALUE'] = '--- Использовать настройки модуля ---';

$MESS[$strLang.'STEP_TIME'] = 'Время одного шага выполнения';
	$MESS[$strHint.'STEP_TIME'] = 'Укажите время выполнения одного шага обработки (в секундах). Указывать значение более 25 секунд не рекомендуется и в некоторых случаях это приведет к ошибке при выполнении.<br/><br/>
Если значение пусто, используются <a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid='.WDA_MODULE.'" target="_blank">настройки модуля</a>.';
$MESS[$strLang.'SHOW_RESULTS_POPUP'] = 'Открывать окно результатов';
	$MESS[$strHint.'SHOW_RESULTS_POPUP'] = 'Выберите режим отображения всплывающего окна с результатами обработки (доступно только при ручном запуске).';
$MESS[$strLang.'EMAIL_ENABLED'] = 'Отправлять email с результатами';
	$MESS[$strHint.'EMAIL_ENABLED'] = 'Выберите режим отправки email-уведомлений с результатами обработки.<br/><br/><b>Внимание!</b> Это работает только при запуске из планировщика или в фоновом режиме.';
$MESS[$strLang.'EMAIL_ADDRESS'] = 'Email-адрес для уведомлений';
	$MESS[$strHint.'EMAIL_ADDRESS'] = 'Укажите email-адрес, на который будут отправляться сообщения с результатами обработки.<br/><br/>Можно указать несколько адресов через запятую (пробел не играет значения).<br/><br/>Если опция отправки включена, но адрес пуст, используются <a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid='.WDA_MODULE.'" target="_blank">настройки модуля</a>.';


?>