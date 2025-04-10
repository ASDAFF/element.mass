<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FIELD'] = 'Поле/свойство';
	$MESS[$strHint.'FIELD'] = 'Выберите поле/свойство, из которого будет взят текст, который и будет использован для генерации символьного кода. Обычно используется название раздела.';

$MESS[$strLang.'SETTINGS_TYPE'] = 'Настройки символьного кода';
	$MESS[$strHint.'SETTINGS_TYPE'] = 'Выберите тип используемых настроек генерации кода.';
		$MESS[$strLang.'SETTINGS_TYPE_IBLOCK'] = 'Использовать настройки инфоблока';
		$MESS[$strLang.'SETTINGS_TYPE_CUSTOM'] = 'Использовать собственные настройки';

$MESS[$strLang.'SETTINGS_UNIQUE'] = 'Проверять на уникальность';
	$MESS[$strHint.'SETTINGS_UNIQUE'] = 'Отметьте галочку, если необходимо обеспечить уникальность сгенерированного кода.';
$MESS[$strLang.'SETTINGS_TRANSLIT'] = 'Транслитерировать';
	$MESS[$strHint.'SETTINGS_TRANSLIT'] = 'Транслитерация это перевод кириллицы в латиницу, например: «Проверка текста» - «proverka-teksta».';
$MESS[$strLang.'SETTINGS_LENGTH'] = 'Максимальная длина результата транслитерации';
	$MESS[$strHint.'SETTINGS_LENGTH'] = 'Укажите, какой может быть максимальная длина сгенерированного текста. Все что превышает - будет обрезано. Если значение пусто, максимальная длина составит 255 символов.';
$MESS[$strLang.'SETTINGS_CASE'] = 'Приведение к регистру';
	$MESS[$strHint.'SETTINGS_CASE'] = 'Укажите, в каком регистре должен быть результат генерации: верхнем («ТЕКСТ») или нижнем («текст»).';
	$MESS[$strLang.'SETTINGS_CASE_SAVE'] = 'сохранить';
	$MESS[$strLang.'SETTINGS_CASE_LOWER'] = 'к нижнему';
	$MESS[$strLang.'SETTINGS_CASE_UPPER'] = 'к верхнему';
$MESS[$strLang.'SETTINGS_REPLACE_SPACE'] = 'Замена для символа пробела';
	$MESS[$strHint.'SETTINGS_REPLACE_SPACE'] = 'Выберите, каким символом будут заменяться символы пробелов.';
$MESS[$strLang.'SETTINGS_REPLACE_OTHER'] = 'Замена для прочих символов';
	$MESS[$strHint.'SETTINGS_REPLACE_OTHER'] = 'Выберите, каким символом будут заменяться остальные символы кроме букв, цифр и пробелов.';
$MESS[$strLang.'SETTINGS_EAT'] = 'Удалять лишние символы замены';
	$MESS[$strHint.'SETTINGS_EAT'] = 'Отметьте опцию, если необходимо удалять лишние символы, например, если в тексте рядом стоят спецсимволы - каждый из них будет заменен, и создастся ряд таких символов, что в некоторых случаях нежелательно.';
$MESS[$strLang.'SETTINGS_GOOGLE'] = 'Использовать внешний сервис для перевода';
	$MESS[$strHint.'SETTINGS_GOOGLE'] = 'Отметьте опцию, если текст необходимо не транслитерировать, а переводить онлайн-сервисом переводчика.<br/><br/>
Имейте в виду, что для этого в <a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=main#opt_optimize_css_files" target="_blank">настройках главного модуля</a> должен быть указан APi-ключ для сервиса «Яндекс.Перевод».';


$MESS[$strLang.'EXISTS_ACTION'] = 'Действие в случае неуникальности кода';
	$MESS[$strHint.'EXISTS_ACTION'] = 'Выберите, как требуется поступить если сгенерированный код уже занят.';
		$MESS[$strLang.'EXISTS_ACTION_ADD_INDEX'] = 'Добавить порядковый индекс';
		$MESS[$strLang.'EXISTS_ACTION_ADD_ID_SECTION'] = 'Добавить ID раздела';

$MESS[$strLang.'ADDITIONAL_TEXT'] = 'Доп. текст в начале и конце';
	$MESS[$strHint.'ADDITIONAL_TEXT'] = 'Укажите здесь доп. текст, который будет добавляться к сгенерированному символьному коду.';

$MESS[$strLang.'PREVENT_CUT'] = 'Не обрезать слова посередине';
	$MESS[$strHint.'PREVENT_CUT'] = 'Отметьте галочку, чтобы механизм не обрезал слова посередине.<br/><br/>Т.е., если последнее слово вместилось только частично, его вместившася часть удаляется, что сокращает общую длину символьного кода.';
?>