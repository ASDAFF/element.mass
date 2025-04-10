<?
$MESS['WDA_PAGE_TITLE'] = 'Антирутин: Массовая обработка элементов и разделов инфоблоков';

# General
$MESS['JSADM_FILES'] = isset($MESS['JSADM_FILES']) ? $MESS['JSADM_FILES'] : '';
$MESS['WDA_CANNOT_START'] = 'Проверьте введенные данные.';
$MESS['WDA_CANNOT_START_NO_ACTIONS'] = 'Запуск невозможен: не задано ни одно действие для обработки.';
$MESS['WDA_CANNOT_SAVE_NO_ACTION'] = 'Сохранение профиля невозможно: не задано ни одно действие для обработки.';
$MESS['WDA_UNKNOWN_ERROR'] = 'Ошибка. Попробуйте еще раз.';
$MESS['WDA_CAREFULLY_NOTICE'] = '<b>Внимание!</b> Имейте в виду, что процесс обработки затрагивает обработку сразу большого количества товаров, а случайный запуск процесса с ошибочными параметрами может привести к серьезным последствиям.<br/>
При настройке профилей всегда добавляйте в фильтр ID одного конкретного товара, чтобы <b style="color:#bf1818">протестировать выгрузку сначала на одном товаре</b>.<br/>
Также, не забывайте регулярно <a href="/bitrix/admin/dump.php?lang='.LANGUAGE_ID.'" target="_blank" style="color:#bf1818"><b>создавать резервные копии сайта</b></a> (как минимум база данных + публичная часть).<br/>
P.S: это уведомление можно отключить в <a href="/bitrix/admin/settings.php?lang=ru&mid=webdebug.antirutin" target="_blank" style="color:#716536">настройках модуля</a>.';

# TabControl
$MESS['WDA_TAB_SETTINGS_NAME'] = 'Настройка';
$MESS['WDA_TAB_SETTINGS_DESC'] = 'Настройка массовой операции';

# Fields
$MESS['WDA_HEADER_ENTITY'] = 'Что обрабатывать';
	$MESS['WDA_TYPE_ELEMENT'] = 'Элементы';
	$MESS['WDA_TYPE_SECTION'] = 'Разделы';
$MESS['WDA_HEADER_SOURCE'] = 'Источник данных';
	$MESS['WDA_IBLOCK_ID'] = 'Инфоблок:';
		$MESS['WDA_IBLOCK_ID_PLACEHOLDER'] = '--- выберите инфоблок ---';
		$MESS['WDA_SELECT_SECTIONS'] = 'Выбрать разделы';
		$MESS['WDA_SECTIONS_MAX_DEPTH'] = 'Вложенность:';
	$MESS['WDA_SECTIONS'] = 'Разделы:';
		$MESS['WDA_SECTIONS_PLACEHOLDER'] = '--- не учитывать раздел ---';
		$MESS['WDA_SECTIONS_ROOT_LEVEL'] = '--- верхний уровень ---';
		$MESS['WDA_INCLUDE_SUBSECTIONS'] = 'С подразделами';
	$MESS['WDA_SECTIONS_FILTER'] = 'Фильтр по разделам';
	$MESS['WDA_SECTIONS_SHOW_FILTER'] = 'Показать фильтр';
	$MESS['WDA_SECTIONS_SELECT_ALL'] = 'Выделить все';
	$MESS['WDA_SECTIONS_SELECT_INVERT'] = 'Инвертировать';
	$MESS['WDA_SECTIONS_UNSELECT_ALL'] = 'Снять выделение';
	$MESS['WDA_SECTIONS_SELECT_SHOW'] = 'Показать выбранные';
$MESS['WDA_FILTER_NOTICE_SECTION_SUBSECTIONS'] = '<b>Внимание!</b> Будут обработаны не выбранные разделы, а те разделы, которые находятся в выбранных.<br/>Другими словами, используется такая же самая логика, что и для элементов.';
$MESS['WDA_FILTER_NOTICE_SECTION_INCLUDE_SUBSECTIONS'] = '<b>Внимание!</b> У Вас используется старая версия Битрикс. Фильтрация по подразделам недоступна.<br/>
Необходимо обновить Битрикс до последней версии. <a href="#EMAIL_HREF#">Задать вопрос по обновлению</a>.';

// Filter
$MESS['WDA_HEADER_FILTER'] = 'Фильтр';
	$MESS['WDA_FILTER_RESULTS'] = 'Найдено:';
	$MESS['WDA_FILTER_SHOW_ARRAY'] = 'Показать массив';
	$MESS['WDA_FILTER_NOTICE_SECTION_RESTRICTIONS'] = 'У Вас используется старая версия Битрикс. Фильтрация по разделам ограничена по функциональности.<br/>
Необходимо обновить Битрикс до последней версии. <a href="#EMAIL_HREF#">Задать вопрос по обновлению</a>.';
	$MESS['WDA_FILTER_NOTICE_SECTION_RESTRICTIONS_ASK_SUBJECT'] = 'Вопрос по продлению лицензии Битрикс.';

// Actions
$MESS['WDA_HEADER_ACTIONS'] = 'Действия';
	$MESS['WDA_HEADER_ACTIONS_PLACEHODER'] = '--- Выберите действие ---';

// Task
$MESS['WDA_HEADER_TASK'] = 'Задача (группа действий)';
	$MESS['WDA_HEADER_TASK_SETTINGS'] = 'Настройки';
$MESS['WDA_TASK_SORT'] = 'Сортировать';

// Buttons
$MESS['WDA_BUTTON_START'] = 'Запустить';
$MESS['WDA_BUTTON_RESUME'] = 'Продолжить';
$MESS['WDA_BUTTON_STOP'] = 'Остановить';
$MESS['WDA_BUTTON_PROFILE_LOAD'] = 'Управление профилями';
$MESS['WDA_BUTTON_PROFILE_SAVE'] = 'Сохранить профиль';
$MESS['WDA_BUTTON_RELOAD_PLUGIN'] = 'Обновить';
$MESS['WDA_BUTTON_SUPPORT'] = 'Техподдержка';

// Other
$MESS['WDA_ERROR_SET_CRON_TASKS'] = 'Ошибка установки задачи в планировщик.'."\n".'Возможно, Ваш сервер не поддерживает этого.'."\n".'Необходимо установить задания вручную.';
$MESS['WDA_MESSAGE_SUPPORT_SUCCESS'] = 'Ваш запрос успешно отправлен!';
$MESS['WDA_NO_PLUGIN_MAILTO_TEXT'] = 'Мне нужно действие, которого нет в списке';
$MESS['WDA_NO_PLUGIN_MAILTO_SUBJECT'] = 'Антирутин: заявка на разработку нового плагина';
$MESS['WDA_NO_PLUGIN_SELECTED'] = 'Выберите действие из списка. #LINK#.';
$MESS['WDA_PAGE_PROFILE_TITLE'] = 'Профиль: ';

# Popup titles
$strLang = 'WDA_POPUP_';
$MESS[$strLang.'PROFILE_SAVE_TITLE'] = 'Сохранение настроек в профиль';
$MESS[$strLang.'PROFILE_LOAD_TITLE'] = 'Управление профилями';
$MESS[$strLang.'PROFILE_LOAD_NOTICE'] = 'Чтобы загрузить профиль, нажмите на строку с его названием.';
$MESS[$strLang.'PROFILE_CRON_TITLE'] = 'Настройка автозапуска профиля';
$MESS[$strLang.'PREVIEW_TITLE'] = 'Результаты фильтрации';
$MESS[$strLang.'ERROR_TITLE'] = 'Ошибка выполнения';
$MESS[$strLang.'DEBUG_TITLE'] = 'Отладочные сообщения';
$MESS[$strLang.'RESULTS_TITLE'] = 'Процесс завершен';
$MESS[$strLang.'HELP_TITLE'] = 'Справка';
$MESS[$strLang.'HELP_TITLE_2'] = 'Справка: #PLUGIN_NAME#';
$MESS[$strLang.'SUPPORT_TITLE'] = 'Техподдержка';

?>