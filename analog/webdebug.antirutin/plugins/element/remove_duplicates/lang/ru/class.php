<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'NAME'] = 'Удаление дубликатов элементов';
$MESS[$strLang.'HINT'] = 'Удаление дубликатов элементов';

$MESS[$strLang.'PROP_NAME'] = 'Сессия удаления дубликатов';
$MESS[$strLang.'PROP_CREATE_ERROR'] = 'Ошибка создания служебного свойства: #ERROR#.';

$MESS[$strLang.'ERROR_NO_FIELD'] = 'Не выбрано поле, которое используется для сравнения.';
$MESS[$strLang.'PROMPT_DELETE'] = 'Вы уверены что хотите запустить удаление дубликатов?'."\n".
'Вместо удаления советуем использовать деактивацию.';
$MESS[$strLang.'PROMPT_CANCELLED'] = 'Выполнение отменено';

$MESS[$strLang.'LOG_FOUND_SAME_ELEMENTS'] = 'Найдены дубликаты: #IDS#.';
$MESS[$strLang.'LOG_TARGET'] = 'Актуальный элемент: #ID#.';
$MESS[$strLang.'LOG_SOURCE'] = 'Старый элемент: #ID#.';
$MESS[$strLang.'LOG_ELEMENT_DELETE'] = 'Элемент #ID# удален.';
$MESS[$strLang.'LOG_ELEMENT_DEACTIVATE'] = 'Элемент #ID# деактивирован.';
?>