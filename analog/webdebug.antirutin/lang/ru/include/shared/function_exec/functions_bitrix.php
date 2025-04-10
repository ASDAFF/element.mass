<?
$strLang = \WD\Antirutin\Plugin::LANG_PREFIX.'_FUNCTION_EXEC_';
$strNotRequired = ' (необязательно)';

$arLang = [
	'htmlspecialcharsbx' => [
		'@' => 'Удаление пробелов слева и справа',
		'FLAGS' => 'Режим обработки кавычек'.$strNotRequired,
	],
	'htmlspecialcharsback' => [
		'@' => 'Преобразование HTML-сущностей в соответствующие символы (напр., &quot; в ")',
	],
	'toLower' => [
		'@' => 'Преобразование к нижнему регистру',
		'LANG' => 'Язык'.$strNotRequired,
	],
	'toUpper' => [
		'@' => 'Преобразование к верхнему регистру',
		'LANG' => 'Язык'.$strNotRequired,
	],
	'roundEx' => [
		'@' => 'Округление числа',
		'PRECISION' => 'Точность'.$strNotRequired,
	],
	'utf8win1251' => [
		'@' => 'Преобразование кодировки из UTF-8 в windows-1251',
	],
	'truncateText' => [
		'@' => 'Обрезка текста',
		'LENGTH' => 'Ограничение по длине',
	],
	'txtToHtml' => [
		'@' => 'Преобразование TXT в HTML',
		'MAKE_URLS' => 'Преобразовавыть URL в <a href="URL">URL</a>'.$strNotRequired,
		'MAX_LENGTH' => 'Максимальная длина фразы без пробелов или символов перевода каретки'.$strNotRequired,
		'QUOTE_ENABLED' => 'Преобразовать <QUOTE>...</QUOTE> в рамку цитаты'.$strNotRequired,
		'NOT_CONVERT_AMPERSAND' => 'Преобразовывать символ "&" в "&amp;"'.$strNotRequired,
		'CODE_ENABLED' => 'Преобразовать <CODE>...</CODE> в readonly textarea'.$strNotRequired,
		'BIU_ENABLED' => 'Преобразовать <B>...</B>, <I>...</I>, <U>...</U> и т.д. в соответствующие HTML тэги'.$strNotRequired,
	],
	'htmlToTxt' => [
		'@' => 'Преобразование HTML в TXT',
		'SITE_URL' => 'URL сайта'.$strNotRequired,
		'MAX_LENGTH' => 'DELETE'.$strNotRequired,
		'DELETE' => 'Символы для удаления'.$strNotRequired,
		'LENGTH' => 'Ограничение по длине'.$strNotRequired,
	],
	'CTextParser::closeTags' => [
		'@' => 'Закрытие открытых тегов в HTML',
	],
	'CUtil::closeTags' => [
		'@' => 'Закрытие открытых тегов в HTML',
	],
	'CUtil::translit' => [
		'@' => 'Транслитерация',
		'LANG' => 'Язык',
		'PARAMS' => 'Массив параметров'.$strNotRequired,
	],
	'normalizePhone' => [
		'@' => 'Форматирование номера телефона',
		'MIN_LENGTH' => 'Минимальная длина'.$strNotRequired,
	],
	'escapePhpString' => [
		'@' => 'Экранирование PHP-кода',
		'ENCLOSER' => 'Тип кавычки'.$strNotRequired,
	],
	'unEscapePhpString' => [
		'@' => 'Снятие экранирования PHP-кода',
		'ENCLOSER' => 'Тип кавычки'.$strNotRequired,
	],
	'randString' => [
		'@' => 'Генерация случайного кода',
		'LENGTH' => 'Длина строки'.$strNotRequired,
		'PASS_CHARS' => 'Набор символов'.$strNotRequired,
	],
];

foreach($arLang as $strFunctionName => $arFunc){
	foreach($arFunc as $key => $value){
		if($key == '@'){
			$MESS[$strLang.$strFunctionName] = $value;
		}
		elseif(preg_match('#^([A-z0-9_-]+)__HINT$#', $key, $arMatch)){
			$MESS[$strLang.$strFunctionName.'__'.$key] = htmlspecialcharsbx($value);
		}
		else{
			$MESS[$strLang.$strFunctionName.'__'.$key] = $value;
		}
	}
}
