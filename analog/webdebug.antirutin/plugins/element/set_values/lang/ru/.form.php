<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FIELD'] = 'Поле/свойство';
	$MESS[$strHint.'FIELD'] = 'Выберите поле/свойство для заполнения.';
$MESS[$strLang.'VALUE'] = 'Значение';
	$MESS[$strHint.'VALUE'] = 'Укажите здесь значение, которым будет заполнено свойство.';
$MESS[$strLang.'USE_CUSTOM_PHP'] = 'Заполнить поле/свойство на основе PHP-кода';
	$MESS[$strHint.'USE_CUSTOM_PHP'] = 'Выполнить собственный PHP-код (код должен вернуть значение для сохранения). Доступен полный массив товара <code>$arElement</code>.';
$MESS[$strLang.'PHP_CODE'] = 'PHP-код';
	$MESS[$strHint.'PHP_CODE'] = 'PHP-код (код должен вернуть значение для сохранения). Доступны следующие данные:
	<ul>
		<li>данные из выбранного поля/свойства <code>$value</code>,</li>
		<li>полный массив товара <code>$arElement</code></li>
	</ul>
	';
$MESS[$strLang.'PHP_DEBUG_MODE'] = 'Режим отладки';
	$MESS[$strHint.'PHP_DEBUG_MODE'] = 'Отметьте галочку, если необходимо показать результат, без сохранения.';

?>