<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'NAME'] = 'Генерация ТП';
$MESS[$strLang.'HINT'] = 'Плагин выполняет генерацию торговых предложений для товаров.';

// Modes
$MESS[$strLang.'MODE_DEFAULT'] = 'По умолчанию (по свойствам)';
$MESS[$strLang.'MODE_SIMPLE'] = 'Простой';

// Errors: default
$MESS[$strLang.'ERROR_DEFAULT_NO_OFFERS'] = 'Инфоблок не имеет ТП.';
$MESS[$strLang.'ERROR_DEFAULT_NO_PROPS'] = 'Не выбраны свойства для генерации ТП.';

// Errors: simple
$MESS[$strLang.'ERROR_SIMPLE_NO_COUNT'] = 'Не указано количество ТП.';
$MESS[$strLang.'ERROR_SIMPLE_IBLOCK_HAS_NO_OFFERS'] = 'Инфоблок не настроен на работу с ТП.';

?>