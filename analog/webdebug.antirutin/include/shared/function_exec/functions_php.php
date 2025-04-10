<?
namespace WD\Antirutin;

Helper::loadMessages(__FILE__);

return [
	'trim' => [
		'VALUE' => true,
		'MASK' => [
			'REQUIRED' => false,
			'DEFAULT_TEXT' => '" \t\n\r\0\x0B"',
		],
	],
	'ltrim' => [
		'VALUE' => true,
		'MASK' => [
			'REQUIRED' => false,
			'DEFAULT_TEXT' => '" \t\n\r\0\x0B"',
		],
	],
	'rtrim' => [
		'VALUE' => true,
		'MASK' => [
			'REQUIRED' => false,
			'DEFAULT_TEXT' => '" \t\n\r\0\x0B"',
		],
	],
	#
	'str_ireplace' => [
		'SEARCH' => [
			'REQUIRED' => true,
		],
		'REPLACE' => [
			'REQUIRED' => true,
		],
		'VALUE' => true,
	],
	'str_replace' => [
		'SEARCH' => [
			'REQUIRED' => true,
		],
		'REPLACE' => [
			'REQUIRED' => true,
		],
		'VALUE' => true,
	],
	'substr_replace' => [
		'VALUE' => true,
		'REPLACEMENT' => [
			'REQUIRED' => true,
		],
		'START' => [
			'REQUIRED' => true,
		],
		'LENGTH' => [],
	],
	'preg_replace' => [
		'PATTERN' => [
			'REQUIRED' => true,
		],
		'REPLACEMENT' => [
			'REQUIRED' => true,
		],
		'VALUE' => true,
		'LIMIT' => [],
	],
	#
	'substr' => [
		'VALUE' => true,
		'START' => [
			'REQUIRED' => true,
		],
		'LENGTH' => [],
	],
	'str_pad' => [
		'VALUE' => true,
		'PAD_LENGTH' => [
			'REQUIRED' => true,
		],
		'PAD_STRING' => [
			'DEFAULT_TEXT' => '" "',
		],
		'PAD_TYPE' => [
			'DEFAULT' => 'STR_PAD_RIGHT',
			'DEFAULT_TEXT' => 'STR_PAD_RIGHT',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(is_numeric($arArgs[1])){
				$arArgs[1] = intVal($arArgs[1]);
			}
			if(isset($arArgs[3])){
				$arArgs[3] = Helper::evalReturn($arArgs[3]);
			}
		}
	],
	'str_repeat' => [
		'VALUE' => true,
		'MULTIPLIER' => [
			'REQUIRED' => true,
		],
	],
	'str_shuffle' => [
		'VALUE' => true,
	],
	'strrev' => [
		'VALUE' => true,
	],
	#
	'base64_decode' => [
		'VALUE' => true,
	],
	'base64_encode' => [
		'VALUE' => true,
	],
	#
	'html_entity_decode' => [
		'VALUE' => true,
		'FLAGS' => [],
		'ENCODING' => [],
	],
	'htmlentities' => [
		'VALUE' => true,
		'FLAGS' => [],
		'ENCODING' => [],
		'DOUBLE_ENCODE' => [],
	],
	'htmlspecialchars' => [
		'VALUE' => true,
		'FLAGS' => [],
		'ENCODING' => [],
		'DOUBLE_ENCODE' => [],
	],
	'strip_tags' => [
		'VALUE' => true,
		'ALLOWABLE_TAGS' => [],
	],
	#
	'addslashes' => [
		'VALUE' => true,
	],
	'stripcslashes' => [
		'VALUE' => true,
	],
	'stripslashes' => [
		'VALUE' => true,
	],
	#
	'urldecode' => [
		'VALUE' => true,
	],
	'urlencode' => [
		'VALUE' => true,
	],
	#
	'strtolower' => [
		'VALUE' => true,
	],
	'strtoupper' => [
		'VALUE' => true,
	],
	'ucfirst' => [
		'VALUE' => true,
	],
	'lcfirst' => [
		'VALUE' => true,
	],
	'ucwords' => [
		'VALUE' => true,
		'DELIMITERS' => [
			'DEFAULT_TEXT' => '" \t\r\n\f\v"',
		],
	],
	#
	'number_format' => [
		'VALUE' => true,
		'DECIMALS' => [
			'DEFAULT' => 0,
		],
		'DEC_POINT' => [
			'DEFAULT' => '.',
		],
		'THOUSANDS_SEP' => [
			'DEFAULT' => ' ',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			$arArgs[0] = Helper::convertDecPoint($arArgs[0]);
		}
	],
	'money_format' => [
		'FORMAT' => [
			'REQUIRED' => true,
		],
		'VALUE' => true,
	],
	'floor' => [
		'VALUE' => true,
	],
	'ceil' => [
		'VALUE' => true,
	],
	'round' => [
		'VALUE' => true,
		'PRECISION' => [],
		'MODE' => [],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = intVal($arArgs[1]);
			};
			if(isset($arArgs[2])){
				$arArgs[2] = Helper::evalReturn($arArgs[3]);
			};
		}
	],
	'intval' => [
		'VALUE' => true,
		'BASE' => [],
	],
	'floatval' => [
		'VALUE' => true,
	],
	'uniqid' => [
		'PREFIX' => [],
		'MORE_ENTROPY' => [],
	],
	'rand' => [
		'MIN' => [],
		'MAX' => [],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(is_string($arArgs[0]) && !strlen($arArgs[0]) || $arArgs[0] < 0 || is_null($arArgs[0])){
				$arArgs[0] = 0;
			}
			if(is_string($arArgs[1]) && !strlen($arArgs[1]) || $arArgs[1] < 0 || is_null($arArgs[1])){
				$arArgs[1] = getrandmax();
			}
			$arArgs[0] = intVal($arArgs[0]);
			$arArgs[1] = intVal($arArgs[1]);
		}
	],
	#
	'nl2br' => [
		'VALUE' => true,
		'IS_XHTML' => [
			'DEFAULT' => 'true',
			'DEFAULT_TEXT' => 'true',
		],
		'__CALLBACK' => function(&$arFunc, &$arArgs){
			if(isset($arArgs[1])){
				$arArgs[1] = Helper::evalReturn($arArgs[1]);
			};
		}
	],
	'wordwrap' => [
		'VALUE' => true,
		'WIDTH' => [
			'DEFAULT_TEXT' => '75',
		],
		'BREAK' => [
			'DEFAULT_TEXT' => '\n',
		],
		'CUT' => [
			'DEFAULT_TEXT' => 'false',
		],
	],
	#
];
