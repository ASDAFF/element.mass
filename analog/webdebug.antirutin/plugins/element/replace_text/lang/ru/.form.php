<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FIELD'] = 'Поле/свойство';
	$MESS[$strHint.'FIELD'] = 'Выберите поле/свойство, в котором будет произведена замена.';

$MESS[$strLang.'MODE'] = 'Режим замены';
	$MESS[$strHint.'MODE'] = 'Выберите режим замен в тексте.';
	$MESS[$strLang.'MODE_SIMPLE'] = 'Простой режим замены';
	$MESS[$strLang.'MODE_MULTILINE'] = 'Режим многострочной замены';
	$MESS[$strLang.'MODE_REG_EXP'] = 'Замена с использованием регулярного выражения';
	$MESS[$strLang.'MODE_APPEND'] = 'Дописать в конце';
	$MESS[$strLang.'MODE_PREPEND'] = 'Дописать в начале';

$MESS[$strLang.'SIMPLE_CASE_SENSITIVE'] = 'Учитывать регистр символов';
	$MESS[$strHint.'SIMPLE_CASE_SENSITIVE'] = 'Отметьте галочку, если необходимо учитывать регистр символов (в таком случае при поиске «текст» слово «Текст» не будет найдено).';
$MESS[$strLang.'SIMPLE_SEARCH'] = 'Что ищем?';
	$MESS[$strHint.'SIMPLE_SEARCH'] = 'Укажите здесь текст для поиска.';
$MESS[$strLang.'SIMPLE_REPLACE'] = 'На что заменяем?';
	$MESS[$strHint.'SIMPLE_REPLACE'] = 'Укажите здесь текст, которым нужно заменить найденный текст.';

$MESS[$strLang.'MULTILINE_CASE_SENSITIVE'] = 'Учитывать регистр символов';
	$MESS[$strHint.'MULTILINE_CASE_SENSITIVE'] = 'Отметьте галочку, если необходимо учитывать регистр символов (в таком случае при поиске «текст» слово «Текст» не будет найдено).';
$MESS[$strLang.'MULTILINE_SEARCH'] = 'Что ищем?';
	$MESS[$strHint.'MULTILINE_SEARCH'] = 'Укажите здесь текст для поиска.<br/><br/>
	Каждая строка считается отдельным выражением для поиска, т.е. сколько строк - столько раз будет выполнен поиск и замена.';
$MESS[$strLang.'MULTILINE_REPLACE'] = 'На что заменяем?';
	$MESS[$strHint.'MULTILINE_REPLACE'] = 'Укажите здесь текст, которым нужно заменить найденный текст.<br/><br/>
	Строка замены для всех строк - общая.';
	
$MESS[$strLang.'REG_EXP_SEARCH'] = 'Регулярное выражение';
	$MESS[$strHint.'REG_EXP_SEARCH'] = 'Укажите здесь регулярное выражение для поиска текста. Примеры:
<ul>
	<li><code>#value_(\\\d+)#i</code></li>
	<li><code>/value_(\\\s+)/ims</code></li>
	<li><code>#(\\\d+)\.(\\\d+)#</code></li>
</ul>
<a href="https://www.php.net/manual/ru/reference.pcre.pattern.syntax.php" target="_blank">Подробнее о регулярных выражениях</a>.<br/></br>
<a href="https://www.phpliveregex.com" target="_blank">Онлайн-сервис проверки регулярных выражений</a>';
$MESS[$strLang.'REG_EXP_SEARCH_HELP'] = '<i>Необходима помощь с составлением регулярного выражения? <a href="mailto:info@wnulled.com?subject=Антирутин: помощь с регулярным выражением для замены текста" class="wda-inline-link">Напишите нам!</a></i>';
$MESS[$strLang.'REG_EXP_REPLACE'] = 'Замена';
	$MESS[$strHint.'REG_EXP_REPLACE'] = 'Укажите здесь текст, которым заменяем текст, найденный по регулярному выражению.<br/>
<p>Обозначение $1 заменяет найденное в первых круглых скобках, $2 - во вторых, и т.д. Примеры:</p>
<ul>
	<li><code>Found: $1</code>,</li>
	<li><code>Value_$1</code>.</li>
</ul>
<a href="https://www.php.net/manual/ru/reference.pcre.pattern.syntax.php" target="_blank">Подробнее о регулярных выражениях</a>.';
$MESS[$strLang.'REG_EXP_LIMIT'] = 'Максимум замен';
	$MESS[$strHint.'REG_EXP_LIMIT'] = 'Укажите здесь число, обозначающее максимальное кол-во замен. Если указано пустое значение или ноль - ограничение не применяется.';
$MESS[$strLang.'APPEND_TEXT'] = 'Дописываемое значение';
	$MESS[$strHint.'APPEND_TEXT'] = 'Укажите здесь текст, который будет добавлен в конце каждого текстового значения.';
$MESS[$strLang.'PREPEND_TEXT'] = 'Дописываемое значение';
	$MESS[$strHint.'PREPEND_TEXT'] = 'Укажите здесь текст, который будет добавлен в начале каждого текстового значения.';


?>