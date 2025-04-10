<?
$strLang = \WD\Antirutin\Plugin::LANG_PREFIX.'_FUNCTION_EXEC_';
$strNotRequired = ' (необязательно)';

$arLang = [
	'Helper::roundEx' => [
		'@' => 'Округление',
		'PRECISION' => 'Точность'.$strNotRequired,
		'FUNC' => 'Функция для обработки (round, floor или ceil)'.$strNotRequired,
	],
	'Helper::convertEncoding' => [
		'@' => 'Конвертация кодировки',
		'FROM' => 'Исходная кодировка',
		'TO' => 'Новая кодировка',
	],
	'Helper::evalExpression' => [
		'@' => 'Выполнение PHP-выражения',
		'EXPRESSION' => 'PHP-выражение (вида $x * 1000)',
		'VAR_VALUE' => 'Значение переменной $x',
		'VAR_NAME' => 'Название переменной в выражении',
	],
	'Helper::execCommand' => [
		'@' => 'Выполнение команды сервера',
		'COMMAND' => 'Команда',
	],
	'Helper::generateGuid' => [
		'@' => 'Генерация GUID',
		'UPPERCASE' => 'Верхний регистр',
	],
	'Helper::ucFirst' => [
		'@' => 'Преобразование первого символа строки в верхний регистр',
	],
	'Helper::lcFirst' => [
		'@' => 'Преобразование первого символа строки в нижний регистр',
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
