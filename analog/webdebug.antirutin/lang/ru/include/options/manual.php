<?
$strLang = 'WDA_OPTIONS_';
$strHint = $strLang.'HINT_';

$MESS[$strLang.'OPTION_MANUAL'] = 'Настройки ручного запуска';
$MESS[$strLang.'OPTION_STEP_TIME'] = 'Время одного шага выполнения';
	$MESS[$strHint.'OPTION_STEP_TIME'] = 'Укажите время выполнения одного шага (в секундах). При большом кол-ве элементов/разделов, когда сам запрос занимает значительную часть времени, имеет смысл увеличить данное значение. При этом время выполнения шага нельзя устанавливать выше таймаутов сервера.';
$MESS[$strLang.'OPTION_SHOW_RESULTS'] = 'Открывать окно результатов';
	$MESS[$strHint.'OPTION_SHOW_RESULTS'] = 'Выберите в каких случаях должно показываться всплывающее окно с результатами обработки.';
$MESS[$strLang.'OPTION_EMAIL_ENABLED'] = 'Отправлять email с результатами';
	$MESS[$strHint.'OPTION_EMAIL_ENABLED'] = 'Укажите, нужно ли отправлять email после завершения процесса.<br/><br/><b>Внимание!</b> Это работает только при запуске из планировщика или в фоновом режиме.';
$MESS[$strLang.'OPTION_EMAIL_ADDRESS'] = 'Email-адрес для уведомлений';
	$MESS[$strHint.'OPTION_EMAIL_ADDRESS'] = 'Укажите адрес email для отправки уведомлений при окончании процесса обработки.<br/><br/>Можно указать несколько адресов через запятую (пробел не играет значения).';
?>