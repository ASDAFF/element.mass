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
		'__CALLBACK' => function(&$arFunc, &$arArguments){
			$strArg = &$arArguments[1];
			if(is_numeric($strArg)){
				$strArg = intVal($strArg);
			}
			#
			$strArg = &$arArguments[3];
			if(!is_null($strArg)){
				$arMap = [
					'STR_PAD_RIGHT' => STR_PAD_RIGHT,
					'STR_PAD_LEFT' => STR_PAD_LEFT,
					'STR_PAD_BOTH' => STR_PAD_BOTH,
				];
				if(array_key_exists($strArg, $arMap)){
					$strArg = $arMap[$strArg];
				}
			}
			unset($strArg);
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
	'icfirst' => [
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
		'__CALLBACK' => function(&$arFunc, &$arArguments){
			$strArg = &$arArguments[0];
			if(is_string($strArg)){
				$arLocaleSettings = localeconv();
				$bPoint = $arLocaleSettings['decimal_point'] == '.';
				$strReplace = $arLocaleSettings['decimal_point'] == '.' ? ',' : '.';
				$strArg = str_replace($bPoint ? ',' : '.', $bPoint ? '.' : ',', $arArguments[0]);
			}
			if(is_numeric($strArg)){
				$strArg = floatVal($strArg);
			}
		}
	],
	'money_format' => [
		'FORMAT' => [
			'DEFAULT' => '#11#',
		],
		'VALUE' => true,
	],
	#
	'nl2br' => [
		'VALUE' => true,
		'IS_XHTML' => [
			'DEFAULT' => '#TRUE#',
			'DEFAULT_TEXT' => 'true',
		],
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
];
