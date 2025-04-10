<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FIELD'] = 'Поле/свойство';
	$MESS[$strHint.'FIELD'] = 'Выберите поле/свойство, которое будет заполнено по шаблону.';
$MESS[$strLang.'TEXT_TYPE'] = 'Тип текста';
	$MESS[$strHint.'TEXT_TYPE'] = 'Укажите тип текста для сохранения';
	$MESS[$strLang.'TEXT_TYPE_NO'] = '--- по умолчанию ---';
	$MESS[$strLang.'TEXT_TYPE_TEXT'] = 'Текст';
	$MESS[$strLang.'TEXT_TYPE_HTML'] = 'HTML';
$MESS[$strLang.'PATTERN_MACRO'] = 'Выберите макрос';
	$MESS[$strHint.'PATTERN_MACRO'] = 'Поочередно добавляйте необходимые макросы в текст. Выбранный макрос вставляется на место курсора.';
	$MESS[$strLang.'PATTERN_MACRO_TITLE'] = 'Добавить макрос: ';
$MESS[$strLang.'PATTERN'] = 'Шаблон';
	$MESS[$strHint.'PATTERN'] = 'Шаблон для генерации итогового значения.';
$MESS[$strLang.'USE_AS_FORMULA'] = 'Выполнить расчёт по формуле';
	$MESS[$strHint.'USE_AS_FORMULA'] = 'Отметьте галочку, если в значении используются арифметические и другие операторы.<br/><br/>
Пример значения при отмеченной галочке:<br/>
<code style="white-space:nowrap;">{=this.catalog.price.BASE} * {=this.property.RATIO}</code><br/><br>
Это посчитает значение цены, умноженное на свойство с кодом RATIO.';

?>