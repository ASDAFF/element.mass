<?
namespace WD\Antirutin;

Helper::loadMessages(__FILE__);

return [
	'Helper::roundEx' => [
		'VALUE' => true,
		'PRECISION' => [
			'DEFAULT' => 0,
		],
		'FUNC' => [
			'DEFAULT' => 'round',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			$arArgs[0] = Helper::convertDecPoint($arArgs[0]);
		}
	],
	'Helper::convertEncoding' => [
		'VALUE' => true,
		'FROM' => [
			'DEFAULT' => Helper::isUtf() ? 'windows-1251' : 'UTF-8',
		],
		'TO' => [
			'DEFAULT' => Helper::isUtf() ? 'UTF-8' : 'windows-1251',
		],
	],
	'Helper::evalExpression' => [
		'EXPRESSION' => [
			'DEFAULT' => '$x * 1000',
			'REQUIRED' => true,
		],
		'VALUE' => true,
		'VAR_NAME' => [
			'DEFAULT' => 'x',
		],
	],
	'Helper::execCommand' => [
		'COMMAND' => [
			'DEFAULT' => 'php -v',
			'REQUIRED' => true,
		],
		'VALUE' => true,
	],
	'Helper::generateGuid' => [
		'UPPERCASE' => [
			'DEFAULT' => 'false',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[0])){
				$arArgs[0] = Helper::evalReturn($arArgs[0]);
			}
		}
	],
	'Helper::ucFirst' => [
		'VALUE' => true,
	],
	'Helper::lcFirst' => [
		'VALUE' => true,
	],
];
