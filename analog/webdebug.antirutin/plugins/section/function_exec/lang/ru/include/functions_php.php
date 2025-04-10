<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);
$strLangFunc = $strLang.'FUNC_';
$strNotRequired = ' (необязательно)';

$arLang = [
	'trim' => [
		'@' => 'Удаление пробелов (и других символов) слева и справа',
		'MASK' => 'Символы для удаления'.$strNotRequired,
	],
	'ltrim' => [
		'@' => 'Удаление пробелов (и других символов) слева',
		'MASK' => 'Символы для удаления'.$strNotRequired,
	],
	'rtrim' => [
		'@' => 'Удаление пробелов (и других символов) справа',
		'MASK' => 'Символы для удаления'.$strNotRequired,
	],
	#
	'str_ireplace' => [
		'@' => 'Замена в строке (без учета регистра)',
		'SEARCH' => 'Ищем',
		'REPLACE' => 'Заменяем',
	],
	'str_replace' => [
		'@' => 'Замена в строке (с учетом регистра)',
		'SEARCH' => 'Ищем',
		'REPLACE' => 'Заменяем',
	],
	'substr_replace' => [
		'@' => 'Замена части строки',
		'REPLACEMENT' => 'Замена',
		'START' => 'Начало',
		'LENGTH' => 'Длина',
	],
	'preg_replace' => [
		'@' => 'Замена в строке регулярным выражением',
		'PATTERN' => 'Искомый шаблон',
		'REPLACEMENT' => 'Строка для замены',
		'LIMIT' => 'Максимум замен'.$strNotRequired,
	],
	#
	'substr' => [
		'@' => 'Замена подстрокой',
		'START' => 'Начало',
		'LENGTH' => 'Длина',
	],
	'str_pad' => [
		'@' => 'Дополнение строки другой строкой',
		'PAD_LENGTH' => 'Длина',
		'PAD_STRING' => 'Строка',
		'PAD_TYPE' => 'Тип операции',
	],
	'str_repeat' => [
		'@' => 'Дополнение строки другой строкой',
		'MULTIPLIER' => 'Количество повторений',
	],
	'str_shuffle' => [
		'@' => 'Случайное перемешивание символов',
	],
	'strrev' => [
		'@' => 'Переворот строки',
	],
	#
	'base64_decode' => [
		'@' => 'Декодирование данных, закодированных MIME base64',
	],
	'base64_encode' => [
		'@' => 'Кодирование данных в формат MIME base64',
	],
	#
	'html_entity_decode' => [
		'@' => 'Преобразование HTML-сущностей в соответствующие им символы',
		'FLAGS' => 'Режим обработки кавычек'.$strNotRequired,
		'ENCODING' => 'Кодировка'.$strNotRequired,
	],
	'htmlentities' => [
		'@' => 'Преобразование всех возможных символов в соответствующие HTML-сущности',
		'FLAGS' => 'Режим обработки кавычек'.$strNotRequired,
		'ENCODING' => 'Кодировка'.$strNotRequired,
		'DOUBLE_ENCODE' => 'Не кодировать сущности дважды'.$strNotRequired,
	],
	'htmlspecialchars' => [
		'@' => 'Преобразование HTML-сущностей',
		'FLAGS' => 'Режим обработки кавычек'.$strNotRequired,
		'ENCODING' => 'Кодировка'.$strNotRequired,
		'DOUBLE_ENCODE' => 'Не кодировать сущности дважды'.$strNotRequired,
	],
	'strip_tags' => [
		'@' => 'Удаление тегов HTML и PHP из строки',
		'ALLOWABLE_TAGS' => 'Оставляемые теги'.$strNotRequired,
		'ALLOWABLE_TAGS__HINT' => 'Пример: <b><i><u><strong>',
	],
	#
	'addslashes' => [
		'@' => 'Эканирование слешами',
	],
	'stripcslashes' => [
		'@' => 'Удаление слешей созданных addslashes',
	],
	'stripslashes' => [
		'@' => 'Удаление слешей',
	],
	#
	'urldecode' => [
		'@' => 'Декодирование URL-кодированной строки',
	],
	'urlencode' => [
		'@' => 'URL-кодирование строки',
	],
	#
	'strtolower' => [
		'@' => 'Преобразование в нижний регистр',
	],
	'strtoupper' => [
		'@' => 'Преобразование в нижний регистр',
	],
	'ucfirst' => [
		'@' => 'Преобразование первого символа строки в верхний регистр',
	],
	'icfirst' => [
		'@' => 'Преобразование первого символа строки в нижний регистр',
	],
	'ucwords' => [
		'@' => 'Преобразование первых символов слов в верхний регистр',
		'DELIMITERS' => 'Символы разделителей слов',
	],
	#
	'number_format' => [
		'@' => 'Форматирование числа',
		'DECIMALS' => 'Число знаков после запятой'.$strNotRequired,
		'DEC_POINT' => 'Разделитель дробной части'.$strNotRequired,
		'THOUSANDS_SEP' => 'Разделитель тысяч'.$strNotRequired,
	],
	'money_format' => [
		'@' => 'Форматирование числа как денежной величины',
		'FORMAT' => 'Формат валюты',
	],
	#
	'nl2br' => [
		'@' => 'Замена переводов строки на HTML-разрыв строки <br> (<br/>)',
		'IS_XHTML' => 'Формат XHTML',
	],
	'wordwrap' => [
		'@' => 'Перенос строки по указанному количеству символов',
		'WIDTH' => 'Количество символов в строке',
		'BREAK' => 'Символ переноса строк',
		'CUT' => 'Обрезать длинные слова',
	],
];

foreach($arLang as $strFunctionName => $arFunc){
	foreach($arFunc as $key => $value){
		if($key == '@'){
			$MESS[$strLangFunc.$strFunctionName] = $value;
		}
		elseif(preg_match('#^([A-z0-9_-]+)__HINT$#', $key, $arMatch)){
			$MESS[$strLangFunc.$strFunctionName.'__'.$key] = htmlspecialcharsbx($value);
		}
		else{
			$MESS[$strLangFunc.$strFunctionName.'__'.$key] = $value;
		}
	}
}
